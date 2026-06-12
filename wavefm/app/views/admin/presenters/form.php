<div class="adm-section-head">
  <h2><?= $presenter ? 'Edit Presenter' : 'Add Presenter' ?></h2>
  <a href="<?= BASE_URL ?>admin/presenters" class="btn-adm btn-adm-secondary"><i class="fas fa-arrow-left"></i> Back</a>
</div>
<form method="POST" action="<?= BASE_URL ?>admin/presenters/<?= $presenter?'update/'.(int)$presenter['id']:'store' ?>" enctype="multipart/form-data">
  <?= $csrf ?>
  <div class="row g-4">
    <div class="col-lg-8">
      <div class="adm-form-card">
        <legend>Presenter Details</legend>
        <div class="row g-3">
          <div class="col-md-6"><label class="form-lbl">Full Name *</label><input type="text" name="name" class="form-inp" required maxlength="120" value="<?= htmlspecialchars($presenter['name']??'') ?>"></div>
          <div class="col-md-6"><label class="form-lbl">Role / Title</label><input type="text" name="role" class="form-inp" maxlength="80" value="<?= htmlspecialchars($presenter['role']??'Presenter') ?>"></div>
          <div class="col-12"><label class="form-lbl">Bio *</label><textarea name="bio" class="form-inp" required rows="5"><?= htmlspecialchars($presenter['bio']??'') ?></textarea></div>
          <div class="col-md-6"><label class="form-lbl">Email</label><input type="email" name="email" class="form-inp" value="<?= htmlspecialchars($presenter['email']??'') ?>"></div>
          <div class="col-md-6"><label class="form-lbl">Display Order</label><input type="number" name="sort_order" class="form-inp" min="0" value="<?= (int)($presenter['sort_order']??0) ?>"></div>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="adm-form-card mb-4">
        <legend>Social Links</legend>
        <div class="form-group"><label class="form-lbl"><i class="fab fa-x-twitter me-1"></i> Twitter/X</label><input type="text" name="twitter" class="form-inp" placeholder="@handle" maxlength="80" value="<?= htmlspecialchars($presenter['twitter']??'') ?>"></div>
        <div class="form-group mt-3"><label class="form-lbl"><i class="fab fa-instagram me-1"></i> Instagram</label><input type="text" name="instagram" class="form-inp" placeholder="@handle" maxlength="80" value="<?= htmlspecialchars($presenter['instagram']??'') ?>"></div>
        <div class="form-group mt-3"><label class="form-lbl"><i class="fab fa-facebook me-1"></i> Facebook URL</label><input type="text" name="facebook" class="form-inp" placeholder="https://…" maxlength="200" value="<?= htmlspecialchars($presenter['facebook']??'') ?>"></div>
      </div>
      <div class="adm-form-card">
        <legend>Photo</legend>
        <?php if (!empty($presenter['photo'])): ?><img src="<?= UPLOAD_URL.htmlspecialchars($presenter['photo']) ?>" style="max-width:100%;border-radius:3px;margin-bottom:12px;max-height:150px;object-fit:cover"><?php endif; ?>
        <input type="file" name="photo" class="form-inp" accept="image/jpeg,image/png,image/webp" style="padding:8px">
        <?php if ($presenter): ?>
        <div class="form-group mt-3"><label style="display:flex;align-items:center;gap:10px;cursor:pointer"><input type="checkbox" name="is_active" value="1" <?= ($presenter['is_active']??1)?'checked':'' ?> style="width:16px;height:16px;accent-color:var(--red)"><span class="form-lbl" style="margin:0">Active</span></label></div>
        <?php endif; ?>
        <div class="mt-4 d-flex gap-3">
          <button type="submit" class="btn-adm btn-adm-primary"><i class="fas fa-save"></i> Save</button>
          <a href="<?= BASE_URL ?>admin/presenters" class="btn-adm btn-adm-secondary">Cancel</a>
        </div>
      </div>
    </div>
  </div>
</form>
