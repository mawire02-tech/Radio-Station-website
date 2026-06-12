<div class="adm-section-head">
  <h2>Site Settings</h2>
</div>

<form method="POST" action="<?= BASE_URL ?>admin/settings/update" enctype="multipart/form-data">
  <?= $csrf ?>
  <div class="row g-4">

    <!-- GENERAL -->
    <div class="col-lg-6">
      <div class="adm-form-card">
        <legend><i class="fas fa-broadcast-tower me-2" style="color:var(--red)"></i>Station Details</legend>
        <div class="row g-3">
          <?php
          $genSettings = $settings['general'] ?? [];
          $fieldMap = [
            'station_name'      => ['label' => 'Station Name',    'type' => 'text'],
            'station_tagline'   => ['label' => 'Tagline',         'type' => 'text'],
            'station_frequency' => ['label' => 'Frequency (MHz)', 'type' => 'text'],
            'station_logo'      => ['label' => 'Station Logo',    'type' => 'file'],
          ];
          foreach ($genSettings as $s):
            if (!isset($fieldMap[$s['setting_key']])) continue;
            $f = $fieldMap[$s['setting_key']];
          ?>
          <div class="col-12">
            <label class="form-lbl"><?= $f['label'] ?></label>
            <?php if ($f['type'] === 'file'): ?>
              <input type="file" name="<?= htmlspecialchars($s['setting_key']) ?>"
                     class="form-inp" accept="image/*">
              <?php if ($s['setting_val']): ?>
                <small class="form-text text-muted">
                  Current: <img src="<?= UPLOAD_URL . htmlspecialchars($s['setting_val']) ?>" alt="Logo" style="max-height:40px; margin-top:5px;">
                </small>
              <?php endif; ?>
            <?php else: ?>
              <input type="<?= $f['type'] ?>" name="<?= htmlspecialchars($s['setting_key']) ?>"
                     class="form-inp" value="<?= htmlspecialchars($s['setting_val']) ?>">
            <?php endif; ?>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- CONTACT -->
    <div class="col-lg-6">
      <div class="adm-form-card">
        <legend><i class="fas fa-address-card me-2" style="color:var(--red)"></i>Contact Details</legend>
        <div class="row g-3">
          <?php
          $contactSettings = $settings['contact'] ?? [];
          $contactMap = [
            'station_email'   => ['label' => 'Email Address', 'type' => 'email'],
            'station_phone'   => ['label' => 'Phone Number',  'type' => 'text'],
            'station_address' => ['label' => 'Address',       'type' => 'textarea'],
          ];
          foreach ($contactSettings as $s):
            if (!isset($contactMap[$s['setting_key']])) continue;
            $f = $contactMap[$s['setting_key']];
          ?>
          <div class="col-12">
            <label class="form-lbl"><?= $f['label'] ?></label>
            <?php if ($f['type'] === 'textarea'): ?>
            <textarea name="<?= htmlspecialchars($s['setting_key']) ?>" class="form-inp" rows="3"><?= htmlspecialchars($s['setting_val']) ?></textarea>
            <?php else: ?>
            <input type="<?= $f['type'] ?>" name="<?= htmlspecialchars($s['setting_key']) ?>"
                   class="form-inp" value="<?= htmlspecialchars($s['setting_val']) ?>">
            <?php endif; ?>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- STREAMING -->
    <div class="col-lg-6">
      <div class="adm-form-card">
        <legend><i class="fas fa-tower-broadcast me-2" style="color:var(--red)"></i>Streaming</legend>
        <div class="row g-3">
          <?php foreach ($settings['streaming'] ?? [] as $s): ?>
          <div class="col-12">
            <label class="form-lbl"><?= htmlspecialchars($s['label']) ?></label>
            <input type="url" name="<?= htmlspecialchars($s['setting_key']) ?>"
                   class="form-inp" value="<?= htmlspecialchars($s['setting_val']) ?>"
                   placeholder="https://stream.example.com/live">
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- SOCIAL -->
    <div class="col-lg-6">
      <div class="adm-form-card">
        <legend><i class="fas fa-share-nodes me-2" style="color:var(--red)"></i>Social Media URLs</legend>
        <div class="row g-3">
          <?php
          $socialIcons = [
            'facebook_url'  => ['icon' => 'fa-facebook-f',  'label' => 'Facebook URL'],
            'twitter_url'   => ['icon' => 'fa-x-twitter',   'label' => 'Twitter / X URL'],
            'instagram_url' => ['icon' => 'fa-instagram',   'label' => 'Instagram URL'],
            'youtube_url'   => ['icon' => 'fa-youtube',     'label' => 'YouTube URL'],
          ];
          foreach ($settings['social'] ?? [] as $s):
            $meta = $socialIcons[$s['setting_key']] ?? null;
            if (!$meta) continue;
          ?>
          <div class="col-12">
            <label class="form-lbl"><i class="fab <?= $meta['icon'] ?> me-1"></i><?= $meta['label'] ?></label>
            <input type="url" name="<?= htmlspecialchars($s['setting_key']) ?>"
                   class="form-inp" value="<?= htmlspecialchars($s['setting_val']) ?>"
                   placeholder="https://…">
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- ABOUT CONTENT -->
    <div class="col-12">
      <div class="adm-form-card">
        <legend><i class="fas fa-file-alt me-2" style="color:var(--red)"></i>About Page Content</legend>
        <div class="row g-3">
          <?php foreach ($settings['about'] ?? [] as $s): ?>
          <div class="col-12">
            <label class="form-lbl"><?= htmlspecialchars($s['label']) ?></label>
            <textarea name="<?= htmlspecialchars($s['setting_key']) ?>" class="form-inp" rows="4"><?= htmlspecialchars($s['setting_val']) ?></textarea>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- SEO -->
    <div class="col-12">
      <div class="adm-form-card">
        <legend><i class="fas fa-search me-2" style="color:var(--red)"></i>SEO</legend>
        <?php foreach ($settings['seo'] ?? [] as $s): ?>
        <div class="form-group">
          <label class="form-lbl"><?= htmlspecialchars($s['label']) ?></label>
          <textarea name="<?= htmlspecialchars($s['setting_key']) ?>" class="form-inp" rows="2"><?= htmlspecialchars($s['setting_val']) ?></textarea>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- LISTENER ANALYTICS -->
    <div class="col-12">
      <div class="adm-form-card">
        <legend><i class="fas fa-chart-line me-2" style="color:var(--red)"></i>Listener Analytics</legend>
        <div class="row g-3">
          <div class="col-md-4">
            <div class="analytics-stat">
              <div class="stat-value" id="liveListenerCount">0</div>
              <div class="stat-label">Live Listeners</div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="analytics-stat">
              <div class="stat-value" id="dailyListenerCount">0</div>
              <div class="stat-label">Today's Listeners</div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="analytics-stat">
              <div class="stat-value" id="totalListenerCount">0</div>
              <div class="stat-label">Total Sessions</div>
            </div>
          </div>
          <div class="col-12">
            <p class="text-muted"><small>Analytics are tracked in real-time. Data refreshes every 30 seconds.</small></p>
            <button type="button" class="btn btn-sm btn-secondary" id="refreshAnalytics">
              <i class="fas fa-sync"></i> Refresh Now
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12">
      <button type="submit" class="btn-adm btn-adm-primary" style="padding:12px 32px">
        <i class="fas fa-save"></i> Save All Settings
      </button>
    </div>
  </div>
</form>

<style>
.analytics-stat {
  background: linear-gradient(135deg, var(--red) 0%, #c71f1f 100%);
  color: white;
  padding: 20px;
  border-radius: 8px;
  text-align: center;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.stat-value {
  font-size: 32px;
  font-weight: bold;
  margin-bottom: 5px;
}
.stat-label {
  font-size: 14px;
  opacity: 0.9;
}
</style>

<script>
function refreshListenerAnalytics() {
  fetch('<?= BASE_URL ?>api/listener-analytics')
    .then(res => res.json())
    .then(data => {
      document.getElementById('liveListenerCount').textContent = data.live_listeners || 0;
      document.getElementById('dailyListenerCount').textContent = data.daily_listeners || 0;
      document.getElementById('totalListenerCount').textContent = data.total_sessions || 0;
    })
    .catch(err => console.error('Error fetching analytics:', err));
}

// Refresh on page load
refreshListenerAnalytics();

// Auto-refresh every 30 seconds
setInterval(refreshListenerAnalytics, 30000);

// Manual refresh button
document.getElementById('refreshAnalytics')?.addEventListener('click', refreshListenerAnalytics);
</script>
