<div class="adm-section-head">
  <h2>Dashboard</h2>
  <span style="font-family:var(--font-mono);font-size:11px;color:var(--muted)">
    <?= date('l, F j, Y') ?>
  </span>
</div>

<!-- STATS -->
<div class="row g-3 mb-4">
  <?php
  $statItems = [
    ['icon'=>'fa-newspaper',   'val'=>$stats['news_count'],      'label'=>'Articles',          'url'=>'admin/news'],
    ['icon'=>'fa-microphone',  'val'=>$stats['presenter_count'], 'label'=>'Presenters',        'url'=>'admin/presenters'],
    ['icon'=>'fa-headphones',  'val'=>$stats['show_count'],      'label'=>'Shows',             'url'=>'admin/shows'],
    ['icon'=>'fa-podcast',     'val'=>$stats['podcast_count'],   'label'=>'Podcasts',          'url'=>'admin/podcasts'],
    ['icon'=>'fa-music',       'val'=>$stats['request_count'],   'label'=>'Pending Requests',  'url'=>'admin/requests?status=pending'],
    ['icon'=>'fa-comments',    'val'=>$stats['feedback_unread'], 'label'=>'Unread Feedback',   'url'=>'admin/requests'],
  ];
  foreach ($statItems as $st): ?>
  <div class="col-6 col-md-4 col-xl-2">
    <a href="<?= BASE_URL . $st['url'] ?>" class="dash-stat text-decoration-none d-flex">
      <div class="dash-stat-icon"><i class="fas <?= $st['icon'] ?>"></i></div>
      <div>
        <div class="dash-stat-val"><?= number_format((int)$st['val']) ?></div>
        <div class="dash-stat-lbl"><?= $st['label'] ?></div>
      </div>
    </a>
  </div>
  <?php endforeach; ?>
</div>

<div class="row g-4">

  <!-- RECENT NEWS -->
  <div class="col-lg-7">
    <div class="adm-table-wrap">
      <div style="padding:16px 18px;border-bottom:1px solid rgba(255,255,255,0.06);display:flex;align-items:center;justify-content:space-between">
        <div style="font-family:var(--font-display);font-size:1.1rem;letter-spacing:0.04em">Recent Articles</div>
        <a href="<?= BASE_URL ?>admin/news/create" class="btn-adm btn-adm-primary"><i class="fas fa-plus"></i> Add</a>
      </div>
      <table class="adm-table">
        <thead><tr><th>Title</th><th>Status</th><th>Date</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($recent_news as $article): ?>
          <tr>
            <td style="max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
              <?= htmlspecialchars($article['title']) ?>
            </td>
            <td>
              <span class="badge-status badge-<?= $article['status'] ?>"><?= $article['status'] ?></span>
            </td>
            <td><?= date('d M Y', strtotime($article['created_at'])) ?></td>
            <td>
              <div class="actions">
                <a href="<?= BASE_URL ?>admin/news/edit/<?= (int)$article['id'] ?>" class="btn-adm btn-adm-edit"><i class="fas fa-pen"></i></a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($recent_news)): ?>
          <tr><td colspan="4" style="text-align:center;color:var(--muted);padding:24px">No articles yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- RECENT REQUESTS -->
  <div class="col-lg-5">
    <div class="adm-table-wrap">
      <div style="padding:16px 18px;border-bottom:1px solid rgba(255,255,255,0.06);display:flex;align-items:center;justify-content:space-between">
        <div style="font-family:var(--font-display);font-size:1.1rem;letter-spacing:0.04em">Music Requests</div>
        <a href="<?= BASE_URL ?>admin/requests" class="btn-adm btn-adm-secondary">View All</a>
      </div>
      <table class="adm-table">
        <thead><tr><th>Song</th><th>Listener</th><th>Status</th></tr></thead>
        <tbody>
          <?php foreach ($recent_requests as $req): ?>
          <tr>
            <td style="max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
              <?= htmlspecialchars($req['song_title']) ?><br>
              <small style="color:var(--muted)"><?= htmlspecialchars($req['artist']) ?></small>
            </td>
            <td><?= htmlspecialchars($req['listener_name']) ?></td>
            <td><span class="badge-status badge-<?= $req['status'] ?>"><?= $req['status'] ?></span></td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($recent_requests)): ?>
          <tr><td colspan="3" style="text-align:center;color:var(--muted);padding:24px">No requests yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- ACTIVITY LOG -->
  <div class="col-12">
    <div class="adm-table-wrap">
      <div style="padding:16px 18px;border-bottom:1px solid rgba(255,255,255,0.06)">
        <div style="font-family:var(--font-display);font-size:1.1rem;letter-spacing:0.04em">Recent Activity</div>
      </div>
      <table class="adm-table">
        <thead><tr><th>User</th><th>Action</th><th>Entity</th><th>IP</th><th>Time</th></tr></thead>
        <tbody>
          <?php foreach ($activity_log as $log): ?>
          <tr>
            <td><?= htmlspecialchars($log['username'] ?? 'System') ?></td>
            <td><?= htmlspecialchars($log['action']) ?></td>
            <td><?= htmlspecialchars($log['entity'] ?? '—') ?> <?= $log['entity_id'] ? '#'.$log['entity_id'] : '' ?></td>
            <td style="font-family:var(--font-mono);font-size:10px"><?= htmlspecialchars($log['ip_address']) ?></td>
            <td style="font-family:var(--font-mono);font-size:10px;white-space:nowrap"><?= date('d M H:i', strtotime($log['created_at'])) ?></td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($activity_log)): ?>
          <tr><td colspan="5" style="text-align:center;color:var(--muted);padding:24px">No activity yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
