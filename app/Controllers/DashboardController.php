<?php
namespace App\Controllers;
use App\Db; use App\Auth;
class DashboardController{
  private Db $db; private Auth $auth;
  public function __construct(Db $db, Auth $auth){ $this->db=$db; $this->auth=$auth; }
  public function index(): string{
    $pdo=$this->db->pdo();
    $tickets_total=(int)$pdo->query("SELECT COUNT(*) c FROM tickets")->fetch()['c']??0;
    $tickets_open=(int)$pdo->query("SELECT COUNT(*) c FROM tickets WHERE status IN ('new','in_progress')")->fetch()['c']??0;
    $reports_total=(int)$pdo->query("SELECT COUNT(*) c FROM seo_reports")->fetch()['c']??0;
    $latest_speed=(float)($pdo->query("SELECT avg_speed FROM seo_reports ORDER BY id DESC LIMIT 1")->fetch()['avg_speed']??0);
    
    $tickets_resolved_today=(int)$pdo->query("SELECT COUNT(*) c FROM tickets WHERE status='resolved' AND DATE(created_at)=CURDATE()")->fetch()['c']??0;
    $avg_seo_score=(float)($pdo->query("SELECT AVG(seo_score) as avg FROM seo_reports")->fetch()['avg']??0);
    $tickets_this_week=(int)$pdo->query("SELECT COUNT(*) c FROM tickets WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetch()['c']??0;
    $reports_this_month=(int)$pdo->query("SELECT COUNT(*) c FROM seo_reports WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetch()['c']??0;
    ob_start(); $title="Dashboard"; include __DIR__."/../Views/layout_top.php"; ?>
    <div class="grid">
      <div class="card">
        <div class="card-title">📝 Toplam Destek Talebi</div>
        <div class="card-value"><?= $tickets_total ?></div>
      </div>
      <div class="card">
        <div class="card-title">🔥 Açık Talep</div>
        <div class="card-value"><?= $tickets_open ?></div>
      </div>
      <div class="card">
        <div class="card-title">📊 SEO Raporu Sayısı</div>
        <div class="card-value"><?= $reports_total ?></div>
      </div>
      <div class="card">
        <div class="card-title">⚡ Son Ortalama Hız</div>
        <div class="card-value"><?= $latest_speed ?></div>
      </div>
    </div>
    
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon">✅</div>
        <div class="stat-info">
          <div class="stat-value"><?= $tickets_resolved_today ?></div>
          <div class="stat-label">Bugün Çözülen</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">📊</div>
        <div class="stat-info">
          <div class="stat-value"><?= round($avg_seo_score) ?></div>
          <div class="stat-label">Ortalama SEO</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">📅</div>
        <div class="stat-info">
          <div class="stat-value"><?= $tickets_this_week ?></div>
          <div class="stat-label">Bu Hafta</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">📈</div>
        <div class="stat-info">
          <div class="stat-value"><?= $reports_this_month ?></div>
          <div class="stat-label">Aylık Rapor</div>
        </div>
      </div>
    </div>
    
    <div class="panel">
      <h2>📈 SEO Skorları (Son 10 Rapor)</h2>
      <canvas id="seoChart"></canvas>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
      fetch('/api/reports/last10').then(r=>r.json()).then(data=>{
        const labels=data.map(x=>x.label);
        const speed=data.map(x=>x.avg_speed);
        const seo=data.map(x=>x.seo_score);
        const acc=data.map(x=>x.accessibility);
        new Chart(document.getElementById('seoChart'),{type:'line',data:{labels,datasets:[{label:'Hız',data:speed},{label:'SEO',data:seo},{label:'Erişilebilirlik',data:acc}]}});
      });
    </script>
    <?php include __DIR__."/../Views/layout_bottom.php"; return ob_get_clean();
  }
}