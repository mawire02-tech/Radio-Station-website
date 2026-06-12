<div class="adm-section-head">
  <h2><?= htmlspecialchars($page_title) ?></h2>
</div>

<form method="POST" action="<?= BASE_URL . ($event ? 'admin/events/update/' . (int)$event['id'] : 'admin/events/store') ?>">
  <?= $csrf ?>
  
  <div class="row g-4">
    <!-- LEFT COLUMN -->
    <div class="col-lg-8">
      <!-- BASIC DETAILS -->
      <div class="adm-form-card">
        <legend><i class="fas fa-calendar me-2" style="color:var(--red)"></i>Event Details</legend>
        <div class="row g-3">
          <div class="col-12">
            <label class="form-lbl">Event Title *</label>
            <input type="text" name="title" class="form-inp" required
                   value="<?= htmlspecialchars($event['title'] ?? '') ?>" placeholder="Enter event name">
          </div>

          <div class="col-md-6">
            <label class="form-lbl">Date *</label>
            <input type="date" name="event_date" class="form-inp" required
                   value="<?= $event['event_date'] ?? '' ?>">
          </div>

          <div class="col-md-3">
            <label class="form-lbl">Start Time</label>
            <input type="time" name="start_time" class="form-inp"
                   value="<?= $event['start_time'] ?? '' ?>">
          </div>

          <div class="col-md-3">
            <label class="form-lbl">End Time</label>
            <input type="time" name="end_time" class="form-inp"
                   value="<?= $event['end_time'] ?? '' ?>">
          </div>

          <div class="col-12">
            <label class="form-lbl">Location</label>
            <input type="text" name="location" class="form-inp"
                   value="<?= htmlspecialchars($event['location'] ?? '') ?>" placeholder="Event location">
          </div>

          <div class="col-12">
            <label class="form-lbl">Description</label>
            <textarea name="description" class="form-inp" rows="6" placeholder="Event description (HTML allowed)"><?= htmlspecialchars($event['description'] ?? '') ?></textarea>
          </div>
        </div>
      </div>
    </div>

    <!-- RIGHT COLUMN -->
    <div class="col-lg-4">
      <!-- FEATURED -->
      <div class="adm-form-card">
        <legend><i class="fas fa-star me-2" style="color:var(--red)"></i>Featured</legend>
        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="isFeatured"
                 <?= ($event['is_featured'] ?? 0) ? 'checked' : '' ?>>
          <label class="form-check-label" for="isFeatured">
            Mark as featured event
          </label>
        </div>
      </div>

      <!-- ACTIONS -->
      <div class="adm-form-card">
        <legend><i class="fas fa-save me-2" style="color:var(--red)"></i>Save</legend>
        <div class="d-grid gap-2">
          <button type="submit" class="btn-adm btn-adm-primary">
            <i class="fas fa-save"></i> <?= $event ? 'Update Event' : 'Create Event' ?>
          </button>
          <a href="<?= BASE_URL ?>admin/events" class="btn-adm btn-adm-secondary">
            <i class="fas fa-times"></i> Cancel
          </a>
        </div>
      </div>
    </div>
  </div>
</form>
