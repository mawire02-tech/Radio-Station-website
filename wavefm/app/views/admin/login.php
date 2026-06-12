<?php if (!empty($flash)): ?>
<div class="flash-msg <?= htmlspecialchars($flash['type']) ?>" style="margin-bottom:20px">
  <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($flash['message']) ?>
</div>
<?php endif; ?>

<?php if (isset($_GET['timeout'])): ?>
<div class="flash-msg error" style="margin-bottom:20px">
  <i class="fas fa-clock"></i> Your session expired. Please log in again.
</div>
<?php endif; ?>

<div class="login-card">
  <div class="login-logo"><?= htmlspecialchars($siteName) ?> <span>FM</span></div>
  <div class="login-sub">Admin Control Panel</div>

  <form method="POST" action="<?= BASE_URL ?>admin/login" novalidate>
    <?= $csrf ?>
    <div class="form-group">
      <label class="form-lbl">Username</label>
      <input type="text" name="username" class="form-inp" placeholder="admin" autocomplete="username" required autofocus>
    </div>
    <div class="form-group" style="margin-top:16px">
      <label class="form-lbl">Password</label>
      <div style="position:relative">
        <input type="password" name="password" id="pwdInput" class="form-inp" placeholder="••••••••" autocomplete="current-password" required style="padding-right:44px">
        <button type="button" onclick="togglePwd()" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--muted);cursor:pointer;font-size:14px" id="pwdEye">
          <i class="fas fa-eye"></i>
        </button>
      </div>
    </div>
    <div style="margin-top:24px">
      <button type="submit" class="btn-red w-100 justify-content-center" style="padding:13px">
        <i class="fas fa-lock"></i> Sign In
      </button>
    </div>
  </form>

  <div style="text-align:center;margin-top:24px">
    <a href="<?= BASE_URL ?>" style="font-family:var(--font-mono);font-size:10px;letter-spacing:0.12em;text-transform:uppercase;color:var(--muted)">
      <i class="fas fa-arrow-left me-1"></i> Back to Site
    </a>
  </div>
</div>

<script>
function togglePwd() {
  const inp = document.getElementById('pwdInput');
  const eye = document.getElementById('pwdEye').querySelector('i');
  if (inp.type === 'password') { inp.type = 'text'; eye.className = 'fas fa-eye-slash'; }
  else { inp.type = 'password'; eye.className = 'fas fa-eye'; }
}
</script>
