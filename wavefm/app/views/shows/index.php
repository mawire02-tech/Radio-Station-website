<?php $days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday']; $today = (int)date('w'); ?>

<div class="page-header">
  <div class="container">
    <div class="section-label">Programming</div>
    <h1>Shows &amp; Schedule</h1>
    <div class="breadcrumb-row"><a href="<?= BASE_URL ?>">Home</a><span class="sep">/</span><span>Shows</span></div>
  </div>
</div>

<!-- WEEKLY SCHEDULE -->
<section class="section section-dark2">
  <div class="container">
    <div class="section-label">Weekly Programme</div>
    <h2 class="section-title mb-5">Full Schedule</h2>

    <div class="schedule-card fade-up">
      <div class="day-tabs">
        <?php foreach ($days as $i => $dayName): ?>
        <button class="day-tab<?= $i === $today ? ' active today-tab' : '' ?>"
                onclick="switchDay(<?= $i ?>)">
          <span class="dn"><?= substr($dayName,0,3) ?></span>
          <span class="dd"><?= substr($dayName,0,1) ?></span>
        </button>
        <?php endforeach; ?>
      </div>

      <?php foreach ($weekly_schedule as $dayIdx => $dayShows): ?>
      <div class="sched-pane" style="display:<?= $dayIdx === $today ? 'block' : 'none' ?>">
        <div class="sched-list">
          <?php if (empty($dayShows)): ?>
            <div class="text-center py-5" style="color:var(--muted)">
              <i class="fas fa-moon fa-2x mb-3 d-block"></i>No scheduled shows on <?= $days[$dayIdx] ?>
            </div>
          <?php else: ?>
            <?php $nowTime = date('H:i:s'); foreach ($dayShows as $show):
              $isNow = ($dayIdx === $today) && $nowTime >= $show['start_time'] && $nowTime < $show['end_time'];
            ?>
            <a href="<?= BASE_URL ?>shows/<?= htmlspecialchars($show['show_slug']) ?>"
               class="sched-row<?= $isNow?' now':'' ?>" style="text-decoration:none">
              <div class="sched-time">
                <?= date('g:i A', strtotime($show['start_time'])) ?>–<?= date('g:i A', strtotime($show['end_time'])) ?>
              </div>
              <div class="sched-dot"></div>
              <div class="flex-grow-1 min-w-0">
                <div class="sched-name">
                  <?= htmlspecialchars($show['show_title']) ?>
                  <?php if ($isNow): ?><span style="color:var(--red);font-size:0.75rem;margin-left:8px">● LIVE NOW</span><?php endif; ?>
                </div>
                <div class="sched-presenter">with <?= htmlspecialchars($show['presenter_name']) ?></div>
              </div>
              <span class="sched-genre"><?= htmlspecialchars($show['genre_name']) ?></span>
            </a>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ALL SHOWS -->
<section class="section section-dark">
  <div class="container">
    <div class="section-label">Our Shows</div>
    <h2 class="section-title mb-5">Current Programme</h2>

    <div class="row g-4">
      <?php foreach ($shows as $i => $show): ?>
      <div class="col-md-6 col-lg-4 fade-up" style="transition-delay:<?= ($i%6)*0.07 ?>s">
        <div class="card-news" style="cursor:pointer" onclick="location.href='<?= BASE_URL ?>shows/<?= htmlspecialchars($show['slug']) ?>'">
          <div class="card-news-img">
            <?php if ($show['image']): ?>
              <img src="<?= UPLOAD_URL.htmlspecialchars($show['image']) ?>" alt="<?= htmlspecialchars($show['title']) ?>" loading="lazy">
            <?php else: ?>
              <div style="width:100%;height:200px;background:linear-gradient(135deg,var(--dark3),var(--mid));display:flex;align-items:center;justify-content:center;flex-direction:column;gap:10px">
                <i class="fas fa-headphones fa-3x" style="color:var(--mid)"></i>
              </div>
            <?php endif; ?>
            <span class="card-cat"><?= htmlspecialchars($show['genre_name']) ?></span>
          </div>
          <div class="card-body">
            <div class="card-date"><i class="fas fa-user me-1"></i><?= htmlspecialchars($show['presenter_name']) ?></div>
            <div class="card-title"><?= htmlspecialchars($show['title']) ?></div>
            <p class="card-excerpt"><?= htmlspecialchars(mb_strimwidth($show['description'],0,100,'…')) ?></p>
          </div>
          <div class="card-footer-row">
            <a href="<?= BASE_URL ?>shows/<?= htmlspecialchars($show['slug']) ?>" class="card-read-more">
              View Show <i class="fas fa-arrow-right"></i>
            </a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- RECENT PODCASTS -->
<?php if (!empty($recent_podcasts)): ?>
<section class="section section-dark2">
  <div class="container">
    <div class="section-label">On Demand</div>
    <h2 class="section-title mb-5">Latest Episodes</h2>
    <div class="row g-4">
      <?php foreach ($recent_podcasts as $i => $pod): ?>
      <div class="col-md-6 col-lg-4 fade-up" style="transition-delay:<?= ($i%6)*0.07 ?>s">
        <div class="card-podcast" style="flex-direction:column;gap:14px">
          <div style="display:flex;gap:14px;align-items:center">
            <div class="cp-thumb"><i class="fas fa-headphones"></i></div>
            <div class="cp-pod-info">
              <div class="cp-pod-title"><?= htmlspecialchars($pod['title']) ?></div>
              <div class="cp-pod-meta"><?= htmlspecialchars($pod['show_title']) ?></div>
            </div>
          </div>
          <div class="cp-pod-actions">
            <button class="btn-play-sm" onclick="showToast('Configure your stream URL to enable on-demand playback.','info')">
              <i class="fas fa-play"></i> Play Episode
            </button>
            <span style="font-family:var(--font-mono);font-size:10px;color:var(--muted)">
              <?= $pod['duration'] ? gmdate('H:i', $pod['duration']) : '' ?>
            </span>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
