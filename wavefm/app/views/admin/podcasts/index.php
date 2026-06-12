<div class="adm-section-head">
  <h2>Manage Podcasts</h2>
  <a href="<?= BASE_URL ?>admin/podcasts/create" class="btn-adm btn-adm-primary">
    <i class="fas fa-plus"></i> Upload Episode
  </a>
</div>

<div class="adm-table-wrap">
  <table class="adm-table">
    <thead>
      <tr>
        <th>#</th>
        <th>Title</th>
        <th>Show</th>
        <th>Duration</th>
        <th>Size</th>
        <th>Published</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($podcasts as $pod): ?>
      <tr>
        <td style="font-family:var(--font-mono);font-size:10px;color:var(--muted)"><?= (int)$pod['id'] ?></td>
        <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
          <?= htmlspecialchars($pod['title']) ?>
        </td>
        <td><?= htmlspecialchars($pod['show_title']) ?></td>
        <td style="font-family:var(--font-mono);font-size:11px">
          <?= $pod['duration'] ? gmdate('H:i:s', $pod['duration']) : '—' ?>
        </td>
        <td style="font-family:var(--font-mono);font-size:11px">
          <?= Security::formatBytes($pod['file_size']) ?>
        </td>
        <td style="font-family:var(--font-mono);font-size:10px">
          <?= date('d M Y', strtotime($pod['published_at'])) ?>
        </td>
        <td>
          <div class="actions">
            <a href="<?= BASE_URL ?>admin/podcasts/delete/<?= (int)$pod['id'] ?>"
               class="btn-adm btn-adm-delete"
               data-confirm="Delete this episode? The audio file will also be removed.">
              <i class="fas fa-trash"></i>
            </a>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>

      <?php if (empty($podcasts)): ?>
      <tr>
        <td colspan="7" style="text-align:center;color:var(--muted);padding:32px">
          <i class="fas fa-podcast fa-2x mb-3 d-block"></i>
          No podcasts uploaded yet.
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
    <a href="<?= BASE_URL ?>admin/podcasts?page=<?= $p ?>"
       class="pg-btn<?= $p === $page ? ' active' : '' ?>">
      <?= $p ?>
    </a>
  <?php endfor; ?>
</div>
<?php endif; ?>
