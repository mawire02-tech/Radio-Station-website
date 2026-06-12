<!-- PAGE HEADER -->
<div class="page-header">
  <div class="container">
    <span class="card-cat d-inline-block mb-3" style="background:<?= htmlspecialchars($article['category_color']) ?>;position:static">
      <?= htmlspecialchars($article['category_name']) ?>
    </span>
    <h1 style="font-size:clamp(2rem,4vw,3.5rem);max-width:800px;line-height:1.05">
      <?= htmlspecialchars($article['title']) ?>
    </h1>
    <div class="breadcrumb-row mt-3">
      <a href="<?= BASE_URL ?>">Home</a><span class="sep">/</span>
      <a href="<?= BASE_URL ?>news">News</a><span class="sep">/</span>
      <span><?= htmlspecialchars(mb_strimwidth($article['title'],0,40,'…')) ?></span>
    </div>
    <div class="d-flex align-items-center gap-4 mt-3" style="font-family:var(--font-mono);font-size:11px;color:var(--muted)">
      <span><i class="fas fa-user me-1"></i><?= htmlspecialchars($article['author_name']) ?></span>
      <span><i class="far fa-calendar me-1"></i><?= date('F j, Y', strtotime($article['published_at'])) ?></span>
      <span><i class="far fa-eye me-1"></i><?= number_format($article['views']) ?> views</span>
    </div>
  </div>
</div>

<section class="section section-dark">
  <div class="container">
    <div class="row g-5">

      <!-- ARTICLE BODY -->
      <div class="col-lg-8">

        <?php if ($article['image']): ?>
        <div class="mb-4" style="border-radius:3px;overflow:hidden">
          <img src="<?= UPLOAD_URL . htmlspecialchars($article['image']) ?>"
               alt="<?= htmlspecialchars($article['title']) ?>"
               style="width:100%;max-height:420px;object-fit:cover" loading="lazy">
        </div>
        <?php endif; ?>

        <div class="article-body" style="color:var(--light);font-size:15px;line-height:1.85">
          <?= $article['body'] /* HTML from editor — sanitise on input, display raw */ ?>
        </div>

        <!-- SHARE -->
        <div class="mt-5 pt-4" style="border-top:1px solid rgba(255,255,255,0.05)">
          <div style="font-family:var(--font-mono);font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);margin-bottom:14px">Share this article</div>
          <div class="d-flex gap-3 flex-wrap">
            <a href="https://twitter.com/intent/tweet?url=<?= urlencode(BASE_URL.'news/'.$article['slug']) ?>&text=<?= urlencode($article['title']) ?>"
               target="_blank" rel="noopener" class="btn-outline" style="font-size:10px;padding:8px 16px">
              <i class="fab fa-x-twitter"></i> Share on X
            </a>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(BASE_URL.'news/'.$article['slug']) ?>"
               target="_blank" rel="noopener" class="btn-outline" style="font-size:10px;padding:8px 16px">
              <i class="fab fa-facebook-f"></i> Share
            </a>
            <button class="btn-outline" style="font-size:10px;padding:8px 16px"
                    onclick="navigator.clipboard.writeText('<?= BASE_URL.'news/'.htmlspecialchars($article['slug']) ?>').then(()=>showToast('Link copied!','success'))">
              <i class="fas fa-link"></i> Copy Link
            </button>
          </div>
        </div>

        <!-- RELATED ARTICLES -->
        <?php if (!empty($related)): ?>
        <div class="mt-5">
          <div class="section-label">More to Read</div>
          <h3 style="font-size:1.6rem;margin-bottom:24px">Related Articles</h3>
          <div class="row g-3">
            <?php foreach ($related as $rel): if ($rel['id'] == $article['id']) continue; ?>
            <div class="col-md-4">
              <div class="card-news" style="transform:none">
                <div class="card-news-img">
                  <?php if ($rel['image']): ?>
                    <img src="<?= UPLOAD_URL . htmlspecialchars($rel['image']) ?>"
                         alt="<?= htmlspecialchars($rel['title']) ?>" loading="lazy" style="height:140px">
                  <?php else: ?>
                    <div style="width:100%;height:140px;background:var(--dark3);display:flex;align-items:center;justify-content:center"><i class="fas fa-newspaper" style="color:var(--mid)"></i></div>
                  <?php endif; ?>
                </div>
                <div class="card-body" style="padding:16px">
                  <div class="card-date" style="font-size:9px"><?= date('M j, Y', strtotime($rel['published_at'])) ?></div>
                  <div class="card-title" style="font-size:1rem"><?= htmlspecialchars($rel['title']) ?></div>
                </div>
                <div class="card-footer-row">
                  <a href="<?= BASE_URL ?>news/<?= htmlspecialchars($rel['slug']) ?>" class="card-read-more" style="font-size:9px">
                    Read <i class="fas fa-arrow-right"></i>
                  </a>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

      </div>

      <!-- SIDEBAR -->
      <div class="col-lg-4">
        <div class="sidebar-card mb-4">
          <div class="sidebar-card-head"><i class="fas fa-info-circle me-2" style="color:var(--red)"></i>Article Info</div>
          <div class="sidebar-card-body">
            <table style="font-size:13px;width:100%;color:var(--muted)">
              <tr><td style="padding:5px 0">Author</td><td style="color:var(--light)"><?= htmlspecialchars($article['author_name']) ?></td></tr>
              <tr><td style="padding:5px 0">Published</td><td style="color:var(--light)"><?= date('d M Y', strtotime($article['published_at'])) ?></td></tr>
              <tr><td style="padding:5px 0">Category</td><td style="color:var(--light)"><?= htmlspecialchars($article['category_name']) ?></td></tr>
              <tr><td style="padding:5px 0">Views</td><td style="color:var(--light)"><?= number_format($article['views']) ?></td></tr>
            </table>
          </div>
        </div>

        <div class="sidebar-card">
          <div class="sidebar-card-head"><i class="fas fa-tags me-2" style="color:var(--red)"></i>Browse Categories</div>
          <div class="sidebar-card-body">
            <?php foreach ($categories as $cat): ?>
            <a href="<?= BASE_URL ?>news?cat=<?= (int)$cat['id'] ?>" class="tag">
              <?= htmlspecialchars($cat['name']) ?>
            </a>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<style>
.article-body h2,.article-body h3,.article-body h4 { font-family:var(--font-display);letter-spacing:0.03em;color:var(--white);margin:2em 0 0.6em; }
.article-body p { margin-bottom:1.4em; }
.article-body a { color:var(--red); }
.article-body ul,.article-body ol { padding-left:1.6em;margin-bottom:1.4em; }
.article-body li { margin-bottom:0.5em; }
.article-body blockquote { border-left:3px solid var(--red);padding:16px 20px;background:var(--dark2);margin:2em 0;font-style:italic;border-radius:0 3px 3px 0; }
.article-body img { max-width:100%;border-radius:3px;margin:1em 0; }
.article-body strong { color:var(--white); }
</style>
