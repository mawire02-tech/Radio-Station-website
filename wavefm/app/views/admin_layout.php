<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($page_title ?? 'Admin') ?> — <?= htmlspecialchars($siteName) ?> Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="<?= ASSET_URL ?>css/style.css">
<link rel="stylesheet" href="<?= ASSET_URL ?>css/admin.css">
<meta name="robots" content="noindex,nofollow">
</head>
<body class="admin-body">

<?php if (Security::isLoggedIn()): ?>
<!-- SIDEBAR OVERLAY -->
<div class="adm-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- SIDEBAR -->
<aside class="adm-sidebar" id="adminSidebar">
  <div class="adm-logo">
    <?php $logo = $this->settings->get('station_logo'); ?>
    <?php if ($logo): ?>
      <img src="<?= UPLOAD_URL . htmlspecialchars($logo) ?>" alt="<?= htmlspecialchars($siteName) ?> Logo" style="max-width:40px; height:auto; margin-right:8px;">
    <?php else: ?>
      <i class="fas fa-broadcast-tower" style="font-size:24px; margin-right:8px;"></i>
    <?php endif; ?>
    <span><?= htmlspecialchars($siteName) ?></span>
    <small>Admin Panel</small>
  </div>

  <div class="adm-user">
    <div class="adm-user-avatar"><?= strtoupper(substr($_SESSION['admin_name'] ?? 'A', 0, 2)) ?></div>
    <div>
      <div class="adm-user-name"><?= htmlspecialchars($_SESSION['admin_name'] ?? '') ?></div>
      <div class="adm-user-role"><?= htmlspecialchars($_SESSION['admin_role'] ?? '') ?></div>
    </div>
  </div>

  <nav class="adm-nav">
    <?php
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $navItems = [
      ['url' => 'admin/dashboard',   'icon' => 'fa-gauge',          'label' => 'Dashboard'],
      ['url' => 'admin/news',        'icon' => 'fa-newspaper',      'label' => 'News'],
      ['url' => 'admin/presenters',  'icon' => 'fa-microphone',     'label' => 'Presenters'],
      ['url' => 'admin/shows',       'icon' => 'fa-headphones',     'label' => 'Shows'],
      ['url' => 'admin/podcasts',    'icon' => 'fa-podcast',        'label' => 'Podcasts'],
      ['url' => 'admin/requests',    'icon' => 'fa-music',          'label' => 'Requests'],
      ['url' => 'admin/events',      'icon' => 'fa-calendar',       'label' => 'Events'],
      ['url' => 'admin/subscribers', 'icon' => 'fa-envelope',       'label' => 'Subscribers'],
      ['url' => 'admin/users',       'icon' => 'fa-users',          'label' => 'Users',   'role'=>'superadmin'],
      ['url' => 'admin/settings',    'icon' => 'fa-sliders',        'label' => 'Settings','role'=>'admin'],
    ];
    foreach ($navItems as $item):
      if (!empty($item['role'])) {
        $hierarchy = ['editor'=>1,'admin'=>2,'superadmin'=>3];
        $userLevel = $hierarchy[$_SESSION['admin_role']] ?? 0;
        $reqLevel  = $hierarchy[$item['role']] ?? 99;
        if ($userLevel < $reqLevel) continue;
      }
      $active = str_contains($uri, '/' . $item['url']) ? ' active' : '';
    ?>
    <a href="<?= BASE_URL . $item['url'] ?>" class="adm-nav-item<?= $active ?>">
      <i class="fas <?= $item['icon'] ?>"></i>
      <?= $item['label'] ?>
    </a>
    <?php endforeach; ?>

    <div class="adm-nav-sep"></div>
    <a href="<?= BASE_URL ?>" class="adm-nav-item" target="_blank">
      <i class="fas fa-external-link-alt"></i> View Site
    </a>
    <a href="<?= BASE_URL ?>admin/logout" class="adm-nav-item" style="color:#ff6b6b">
      <i class="fas fa-right-from-bracket"></i> Log Out
    </a>
  </nav>
</aside>

<!-- MAIN -->
<div class="adm-main">
  <!-- TOP BAR -->
  <div class="adm-topbar">
    <button class="adm-toggle" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
    <div class="adm-topbar-title"><?= htmlspecialchars($page_title ?? 'Admin') ?></div>
    <div class="adm-topbar-right">
      <a href="<?= BASE_URL ?>" class="adm-topbar-btn" target="_blank" title="View Site">
        <i class="fas fa-external-link-alt"></i>
      </a>
      <a href="<?= BASE_URL ?>admin/logout" class="adm-topbar-btn" title="Log out" style="color:#ff6b6b">
        <i class="fas fa-right-from-bracket"></i>
      </a>
    </div>
  </div>

  <!-- FLASH — uses $flash passed from controller via getFlash(), fallback to session for redirects -->
  <?php
  $layoutFlash = $flash ?? null;
  if (!$layoutFlash && !empty($_SESSION['flash'])) {
      $layoutFlash = $_SESSION['flash'];
      unset($_SESSION['flash']);
  }
  ?>
  <?php if ($layoutFlash): ?>
  <div class="container-fluid px-4 pt-3">
    <div class="flash-msg <?= htmlspecialchars($layoutFlash['type']) ?>">
      <i class="fas fa-<?= $layoutFlash['type']==='success'?'check-circle':($layoutFlash['type']==='error'?'exclamation-circle':'exclamation-triangle') ?>"></i>
      <?= htmlspecialchars($layoutFlash['message']) ?>
    </div>
  </div>
  <?php endif; ?>

  <!-- CONTENT -->
  <div class="adm-content">
    <?= $content ?>
  </div>

  <div class="adm-footer">
    © <?= date('Y') ?> <?= htmlspecialchars($siteName) ?> Admin Panel &nbsp;·&nbsp; v<?= APP_VERSION ?>
  </div>
</div>

<?php else: ?>
<!-- LOGIN PAGE (no sidebar) -->
<div class="adm-login-page">
  <?= $content ?>
</div>
<?php endif; ?>

<div class="toast-container position-fixed bottom-0 end-0 p-3" id="toastContainer"></div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
<script src="<?= ASSET_URL ?>js/app.js"></script>
</body>
</html>
