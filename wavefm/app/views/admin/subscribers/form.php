<div class="adm-section-head">
  <h2><?= htmlspecialchars($page_title) ?></h2>
  <a href="<?= BASE_URL ?>admin/subscribers" class="btn-adm btn-adm-secondary">
    <i class="fas fa-arrow-left"></i> Back
  </a>
</div>

<form method="POST" action="<?= BASE_URL ?>admin/subscribers/<?= $subscriber ? 'update/' . (int)$subscriber['id'] : 'store' ?>">
  <?= $csrf ?>
  
  <div class="adm-form-card" style="max-width:640px">
    <legend><?= $subscriber ? 'Edit Subscriber' : 'Add Subscriber' ?></legend>
    
    <div class="row g-3">
      <div class="col-12">
        <label class="form-lbl">Email Address *</label>
        <input type="email" name="email" class="form-inp" required
               value="<?= htmlspecialchars($subscriber['email'] ?? '') ?>"
               <?= $subscriber ? 'readonly style="background:var(--bg-secondary);cursor:not-allowed"' : '' ?>
               placeholder="subscriber@example.com">
        <small class="form-text text-muted" style="display:block;margin-top:4px">
          The email address to receive the weekly programme guide
        </small>
      </div>

      <div class="col-12">
        <label class="form-lbl">Name (Optional)</label>
        <input type="text" name="name" class="form-inp" maxlength="120"
               value="<?= htmlspecialchars($subscriber['name'] ?? '') ?>"
               placeholder="e.g. John Doe">
      </div>

      <?php if ($subscriber): ?>
      <div class="col-12">
        <label style="display:flex;align-items:center;gap:10px;cursor:pointer">
          <input type="checkbox" name="is_active" value="1"
                 <?= $subscriber['is_active'] ? 'checked' : '' ?>
                 style="width:16px;height:16px;accent-color:var(--red)">
          <span class="form-lbl" style="margin:0">Active (receiving emails)</span>
        </label>
      </div>

      <div class="col-12">
        <div style="font-size:12px;color:var(--muted);margin-top:8px">
          <strong>Subscribed:</strong> <?= date('d M Y \a\t H:i', strtotime($subscriber['subscribed_at'])) ?><br>
          <?php if ($subscriber['unsubscribed_at']): ?>
            <strong>Unsubscribed:</strong> <?= date('d M Y \a\t H:i', strtotime($subscriber['unsubscribed_at'])) ?><br>
          <?php endif; ?>
        </div>
      </div>
      <?php endif; ?>

      <div class="col-12 d-flex gap-3">
        <button type="submit" class="btn-adm btn-adm-primary">
          <i class="fas fa-save"></i> Save
        </button>
        <a href="<?= BASE_URL ?>admin/subscribers" class="btn-adm btn-adm-secondary">Cancel</a>
      </div>
    </div>
  </div>
</form>
