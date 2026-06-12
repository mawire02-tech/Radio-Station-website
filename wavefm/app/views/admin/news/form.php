<!-- admin/news/form.php -->
<div class="adm-section-head">
  <h2><?= $article ? 'Edit Article' : 'New Article' ?></h2>
  <a href="<?= BASE_URL ?>admin/news" class="btn-adm btn-adm-secondary">
    <i class="fas fa-arrow-left"></i> Back
  </a>
</div>

<form method="POST" action="<?= BASE_URL ?>admin/news/<?= $article ? 'update/'.(int)$article['id'] : 'store' ?>" enctype="multipart/form-data" novalidate>
  <?= $csrf ?>
  <div class="row g-4">
    <div class="col-lg-8">
      <div class="adm-form-card">
        <div class="form-group">
          <label class="form-lbl">Title <span style="color:var(--red)">*</span></label>
          <input type="text" name="title" class="form-inp" required maxlength="255"
                 value="<?= htmlspecialchars($article['title'] ?? '') ?>" placeholder="Article title">
        </div>

        <div class="form-group mt-3">
          <label class="form-lbl">Excerpt / Summary <span style="color:var(--red)">*</span></label>
          <textarea name="excerpt" class="form-inp" required rows="3" maxlength="500"
                    placeholder="Brief summary shown in listings…"><?= htmlspecialchars($article['excerpt'] ?? '') ?></textarea>
        </div>

        <div class="form-group mt-3">
          <label class="form-lbl">Body <span style="color:var(--red)">*</span></label>
          <textarea name="body" id="bodyEditor" class="form-inp" required rows="14"
                    placeholder="Full article content (HTML supported)…"><?= htmlspecialchars($article['body'] ?? '') ?></textarea>
          <span style="font-family:var(--font-mono);font-size:10px;color:var(--muted);display:block;margin-top:6px">
            HTML tags are allowed: &lt;p&gt; &lt;h2&gt; &lt;h3&gt; &lt;ul&gt; &lt;ol&gt; &lt;li&gt; &lt;blockquote&gt; &lt;strong&gt; &lt;em&gt; &lt;a&gt;
          </span>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="adm-form-card mb-4">
        <legend>Publish Settings</legend>

        <div class="form-group">
          <label class="form-lbl">Status</label>
          <select name="status" class="form-inp">
            <?php foreach (['draft','published','archived'] as $s): ?>
            <option value="<?= $s ?>"<?= (($article['status']??'draft')===$s)?' selected':'' ?>><?= ucfirst($s) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group mt-3">
          <label class="form-lbl">Category <span style="color:var(--red)">*</span></label>
          <select name="category_id" class="form-inp" required>
            <option value="">Select category…</option>
            <?php foreach ($categories as $cat): ?>
            <option value="<?= (int)$cat['id'] ?>"<?= (($article['category_id']??0)==$cat['id'])?' selected':'' ?>>
              <?= htmlspecialchars($cat['name']) ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group mt-3">
          <label style="display:flex;align-items:center;gap:10px;cursor:pointer">
            <input type="checkbox" name="featured" value="1"
                   <?= ($article['featured']??0) ? 'checked' : '' ?>
                   style="width:16px;height:16px;accent-color:var(--red)">
            <span class="form-lbl" style="margin:0">Featured Article</span>
          </label>
        </div>

        <div class="form-group mt-3">
          <label class="form-lbl">Cover Image</label>
          <?php if (!empty($article['image'])): ?>
            <div style="margin-bottom:8px">
              <img src="<?= UPLOAD_URL.htmlspecialchars($article['image']) ?>" style="max-width:100%;border-radius:3px;max-height:120px;object-fit:cover">
            </div>
          <?php endif; ?>
          <input type="file" name="image" class="form-inp" accept="image/jpeg,image/png,image/webp,image/gif" style="padding:8px">
          <span style="font-family:var(--font-mono);font-size:10px;color:var(--muted);display:block;margin-top:4px">Max 10MB. JPEG/PNG/WEBP/GIF</span>
        </div>

        <div class="mt-4 d-flex gap-3 flex-wrap">
          <button type="submit" class="btn-adm btn-adm-primary">
            <i class="fas fa-save"></i> <?= $article ? 'Update Article' : 'Publish' ?>
          </button>
          <a href="<?= BASE_URL ?>admin/news" class="btn-adm btn-adm-secondary">Cancel</a>
        </div>
      </div>
    </div>
  </div>
</form>
