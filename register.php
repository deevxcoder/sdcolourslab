<?php
$pageTitle = 'Register – SD Colours Photobook Lab';
require_once 'includes/auth.php';
require_once 'includes/db.php';
startSession();

if (isLoggedIn()) {
    header('Location: ' . (isAdmin() ? '/admin/index.php' : '/photographer/index.php'));
    exit;
}

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $studio = trim($_POST['studio_name'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (!$name || !$email || !$password) {
        $error = 'Please fill in all required fields.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        try {
            $db = getDB();
            $check = $db->prepare('SELECT id FROM users WHERE email = ?');
            $check->execute([$email]);
            if ($check->fetch()) {
                $error = 'An account with this email already exists.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare('INSERT INTO users (name, email, password_hash, phone, studio_name, city, role, status) VALUES (?, ?, ?, ?, ?, ?, \'photographer\', \'pending\')');
                $stmt->execute([$name, $email, $hash, $phone, $studio, $city]);
                $success = 'Registration successful! Your account is pending admin approval. You will be able to login once approved.';
            }
        } catch (Exception $e) {
            $error = 'Registration failed. Please try again.';
        }
    }
}

require_once 'includes/header.php';
?>
<style>
.auth-wrap { min-height: 80vh; display: flex; align-items: center; justify-content: center; padding: 2rem; background: #f8f9fa; }
.auth-card { background: #fff; border-radius: 16px; box-shadow: 0 4px 30px rgba(0,0,0,.08); padding: 2.5rem; width: 100%; max-width: 500px; }
.auth-card h1 { font-size: 1.7rem; font-weight: 800; margin-bottom: .25rem; }
.auth-card > p { color: #6b7280; margin-bottom: 1.75rem; }
.form-group { margin-bottom: 1.1rem; }
.form-group label { display: block; font-weight: 600; font-size: .875rem; margin-bottom: .4rem; color: #374151; }
.form-group input { width: 100%; padding: .65rem .9rem; border: 1.5px solid #e5e7eb; border-radius: 8px; font-size: .95rem; box-sizing: border-box; }
.form-group input:focus { outline: none; border-color: var(--primary); }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.btn-submit { width: 100%; background: var(--primary); color: #fff; padding: .8rem; border: none; border-radius: 8px; font-size: 1rem; font-weight: 700; cursor: pointer; margin-top: .5rem; }
.alert-error { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: .75rem 1rem; border-radius: 8px; margin-bottom: 1.25rem; font-size: .9rem; }
.alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #16a34a; padding: .75rem 1rem; border-radius: 8px; margin-bottom: 1.25rem; font-size: .9rem; }
.auth-link { text-align: center; margin-top: 1.25rem; font-size: .875rem; color: #6b7280; }
.auth-link a { color: var(--primary); font-weight: 600; text-decoration: none; }
@media(max-width:500px){.form-row{grid-template-columns:1fr;}}
</style>
<div class="auth-wrap">
  <div class="auth-card">
    <h1>Photographer Registration</h1>
    <p>Create your account and start ordering prints online</p>
    <?php if ($error): ?><div class="alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($success): ?>
      <div class="alert-success"><?= htmlspecialchars($success) ?></div>
      <div class="auth-link"><a href="/login.php">← Back to Login</a></div>
    <?php else: ?>
    <form method="POST">
      <div class="form-group">
        <label>Full Name *</label>
        <input type="text" name="name" required placeholder="Your full name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Email Address *</label>
        <input type="email" name="email" required placeholder="you@example.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Phone Number</label>
          <input type="tel" name="phone" placeholder="+91 XXXXX XXXXX" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>City</label>
          <input type="text" name="city" placeholder="Your city" value="<?= htmlspecialchars($_POST['city'] ?? '') ?>">
        </div>
      </div>
      <div class="form-group">
        <label>Studio / Business Name</label>
        <input type="text" name="studio_name" placeholder="Your photography studio name" value="<?= htmlspecialchars($_POST['studio_name'] ?? '') ?>">
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Password *</label>
          <input type="password" name="password" required placeholder="Min. 6 characters">
        </div>
        <div class="form-group">
          <label>Confirm Password *</label>
          <input type="password" name="confirm_password" required placeholder="Repeat password">
        </div>
      </div>
      <button type="submit" class="btn-submit">Create Account</button>
    </form>
    <div class="auth-link">Already have an account? <a href="/login.php">Sign In</a></div>
    <?php endif; ?>
  </div>
</div>
<?php require_once 'includes/footer.php'; ?>
