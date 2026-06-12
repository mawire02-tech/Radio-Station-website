<div class="adm-section-head">
  <h2><?= htmlspecialchars($page_title) ?></h2>
</div>

<form method="POST" action="<?= BASE_URL ?>admin/podcasts/store" enctype="multipart/form-data">
  <?= $csrf ?>
  
  <div class="row g-4">
    <!-- LEFT COLUMN -->
    <div class="col-lg-8">
      <!-- BASIC DETAILS -->
      <div class="adm-form-card">
        <legend><i class="fas fa-podcast me-2" style="color:var(--red)"></i>Podcast Details</legend>
        <div class="row g-3">
          <div class="col-12">
            <label class="form-lbl">Show *</label>
            <select name="show_id" class="form-inp" required>
              <option value="">— Select Show —</option>
              <?php foreach ($shows as $show): ?>
                <option value="<?= (int)$show['id'] ?>"><?= htmlspecialchars($show['title']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-12">
            <label class="form-lbl">Episode Title *</label>
            <input type="text" name="title" class="form-inp" required
                   value="<?= htmlspecialchars($podcast['title'] ?? '') ?>" 
                   placeholder="e.g. Midnight Frequencies — Episode 48">
          </div>

          <div class="col-12">
            <label class="form-lbl">Description</label>
            <textarea name="description" class="form-inp" rows="4" 
                      placeholder="Episode description"><?= htmlspecialchars($podcast['description'] ?? '') ?></textarea>
          </div>

          <div class="col-md-6">
            <label class="form-lbl">Duration (seconds)</label>
            <input type="number" name="duration" class="form-inp" min="0"
                   value="<?= (int)($podcast['duration'] ?? 0) ?>" 
                   placeholder="e.g. 7200">
          </div>

          <div class="col-md-6">
            <label class="form-lbl">Published Date</label>
            <input type="datetime-local" name="published_at" class="form-inp"
                   value="<?= $podcast ? str_replace(' ', 'T', $podcast['published_at']) : '' ?>">
          </div>
        </div>
      </div>
    </div>

    <!-- RIGHT COLUMN -->
    <div class="col-lg-4">
      <!-- AUDIO FILE -->
      <div class="adm-form-card">
        <legend><i class="fas fa-file-audio me-2" style="color:var(--red)"></i>Audio File *</legend>
        <div class="mb-3">
          <input type="file" name="audio_file" class="form-inp" accept="audio/*" required>
          <small class="form-text text-muted d-block mt-2">
            Supported: MP3, OGG, WAV<br>
            Max size: 10 MB
          </small>
        </div>
        <?php if ($podcast && $podcast['audio_file']): ?>
          <div class="alert alert-info" role="alert">
            <small>Current file: <strong><?= htmlspecialchars($podcast['audio_file']) ?></strong></small>
          </div>
        <?php endif; ?>
      </div>

      <!-- ACTIONS -->
      <div class="adm-form-card">
        <legend><i class="fas fa-save me-2" style="color:var(--red)"></i>Save</legend>
        <div class="d-grid gap-2">
          <button type="submit" class="btn-adm btn-adm-primary">
            <i class="fas fa-upload"></i> Upload Podcast
          </button>
          <a href="<?= BASE_URL ?>admin/podcasts" class="btn-adm btn-adm-secondary">
            <i class="fas fa-times"></i> Cancel
          </a>
        </div>
      </div>
    </div>
  </div>
</form>
