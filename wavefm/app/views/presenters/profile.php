<div class="page-header">
  <div class="container">
    <div class="breadcrumb-row">
      <a href="<?= BASE_URL ?>">Home</a><span class="sep">/</span>
      <a href="<?= BASE_URL ?>presenters">Presenters</a><span class="sep">/</span>
      <span><?= htmlspecialchars($presenter['name']) ?></span>
    </div>
  </div>
</div>

<section class="section section-dark">
  <div class="container">
    <div class="row g-5">
      <!-- PHOTO + SOCIALS -->
      <div class="col-lg-4">
        <div style="background:var(--dark2);border:1px solid rgba(255,255,255,0.05);border-radius:3px;overflow:hidden">
          <?php if ($presenter['photo']): ?>
            <img src="<?= UPLOAD_URL.htmlspecialchars($presenter['photo']) ?>"
                 alt="<?= htmlspecialchars($presenter['name']) ?>"
                 style="width:100%;aspect-ratio:1;object-fit:cover;object-position:top;filter:grayscale(10%)">
          <?php else: ?>
            <div style="width:100%;aspect-ratio:1;background:linear-gradient(135deg,var(--dark3),var(--mid));display:flex;align-items:center;justify-content:center">
              <i class="fas fa-user fa-5x" style="color:var(--mid)"></i>
            </div>
          <?php endif; ?>
          <div style="padding:24px">
            <div style="font-family:var(--font-mono);font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);margin-bottom:8px">Follow</div>
            <div style="display:flex;gap:12px;flex-wrap:wrap">
              <?php if ($presenter['twitter']): ?>
              <a href="https://twitter.com/<?= htmlspecialchars(ltrim($presenter['twitter'],'@')) ?>"
                 target="_blank" rel="noopener"
                 style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);color:var(--light);padding:8px 14px;border-radius:2px;font-family:var(--font-mono);font-size:10px;letter-spacing:0.1em;text-transform:uppercase;text-decoration:none;transition:all 0.2s">
                <i class="fab fa-x-twitter"></i> Twitter
              </a>
              <?php endif; ?>
              <?php if ($presenter['instagram']): ?>
              <a href="https://instagram.com/<?= htmlspecialchars(ltrim($presenter['instagram'],'@')) ?>"
                 target="_blank" rel="noopener"
                 style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);color:var(--light);padding:8px 14px;border-radius:2px;font-family:var(--font-mono);font-size:10px;letter-spacing:0.1em;text-transform:uppercase;text-decoration:none">
                <i class="fab fa-instagram"></i> Instagram
              </a>
              <?php endif; ?>
              <?php if ($presenter['facebook']): ?>
              <a href="<?= htmlspecialchars($presenter['facebook']) ?>"
                 target="_blank" rel="noopener"
                 style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);color:var(--light);padding:8px 14px;border-radius:2px;font-family:var(--font-mono);font-size:10px;letter-spacing:0.1em;text-transform:uppercase;text-decoration:none">
                <i class="fab fa-facebook-f"></i> Facebook
              </a>
              <?php endif; ?>
            </div>
            <?php if ($presenter['email']): ?>
            <div style="margin-top:16px;font-size:13px;color:var(--muted)">
              <i class="fas fa-envelope me-2" style="color:var(--red)"></i>
              <a href="mailto:<?= htmlspecialchars($presenter['email']) ?>" style="color:var(--light)">
                <?= htmlspecialchars($presenter['email']) ?>
              </a>
            </div>
            <?php endif; ?>
          </div>
        </div>
        <div class="mt-3">
          <a href="<?= BASE_URL ?>presenters" class="btn-outline w-100 justify-content-center">
            <i class="fas fa-arrow-left"></i> All Presenters
          </a>
        </div>
      </div>

      <!-- BIO + SHOWS -->
      <div class="col-lg-8">
        <div class="section-label"><?= htmlspecialchars($presenter['role']) ?></div>
        <h1 style="font-size:clamp(2.5rem,5vw,4.5rem);margin-bottom:24px"><?= htmlspecialchars($presenter['name']) ?></h1>
        <div style="color:var(--light);font-size:15px;line-height:1.85">
          <p><?= nl2br(htmlspecialchars($presenter['bio'])) ?></p>
        </div>

        <?php if ($presenter['show_titles']): ?>
        <div style="margin-top:36px">
          <div class="section-label">On Air</div>
          <h3 style="font-size:1.6rem;margin-bottom:20px">Shows by <?= htmlspecialchars($presenter['name']) ?></h3>
          <div style="display:flex;flex-direction:column;gap:12px">
            <?php foreach (explode('||', $presenter['show_titles']) as $showTitle):
              if (!trim($showTitle)) continue;
            ?>
            <div style="background:var(--dark2);border:1px solid rgba(255,255,255,0.05);border-radius:3px;padding:18px 22px;display:flex;align-items:center;gap:16px">
              <div style="width:44px;height:44px;border-radius:4px;background:rgba(232,43,43,0.1);border:1px solid rgba(232,43,43,0.2);display:flex;align-items:center;justify-content:center;color:var(--red);font-size:18px;flex-shrink:0">
                <i class="fas fa-headphones"></i>
              </div>
              <div>
                <div style="font-family:var(--font-display);font-size:1.15rem;letter-spacing:0.04em;color:var(--white)"><?= htmlspecialchars($showTitle) ?></div>
                <div style="font-size:12px;color:var(--muted)">Hosted by <?= htmlspecialchars($presenter['name']) ?></div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <!-- REQUEST SHOUTOUT CTA -->
        <div style="background:var(--dark2);border:1px solid rgba(255,255,255,0.05);border-radius:3px;padding:28px;margin-top:36px;display:flex;align-items:center;gap:24px;flex-wrap:wrap">
          <div style="flex:1;min-width:200px">
            <div style="font-family:var(--font-display);font-size:1.4rem;letter-spacing:0.04em;color:var(--white);margin-bottom:6px">
              Request a Song from <?= htmlspecialchars($presenter['name']) ?>
            </div>
            <div style="font-size:13px;color:var(--muted)">Send a shout-out or music request for the next show.</div>
          </div>
          <a href="<?= BASE_URL ?>requests" class="btn-red flex-shrink-0">
            <i class="fas fa-music"></i> Make a Request
          </a>
        </div>
      </div>
    </div>
  </div>
</section>
