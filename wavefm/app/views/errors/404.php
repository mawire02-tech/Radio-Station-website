<!-- 404.php -->
<div style="min-height:70vh;display:flex;align-items:center;justify-content:center;text-align:center;padding:40px 20px">
  <div>
    <div style="font-family:var(--font-display);font-size:clamp(6rem,15vw,12rem);line-height:1;color:var(--dark3);letter-spacing:-0.02em">404</div>
    <div style="font-family:var(--font-display);font-size:clamp(1.5rem,4vw,3rem);color:var(--white);margin-bottom:16px">Page Not Found</div>
    <p style="color:var(--muted);max-width:400px;margin:0 auto 32px;font-size:15px">
      The page you're looking for doesn't exist or may have been moved.
    </p>
    <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap">
      <a href="<?= BASE_URL ?>" class="btn-red"><i class="fas fa-home"></i> Go Home</a>
      <a href="<?= BASE_URL ?>news" class="btn-outline">Browse News</a>
    </div>
  </div>
</div>
