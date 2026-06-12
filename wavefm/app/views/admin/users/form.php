<div class="adm-section-head">
  <h2><?= $user ? 'Edit User' : 'Add User' ?></h2>
  <a href="<?= BASE_URL ?>admin/users" class="btn-adm btn-adm-secondary">
    <i class="fas fa-arrow-left"></i> Back
  </a>
</div>

<form method="POST" action="<?= BASE_URL ?>admin/users/<?= $user ? 'update/'.(int)$user['id'] : 'store' ?>" novalidate>
  <?= $csrf ?>
  <div class="adm-form-card" style="max-width:600px">
    <legend><?= $user ? 'Edit Account' : 'Create New Admin Account' ?></legend>
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-lbl">Full Name *</label>
        <input type="text" name="full_name" class="form-inp" required maxlength="120"
               value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" placeholder="Jane Smith">
      </div>
      <div class="col-md-6">
        <label class="form-lbl">Username *</label>
        <input type="text" name="username" class="form-inp" required maxlength="60"
               value="<?= htmlspecialchars($user['username'] ?? '') ?>" placeholder="janesmith"
               autocomplete="off">
      </div>
      <div class="col-12">
        <label class="form-lbl">Email Address *</label>
        <input type="email" name="email" class="form-inp" required maxlength="120"
               value="<?= htmlspecialchars($user['email'] ?? '') ?>" placeholder="jane@wavefm.local">
      </div>
      <div class="col-md-6">
        <label class="form-lbl">
          <?= $user ? 'New Password' : 'Password *' ?>
          <span style="color:var(--muted);font-size:9px"><?= $user ? '(leave blank to keep)' : '' ?></span>
        </label>
        <input type="password" name="password" class="form-inp"
               <?= $user ? '' : 'required' ?> minlength="8"
               placeholder="Min 8 chars, upper/lower/number/special"
               autocomplete="new-password">
      </div>
      <div class="col-md-6">
        <label class="form-lbl">Role *</label>
        <select name="role" class="form-inp" required>
          <option value="editor"    <?= ($user['role']??'editor')==='editor'    ? 'selected':'' ?>>Editor</option>
          <option value="admin"     <?= ($user['role']??'')==='admin'           ? 'selected':'' ?>>Admin</option>
          <?php if ($_SESSION['admin_role'] === 'superadmin'): ?>
          <option value="superadmin"<?= ($user['role']??'')==='superadmin'      ? 'selected':'' ?>>Super Admin</option>
          <?php endif; ?>
        </select>
        <span style="font-family:var(--font-mono);font-size:10px;color:var(--muted);display:block;margin-top:4px">
          Editor: news only &nbsp;·&nbsp; Admin: all content &nbsp;·&nbsp; Super Admin: full access
        </span>
      </div>

      <?php if ($user): ?>
      <div class="col-12">
        <label style="display:flex;align-items:center;gap:10px;cursor:pointer">
          <input type="checkbox" name="is_active" value="1"
                 <?= ($user['is_active'] ?? 1) ? 'checked' : '' ?>
                 style="width:16px;height:16px;accent-color:var(--red)">
          <span class="form-lbl" style="margin:0">Account Active</span>
        </label>
      </div>
      <?php endif; ?>

      <div class="col-12 mt-2 d-flex gap-3 flex-wrap">
        <button type="submit" class="btn-adm btn-adm-primary">
          <i class="fas fa-save"></i> <?= $user ? 'Update User' : 'Create User' ?>
        </button>
        <a href="<?= BASE_URL ?>admin/users" class="btn-adm btn-adm-secondary">Cancel</a>
      </div>
    </div>
  </div>
</form>
