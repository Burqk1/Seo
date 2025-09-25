<?php include __DIR__ . "/layout_top.php"; ?>
<section class="panel">
  <h2>📝 Yeni Destek Talebi</h2>
  <form method="post" action="/tickets/store" class="form-grid">
    <div>
      <label>Konu</label>
      <input name="subject" required placeholder="Örn: Site menüsü mobilde bozuluyor">
    </div>
    <div class="full">
      <label>Açıklama</label>
      <textarea name="body" rows="4" required placeholder="Sorunu ayrıntılı anlatın..."></textarea>
    </div>
    <input type="hidden" name="_csrf" value="<?= $this->auth->csrf() ?>">
    <div class="full"><button type="submit">Talebi Kaydet</button></div>
  </form>
</section>

<section class="panel">
  <h2>📋 Tüm Talepler</h2>
  <table class="table">
    <thead><tr><th>ID</th><th>Konu</th><th>Durum</th><th>Oluşturan</th><th>Tarih</th><th>Güncelle</th></tr></thead>
    <tbody>
      <?php foreach ($list as $r): ?>
      <tr>
        <td>#<?= $r['id'] ?></td>
        <td><?= htmlspecialchars($r['subject']) ?></td>
        <td><span class="badge <?= $r['status'] ?>"><?= 
          $r['status'] === 'new' ? 'Yeni' : 
          ($r['status'] === 'in_progress' ? 'İşlemde' : 
          ($r['status'] === 'resolved' ? 'Çözüldü' : $r['status'])) 
        ?></span></td>
        <td><?= $r['created_by'] ?></td>
        <td><?= $r['created_at'] ?></td>
        <td>
          <form method="post" action="/tickets/update" class="inline">
            <input type="hidden" name="id" value="<?= $r['id'] ?>">
            <select name="status">
              <?php 
              $statusOptions = [
                'new' => 'Yeni',
                'in_progress' => 'İşlemde', 
                'resolved' => 'Çözüldü'
              ];
              foreach($statusOptions as $value => $label): ?>
                <option value="<?= $value ?>" <?= $value===$r['status']?'selected':'' ?>><?= $label ?></option>
              <?php endforeach; ?>
            </select>
            <input type="hidden" name="_csrf" value="<?= $this->auth->csrf() ?>">
            <button>Kaydet</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>
<?php include __DIR__ . "/layout_bottom.php"; ?>
