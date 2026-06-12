<div class="page-header">
  <div class="container">
    <div class="section-label">Meet the Team</div>
    <h1>Our Presenters</h1>
    <div class="breadcrumb-row"><a href="<?= BASE_URL ?>">Home</a><span class="sep">/</span><span>Presenters</span></div>
  </div>
</div>

<section class="section section-dark">
  <div class="container">
    <?php if (empty($presenters)): ?>
      <div class="text-center py-5">
        <i class="fas fa-microphone fa-3x mb-3" style="color:var(--mid)"></i>
        <p style="color:var(--muted)">No presenters added yet.</p>
      </div>
    <?php else: ?>
    <div class="row g-4">
      <?php foreach ($presenters as $i => $p): ?>
      <div class="col-md-6 col-lg-4 fade-up" style="transition-delay:<?= ($i%6)*0.08 ?>s">
        <div class="card-presenter" onclick="location.href='<?= BASE_URL ?>presenters/<?= htmlspecialchars($p['slug']) ?>'">
          <div class="cp-img">
            <?php if ($p['photo']): ?>
              <img src="<?= UPLOAD_URL.htmlspecialchars($p['photo']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" loading="lazy">
            <?php else: ?>
              <div style="width:100%;height:260px;background:linear-gradient(135deg,var(--dark3),var(--mid));display:flex;align-items:center;justify-content:center">
                <i class="fas fa-user fa-4x" style="color:var(--mid)"></i>
              </div>
            <?php endif; ?>
            <div class="cp-overlay">
              <div class="cp-socials">
                <?php if ($p['twitter']): ?><a href="https://twitter.com/<?= htmlspecialchars(ltrim($p['twitter'],'@')) ?>" target="_blank" rel="noopener" onclick="event.stopPropagation()"><i class="fab fa-x-twitter"></i></a><?php endif; ?>
                <?php if ($p['instagram']): ?><a href="https://instagram.com/<?= htmlspecialchars(ltrim($p['instagram'],'@')) ?>" target="_blank" rel="noopener" onclick="event.stopPropagation()"><i class="fab fa-instagram"></i></a><?php endif; ?>
                <?php if ($p['facebook']): ?><a href="<?= htmlspecialchars($p['facebook']) ?>" target="_blank" rel="noopener" onclick="event.stopPropagation()"><i class="fab fa-facebook-f"></i></a><?php endif; ?>
              </div>
            </div>
          </div>
          <div class="cp-info">
            <div class="cp-name"><?= htmlspecialchars($p['name']) ?></div>
            <div class="cp-role"><?= htmlspecialchars($p['role']) ?></div>
            <div class="cp-bio"><?= htmlspecialchars(mb_strimwidth($p['bio'],0,100,'…')) ?></div>
            <?php if ($p['show_titles']): ?>
            <div class="cp-show">
              <i class="fas fa-broadcast-tower"></i>
              <?= htmlspecialchars(mb_strimwidth($p['show_titles'],0,50,'…')) ?>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>
