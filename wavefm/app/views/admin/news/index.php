<!-- admin/news/index.php -->
<div class="adm-section-head">
  <h2>Manage News</h2>
  <a href="<?= BASE_URL ?>admin/news/create" class="btn-adm btn-adm-primary">
    <i class="fas fa-plus"></i> New Article
  </a>
</div>

<!-- SEARCH -->
<form method="GET" action="<?= BASE_URL ?>admin/news" class="mb-4">
  <div class="search-wrap" style="max-width:360px">
    <input type="text" name="q" class="form-inp" placeholder="Search articles…" value="<?= htmlspecialchars($search) ?>">
    <i class="fas fa-search search-icon"></i>
  </div>
</form>

<div class="adm-table-wrap">
  <table class="adm-table">
    <thead>
      <tr>
        <th>#</th><th>Title</th><th>Category</th><th>Author</th>
        <th>Status</th><th>Featured</th><th>Views</th><th>Date</th><th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($articles as $a): ?>
      <tr>
        <td style="font-family:var(--font-mono);font-size:10px;color:var(--muted)"><?= (int)$a['id'] ?></td>
        <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= htmlspecialchars($a['title']) ?></td>
        <td><?= htmlspecialchars($a['category_name']) ?></td>
        <td><?= htmlspecialchars($a['author_name']) ?></td>
        <td><span class="badge-status badge-<?= $a['status'] ?>"><?= $a['status'] ?></span></td>
        <td><?= $a['featured'] ? '<i class="fas fa-star" style="color:var(--gold)"></i>' : '—' ?></td>
        <td style="font-family:var(--font-mono);font-size:11px"><?= number_format($a['views']) ?></td>
        <td style="font-family:var(--font-mono);font-size:10px;white-space:nowrap"><?= date('d M Y', strtotime($a['created_at'])) ?></td>
        <td>
          <div class="actions">
            <a href="<?= BASE_URL ?>news/<?= htmlspecialchars($a['slug']) ?>" target="_blank" class="btn-adm btn-adm-secondary" title="View"><i class="fas fa-eye"></i></a>
            <a href="<?= BASE_URL ?>admin/news/edit/<?= (int)$a['id'] ?>" class="btn-adm btn-adm-edit"><i class="fas fa-pen"></i></a>
            <a href="<?= BASE_URL ?>admin/news/delete/<?= (int)$a['id'] ?>" class="btn-adm btn-adm-delete"
               data-confirm="Delete '<?= htmlspecialchars(addslashes($a['title'])) ?>'? This cannot be undone.">
              <i class="fas fa-trash"></i>
            </a>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($articles)): ?>
      <tr><td colspan="9" style="text-align:center;color:var(--muted);padding:32px">No articles found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- PAGINATION -->
<?php if (ceil($total / $per_page) > 1):
  $totalPages = ceil($total / $per_page);
  $qs = $search ? '&q=' . urlencode($search) : '';
?>
<div class="pagination-wrap mt-4">
  <?php for ($p = 1; $p <= $totalPages; $p++): ?>
  <a href="<?= BASE_URL ?>admin/news?page=<?= $p . $qs ?>" class="pg-btn<?= $p===$page?' active':'' ?>"><?= $p ?></a>
  <?php endfor; ?>
</div>
<?php endif; ?>
