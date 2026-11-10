<?php
namespace App\Controllers;

use App\Db;
use App\Auth;
use App\Services\LighthouseService;
use App\Services\SeoAnalyzer;


class ReportController
{
    private Db $db;
    private Auth $auth;
    private LighthouseService $lighthouse;
    private SeoAnalyzer $seoAnalyzer;

    public function __construct(Db $db, Auth $auth)
    {
        $this->db = $db;
        $this->auth = $auth;
        $this->lighthouse = new LighthouseService();
        $this->seoAnalyzer = new SeoAnalyzer();
    }


    public function index(): string
    {
        $this->auth->requireLogin();
        
        $list = $this->db->pdo()->query("SELECT * FROM seo_reports ORDER BY id DESC")->fetchAll();
        
        ob_start();
        $title = "SEO Raporları";
        include __DIR__ . "/../Views/reports_index.php";
        return ob_get_clean();
    }


    public function upload(): void
    {
        $this->auth->requireLogin();
        $this->auth->checkCsrf();

        if (!isset($_FILES['json']) || $_FILES['json']['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            exit('Yükleme hatası');
        }

        $name = 'lh_' . time() . '.json';
        $dest = __DIR__ . "/../../storage/reports/" . $name;
        move_uploaded_file($_FILES['json']['tmp_name'], $dest);

        $raw = file_get_contents($dest);
        $data = json_decode($raw, true);

        $seo = $data['categories']['seo']['score'] ?? 0;
        $perf = $data['categories']['performance']['score'] ?? 0;
        $acc = $data['categories']['accessibility']['score'] ?? 0;

        $st = $this->db->pdo()->prepare("INSERT INTO seo_reports(file_path, seo_score, avg_speed, accessibility, label, analysis_type) VALUES(?,?,?,?,?,?)");
        $st->execute([
            "/storage/reports/" . $name,
            round($seo * 100),
            round($perf * 100),
            round($acc * 100),
            $_POST['label'] ?? ('Rapor ' . date('Y-m-d')),
            'manual_upload'
        ]);

        header('Location: /reports');
    }


    public function analyzeUrl(): void
    {
        $this->auth->requireLogin();
        $this->auth->checkCsrf();

        $url = trim($_POST['url'] ?? '');
        $label = trim($_POST['label'] ?? '');

        if (empty($url)) {
            http_response_code(400);
            exit('URL gerekli');
        }

        if (!preg_match('/^https?:\/\//', $url)) {
            $url = 'https://' . $url;
        }

        try {
            $lighthouseData = $this->lighthouse->analyzeUrl($url);
            
            $seoData = $this->seoAnalyzer->analyzeUrl($url);
            
            $combinedReport = $this->createCombinedReport($lighthouseData, $seoData);
            
            $filePath = $this->lighthouse->saveReport($combinedReport, $label ?: $url);
            
            $st = $this->db->pdo()->prepare("INSERT INTO seo_reports(file_path, seo_score, avg_speed, accessibility, label, analysis_type, url, seo_issues) VALUES(?,?,?,?,?,?,?,?)");
            $st->execute([
                $filePath,
                round($lighthouseData['categories']['seo']['score'] * 100),
                round($lighthouseData['categories']['performance']['score'] * 100),
                round($lighthouseData['categories']['accessibility']['score'] * 100),
                $label ?: ('Analiz: ' . parse_url($url, PHP_URL_HOST)),
                'automatic_analysis',
                $url,
                count($seoData['issues'])
            ]);

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Analiz tamamlandı',
                'lighthouse_score' => round($lighthouseData['categories']['seo']['score'] * 100),
                'seo_score' => $seoData['overall_score'],
                'issues_found' => count($seoData['issues'])
            ]);

        } catch (\Exception $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Analiz hatası: ' . $e->getMessage()
            ]);
        }
    }


    public function analyzeBulk(): void
    {
        $this->auth->requireLogin();
        $this->auth->checkCsrf();

        $urls = array_filter(array_map('trim', explode("\n", $_POST['urls'] ?? '')));
        $label = trim($_POST['label'] ?? 'Toplu Analiz');

        if (empty($urls)) {
            http_response_code(400);
            exit('En az bir URL gerekli');
        }

        if (count($urls) > 10) {
            http_response_code(400);
            exit('Maksimum 10 URL analiz edilebilir');
        }

        try {
            $results = [];
            
            foreach ($urls as $index => $url) {
                if (!preg_match('/^https?:\/\//', $url)) {
                    $url = 'https://' . $url;
                }

                $lighthouseData = $this->lighthouse->analyzeUrl($url);
                $seoData = $this->seoAnalyzer->analyzeUrl($url);
                $combinedReport = $this->createCombinedReport($lighthouseData, $seoData);
                
                $filePath = $this->lighthouse->saveReport($combinedReport, "$label - " . ($index + 1));
                
                $st = $this->db->pdo()->prepare("INSERT INTO seo_reports(file_path, seo_score, avg_speed, accessibility, label, analysis_type, url, seo_issues) VALUES(?,?,?,?,?,?,?,?)");
                $st->execute([
                    $filePath,
                    round($lighthouseData['categories']['seo']['score'] * 100),
                    round($lighthouseData['categories']['performance']['score'] * 100),
                    round($lighthouseData['categories']['accessibility']['score'] * 100),
                    "$label - " . parse_url($url, PHP_URL_HOST),
                    'bulk_analysis',
                    $url,
                    count($seoData['issues'])
                ]);

                $results[] = [
                    'url' => $url,
                    'lighthouse_score' => round($lighthouseData['categories']['seo']['score'] * 100),
                    'seo_score' => $seoData['overall_score']
                ];

                if ($index < count($urls) - 1) {
                    sleep(2);
                }
            }

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => count($urls) . ' URL analiz edildi',
                'results' => $results
            ]);

        } catch (\Exception $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Toplu analiz hatası: ' . $e->getMessage()
            ]);
        }
    }


    public function apiLast10(): void
    {
        $this->auth->requireLogin();
        header('Content-Type: application/json; charset=utf-8');
        
        $list = $this->db->pdo()->query("
            SELECT label, seo_score, avg_speed, accessibility, analysis_type, url, seo_issues, created_at 
            FROM seo_reports 
            ORDER BY id DESC 
            LIMIT 10
        ")->fetchAll();
        
        echo json_encode($list);
    }


    public function apiReportDetail(): void
    {
        $this->auth->requireLogin();

        $reportId = (int)($_GET['id'] ?? 0);
        
        if (!$reportId) {
            http_response_code(400);
            exit('Rapor ID gerekli');
        }

        $report = $this->db->pdo()->prepare("SELECT * FROM seo_reports WHERE id = ?");
        $report->execute([$reportId]);
        $reportData = $report->fetch();

        if (!$reportData) {
            http_response_code(404);
            exit('Rapor bulunamadı');
        }

        $jsonPath = __DIR__ . '/../../' . ltrim($reportData['file_path'], '/');
        $jsonData = [];
        
        if (file_exists($jsonPath)) {
            $jsonData = json_decode(file_get_contents($jsonPath), true) ?? [];
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'report' => $reportData,
            'lighthouse_data' => $jsonData
        ]);
    }


    private function createCombinedReport(array $lighthouseData, array $seoData): array
    {
        return [
            'url' => $lighthouseData['url'],
            'timestamp' => $lighthouseData['timestamp'],
            'categories' => $lighthouseData['categories'],
            'audits' => $lighthouseData['audits'],
            'metrics' => $lighthouseData['metrics'],
            'seo_analysis' => [
                'overall_score' => $seoData['overall_score'],
                'issues' => $seoData['issues'],
                'recommendations' => $seoData['recommendations'],
                'technical_details' => $seoData['technical_details']
            ],
            'analysis_summary' => [
                'lighthouse_seo' => round($lighthouseData['categories']['seo']['score'] * 100),
                'custom_seo' => $seoData['overall_score'],
                'performance' => round($lighthouseData['categories']['performance']['score'] * 100),
                'accessibility' => round($lighthouseData['categories']['accessibility']['score'] * 100),
                'total_issues' => count($seoData['issues']),
                'critical_issues' => count(array_filter($seoData['issues'], fn($issue) => $issue['type'] === 'critical'))
            ]
        ];
    }

 
    public function generateDemo(): void
    {
        $this->auth->requireLogin();
        
        $demoUrls = [
            'https://demo1.example.com',
            'https://demo2.example.com',
            'https://demo3.example.com'
        ];

        foreach ($demoUrls as $index => $url) {
            $lighthouseData = $this->lighthouse->analyzeUrl($url);
            $seoData = $this->seoAnalyzer->analyzeUrl($url);
            $combinedReport = $this->createCombinedReport($lighthouseData, $seoData);
            
            $filePath = $this->lighthouse->saveReport($combinedReport, "Demo Rapor " . ($index + 1));
            
            $st = $this->db->pdo()->prepare("INSERT INTO seo_reports(file_path, seo_score, avg_speed, accessibility, label, analysis_type, url, seo_issues) VALUES(?,?,?,?,?,?,?,?)");
            $st->execute([
                $filePath,
                round($lighthouseData['categories']['seo']['score'] * 100),
                round($lighthouseData['categories']['performance']['score'] * 100),
                round($lighthouseData['categories']['accessibility']['score'] * 100),
                "Demo Rapor " . ($index + 1),
                'demo',
                $url,
                count($seoData['issues'])
            ]);
        }

        header('Location: /reports');
    }
}