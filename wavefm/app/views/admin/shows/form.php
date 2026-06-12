<div class="adm-section-head">
  <h2><?= $show ? 'Edit Show' : 'Add Show' ?></h2>
  <a href="<?= BASE_URL ?>admin/shows" class="btn-adm btn-adm-secondary">
    <i class="fas fa-arrow-left"></i> Back
  </a>
</div>

<form method="POST" action="<?= BASE_URL ?>admin/shows/<?= $show ? 'update/' . (int)$show['id'] : 'store' ?>">
  <?= $csrf ?>
  <div class="adm-form-card" style="max-width:640px">
    <legend><?= $show ? 'Edit Show Details' : 'New Show' ?></legend>
    <div class="row g-3">

      <div class="col-12">
        <label class="form-lbl">Show Title *</label>
        <input type="text" name="title" class="form-inp" required maxlength="120"
               value="<?= htmlspecialchars($show['title'] ?? '') ?>">
      </div>

      <div class="col-md-6">
        <label class="form-lbl">Presenter *</label>
        <select name="presenter_id" class="form-inp" required>
          <option value="">Select presenter…</option>
          <?php foreach ($presenters as $p): ?>
          <option value="<?= (int)$p['id'] ?>"
                  <?= ($show['presenter_id'] ?? 0) == $p['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($p['name']) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-md-6">
        <label class="form-lbl">Genre *</label>
        <select name="genre_id" class="form-inp" required>
          <option value="">Select genre…</option>
          <?php foreach ($genres as $g): ?>
          <option value="<?= (int)$g['id'] ?>"
                  <?= ($show['genre_id'] ?? 0) == $g['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($g['name']) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-12">
        <label class="form-lbl">Description *</label>
        <textarea name="description" class="form-inp" required rows="5"><?= htmlspecialchars($show['description'] ?? '') ?></textarea>
      </div>

      <?php if ($show): ?>
      <div class="col-12">
        <label style="display:flex;align-items:center;gap:10px;cursor:pointer">
          <input type="checkbox" name="is_active" value="1"
                 <?= $show['is_active'] ? 'checked' : '' ?>
                 style="width:16px;height:16px;accent-color:var(--red)">
          <span class="form-lbl" style="margin:0">Active</span>
        </label>
      </div>
      <?php endif; ?>

      <div class="col-12 d-flex gap-3">
        <button type="submit" class="btn-adm btn-adm-primary">
          <i class="fas fa-save"></i> Save
        </button>
        <a href="<?= BASE_URL ?>admin/shows" class="btn-adm btn-adm-secondary">Cancel</a>
      </div>

    </div>
  </div>
</form>
