<div class="adm-section-head">
  <h2><?= htmlspecialchars($page_title) ?></h2>
  <a href="<?= BASE_URL ?>admin/subscribers/create" class="btn-adm btn-adm-primary">
    <i class="fas fa-plus"></i> Add Subscriber
  </a>
</div>

<?php if ($flash['success'] ?? null): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
  <i class="fas fa-check-circle me-2"></i> <?= htmlspecialchars($flash['success']) ?>
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if ($flash['error'] ?? null): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
  <i class="fas fa-exclamation-circle me-2"></i> <?= htmlspecialchars($flash['error']) ?>
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- SEARCH & STATS -->
<div class="adm-form-card" style="margin-bottom:20px">
  <div class="row g-3 align-items-end">
    <div class="col-md-8">
      <form method="GET" action="<?= BASE_URL ?>admin/subscribers" style="display:flex;gap:8px;align-items:end">
        <div style="flex:1">
          <label class="form-lbl">Search by email or name</label>
          <input type="text" name="q" class="form-inp" 
                 value="<?= htmlspecialchars($search) ?>" 
                 placeholder="e.g. john@example.com">
        </div>
        <button type="submit" class="btn-adm btn-adm-primary">
          <i class="fas fa-search"></i> Search
        </button>
        <?php if ($search): ?>
        <a href="<?= BASE_URL ?>admin/subscribers" class="btn-adm btn-adm-secondary">Clear</a>
        <?php endif; ?>
      </form>
    </div>
    <div class="col-md-4" style="text-align:right;padding-bottom:12px">
      <div style="font-size:12px;color:var(--muted);margin-bottom:4px">Active Subscribers</div>
      <div style="font-size:32px;font-weight:bold;color:var(--red)"><?= (int)$count ?></div>
    </div>
  </div>
</div>

<!-- SUBSCRIBERS TABLE -->
<div class="adm-table-wrap">
  <table class="adm-table">
    <thead>
      <tr>
        <th>#</th>
        <th>Email</th>
        <th>Name</th>
        <th>Status</th>
        <th>Subscribed</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($subscribers as $sub): ?>
      <tr>
        <td style="font-family:var(--font-mono);font-size:10px;color:var(--muted)"><?= (int)$sub['id'] ?></td>
        <td>
          <a href="mailto:<?= htmlspecialchars($sub['email']) ?>" style="color:var(--link)">
            <?= htmlspecialchars($sub['email']) ?>
          </a>
        </td>
        <td><?= $sub['name'] ? htmlspecialchars($sub['name']) : '—' ?></td>
        <td>
          <span class="badge-status <?= $sub['is_active'] ? 'badge-active' : 'badge-draft' ?>">
            <?= $sub['is_active'] ? 'Active' : 'Inactive' ?>
          </span>
        </td>
        <td style="font-family:var(--font-mono);font-size:10px">
          <?= date('d M Y', strtotime($sub['subscribed_at'])) ?>
        </td>
        <td>
          <div class="actions">
            <a href="<?= BASE_URL ?>admin/subscribers/edit/<?= (int)$sub['id'] ?>" class="btn-adm btn-adm-edit">
              <i class="fas fa-pen"></i>
            </a>
            <a href="<?= BASE_URL ?>admin/subscribers/delete/<?= (int)$sub['id'] ?>"
               class="btn-adm btn-adm-delete"
               data-confirm="Unsubscribe '<?= htmlspecialchars(addslashes($sub['email'])) ?>'?">
              <i class="fas fa-trash"></i>
            </a>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>

      <?php if (empty($subscribers)): ?>
      <tr>
        <td colspan="6" style="text-align:center;color:var(--muted);padding:32px">
          <?= $search ? 'No subscribers match your search.' : 'No subscribers yet.' ?>
        </td>
      </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- PAGINATION -->
<?php if ($total > $per_page): ?>
<div class="adm-pagination">
  <?php
    $total_pages = ceil($total / $per_page);
    $prev_page = max(1, $page - 1);
    $next_page = min($total_pages, $page + 1);
    $query_param = $search ? '?q=' . urlencode($search) : '';
  ?>
  <a href="<?= BASE_URL ?>admin/subscribers<?= $page > 1 ? $query_param ? $query_param . '&page=' . $prev_page : '?page=' . $prev_page : '#' ?>"
     class="<?= $page > 1 ? '' : 'disabled' ?>">← Previous</a>
  
  <span style="margin: 0 10px">Page <?= (int)$page ?> of <?= (int)$total_pages ?></span>
  
  <a href="<?= BASE_URL ?>admin/subscribers<?= $page < $total_pages ? $query_param ? $query_param . '&page=' . $next_page : '?page=' . $next_page : '#' ?>"
     class="<?= $page < $total_pages ? '' : 'disabled' ?>">Next →</a>
</div>
<?php endif; ?>
