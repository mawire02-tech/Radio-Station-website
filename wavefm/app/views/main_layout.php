<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="<?= htmlspecialchars($this->settings->get('meta_description')) ?>">
<title><?= htmlspecialchars($page_title ?? 'Home') ?> — <?= htmlspecialchars($siteName) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,700;1,300&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="<?= ASSET_URL ?>css/style.css">
</head>
<body>

<!-- LIVE TICKER -->
<div class="ticker-bar" aria-live="polite" aria-label="Station ticker">
  <div class="ticker-inner">
    <span class="ticker-label"><i class="fas fa-broadcast-tower"></i> LIVE</span>
    <div class="ticker-wrap">
      <div class="ticker-move" id="tickerContent">
        <span>NOW ON AIR: Tune into <?= htmlspecialchars($siteName) ?> <?= htmlspecialchars($siteFrequency) ?> MHz</span>
        <span class="sep">◆</span>
        <span>MUSIC REQUESTS OPEN — USE THE REQUEST FORM</span>
        <span class="sep">◆</span>
        <span><?= htmlspecialchars($siteTagline) ?></span>
        <span class="sep">◆</span>
        <span>FOLLOW US ON SOCIAL MEDIA FOR UPDATES</span>
        <span class="sep">◆</span>
        <span>NOW ON AIR: Tune into <?= htmlspecialchars($siteName) ?> <?= htmlspecialchars($siteFrequency) ?> MHz</span>
        <span class="sep">◆</span>
        <span>MUSIC REQUESTS OPEN — USE THE REQUEST FORM</span>
        <span class="sep">◆</span>
        <span><?= htmlspecialchars($siteTagline) ?></span>
        <span class="sep">◆</span>
        <span>FOLLOW US ON SOCIAL MEDIA FOR UPDATES</span>
      </div>
    </div>
  </div>
</div>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg sticky-top" id="mainNav">
  <div class="container">
    <a class="navbar-brand" href="<?= BASE_URL ?>">
      <?php $logo = $this->settings->get('station_logo'); ?>
      <?php if ($logo): ?>
        <img src="<?= UPLOAD_URL . htmlspecialchars($logo) ?>" alt="<?= htmlspecialchars($siteName) ?> Logo" class="navbar-logo" width="50px" height="50px">
      <?php else: ?>
        <i class="fas fa-broadcast-tower"></i>
      <?php endif; ?>
      <span class="brand-text"><?= htmlspecialchars($siteName) ?> <span class="freq"><?= htmlspecialchars($siteFrequency) ?></span></span>
      <small>Community Radio</small>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-label="Toggle navigation">
      <i class="fas fa-bars"></i>
    </button>
    <div class="collapse navbar-collapse" id="navbarMain">
      <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link<?= (strpos($_SERVER['REQUEST_URI'],'news') === false && strpos($_SERVER['REQUEST_URI'],'shows') === false && strpos($_SERVER['REQUEST_URI'],'presenters') === false && strpos($_SERVER['REQUEST_URI'],'requests') === false && strpos($_SERVER['REQUEST_URI'],'about') === false) ? ' active' : '' ?>" href="<?= BASE_URL ?>">Home</a></li>
        <li class="nav-item"><a class="nav-link<?= (strpos($_SERVER['REQUEST_URI'],'/news') !== false) ? ' active' : '' ?>" href="<?= BASE_URL ?>news">News</a></li>
        <li class="nav-item"><a class="nav-link<?= (strpos($_SERVER['REQUEST_URI'],'/shows') !== false) ? ' active' : '' ?>" href="<?= BASE_URL ?>shows">Shows</a></li>
        <li class="nav-item"><a class="nav-link<?= (strpos($_SERVER['REQUEST_URI'],'/presenters') !== false) ? ' active' : '' ?>" href="<?= BASE_URL ?>presenters">Presenters</a></li>
        <li class="nav-item"><a class="nav-link<?= (strpos($_SERVER['REQUEST_URI'],'/requests') !== false) ? ' active' : '' ?>" href="<?= BASE_URL ?>requests">Requests</a></li>
        <li class="nav-item"><a class="nav-link<?= (strpos($_SERVER['REQUEST_URI'],'/about') !== false) ? ' active' : '' ?>" href="<?= BASE_URL ?>about">About</a></li>
      </ul>
      <div class="d-flex align-items-center gap-3">
        <button class="btn-listen" id="navPlayerToggle" onclick="toggleMiniPlayer()" title="Listen Live">
          <span class="live-dot"></span> Listen Live
        </button>
      </div>
    </div>
  </div>
</nav>

<!-- MINI PLAYER (sticky) -->
<div class="mini-player" id="miniPlayer">
  <div class="container-fluid">
    <div class="mini-player-inner">
      <div class="mini-player-info">
        <span class="mini-now">NOW PLAYING</span>
        <span class="mini-title" id="miniTitle">Loading stream...</span>
      </div>
      <div class="mini-player-controls">
        <button class="mini-btn" id="miniPlayBtn" onclick="toggleStream()">
          <i class="fas fa-play" id="miniPlayIcon"></i>
        </button>
        <div class="mini-vol">
          <i class="fas fa-volume-low"></i>
          <input type="range" id="miniVolume" min="0" max="1" step="0.05" value="0.8" oninput="setStreamVol(this.value)">
        </div>
        <button class="mini-close" onclick="toggleMiniPlayer()"><i class="fas fa-times"></i></button>
      </div>
    </div>
  </div>
</div>
<audio id="liveStream" src="<?= htmlspecialchars($streamUrl) ?>" preload="none"></audio>

<!-- MAIN CONTENT -->
<main id="mainContent">
<?= $content ?>
</main>

<!-- SOCIAL STRIP -->
<div class="social-strip">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-md-4">
        <p class="social-strip-label">Follow <?= htmlspecialchars($siteName) ?></p>
      </div>
      <div class="col-md-8">
        <div class="social-links">
          <?php $fb = $this->settings->get('facebook_url'); if($fb): ?>
          <a href="<?= htmlspecialchars($fb) ?>" target="_blank" rel="noopener" class="social-link fb">
            <i class="fab fa-facebook-f"></i> Facebook
          </a>
          <?php endif; ?>
          <?php $tw = $this->settings->get('twitter_url'); if($tw): ?>
          <a href="<?= htmlspecialchars($tw) ?>" target="_blank" rel="noopener" class="social-link tw">
            <i class="fab fa-x-twitter"></i> Twitter / X
          </a>
          <?php endif; ?>
          <?php $ig = $this->settings->get('instagram_url'); if($ig): ?>
          <a href="<?= htmlspecialchars($ig) ?>" target="_blank" rel="noopener" class="social-link ig">
            <i class="fab fa-instagram"></i> Instagram
          </a>
          <?php endif; ?>
          <?php $yt = $this->settings->get('youtube_url'); if($yt): ?>
          <a href="<?= htmlspecialchars($yt) ?>" target="_blank" rel="noopener" class="social-link yt">
            <i class="fab fa-youtube"></i> YouTube
          </a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- FOOTER -->
<footer>
  <div class="footer-top">
    <div class="container">
      <div class="row g-5">
        <div class="col-lg-4">
          <div class="footer-brand"><?= htmlspecialchars($siteName) ?> <span><?= htmlspecialchars($siteFrequency) ?></span></div>
          <p class="footer-tagline"><?= htmlspecialchars($siteTagline) ?></p>
          <div class="footer-contact mt-4">
            <div class="footer-contact-item"><i class="fas fa-envelope"></i> <?= htmlspecialchars($this->settings->get('station_email')) ?></div>
            <div class="footer-contact-item"><i class="fas fa-phone"></i> <?= htmlspecialchars($this->settings->get('station_phone')) ?></div>
            <div class="footer-contact-item"><i class="fas fa-location-dot"></i> <?= htmlspecialchars($this->settings->get('station_address')) ?></div>
          </div>
        </div>
        <div class="col-6 col-lg-2">
          <h6 class="footer-heading">Navigate</h6>
          <ul class="footer-links">
            <li><a href="<?= BASE_URL ?>">Home</a></li>
            <li><a href="<?= BASE_URL ?>news">News</a></li>
            <li><a href="<?= BASE_URL ?>shows">Shows</a></li>
            <li><a href="<?= BASE_URL ?>presenters">Presenters</a></li>
          </ul>
        </div>
        <div class="col-6 col-lg-2">
          <h6 class="footer-heading">Engage</h6>
          <ul class="footer-links">
            <li><a href="<?= BASE_URL ?>requests">Music Requests</a></li>
            <li><a href="<?= BASE_URL ?>requests#shoutout">Shout Outs</a></li>
            <li><a href="<?= BASE_URL ?>requests#feedback">Feedback</a></li>
            <li><a href="<?= BASE_URL ?>about">About Us</a></li>
            <li><a href="<?= BASE_URL ?>about#contact">Contact</a></li>
          </ul>
        </div>
        <div class="col-lg-4">
          <h6 class="footer-heading">Newsletter</h6>
          <p class="footer-newsletter-text">Get our weekly programme guide delivered to your inbox.</p>
          <form class="footer-newsletter-form" id="newsletterForm" onsubmit="handleNewsletterSubmit(event)">
            <div class="input-group">
              <input type="email" class="form-control" placeholder="your@email.com" name="email" required>
              <button class="btn btn-red" type="submit" id="newsletterBtn">Subscribe</button>
            </div>
            <small id="newsletterMsg" style="display:none;margin-top:8px;display:block;color:var(--red)"></small>
          </form>
        </div>
      </div>
    </div>
  </div>
  <div class="footer-bottom">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-6">
          <p class="footer-copy">© <?= date('Y') ?> <?= htmlspecialchars($siteName) ?>. All rights reserved. Licensed community broadcaster.</p>
        </div>
        <div class="col-md-6 text-md-end">
          <p class="footer-dev">
            Designed &amp; Developed with <i class="fas fa-heart text-danger mx-1"></i> by
            <a href="#" target="_blank">TechSupport Team</a>
            &nbsp;·&nbsp;
            <a href="<?= BASE_URL ?>admin/login" class="admin-link"><i class="fas fa-lock"></i> Admin</a>
          </p>
        </div>
      </div>
    </div>
  </div>
</footer>

<!-- TOAST CONTAINER -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" id="toastContainer"></div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
<script src="<?= ASSET_URL ?>js/app.js"></script>

<!-- Newsletter Subscription Handler -->
<script>
function handleNewsletterSubmit(event) {
  event.preventDefault();
  const form = document.getElementById('newsletterForm');
  const email = form.querySelector('input[name="email"]').value;
  const btn = document.getElementById('newsletterBtn');
  const msg = document.getElementById('newsletterMsg');
  
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Subscribing...';
  
  fetch('<?= BASE_URL ?>api/subscribe', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ email: email })
  })
  .then(res => res.json())
  .then(data => {
    msg.style.display = 'block';
    if (data.success) {
      msg.style.color = '#27AE60';
      msg.textContent = data.message || 'Thank you for subscribing!';
      form.reset();
      setTimeout(() => {
        msg.style.display = 'none';
        btn.disabled = false;
        btn.innerHTML = 'Subscribe';
      }, 3000);
    } else {
      msg.style.color = 'var(--red)';
      msg.textContent = data.error || 'Subscription failed';
      btn.disabled = false;
      btn.innerHTML = 'Subscribe';
    }
  })
  .catch(err => {
    msg.style.display = 'block';
    msg.style.color = 'var(--red)';
    msg.textContent = 'Error occurred. Please try again.';
    btn.disabled = false;
    btn.innerHTML = 'Subscribe';
  });
  return false;
}
</script>

<!-- Track Listener Analytics -->
<script>
// Track listener on page load
fetch('<?= BASE_URL ?>api/track-listener', {
  method: 'POST',
  headers: {'Content-Type': 'application/json'},
  body: JSON.stringify({ page: window.location.pathname })
}).catch(err => console.error('Listener tracking error:', err));
</script>
</body>
</html>
