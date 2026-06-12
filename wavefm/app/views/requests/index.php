<meta name="base-url" content="<?= BASE_URL ?>">

<div class="page-header">
  <div class="container">
    <div class="section-label">Get Involved</div>
    <h1>Music Requests &amp; Shout-Outs</h1>
    <div class="breadcrumb-row"><a href="<?= BASE_URL ?>">Home</a><span class="sep">/</span><span>Requests</span></div>
  </div>
</div>

<section class="section section-dark">
  <div class="container">

    <!-- FLASH -->
    <?php if (!empty($flash)): ?>
    <div class="flash-msg <?= htmlspecialchars($flash['type']) ?> mb-4">
      <i class="fas fa-<?= $flash['type']==='success'?'check-circle':($flash['type']==='error'?'exclamation-circle':'exclamation-triangle') ?>"></i>
      <?= htmlspecialchars($flash['message']) ?>
    </div>
    <?php endif; ?>

    <div class="row g-5">
      <div class="col-lg-8">

        <!-- ── SONG REQUEST ───────────────────────────── -->
        <div id="request">
          <div class="section-label">Request a Track</div>
          <h2 class="section-title">Song Request</h2>
          <p class="lead-text mb-4">Fill in the form below and we'll try to play your request on the next show. Shout-outs are optional but always fun!</p>

          <div class="form-card mb-5">
            <form method="POST" action="<?= BASE_URL ?>requests/submit" novalidate>
              <?= $csrf ?>
              <div class="row g-3">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="form-lbl">Your Name <span style="color:var(--red)">*</span></label>
                    <input type="text" name="listener_name" class="form-inp" placeholder="e.g. Sarah from Brixton" maxlength="100" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="form-lbl">Email <span style="color:var(--muted);font-size:9px">(optional)</span></label>
                    <input type="email" name="listener_email" class="form-inp" placeholder="your@email.com" maxlength="120">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="form-lbl">Song Title <span style="color:var(--red)">*</span></label>
                    <input type="text" name="song_title" class="form-inp" placeholder="e.g. Purple Rain" maxlength="150" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="form-lbl">Artist <span style="color:var(--red)">*</span></label>
                    <input type="text" name="artist" class="form-inp" placeholder="e.g. Prince" maxlength="120" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="form-lbl">Preferred Show <span style="color:var(--muted);font-size:9px">(optional)</span></label>
                    <select name="show_id" class="form-inp">
                      <option value="">Any show</option>
                      <?php foreach ($shows as $s): ?>
                      <option value="<?= (int)$s['id'] ?>"><?= htmlspecialchars($s['title']) ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-group" id="shoutout">
                    <label class="form-lbl">Shout-Out Message <span style="color:var(--muted);font-size:9px">(optional)</span></label>
                    <textarea name="shoutout" class="form-inp" placeholder="e.g. Big love to Mum on her birthday! 🎂" maxlength="500" rows="3"></textarea>
                  </div>
                </div>
                <div class="col-12">
                  <button type="submit" class="btn-red">
                    <i class="fas fa-paper-plane"></i> Submit Request
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>

        <!-- ── LISTENER FEEDBACK ─────────────────────── -->
        <div id="feedback">
          <div class="section-label">Your Voice</div>
          <h2 class="section-title">Send Feedback</h2>
          <p class="lead-text mb-4">We love hearing from our listeners. Share a compliment, suggestion or general message below.</p>

          <div class="form-card">
            <form method="POST" action="<?= BASE_URL ?>requests/feedback" novalidate>
              <?= $csrf ?>
              <div class="row g-3">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="form-lbl">Your Name <span style="color:var(--red)">*</span></label>
                    <input type="text" name="name" class="form-inp" placeholder="Your name" maxlength="100" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="form-lbl">Email <span style="color:var(--muted);font-size:9px">(optional)</span></label>
                    <input type="email" name="email" class="form-inp" placeholder="your@email.com" maxlength="120">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="form-lbl">Type</label>
                    <select name="type" class="form-inp">
                      <option value="general">General</option>
                      <option value="compliment">Compliment</option>
                      <option value="suggestion">Suggestion</option>
                      <option value="complaint">Complaint</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="form-lbl">Subject <span style="color:var(--red)">*</span></label>
                    <input type="text" name="subject" class="form-inp" placeholder="What's it about?" maxlength="200" required>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-group">
                    <label class="form-lbl">Message <span style="color:var(--red)">*</span></label>
                    <textarea name="message" class="form-inp" placeholder="Your message…" rows="4" required></textarea>
                  </div>
                </div>
                <div class="col-12">
                  <button type="submit" class="btn-red">
                    <i class="fas fa-comment-dots"></i> Send Feedback
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>

      </div><!-- /col-lg-8 -->

      <!-- SIDEBAR: GENRE POLL -->
      <div class="col-lg-4">
        <div class="poll-card mb-4">
          <div class="section-label" style="margin-bottom:8px">Listener Vote</div>
          <div class="poll-title">What genre do you want more of?</div>

          <?php
          $pollTotal = array_sum(array_column($poll_results, 'votes'));
          foreach ($poll_results as $genre):
            $pct = $pollTotal > 0 ? round(($genre['votes'] / $pollTotal) * 100) : 0;
          ?>
          <div class="poll-opt" onclick="<?= $has_voted ? "showToast('You have already voted this week.','warning')" : "votePoll({$genre['id']})" ?>">
            <div class="poll-opt-label">
              <span class="poll-opt-name"><?= htmlspecialchars($genre['name']) ?></span>
              <span class="poll-opt-pct" data-genre="<?= (int)$genre['id'] ?>"><?= $pct ?>%</span>
            </div>
            <div class="poll-track">
              <div class="poll-fill" data-genre="<?= (int)$genre['id'] ?>" data-width="<?= $pct ?>" style="width:0%"></div>
            </div>
          </div>
          <?php endforeach; ?>

          <div style="margin-top:16px;font-family:var(--font-mono);font-size:10px;letter-spacing:0.1em;color:var(--muted)">
            <?= number_format($pollTotal) ?> total vote<?= $pollTotal!==1?'s':'' ?>
            <?php if ($has_voted): ?>
              &nbsp;·&nbsp; <span style="color:var(--red)">You've voted this week</span>
            <?php else: ?>
              &nbsp;·&nbsp; Click a genre to vote
            <?php endif; ?>
          </div>
        </div>

        <!-- HOW TO REACH US -->
        <div class="sidebar-card">
          <div class="sidebar-card-head"><i class="fas fa-info-circle me-2" style="color:var(--red)"></i>Other Ways to Request</div>
          <div class="sidebar-card-body">
            <div style="display:flex;flex-direction:column;gap:14px">
              <div style="display:flex;gap:12px;align-items:flex-start">
                <i class="fas fa-phone" style="color:var(--red);margin-top:2px;width:16px;flex-shrink:0"></i>
                <div>
                  <div style="font-family:var(--font-mono);font-size:10px;letter-spacing:0.1em;text-transform:uppercase;color:var(--muted);margin-bottom:2px">Call In</div>
                  <div style="font-size:13px;color:var(--light)"><?= htmlspecialchars($this->settings->get('station_phone')) ?></div>
                </div>
              </div>
              <div style="display:flex;gap:12px;align-items:flex-start">
                <i class="fas fa-envelope" style="color:var(--red);margin-top:2px;width:16px;flex-shrink:0"></i>
                <div>
                  <div style="font-family:var(--font-mono);font-size:10px;letter-spacing:0.1em;text-transform:uppercase;color:var(--muted);margin-bottom:2px">Email Us</div>
                  <div style="font-size:13px;color:var(--light)"><?= htmlspecialchars($this->settings->get('station_email')) ?></div>
                </div>
              </div>
              <div style="display:flex;gap:12px;align-items:flex-start">
                <i class="fab fa-x-twitter" style="color:var(--red);margin-top:2px;width:16px;flex-shrink:0"></i>
                <div>
                  <div style="font-family:var(--font-mono);font-size:10px;letter-spacing:0.1em;text-transform:uppercase;color:var(--muted);margin-bottom:2px">Social Media</div>
                  <div style="font-size:13px;color:var(--light)">Tweet or DM us anytime</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
