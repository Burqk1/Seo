<?php include __DIR__ . "/layout_top.php"; ?>

<!-- Otomatik URL Analizi -->
<section class="panel">
  <h2>🔍 URL Analizi (Otomatik)</h2>
  <p class="help-text">URL girerek otomatik Lighthouse ve SEO analizi yapın</p>
  <form id="urlAnalysisForm" class="form-grid">
    <div>
      <label>Web Sitesi URL</label>
      <input name="url" type="url" placeholder="https://example.com" required>
    </div>
    <div>
      <label>Etiket (Opsiyonel)</label>
      <input name="label" placeholder="Örn: Anasayfa Analizi">
    </div>
    <input type="hidden" name="_csrf" value="<?= $this->auth->csrf() ?>">
    <div class="full">
      <button type="submit" id="analyzeBtn">🚀 Analiz Et</button>
      <div id="analysisProgress" style="display: none;">
        <div class="progress-bar">
          <div class="progress-fill"></div>
        </div>
        <p>Analiz yapılıyor... Bu işlem 30-60 saniye sürebilir.</p>
      </div>
    </div>
  </form>
</section>

<!-- Toplu URL Analizi -->
<section class="panel">
  <h2>📊 Toplu URL Analizi</h2>
  <p class="help-text">Birden fazla URL'yi aynı anda analiz edin (maksimum 10 URL)</p>
  <form id="bulkAnalysisForm" class="form-grid">
    <div class="full">
      <label>URL Listesi (Her satıra bir URL)</label>
      <textarea name="urls" rows="6" placeholder="https://example.com&#10;https://example.com/about&#10;https://example.com/contact" required></textarea>
    </div>
    <div>
      <label>Etiket</label>
      <input name="label" placeholder="Toplu Analiz - Proje Adı" required>
    </div>
    <input type="hidden" name="_csrf" value="<?= $this->auth->csrf() ?>">
    <div class="full">
      <button type="submit" id="bulkAnalyzeBtn">📈 Toplu Analiz Et</button>
      <div id="bulkProgress" style="display: none;">
        <div class="progress-bar">
          <div class="progress-fill"></div>
        </div>
        <p id="bulkProgressText">Analiz yapılıyor...</p>
      </div>
    </div>
  </form>
</section>

<!-- Manuel JSON Yükleme -->
<section class="panel">
  <h2>📤 Manuel Lighthouse JSON Yükle</h2>
  <p class="help-text">Hazır Lighthouse JSON dosyanızı yükleyin</p>
  <form method="post" action="/reports/upload" enctype="multipart/form-data" class="form-grid" id="uploadForm">
    <div>
      <label>Etiket</label>
      <input name="label" placeholder="Örn: Anasayfa 2025-08" required>
    </div>
    <div class="full">
      <label>JSON Dosya</label>
      <div class="drag-drop-area" id="dragDropArea">
        <div class="drag-drop-content">
          <div class="drag-drop-icon">📁</div>
          <div class="drag-drop-text">
            <strong>Dosyayı buraya sürükleyin</strong> veya <span class="drag-drop-browse">göz atın</span>
          </div>
          <div class="drag-drop-info">Sadece .json dosyaları kabul edilir</div>
          <input type="file" name="json" accept=".json,application/json" required id="fileInput" style="display: none;">
        </div>
        <div class="file-preview" id="filePreview" style="display: none;">
          <div class="file-info">
            <span class="file-name"></span>
            <span class="file-size"></span>
          </div>
          <button type="button" class="remove-file" id="removeFile">&times;</button>
        </div>
      </div>
    </div>
    <input type="hidden" name="_csrf" value="<?= $this->auth->csrf() ?>">
    <div class="full"><button type="submit" id="submitBtn" disabled>Yükle</button></div>
  </form>
</section>

<section class="panel">
  <h2>📈 Raporlar</h2>
  <div style="margin-bottom: 15px;">
    <a href="/reports/demo" class="btn btn-secondary">🎯 Demo Raporlar Oluştur</a>
  </div>
  <div class="table-responsive">
    <table class="table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Etiket</th>
        <th>Tip</th>
        <th>URL</th>
        <th>Performans</th>
        <th>SEO</th>
        <th>Erişilebilirlik</th>
        <th>Sorunlar</th>
        <th>Tarih</th>
        <th>İşlemler</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($list as $r): ?>
      <tr>
        <td>#<?= $r['id'] ?></td>
        <td><?= htmlspecialchars($r['label']) ?></td>
        <td>
          <?php
          $typeLabels = [
            'manual_upload' => '📤 Manuel',
            'automatic_analysis' => '🔍 Otomatik',
            'bulk_analysis' => '📊 Toplu',
            'demo' => '🎯 Demo'
          ];
          $type = $r['analysis_type'] ?? 'manual_upload';
          ?>
          <span class="badge <?= $type === 'demo' ? 'warn' : ($type === 'automatic_analysis' ? 'success' : 'info') ?>">
            <?= $typeLabels[$type] ?? $type ?>
          </span>
        </td>
        <td>
          <?php if (!empty($r['url'])): ?>
            <a href="<?= htmlspecialchars($r['url']) ?>" target="_blank" title="<?= htmlspecialchars($r['url']) ?>">
              <?= htmlspecialchars(parse_url($r['url'], PHP_URL_HOST) ?: $r['url']) ?>
            </a>
          <?php else: ?>
            <span class="text-muted">-</span>
          <?php endif; ?>
        </td>
        <td>
          <span class="badge <?= $r['avg_speed'] >= 80 ? 'success' : ($r['avg_speed'] >= 60 ? 'warn' : 'error') ?>">
            <?= $r['avg_speed'] ?>
          </span>
        </td>
        <td>
          <span class="badge <?= $r['seo_score'] >= 80 ? 'success' : ($r['seo_score'] >= 60 ? 'warn' : 'error') ?>">
            <?= $r['seo_score'] ?>
          </span>
        </td>
        <td>
          <span class="badge <?= $r['accessibility'] >= 80 ? 'success' : ($r['accessibility'] >= 60 ? 'warn' : 'error') ?>">
            <?= $r['accessibility'] ?>
          </span>
        </td>
        <td>
          <?php if (isset($r['seo_issues']) && $r['seo_issues'] > 0): ?>
            <span class="badge error"><?= $r['seo_issues'] ?></span>
          <?php else: ?>
            <span class="badge success">0</span>
          <?php endif; ?>
        </td>
        <td><?= date('d.m.Y H:i', strtotime($r['created_at'])) ?></td>
        <td>
          <a href="<?= $r['file_path'] ?>" target="_blank" class="btn btn-sm">📄 JSON</a>
          <button onclick="showReportDetails(<?= $r['id'] ?>)" class="btn btn-sm btn-primary">📊 Detay</button>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  </div>
  
  <?php if (empty($list)): ?>
    <div class="empty-state">
      <p>🔍 Henüz rapor yok. Yukarıdaki formları kullanarak analiz başlatın!</p>
    </div>
  <?php endif; ?>
</section>

<!-- Rapor Detay Modal -->
<div id="reportModal" class="modal" style="display: none;">
  <div class="modal-content">
    <div class="modal-header">
      <h3>📊 Rapor Detayları</h3>
      <span class="modal-close" onclick="closeReportModal()">&times;</span>
    </div>
    <div class="modal-body" id="reportDetails">
      <div class="loading">Yükleniyor...</div>
    </div>
  </div>
</div>
<?php include __DIR__ . "/layout_bottom.php"; ?>
