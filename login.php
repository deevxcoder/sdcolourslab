<?php
$pageTitle = 'Login – SD Colours Photobook Lab';
require_once 'includes/auth.php';
startSession();

if (isLoggedIn()) {
    header('Location: ' . (isAdmin() ? '/admin/index.php' : '/photographer/index.php'));
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = loginUser(trim($_POST['email'] ?? ''), $_POST['password'] ?? '');
    if (isset($result['success'])) {
        header('Location: ' . ($result['role'] === 'admin' ? '/admin/index.php' : '/photographer/index.php'));
        exit;
    }
    $error = $result['error'];
}

require_once 'includes/header.php';
?>
<style>
.auth-wrap { min-height: 80vh; display: flex; align-items: center; justify-content: center; padding: 2rem; background: #f8f9fa; }
.auth-card { background: #fff; border-radius: 16px; box-shadow: 0 4px 30px rgba(0,0,0,.08); padding: 2.5rem; width: 100%; max-width: 420px; }
.auth-card h1 { font-size: 1.7rem; font-weight: 800; margin-bottom: .25rem; }
.auth-card p { color: #6b7280; margin-bottom: 1.75rem; }
.form-group { margin-bottom: 1.25rem; }
.form-group label { display: block; font-weight: 600; font-size: .875rem; margin-bottom: .4rem; color: #374151; }
.form-group input, .form-group select, .form-group textarea { width: 100%; padding: .65rem .9rem; border: 1.5px solid #e5e7eb; border-radius: 8px; font-size: .95rem; transition: border-color .2s; box-sizing: border-box; }
.form-group input:focus, .form-group select:focus { outline: none; border-color: var(--primary); }
.btn-submit { width: 100%; background: var(--primary); color: #fff; padding: .8rem; border: none; border-radius: 8px; font-size: 1rem; font-weight: 700; cursor: pointer; }
.btn-submit:hover { opacity: .9; }
.alert-error { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: .75rem 1rem; border-radius: 8px; margin-bottom: 1.25rem; font-size: .9rem; }
.auth-link { text-align: center; margin-top: 1.25rem; font-size: .875rem; color: #6b7280; }
.auth-link a { color: var(--primary); font-weight: 600; text-decoration: none; }
</style>

<div class="auth-wrap">
  <div class="auth-card">
    <h1>Welcome Back</h1>
    <p>Login to your SD Colours account</p>
    <?php if ($error): ?><div class="alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="POST">
      <div class="form-group">
        <label>Email Address</label>
        <input type="email" name="email" required placeholder="you@example.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required placeholder="••••••••">
      </div>
      <button type="submit" class="btn-submit">Sign In</button>
    </form>
    <div class="auth-link">Don't have an account? <a href="/register.php">Register as Photographer</a></div>
    <div class="auth-link" style="margin-top:.5rem;">
      <small style="color:#9ca3af;">Admin: admin@sdcolours.com / admin123</small>
    </div>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
