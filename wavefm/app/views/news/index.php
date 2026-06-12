<!-- PAGE HEADER -->
<div class="page-header">
  <div class="container">
    <div class="section-label">Station Updates</div>
    <h1>News &amp; Updates</h1>
    <div class="breadcrumb-row">
      <a href="<?= BASE_URL ?>">Home</a>
      <span class="sep">/</span>
      <span>News</span>
    </div>
  </div>
</div>

<section class="section section-dark">
  <div class="container">
    <div class="row g-5">

      <!-- MAIN COLUMN -->
      <div class="col-lg-8">

        <!-- SEARCH BAR -->
        <form method="GET" action="<?= BASE_URL ?>news" class="mb-4">
          <div class="search-wrap">
            <input type="text"
                   name="q"
                   class="form-inp"
                   placeholder="Search articles…"
                   value="<?= htmlspecialchars($search) ?>"
                   aria-label="Search articles">
            <i class="fas fa-search search-icon"></i>
          </div>
          <?php if ($category_id): ?>
            <input type="hidden" name="cat" value="<?= (int)$category_id ?>">
          <?php endif; ?>
        </form>

        <!-- ACTIVE FILTERS -->
        <?php if ($search || $category_id): ?>
        <div class="mb-4" style="font-family:var(--font-mono);font-size:11px;color:var(--muted)">
          Showing results
          <?= $search ? ' for "<strong style="color:var(--white)">' . htmlspecialchars($search) . '</strong>"' : '' ?>
          &nbsp;·&nbsp; <?= $total ?> article<?= $total !== 1 ? 's' : '' ?> found
          &nbsp;·&nbsp; <a href="<?= BASE_URL ?>news" style="color:var(--red)">Clear filters</a>
        </div>
        <?php endif; ?>

        <!-- ARTICLES GRID -->
        <?php if (empty($articles)): ?>
          <div class="text-center py-5">
            <i class="fas fa-newspaper fa-3x mb-3" style="color:var(--mid)"></i>
            <p style="color:var(--muted)">No articles found.</p>
          </div>
        <?php else: ?>
          <div class="row g-4">
            <?php foreach ($articles as $i => $article): ?>
            <div class="col-md-6 fade-up" style="transition-delay:<?= ($i % 6) * 0.06 ?>s">
              <div class="card-news">
                <div class="card-news-img">
                  <?php if ($article['image']): ?>
                    <img src="<?= UPLOAD_URL . htmlspecialchars($article['image']) ?>"
                         alt="<?= htmlspecialchars($article['title']) ?>" loading="lazy">
                  <?php else: ?>
                    <div style="width:100%;height:200px;background:linear-gradient(135deg,var(--dark3),var(--mid));display:flex;align-items:center;justify-content:center">
                      <i class="fas fa-newspaper fa-2x" style="color:var(--mid)"></i>
                    </div>
                  <?php endif; ?>
                  <span class="card-cat" style="background:<?= htmlspecialchars($article['category_color']) ?>">
                    <?= htmlspecialchars($article['category_name']) ?>
                  </span>
                </div>
                <div class="card-body">
                  <div class="card-date">
                    <i class="far fa-calendar me-1"></i>
                    <?= date('M j, Y', strtotime($article['published_at'])) ?>
                    &nbsp;·&nbsp; <?= htmlspecialchars($article['author_name']) ?>
                  </div>
                  <div class="card-title"><?= htmlspecialchars($article['title']) ?></div>
                  <p class="card-excerpt"><?= htmlspecialchars(mb_strimwidth($article['excerpt'], 0, 100, '…')) ?></p>
                </div>
                <div class="card-footer-row">
                  <a href="<?= BASE_URL ?>news/<?= htmlspecialchars($article['slug']) ?>" class="card-read-more">
                    Read More <i class="fas fa-arrow-right"></i>
                  </a>
                  <span class="card-meta"><i class="far fa-eye me-1"></i><?= number_format($article['views']) ?></span>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>

          <!-- PAGINATION -->
          <?php
          $totalPages = ceil($total / $per_page);
          if ($totalPages > 1):
            $qs = http_build_query(array_filter(['q'=>$search,'cat'=>$category_id]));
            $base = BASE_URL . 'news?' . ($qs ? $qs . '&' : '');
          ?>
          <div class="pagination-wrap">
            <a href="<?= $base ?>page=<?= max(1,$page-1) ?>" class="pg-btn<?= $page<=1?' disabled':'' ?>">
              <i class="fas fa-chevron-left"></i>
            </a>
            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
              <?php if ($p===1||$p===$totalPages||abs($p-$page)<=2): ?>
                <a href="<?= $base ?>page=<?= $p ?>" class="pg-btn<?= $p===$page?' active':'' ?>"><?= $p ?></a>
              <?php elseif (abs($p-$page)===3): ?>
                <span class="pg-btn" style="pointer-events:none">…</span>
              <?php endif; ?>
            <?php endfor; ?>
            <a href="<?= $base ?>page=<?= min($totalPages,$page+1) ?>" class="pg-btn<?= $page>=$totalPages?' disabled':'' ?>">
              <i class="fas fa-chevron-right"></i>
            </a>
          </div>
          <?php endif; ?>
        <?php endif; ?>
      </div>

      <!-- SIDEBAR -->
      <div class="col-lg-4">

        <!-- CATEGORIES -->
        <div class="sidebar-card">
          <div class="sidebar-card-head"><i class="fas fa-tags me-2" style="color:var(--red)"></i>Categories</div>
          <div class="sidebar-card-body">
            <a href="<?= BASE_URL ?>news" class="tag<?= !$category_id?' active':'' ?>">All</a>
            <?php foreach ($categories as $cat): ?>
            <a href="<?= BASE_URL ?>news?cat=<?= (int)$cat['id'] ?>" class="tag<?= $category_id==$cat['id']?' active':'' ?>">
              <?= htmlspecialchars($cat['name']) ?>
            </a>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- ARCHIVES -->
        <?php if (!empty($archives)): ?>
        <div class="sidebar-card">
          <div class="sidebar-card-head"><i class="fas fa-archive me-2" style="color:var(--red)"></i>Archives</div>
          <div class="sidebar-card-body" style="padding:0">
            <?php foreach ($archives as $arc): ?>
            <a href="<?= BASE_URL ?>news?arc=<?= htmlspecialchars($arc['ym']) ?>"
               style="display:flex;align-items:center;justify-content:space-between;padding:10px 18px;border-bottom:1px solid rgba(255,255,255,0.04);font-size:13px;color:var(--light);transition:color 0.2s">
              <?= htmlspecialchars($arc['label']) ?>
              <span style="font-family:var(--font-mono);font-size:10px;color:var(--muted)"><?= (int)$arc['cnt'] ?></span>
            </a>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

      </div><!-- /sidebar -->
    </div>
  </div>
</section>
