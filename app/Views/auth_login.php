<?php include __DIR__ . "/layout_top.php"; ?>
<div class="auth-wrap">
  <div class="auth-grid">
    <form class="auth-card" method="post" action="/login">
      <h1>Giriş Yap</h1>
      <p class="muted">Hesabın yoksa sağdaki formdan kayıt olabilirsin.</p>
      <?php if (!empty($error ?? null)): ?><div class="alert"><?= htmlspecialchars($error) ?></div><?php endif; ?>
      <label>E‑posta</label><input type="email" name="email" required placeholder="ornek@firma.com">
      <label>Şifre</label><input type="password" name="password" required placeholder="••••••••">
      <input type="hidden" name="_csrf" value="<?= $this->auth->csrf() ?>">
      <button type="submit">Giriş</button>
      <p class="muted">Kısayol: <kbd>Alt</kbd> + <kbd>G</kbd></p>
    </form>

    <form class="auth-card" method="post" action="/register">
      <h1>Kayıt Ol</h1>
      <p class="muted">Yeni hesap oluşturarak panele erişebilirsin.</p>
      <label>Ad Soyad</label><input name="name" required placeholder="Ad Soyad">
      <label>E‑posta</label><input type="email" name="email" required placeholder="ornek@firma.com">
      <label>Şifre</label><input type="password" name="password" required minlength="6" placeholder="En az 6 karakter">
      <input type="hidden" name="_csrf" value="<?= $this->auth->csrf() ?>">
      <button type="submit">Kayıt</button>
    </form>
  </div>
</div>
<?php include __DIR__ . "/layout_bottom.php"; ?>
