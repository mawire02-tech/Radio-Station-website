<div class="adm-section-head">
  <h2>Manage Users</h2>
  <a href="<?= BASE_URL ?>admin/users/create" class="btn-adm btn-adm-primary">
    <i class="fas fa-plus"></i> Add User
  </a>
</div>

<div class="adm-table-wrap">
  <table class="adm-table">
    <thead>
      <tr>
        <th>#</th>
        <th>Name</th>
        <th>Username</th>
        <th>Email</th>
        <th>Role</th>
        <th>Status</th>
        <th>Last Login</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $u): ?>
      <tr>
        <td style="font-family:var(--font-mono);font-size:10px;color:var(--muted)"><?= (int)$u['id'] ?></td>
        <td>
          <div style="display:flex;align-items:center;gap:10px">
            <div style="width:32px;height:32px;border-radius:50%;background:var(--red);display:flex;align-items:center;justify-content:center;font-family:var(--font-mono);font-size:11px;color:#fff;flex-shrink:0">
              <?= strtoupper(substr($u['full_name'], 0, 2)) ?>
            </div>
            <?= htmlspecialchars($u['full_name']) ?>
          </div>
        </td>
        <td style="font-family:var(--font-mono);font-size:12px"><?= htmlspecialchars($u['username']) ?></td>
        <td style="font-size:13px"><?= htmlspecialchars($u['email']) ?></td>
        <td>
          <span class="badge-status" style="<?= match($u['role']) {
            'superadmin' => 'background:rgba(212,168,71,0.15);color:var(--gold);border:1px solid rgba(212,168,71,0.25)',
            'admin'      => 'background:rgba(52,152,219,0.15);color:#3498db;border:1px solid rgba(52,152,219,0.25)',
            default      => 'background:rgba(149,165,166,0.15);color:#95a5a6;border:1px solid rgba(149,165,166,0.2)'
          } ?>">
            <?= $u['role'] ?>
          </span>
        </td>
        <td>
          <span class="badge-status <?= $u['is_active'] ? 'badge-active' : 'badge-draft' ?>">
            <?= $u['is_active'] ? 'Active' : 'Inactive' ?>
          </span>
        </td>
        <td style="font-family:var(--font-mono);font-size:10px;color:var(--muted)">
          <?= $u['last_login'] ? date('d M Y H:i', strtotime($u['last_login'])) : 'Never' ?>
        </td>
        <td>
          <div class="actions">
            <a href="<?= BASE_URL ?>admin/users/edit/<?= (int)$u['id'] ?>" class="btn-adm btn-adm-edit">
              <i class="fas fa-pen"></i>
            </a>
            <?php if ((int)$u['id'] !== (int)$_SESSION['admin_id'] && $u['role'] !== 'superadmin'): ?>
            <a href="<?= BASE_URL ?>admin/users/delete/<?= (int)$u['id'] ?>"
               class="btn-adm btn-adm-delete"
               data-confirm="Delete user '<?= htmlspecialchars(addslashes($u['username'])) ?>'? This action cannot be undone.">
              <i class="fas fa-trash"></i>
            </a>
            <?php endif; ?>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($users)): ?>
      <tr>
        <td colspan="8" style="text-align:center;color:var(--muted);padding:32px">No users found.</td>
      </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- PAGINATION -->
<?php $totalPages = ceil($total / $per_page); if ($totalPages > 1): ?>
<div class="pagination-wrap mt-4">
  <?php for ($p = 1; $p <= $totalPages; $p++): ?>
    <a href="<?= BASE_URL ?>admin/users?page=<?= $p ?>" class="pg-btn<?= $p===$page?' active':'' ?>"><?= $p ?></a>
  <?php endfor; ?>
</div>
<?php endif; ?>
