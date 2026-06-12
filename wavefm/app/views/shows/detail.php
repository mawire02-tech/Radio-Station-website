<div class="page-header">
  <div class="container">
    <span class="card-cat d-inline-block mb-3" style="position:static"><?= htmlspecialchars($show['genre_name']) ?></span>
    <h1><?= htmlspecialchars($show['title']) ?></h1>
    <div class="breadcrumb-row">
      <a href="<?= BASE_URL ?>">Home</a><span class="sep">/</span>
      <a href="<?= BASE_URL ?>shows">Shows</a><span class="sep">/</span>
      <span><?= htmlspecialchars($show['title']) ?></span>
    </div>
  </div>
</div>

<section class="section section-dark">
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-8">
        <div style="background:var(--dark2);border:1px solid rgba(255,255,255,0.05);border-radius:3px;padding:36px;margin-bottom:40px">
          <div class="d-flex align-items-center gap-4 mb-4 flex-wrap">
            <div style="width:80px;height:80px;border-radius:4px;background:var(--dark3);display:flex;align-items:center;justify-content:center;font-size:32px;color:var(--mid);flex-shrink:0">
              <i class="fas fa-headphones"></i>
            </div>
            <div>
              <h2 style="font-size:2rem;margin-bottom:4px"><?= htmlspecialchars($show['title']) ?></h2>
              <div style="font-family:var(--font-mono);font-size:11px;letter-spacing:0.12em;text-transform:uppercase;color:var(--muted)">
                <i class="fas fa-user me-1" style="color:var(--red)"></i>
                <a href="<?= BASE_URL ?>presenters/<?= htmlspecialchars($show['presenter_slug']) ?>" style="color:var(--light)">
                  <?= htmlspecialchars($show['presenter_name']) ?>
                </a>
                &nbsp;·&nbsp; <?= htmlspecialchars($show['genre_name']) ?>
              </div>
            </div>
          </div>
          <p style="color:var(--light);font-size:15px;line-height:1.8"><?= nl2br(htmlspecialchars($show['description'])) ?></p>
        </div>

        <!-- EPISODES -->
        <div class="section-label">On Demand</div>
        <h3 style="font-size:1.8rem;margin-bottom:24px">Episodes</h3>

        <?php if (empty($episodes)): ?>
          <div style="background:var(--dark2);border:1px solid rgba(255,255,255,0.05);border-radius:3px;padding:40px;text-align:center;color:var(--muted)">
            <i class="fas fa-podcast fa-3x mb-3 d-block"></i>
            No episodes uploaded yet for this show.
          </div>
        <?php else: ?>
          <div class="d-flex flex-column gap-3">
            <?php foreach ($episodes as $ep): ?>
            <div class="card-podcast">
              <div class="cp-thumb"><i class="fas fa-headphones"></i></div>
              <div class="cp-pod-info">
                <div class="cp-pod-title"><?= htmlspecialchars($ep['title']) ?></div>
                <div class="cp-pod-meta">
                  <?= date('d M Y', strtotime($ep['published_at'])) ?>
                  &nbsp;·&nbsp;
                  <?= $ep['duration'] ? gmdate('H:i:s', $ep['duration']) : 'N/A' ?>
                  &nbsp;·&nbsp;
                  <?= Security::formatBytes($ep['file_size']) ?>
                </div>
                <?php if ($ep['description']): ?>
                <div class="cp-pod-desc"><?= htmlspecialchars($ep['description']) ?></div>
                <?php endif; ?>
                <div class="cp-pod-actions">
                  <button class="btn-play-sm" onclick="showToast('Audio playback requires a configured stream URL.','info')">
                    <i class="fas fa-play"></i> Play
                  </button>
                  <a href="<?= BASE_URL ?>public/uploads/<?= htmlspecialchars($ep['audio_file']) ?>"
                     download class="btn-play-sm" style="text-decoration:none">
                    <i class="fas fa-download"></i> Download
                  </a>
                  <span style="font-family:var(--font-mono);font-size:10px;color:var(--muted)">
                    <i class="fas fa-download me-1"></i><?= number_format($ep['downloads']) ?>
                  </span>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <!-- SIDEBAR -->
      <div class="col-lg-4">
        <div class="sidebar-card">
          <div class="sidebar-card-head"><i class="fas fa-info-circle me-2" style="color:var(--red)"></i>Show Info</div>
          <div class="sidebar-card-body">
            <table style="font-size:13px;width:100%;color:var(--muted)">
              <tr><td style="padding:6px 0;width:45%">Presenter</td><td style="color:var(--light)"><?= htmlspecialchars($show['presenter_name']) ?></td></tr>
              <tr><td style="padding:6px 0">Genre</td><td style="color:var(--light)"><?= htmlspecialchars($show['genre_name']) ?></td></tr>
              <tr><td style="padding:6px 0">Episodes</td><td style="color:var(--light)"><?= count($episodes) ?></td></tr>
              <tr><td style="padding:6px 0">Status</td><td><span style="color:<?= $show['is_active']?'#2ecc71':'var(--muted)' ?>;font-family:var(--font-mono);font-size:10px"><?= $show['is_active']?'Active':'Inactive' ?></span></td></tr>
            </table>
          </div>
        </div>
        <div class="mt-3">
          <a href="<?= BASE_URL ?>shows" class="btn-outline w-100 justify-content-center">
            <i class="fas fa-arrow-left"></i> All Shows
          </a>
        </div>
      </div>
    </div>
  </div>
</section>
