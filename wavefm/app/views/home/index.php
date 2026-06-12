<?php
// helpers
$days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
$today = (int)date('w');
?>
<meta name="base-url" content="<?= BASE_URL ?>">

<!-- ── HERO ─────────────────────────────────────────────────── -->
<section class="hero">
  <div class="hero-bg"></div>
  <div class="hero-grid"></div>

  <!-- LEFT CONTENT -->
  <div class="hero-content">
    <div class="hero-eyebrow">Streaming Live Now</div>
    <h1 class="hero-title">
      THE<br>
      <span class="accent">SOUND</span><br>
      <span class="stroke">OF YOUR</span><br>
      COMMUNITY
    </h1>
    <p class="hero-sub">
      <?= htmlspecialchars($siteName) ?> <?= htmlspecialchars($siteFrequency) ?> MHz — your local voice.
      Original music, real stories, and shows that connect our community 24 hours a day.
    </p>
    <div class="hero-actions">
      <button class="btn-red" onclick="toggleHeroPlay()">
        <i class="fas fa-play" id="heroPlayIcon"></i> Listen Live
      </button>
      <a href="<?= BASE_URL ?>shows" class="btn-outline">
        <i class="fas fa-calendar-alt"></i> Show Schedule
      </a>
    </div>
  </div>

  <!-- RIGHT — LIVE PLAYER -->
  <div class="player-col">
    <div class="player-header">
      <span class="player-header-label">Live Player</span>
      <span class="on-air-badge"><span class="dot"></span> On Air</span>
    </div>

    <div class="vinyl-wrap">
      <div class="vinyl-disc" id="vinylDisc"></div>
      <div class="eq-visual">
        <?php foreach([0.5,0.7,0.4,0.8,0.6,0.45,0.75] as $d): ?>
        <div class="eq-bar" style="--d:<?= $d ?>s;height:<?= rand(20,80) ?>%"></div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="np-info">
      <div class="np-song" id="npSong">Midnight Frequencies</div>
      <div class="np-artist" id="npArtist">DJ Solaris · Electronic / Ambient</div>
      <div class="np-show"><i class="fas fa-broadcast-tower"></i> Now Broadcasting</div>
    </div>

    <div class="player-progress">
      <div class="progress-track">
        <div class="progress-fill" id="progressFill"></div>
      </div>
      <div class="time-row">
        <span>LIVE</span>
        <span>∞</span>
      </div>
    </div>

    <div class="player-btns">
      <button class="pbtn" title="Previous" onclick="showToast('Previous track not available on live stream.','info')">
        <i class="fas fa-backward-step"></i>
      </button>
      <button class="pbtn pbtn-main" id="mainPlayBtn" onclick="toggleHeroPlay()">
        <i class="fas fa-play" id="mainPlayIcon"></i>
      </button>
      <button class="pbtn" title="Next" onclick="showToast('Next track not available on live stream.','info')">
        <i class="fas fa-forward-step"></i>
      </button>
    </div>

    <div class="vol-row">
      <i class="fas fa-volume-low"></i>
      <input type="range" id="mainVolSlider" min="0" max="1" step="0.05" value="0.8" oninput="setStreamVol(this.value)">
      <i class="fas fa-volume-high"></i>
    </div>
  </div>
</section>

<!-- ── STATS BAR ──────────────────────────────────────────────── -->
<div class="stats-bar">
  <div class="row g-0">
    <div class="col-6 col-md-3">
      <div class="stat-cell">
        <div class="stat-num"><?= htmlspecialchars($siteFrequency) ?><sup>MHz</sup></div>
        <div class="stat-lbl">Frequency</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-cell">
        <div class="stat-num">24<sup>h</sup></div>
        <div class="stat-lbl">Daily Broadcasting</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-cell">
        <div class="stat-num">12<sup>K</sup></div>
        <div class="stat-lbl">Weekly Listeners</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-cell">
        <div class="stat-num">25<sup>+</sup></div>
        <div class="stat-lbl">Years On Air</div>
      </div>
    </div>
  </div>
</div>

<!-- ── LATEST NEWS ─────────────────────────────────────────────── -->
<section class="section section-dark">
  <div class="container">
    <div class="row align-items-end section-head">
      <div class="col-md-8">
        <div class="section-label">Latest News</div>
        <h2 class="section-title">From the Station</h2>
      </div>
      <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <a href="<?= BASE_URL ?>news" class="btn-outline">All Articles <i class="fas fa-arrow-right ms-1"></i></a>
      </div>
    </div>

    <?php if (empty($featured_news)): ?>
      <div class="text-center py-5">
        <i class="fas fa-newspaper fa-3x mb-3" style="color:var(--mid)"></i>
        <p style="color:var(--muted)">No news articles published yet.</p>
      </div>
    <?php else: ?>
    <div class="row g-4">
      <?php foreach ($featured_news as $i => $article): ?>
      <div class="col-md-4 fade-up" style="transition-delay:<?= $i * 0.1 ?>s">
        <div class="card-news">
          <div class="card-news-img">
            <?php if ($article['image']): ?>
              <img src="<?= UPLOAD_URL . htmlspecialchars($article['image']) ?>" alt="<?= htmlspecialchars($article['title']) ?>" loading="lazy">
            <?php else: ?>
              <div style="width:100%;height:200px;background:linear-gradient(135deg,var(--dark3),var(--mid));display:flex;align-items:center;justify-content:center">
                <i class="fas fa-newspaper fa-3x" style="color:var(--mid)"></i>
              </div>
            <?php endif; ?>
            <span class="card-cat" style="background:<?= htmlspecialchars($article['category_color']) ?>">
              <?= htmlspecialchars($article['category_name']) ?>
            </span>
          </div>
          <div class="card-body">
            <div class="card-date">
              <i class="far fa-calendar me-1"></i>
              <?= date('M j, Y', strtotime($article['published_at'])) ?>
            </div>
            <div class="card-title"><?= htmlspecialchars($article['title']) ?></div>
            <p class="card-excerpt"><?= htmlspecialchars(mb_strimwidth($article['excerpt'], 0, 110, '…')) ?></p>
          </div>
          <div class="card-footer-row">
            <a href="<?= BASE_URL ?>news/<?= htmlspecialchars($article['slug']) ?>" class="card-read-more">
              Read More <i class="fas fa-arrow-right"></i>
            </a>
            <span class="card-meta"><i class="far fa-eye me-1"></i><?= number_format($article['views']) ?></span>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- ── TODAY'S SCHEDULE ─────────────────────────────────────── -->
<section class="section">
  <div class="container">
    <div class="row align-items-end section-head">
      <div class="col-md-8">
        <div class="section-label">On Air</div>
        <h2 class="section-title">Show Schedule</h2>
      </div>
      <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <a href="<?= BASE_URL ?>shows" class="btn-outline">Full Schedule <i class="fas fa-arrow-right ms-1"></i></a>
      </div>
    </div>

    <div class="schedule-card fade-up">
      <!-- Day tabs — show Mon-Sun starting from today -->
      <div class="day-tabs" role="tablist">
        <?php
        for ($d = 0; $d < 7; $d++):
          $dayIdx = ($today + $d) % 7;
          $isToday = $d === 0;
          $date = date('j', strtotime("+$d days"));
          $dayName = substr($days[$dayIdx], 0, 3);
        ?>
        <button class="day-tab<?= $isToday ? ' active today-tab' : '' ?>"
                role="tab"
                onclick="switchDay(<?= $d ?>)"
                aria-selected="<?= $isToday ? 'true' : 'false' ?>">
          <span class="dn"><?= $dayName ?></span>
          <span class="dd"><?= $date ?></span>
        </button>
        <?php endfor; ?>
      </div>

      <?php
      // Build 7 days of schedule panes
      $schedModel = new ScheduleModel();
      $nowTime = date('H:i:s');
      for ($d = 0; $d < 7; $d++):
        $dayIdx = ($today + $d) % 7;
        $shows = $schedModel->getByDay($dayIdx);
      ?>
      <div class="sched-pane" style="display:<?= $d === 0 ? 'block' : 'none' ?>">
        <div class="sched-list">
          <?php if (empty($shows)): ?>
            <div class="text-center py-4" style="color:var(--muted)">
              <i class="fas fa-moon me-2"></i>No scheduled shows
            </div>
          <?php else: ?>
            <?php foreach ($shows as $show):
              $isNow = $d === 0 && $nowTime >= $show['start_time'] && $nowTime < $show['end_time'];
            ?>
            <div class="sched-row<?= $isNow ? ' now' : '' ?>">
              <div class="sched-time">
                <?= date('g:i A', strtotime($show['start_time'])) ?><br>
                <small style="color:var(--dark3);font-size:9px"><?= date('g:i A', strtotime($show['end_time'])) ?></small>
              </div>
              <div class="sched-dot"></div>
              <div class="flex-grow-1 min-w-0">
                <div class="sched-name">
                  <?= htmlspecialchars($show['show_title']) ?>
                  <?php if ($isNow): ?> <span style="color:var(--red);font-size:0.8rem"> ● LIVE</span><?php endif; ?>
                </div>
                <div class="sched-presenter"><?= htmlspecialchars($show['presenter_name']) ?></div>
              </div>
              <span class="sched-genre"><?= htmlspecialchars($show['genre_name']) ?></span>
            </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
      <?php endfor; ?>
    </div>
  </div>
</section>

<!-- ── LATEST PODCASTS ─────────────────────────────────────── -->
<section class="section section-dark2">
  <div class="container">
    <div class="row align-items-end section-head">
      <div class="col-md-8">
        <div class="section-label">On Demand</div>
        <h2 class="section-title">Latest Episodes</h2>
      </div>
      <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <a href="<?= BASE_URL ?>shows" class="btn-outline">All Episodes <i class="fas fa-arrow-right ms-1"></i></a>
      </div>
    </div>

    <div class="row g-4">
      <?php foreach ($recent_podcasts as $i => $pod): ?>
      <div class="col-md-6 fade-up" style="transition-delay:<?= $i * 0.08 ?>s">
        <div class="card-podcast">
          <div class="cp-thumb">
            <i class="fas fa-headphones"></i>
          </div>
          <div class="cp-pod-info">
            <div class="cp-pod-title"><?= htmlspecialchars($pod['title']) ?></div>
            <div class="cp-pod-meta">
              <?= htmlspecialchars($pod['show_title']) ?> &nbsp;·&nbsp;
              <?= htmlspecialchars($pod['presenter_name']) ?> &nbsp;·&nbsp;
              <?= $pod['duration'] ? gmdate('H:i', $pod['duration']) : 'N/A' ?>
            </div>
            <?php if ($pod['description']): ?>
            <div class="cp-pod-desc"><?= htmlspecialchars(mb_strimwidth($pod['description'], 0, 90, '…')) ?></div>
            <?php endif; ?>
            <div class="cp-pod-actions">
              <button class="btn-play-sm" onclick="showToast('Stream URL required to play on-demand audio.','info')">
                <i class="fas fa-play"></i> Play
              </button>
              <span style="font-family:var(--font-mono);font-size:10px;color:var(--muted)">
                <i class="fas fa-download me-1"></i><?= number_format($pod['downloads']) ?>
              </span>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ── CTA ─────────────────────────────────────────────────── -->
<section class="cta-section">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-7 mb-4 mb-lg-0">
        <div class="section-label" style="color:rgba(255,255,255,0.7)"><span style="background:rgba(255,255,255,0.4)"></span>Request a Song</div>
        <h2 class="cta-title">YOUR MUSIC,<br>YOUR STATION</h2>
        <p class="cta-sub">Send us your song requests, shout-outs, and feedback. Our presenters love hearing from listeners.</p>
        <div class="d-flex gap-3 flex-wrap">
          <a href="<?= BASE_URL ?>requests" class="btn-white">
            <i class="fas fa-music"></i> Request a Song
          </a>
          <a href="<?= BASE_URL ?>requests#shoutout" class="btn-white-outline">
            <i class="fas fa-comment-dots"></i> Send Shout-Out
          </a>
        </div>
      </div>
      <div class="col-lg-5">
        <div class="row g-3">
          <?php foreach ($upcoming_events as $event): ?>
          <div class="col-12">
            <div style="background:rgba(0,0,0,0.2);border:1px solid rgba(255,255,255,0.12);border-radius:3px;padding:14px 18px;display:flex;align-items:center;gap:16px">
              <div style="text-align:center;min-width:48px">
                <div style="font-family:var(--font-display);font-size:1.8rem;color:#fff;line-height:1"><?= date('j', strtotime($event['event_date'])) ?></div>
                <div style="font-family:var(--font-mono);font-size:9px;letter-spacing:0.15em;text-transform:uppercase;color:rgba(255,255,255,0.6)"><?= date('M', strtotime($event['event_date'])) ?></div>
              </div>
              <div style="border-left:1px solid rgba(255,255,255,0.15);padding-left:16px;flex:1;min-width:0">
                <div style="font-family:var(--font-display);font-size:1.05rem;letter-spacing:0.03em;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= htmlspecialchars($event['title']) ?></div>
                <?php if ($event['location']): ?>
                <div style="font-size:12px;color:rgba(255,255,255,0.6)"><i class="fas fa-location-dot me-1"></i><?= htmlspecialchars($event['location']) ?></div>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
          <?php if (empty($upcoming_events)): ?>
            <div class="col-12">
              <div style="background:rgba(0,0,0,0.2);border:1px solid rgba(255,255,255,0.12);border-radius:3px;padding:20px;text-align:center;color:rgba(255,255,255,0.6);font-size:13px">
                No upcoming events scheduled.
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>
