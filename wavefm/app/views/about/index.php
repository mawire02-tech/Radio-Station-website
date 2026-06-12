<?php
$calYear  = isset($_GET['cal_year'])  ? (int)$_GET['cal_year']  : (int)date('Y');
$calMonth = isset($_GET['cal_month']) ? (int)$_GET['cal_month'] : (int)date('m');
$calMonth = max(1, min(12, $calMonth));
$eventModel = new EventModel();
$monthEvents = $eventModel->getByMonth($calYear, $calMonth);
$eventDays = array_column($monthEvents, 'event_date');
$eventDays = array_map(fn($d) => (int)date('j', strtotime($d)), $eventDays);
?>
<meta name="base-url" content="<?= BASE_URL ?>">

<div class="page-header">
  <div class="container">
    <div class="section-label">Who We Are</div>
    <h1>About <?= htmlspecialchars($siteName) ?></h1>
    <div class="breadcrumb-row"><a href="<?= BASE_URL ?>">Home</a><span class="sep">/</span><span>About</span></div>
  </div>
</div>

<!-- MISSION & HISTORY -->
<section class="section section-dark">
  <div class="container">

    <!-- FLASH -->
    <?php if (!empty($flash)): ?>
    <div class="flash-msg <?= htmlspecialchars($flash['type']) ?> mb-5">
      <i class="fas fa-check-circle"></i> <?= htmlspecialchars($flash['message']) ?>
    </div>
    <?php endif; ?>

    <div class="about-grid">
      <div class="about-img-col">
        <div style="width:100%;height:500px;background:linear-gradient(135deg,var(--dark3) 0%,var(--mid) 50%,var(--dark3) 100%);border-radius:3px;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:20px;position:relative">
          <i class="fas fa-broadcast-tower" style="font-size:5rem;color:var(--muted)"></i>
          <div style="font-family:var(--font-display);font-size:3rem;letter-spacing:0.08em;color:var(--white)"><?= htmlspecialchars($siteName) ?></div>
          <div style="font-family:var(--font-mono);font-size:11px;letter-spacing:0.25em;text-transform:uppercase;color:var(--muted)">On Air Since 1998</div>
        </div>
        <div class="about-badge">
          <div class="num">25<sup style="font-size:1.5rem">+</sup></div>
          <div class="lbl">Years Broadcasting</div>
        </div>
      </div>

      <div class="about-text">
        <div class="section-label">Our Mission</div>
        <h2><?= htmlspecialchars($siteName) ?> —<br>A Voice for Everyone</h2>
        <p><?= nl2br(htmlspecialchars($history)) ?></p>

        <div class="mission-quote">
          <p><?= nl2br(htmlspecialchars($mission)) ?></p>
        </div>

        <div class="row g-3 mt-4">
          <div class="col-6">
            <div style="background:var(--dark2);border:1px solid rgba(255,255,255,0.05);border-radius:3px;padding:20px;text-align:center">
              <div style="font-family:var(--font-display);font-size:2.5rem;color:var(--white)">12<span style="color:var(--red)">K</span></div>
              <div style="font-family:var(--font-mono);font-size:9px;letter-spacing:0.15em;text-transform:uppercase;color:var(--muted)">Weekly Listeners</div>
            </div>
          </div>
          <div class="col-6">
            <div style="background:var(--dark2);border:1px solid rgba(255,255,255,0.05);border-radius:3px;padding:20px;text-align:center">
              <div style="font-family:var(--font-display);font-size:2.5rem;color:var(--white)">24<span style="color:var(--red)">h</span></div>
              <div style="font-family:var(--font-mono);font-size:9px;letter-spacing:0.15em;text-transform:uppercase;color:var(--muted)">Daily Programming</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- EVENT CALENDAR -->
<section class="section section-dark2" id="events">
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-6">
        <div class="section-label">What's On</div>
        <h2 class="section-title mb-4">Event Calendar</h2>

        <div class="cal-card" id="calendarWidget">
          <!-- Header -->
          <div class="cal-head">
            <button class="cal-nav-btn" onclick="prevMonth()"><i class="fas fa-chevron-left"></i></button>
            <div class="cal-month-label"><?= date('F Y', mktime(0,0,0,$calMonth,1,$calYear)) ?></div>
            <button class="cal-nav-btn" onclick="nextMonth()"><i class="fas fa-chevron-right"></i></button>
          </div>

          <!-- Day labels -->
          <div class="cal-grid">
            <div class="cal-dow">
              <?php foreach (['Su','Mo','Tu','We','Th','Fr','Sa'] as $dn): ?>
              <span><?= $dn ?></span>
              <?php endforeach; ?>
            </div>

            <!-- Date cells -->
            <?php
            $firstDay = (int)date('w', mktime(0,0,0,$calMonth,1,$calYear));
            $daysInMonth = (int)date('t', mktime(0,0,0,$calMonth,1,$calYear));
            $todayD = (int)date('j'); $todayM = (int)date('n'); $todayY = (int)date('Y');
            ?>
            <div class="cal-dates">
              <?php for ($cell = 0; $cell < $firstDay; $cell++): ?>
                <div class="cal-d empty"></div>
              <?php endfor; ?>
              <?php for ($day = 1; $day <= $daysInMonth; $day++): ?>
                <?php
                $isToday    = $day===$todayD && $calMonth===$todayM && $calYear===$todayY;
                $hasEvent   = in_array($day, $eventDays, true);
                ?>
                <div class="cal-d<?= $isToday?' today':'' ?><?= $hasEvent?' has-event':'' ?>"
                     title="<?= $hasEvent ? 'Event scheduled' : '' ?>"><?= $day ?></div>
              <?php endfor; ?>
            </div>
          </div>

          <!-- Upcoming events this month -->
          <?php if (!empty($monthEvents)): ?>
          <div class="cal-events">
            <?php foreach ($monthEvents as $ev): ?>
            <div class="cal-event-item">
              <div class="cal-event-date">
                <?= date('d M', strtotime($ev['event_date'])) ?>
                <?php if ($ev['start_time']): ?>
                  <br><small><?= date('g:i A', strtotime($ev['start_time'])) ?></small>
                <?php endif; ?>
              </div>
              <div>
                <div class="cal-event-name"><?= htmlspecialchars($ev['title']) ?></div>
                <?php if ($ev['location']): ?>
                <div style="font-size:11px;color:var(--muted)"><i class="fas fa-location-dot me-1"></i><?= htmlspecialchars($ev['location']) ?></div>
                <?php endif; ?>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
        </div>

        <script>initCalendar(<?= $calYear ?>, <?= $calMonth ?>);</script>
      </div>

      <!-- UPCOMING EVENTS LIST -->
      <div class="col-lg-6">
        <div class="section-label">Coming Up</div>
        <h2 class="section-title mb-4">Upcoming Events</h2>
        <?php if (empty($upcoming_events)): ?>
          <div style="background:var(--dark2);border:1px solid rgba(255,255,255,0.05);border-radius:3px;padding:40px;text-align:center;color:var(--muted)">
            No upcoming events scheduled at this time.
          </div>
        <?php else: ?>
          <div class="d-flex flex-column gap-3">
            <?php foreach ($upcoming_events as $ev): ?>
            <div style="background:var(--dark2);border:1px solid rgba(255,255,255,0.05);border-radius:3px;padding:20px;display:flex;gap:18px;align-items:flex-start">
              <div style="text-align:center;min-width:56px;background:var(--red);border-radius:3px;padding:10px 8px">
                <div style="font-family:var(--font-display);font-size:1.8rem;color:#fff;line-height:1"><?= date('j', strtotime($ev['event_date'])) ?></div>
                <div style="font-family:var(--font-mono);font-size:9px;letter-spacing:0.12em;text-transform:uppercase;color:rgba(255,255,255,0.8)"><?= date('M', strtotime($ev['event_date'])) ?></div>
              </div>
              <div>
                <div style="font-family:var(--font-display);font-size:1.2rem;letter-spacing:0.03em;color:var(--white);margin-bottom:4px"><?= htmlspecialchars($ev['title']) ?></div>
                <?php if ($ev['location']): ?>
                <div style="font-size:12px;color:var(--muted);margin-bottom:4px"><i class="fas fa-location-dot me-1" style="color:var(--red)"></i><?= htmlspecialchars($ev['location']) ?></div>
                <?php endif; ?>
                <?php if ($ev['start_time']): ?>
                <div style="font-size:12px;color:var(--muted)"><i class="fas fa-clock me-1" style="color:var(--red)"></i><?= date('g:i A', strtotime($ev['start_time'])) ?><?= $ev['end_time'] ? ' — ' . date('g:i A', strtotime($ev['end_time'])) : '' ?></div>
                <?php endif; ?>
                <?php if ($ev['description']): ?>
                <div style="font-size:13px;color:var(--light);margin-top:8px"><?= htmlspecialchars(mb_strimwidth($ev['description'],0,120,'…')) ?></div>
                <?php endif; ?>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<!-- CONTACT SECTION -->
<section class="section section-dark" id="contact">
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-5">
        <div class="section-label">Get in Touch</div>
        <h2 class="section-title mb-4">Contact Us</h2>
        <div class="row g-3">
          <div class="col-12">
            <div class="contact-card">
              <div class="contact-icon"><i class="fas fa-envelope"></i></div>
              <h5>Email</h5>
              <p><a href="mailto:<?= htmlspecialchars($station_email) ?>"><?= htmlspecialchars($station_email) ?></a></p>
            </div>
          </div>
          <div class="col-12">
            <div class="contact-card">
              <div class="contact-icon"><i class="fas fa-phone"></i></div>
              <h5>Phone</h5>
              <p><?= htmlspecialchars($station_phone) ?></p>
            </div>
          </div>
          <div class="col-12">
            <div class="contact-card">
              <div class="contact-icon"><i class="fas fa-location-dot"></i></div>
              <h5>Address</h5>
              <p><?= nl2br(htmlspecialchars($station_address)) ?></p>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-7">
        <div class="section-label">Write to Us</div>
        <h2 class="section-title mb-4">Send a Message</h2>
        <div class="form-card">
          <form method="POST" action="<?= BASE_URL ?>about/contact" novalidate>
            <?= $csrf ?>
            <div class="row g-3">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-lbl">Your Name <span style="color:var(--red)">*</span></label>
                  <input type="text" name="name" class="form-inp" placeholder="Full name" maxlength="100" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-lbl">Email</label>
                  <input type="email" name="email" class="form-inp" placeholder="your@email.com" maxlength="120">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-lbl">Type</label>
                  <select name="type" class="form-inp">
                    <option value="general">General Enquiry</option>
                    <option value="compliment">Compliment</option>
                    <option value="suggestion">Suggestion</option>
                    <option value="complaint">Complaint</option>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-lbl">Subject <span style="color:var(--red)">*</span></label>
                  <input type="text" name="subject" class="form-inp" placeholder="Subject" maxlength="200" required>
                </div>
              </div>
              <div class="col-12">
                <div class="form-group">
                  <label class="form-lbl">Message <span style="color:var(--red)">*</span></label>
                  <textarea name="message" class="form-inp" placeholder="How can we help?" rows="5" required></textarea>
                </div>
              </div>
              <div class="col-12">
                <button type="submit" class="btn-red">
                  <i class="fas fa-paper-plane"></i> Send Message
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
