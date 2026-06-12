<div class="adm-section-head">
  <h2>Events</h2>
  <a href="<?= BASE_URL ?>admin/events/create" class="btn-adm btn-adm-primary">
    <i class="fas fa-plus"></i> Add Event
  </a>
</div>

<?php if ($flash): ?>
<div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show" role="alert">
  <?= htmlspecialchars($flash['message']) ?>
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="adm-search-box">
  <form method="GET" action="<?= BASE_URL ?>admin/events" class="d-flex gap-2">
    <input type="text" name="q" class="form-inp" placeholder="Search events..." value="<?= htmlspecialchars($search) ?>">
    <button type="submit" class="btn-adm btn-adm-secondary"><i class="fas fa-search"></i> Search</button>
    <?php if ($search): ?>
      <a href="<?= BASE_URL ?>admin/events" class="btn-adm btn-adm-secondary"><i class="fas fa-times"></i> Clear</a>
    <?php endif; ?>
  </form>
</div>

<div class="adm-table-wrap">
  <table class="adm-table">
    <thead>
      <tr>
        <th>Event Name</th>
        <th>Date</th>
        <th>Time</th>
        <th>Location</th>
        <th>Featured</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($events): ?>
        <?php foreach ($events as $event): ?>
          <tr>
            <td><strong><?= htmlspecialchars($event['title']) ?></strong></td>
            <td><?= date('M d, Y', strtotime($event['event_date'])) ?></td>
            <td>
              <?php if ($event['start_time']): ?>
                <?= date('H:i', strtotime($event['start_time'])) ?>
                <?php if ($event['end_time']): ?>
                  - <?= date('H:i', strtotime($event['end_time'])) ?>
                <?php endif; ?>
              <?php else: ?>
                <span class="text-muted">All day</span>
              <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($event['location'] ?? 'N/A') ?></td>
            <td>
              <?php if ($event['is_featured']): ?>
                <span class="badge bg-success"><i class="fas fa-star"></i> Featured</span>
              <?php else: ?>
                <span class="text-muted">—</span>
              <?php endif; ?>
            </td>
            <td class="adm-table-actions">
              <a href="<?= BASE_URL ?>admin/events/edit/<?= (int)$event['id'] ?>" class="btn-adm-sm btn-adm-secondary" title="Edit">
                <i class="fas fa-edit"></i>
              </a>
              <a href="<?= BASE_URL ?>admin/events/delete/<?= (int)$event['id'] ?>" class="btn-adm-sm btn-adm-danger" onclick="return confirm('Delete this event?')" title="Delete">
                <i class="fas fa-trash"></i>
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="6" class="text-center text-muted py-4">
            <?php if ($search): ?>
              No events found matching "<strong><?= htmlspecialchars($search) ?></strong>"
            <?php else: ?>
              No events yet. <a href="<?= BASE_URL ?>admin/events/create">Create one</a>
            <?php endif; ?>
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php if ($total > $per_page): ?>
<nav class="adm-pagination">
  <?php for ($i = 1; $i <= ceil($total / $per_page); $i++): ?>
    <a href="<?= BASE_URL ?>admin/events?page=<?= $i ?><?= $search ? '&q=' . urlencode($search) : '' ?>" class="<?= $page === $i ? 'active' : '' ?>">
      <?= $i ?>
    </a>
  <?php endfor; ?>
</nav>
<?php endif; ?>
