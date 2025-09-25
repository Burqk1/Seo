<?php
$user = $this->auth->user();
?>
<!doctype html><html lang="tr"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= htmlspecialchars($title ?? 'Panel') ?> · Bilişim</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/css/styles.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="/js/app.js"></script>
</head><body>
<header class="modern-navbar">
  <div class="navbar-container">
    <div class="navbar-brand">
      <div class="brand-icon">⚡</div>
      <div class="brand-text">
        <div class="brand-title">Bilişim</div>
        <div class="brand-subtitle">Destek & SEO</div>
      </div>
    </div>

    <nav class="navbar-nav" id="navbarNav">
      <?php if($user): ?>
        <div class="nav-links">
          <a href="/" class="nav-link <?= $_SERVER['REQUEST_URI'] === '/' ? 'active' : '' ?>" data-text="Dashboard">
            <span class="nav-icon">📊</span>
            <span class="nav-text">Dashboard</span>
          </a>
          <a href="/tickets" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/tickets') !== false ? 'active' : '' ?>" data-text="Destek">
            <span class="nav-icon">🎫</span>
            <span class="nav-text">Destek</span>
          </a>
          <a href="/reports" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/reports') !== false ? 'active' : '' ?>" data-text="SEO Rapor">
            <span class="nav-icon">📈</span>
            <span class="nav-text">SEO Rapor</span>
          </a>
        </div>

      <?php else: ?>
        <div class="nav-links">
          <a href="/login" class="nav-link login-btn">
            <span class="nav-icon">🔐</span>
            <span class="nav-text">Giriş Yap</span>
          </a>
        </div>
      <?php endif; ?>
    </nav>

    <?php if($user): ?>
    <div class="navbar-controls">
      <button class="theme-toggle" id="themeToggle" title="Tema Değiştir">
        <span class="theme-icon">🌙</span>
      </button>
      
      <div class="user-dropdown" id="userDropdown">
            <button class="user-trigger">
              <div class="user-avatar">
                <span><?= strtoupper(substr($user['name'], 0, 1)) ?></span>
              </div>
              <div class="user-info">
                <div class="user-name"><?= htmlspecialchars($user['name']) ?></div>
                <div class="user-role">Admin</div>
              </div>
              <span class="dropdown-arrow">▼</span>
            </button>
            
            <div class="dropdown-menu">
              <a href="/profile" class="dropdown-item">
                <span class="dropdown-icon">👤</span>
                Profil
              </a>
              <a href="/settings" class="dropdown-item">
                <span class="dropdown-icon">⚙️</span>
                Ayarlar
              </a>
              <div class="dropdown-divider"></div>
              <form action="/logout" method="post" class="dropdown-form">
                <input type="hidden" name="_csrf" value="<?= $this->auth->csrf() ?>">
                <button type="submit" class="dropdown-item logout-btn">
                  <span class="dropdown-icon">🚪</span>
                  Çıkış Yap
                </button>
              </form>
            </div>
          </div>
        </div>
    <?php endif; ?>

    <button class="mobile-toggle" id="mobileToggle">
      <span class="hamburger-line"></span>
      <span class="hamburger-line"></span>
      <span class="hamburger-line"></span>
    </button>
  </div>

  <div class="mobile-menu" id="mobileMenu">
    <div class="mobile-menu-content">
      <?php if($user): ?>
        <div class="mobile-user-info">
          <div class="mobile-avatar">
            <span><?= strtoupper(substr($user['name'], 0, 1)) ?></span>
          </div>
          <div class="mobile-user-details">
            <div class="mobile-user-name"><?= htmlspecialchars($user['name']) ?></div>
            <div class="mobile-user-role">Admin</div>
          </div>
        </div>
        
        <div class="mobile-nav-links">
          <a href="/" class="mobile-nav-link">
            <span class="mobile-nav-icon">📊</span>
            Dashboard
          </a>
          <a href="/tickets" class="mobile-nav-link">
            <span class="mobile-nav-icon">🎫</span>
            Destek
          </a>
          <a href="/reports" class="mobile-nav-link">
            <span class="mobile-nav-icon">📈</span>
            SEO Rapor
          </a>
          <div class="mobile-divider"></div>
          <a href="/profile" class="mobile-nav-link">
            <span class="mobile-nav-icon">👤</span>
            Profil
          </a>
          <a href="/settings" class="mobile-nav-link">
            <span class="mobile-nav-icon">⚙️</span>
            Ayarlar
          </a>
          <form action="/logout" method="post" class="mobile-logout-form">
            <input type="hidden" name="_csrf" value="<?= $this->auth->csrf() ?>">
            <button type="submit" class="mobile-nav-link logout">
              <span class="mobile-nav-icon">🚪</span>
              Çıkış Yap
            </button>
          </form>
        </div>
      <?php else: ?>
        <div class="mobile-nav-links">
          <a href="/login" class="mobile-nav-link">
            <span class="mobile-nav-icon">🔐</span>
            Giriş Yap
          </a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</header>
<main class="container">
