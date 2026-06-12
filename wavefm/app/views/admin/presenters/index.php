<!-- admin/presenters/index.php -->
<div class="adm-section-head">
  <h2>Manage Presenters</h2>
  <a href="<?= BASE_URL ?>admin/presenters/create" class="btn-adm btn-adm-primary"><i class="fas fa-plus"></i> Add Presenter</a>
</div>
<div class="adm-table-wrap">
  <table class="adm-table">
    <thead><tr><th>#</th><th>Name</th><th>Role</th><th>Shows</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach ($presenters as $p): ?>
      <tr>
        <td style="font-family:var(--font-mono);font-size:10px;color:var(--muted)"><?= (int)$p['id'] ?></td>
        <td>
          <div style="display:flex;align-items:center;gap:12px">
            <?php if ($p['photo']): ?>
              <img src="<?= UPLOAD_URL.htmlspecialchars($p['photo']) ?>" style="width:36px;height:36px;border-radius:50%;object-fit:cover">
            <?php else: ?>
              <div style="width:36px;height:36px;border-radius:50%;background:var(--dark3);display:flex;align-items:center;justify-content:center;color:var(--muted);font-size:14px"><i class="fas fa-user"></i></div>
            <?php endif; ?>
            <?= htmlspecialchars($p['name']) ?>
          </div>
        </td>
        <td><?= htmlspecialchars($p['role']) ?></td>
        <td><?= (int)$p['show_count'] ?></td>
        <td><span class="badge-status <?= $p['is_active']?'badge-active':'badge-draft' ?>"><?= $p['is_active']?'Active':'Inactive' ?></span></td>
        <td>
          <div class="actions">
            <a href="<?= BASE_URL ?>admin/presenters/edit/<?= (int)$p['id'] ?>" class="btn-adm btn-adm-edit"><i class="fas fa-pen"></i></a>
            <a href="<?= BASE_URL ?>admin/presenters/delete/<?= (int)$p['id'] ?>" class="btn-adm btn-adm-delete"
               data-confirm="Delete presenter '<?= htmlspecialchars(addslashes($p['name'])) ?>'?"><i class="fas fa-trash"></i></a>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($presenters)): ?><tr><td colspan="6" style="text-align:center;color:var(--muted);padding:32px">No presenters yet.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
<?php if (ceil($total/$per_page)>1): $tp=ceil($total/$per_page); ?>
<div class="pagination-wrap mt-4">
  <?php for($p=1;$p<=$tp;$p++): ?><a href="<?= BASE_URL ?>admin/presenters?page=<?= $p ?>" class="pg-btn<?= $p===$page?' active':'' ?>"><?= $p ?></a><?php endfor; ?>
</div>
<?php endif; ?>
