<div class="adm-section-head">
  <h2>Manage Shows</h2>
  <a href="<?= BASE_URL ?>admin/shows/create" class="btn-adm btn-adm-primary">
    <i class="fas fa-plus"></i> Add Show
  </a>
</div>

<div class="adm-table-wrap">
  <table class="adm-table">
    <thead>
      <tr>
        <th>#</th>
        <th>Title</th>
        <th>Presenter</th>
        <th>Genre</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($shows as $s): ?>
      <tr>
        <td style="font-family:var(--font-mono);font-size:10px;color:var(--muted)"><?= (int)$s['id'] ?></td>
        <td><?= htmlspecialchars($s['title']) ?></td>
        <td><?= htmlspecialchars($s['presenter_name']) ?></td>
        <td><?= htmlspecialchars($s['genre_name']) ?></td>
        <td>
          <span class="badge-status <?= $s['is_active'] ? 'badge-active' : 'badge-draft' ?>">
            <?= $s['is_active'] ? 'Active' : 'Inactive' ?>
          </span>
        </td>
        <td>
          <div class="actions">
            <a href="<?= BASE_URL ?>admin/shows/edit/<?= (int)$s['id'] ?>" class="btn-adm btn-adm-edit">
              <i class="fas fa-pen"></i>
            </a>
            <a href="<?= BASE_URL ?>admin/shows/delete/<?= (int)$s['id'] ?>"
               class="btn-adm btn-adm-delete"
               data-confirm="Delete show '<?= htmlspecialchars(addslashes($s['title'])) ?>'?">
              <i class="fas fa-trash"></i>
            </a>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>

      <?php if (empty($shows)): ?>
      <tr>
        <td colspan="6" style="text-align:center;color:var(--muted);padding:32px">
          No shows yet.
        </td>
      </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php if (ceil($total / $per_page) > 1):
  $totalPages = ceil($total / $per_page);
?>
<div class="pagination-wrap mt-4">
  <?php for ($p = 1; $p <= $totalPages; $p++): ?>
    <a href="<?= BASE_URL ?>admin/shows?page=<?= $p ?>"
       class="pg-btn<?= $p === $page ? ' active' : '' ?>">
      <?= $p ?>
    </a>
  <?php endfor; ?>
</div>
<?php endif; ?>
