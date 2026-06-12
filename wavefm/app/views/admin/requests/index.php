<div class="adm-section-head">
  <h2>Music Requests</h2>
  <div style="font-family:var(--font-mono);font-size:11px;color:var(--muted)">
    Total: <?= number_format($total) ?> request<?= $total !== 1 ? 's' : '' ?>
  </div>
</div>

<!-- STATUS FILTER TABS -->
<div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:24px">
  <?php
  $statuses = ['' => 'All', 'pending' => 'Pending', 'approved' => 'Approved', 'played' => 'Played', 'rejected' => 'Rejected'];
  foreach ($statuses as $val => $label):
    $active = $status === $val;
  ?>
  <a href="<?= BASE_URL ?>admin/requests<?= $val ? '?status=' . $val : '' ?>"
     class="btn-adm <?= $active ? 'btn-adm-primary' : 'btn-adm-secondary' ?>">
    <?= $label ?>
  </a>
  <?php endforeach; ?>
</div>

<div class="adm-table-wrap">
  <table class="adm-table">
    <thead>
      <tr>
        <th>#</th>
        <th>Song / Artist</th>
        <th>Listener</th>
        <th>Shout-Out</th>
        <th>Show</th>
        <th>Status</th>
        <th>Date</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($requests as $req): ?>
      <tr>
        <td style="font-family:var(--font-mono);font-size:10px;color:var(--muted)"><?= (int)$req['id'] ?></td>
        <td>
          <strong style="color:var(--white)"><?= htmlspecialchars($req['song_title']) ?></strong><br>
          <small style="color:var(--muted)"><?= htmlspecialchars($req['artist']) ?></small>
        </td>
        <td>
          <?= htmlspecialchars($req['listener_name']) ?>
          <?php if ($req['listener_email']): ?>
          <br><small style="color:var(--muted)"><?= htmlspecialchars($req['listener_email']) ?></small>
          <?php endif; ?>
        </td>
        <td style="max-width:180px">
          <?php if ($req['shoutout']): ?>
            <span style="font-size:12px;color:var(--light)" title="<?= htmlspecialchars($req['shoutout']) ?>">
              <?= htmlspecialchars(mb_strimwidth($req['shoutout'], 0, 60, '…')) ?>
            </span>
          <?php else: ?>
            <span style="color:var(--muted)">—</span>
          <?php endif; ?>
        </td>
        <td><?= $req['show_title'] ? htmlspecialchars($req['show_title']) : '<span style="color:var(--muted)">Any</span>' ?></td>
        <td>
          <span class="badge-status badge-<?= $req['status'] ?>" id="req-status-<?= (int)$req['id'] ?>">
            <?= $req['status'] ?>
          </span>
        </td>
        <td style="font-family:var(--font-mono);font-size:10px;white-space:nowrap">
          <?= date('d M Y H:i', strtotime($req['created_at'])) ?>
        </td>
        <td>
          <div class="actions" style="flex-wrap:wrap;gap:6px">
            <!-- Quick status change -->
            <form method="POST" action="<?= BASE_URL ?>admin/requests/status" style="display:inline">
              <?= Security::csrfField() ?>
              <input type="hidden" name="id" value="<?= (int)$req['id'] ?>">
              <select name="status" class="form-inp" style="padding:4px 8px;font-size:10px;font-family:var(--font-mono);width:auto;height:auto"
                      onchange="this.form.submit()">
                <?php foreach (['pending','approved','played','rejected'] as $s): ?>
                <option value="<?= $s ?>"<?= $req['status']===$s?' selected':'' ?>><?= ucfirst($s) ?></option>
                <?php endforeach; ?>
              </select>
            </form>
            <a href="<?= BASE_URL ?>admin/requests/delete/<?= (int)$req['id'] ?>"
               class="btn-adm btn-adm-delete"
               data-confirm="Delete this request from <?= htmlspecialchars(addslashes($req['listener_name'])) ?>?">
              <i class="fas fa-trash"></i>
            </a>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($requests)): ?>
      <tr>
        <td colspan="8" style="text-align:center;color:var(--muted);padding:40px">
          <i class="fas fa-music fa-2x mb-3 d-block"></i>
          No <?= $status ? $status : '' ?> requests found.
        </td>
      </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- PAGINATION -->
<?php
$totalPages = ceil($total / $per_page);
if ($totalPages > 1):
  $qs = $status ? '?status=' . urlencode($status) . '&' : '?';
?>
<div class="pagination-wrap mt-4">
  <a href="<?= BASE_URL ?>admin/requests<?= $qs ?>page=<?= max(1,$page-1) ?>" class="pg-btn<?= $page<=1?' disabled':'' ?>">
    <i class="fas fa-chevron-left"></i>
  </a>
  <?php for ($p = 1; $p <= $totalPages; $p++): ?>
    <a href="<?= BASE_URL ?>admin/requests<?= $qs ?>page=<?= $p ?>" class="pg-btn<?= $p===$page?' active':'' ?>"><?= $p ?></a>
  <?php endfor; ?>
  <a href="<?= BASE_URL ?>admin/requests<?= $qs ?>page=<?= min($totalPages,$page+1) ?>" class="pg-btn<?= $page>=$totalPages?' disabled':'' ?>">
    <i class="fas fa-chevron-right"></i>
  </a>
</div>
<?php endif; ?>
