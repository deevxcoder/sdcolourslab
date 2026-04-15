<?php
$pageTitle = 'Manage Photographers – Admin';
require_once '../includes/auth.php';
requireAdmin();
require_once '../includes/db.php';

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $userId = (int)$_POST['user_id'];
    $status = in_array($_POST['status'], ['pending','approved','rejected']) ? $_POST['status'] : 'pending';
    $stmt = $db->prepare("UPDATE users SET status=? WHERE id=? AND role='photographer'");
    $stmt->execute([$status, $userId]);
    header('Location: /admin/photographers.php?updated=1');
    exit;
}

$filter = $_GET['status'] ?? 'all';
$where = $filter !== 'all' ? "WHERE role='photographer' AND status=?" : "WHERE role='photographer'";
$params = $filter !== 'all' ? [$filter] : [];
$stmt = $db->prepare("SELECT u.*, (SELECT COUNT(*) FROM orders WHERE photographer_id=u.id) as order_count, (SELECT COALESCE(SUM(total),0) FROM orders WHERE photographer_id=u.id AND status!='cancelled') as total_spent FROM users u $where ORDER BY u.created_at DESC");
$stmt->execute($params);
$photographers = $stmt->fetchAll();

require_once '../includes/header.php';
?>
<style>
.admin-wrap { max-width: 1200px; margin: 0 auto; padding: 2rem; padding-top: 5.5rem; }
.photo-card { background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,.06); padding: 1.5rem; display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; gap: 1rem; flex-wrap: wrap; }
.photo-avatar { width: 48px; height: 48px; border-radius: 50%; background: linear-gradient(135deg,var(--primary),#7c3aed); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 800; font-size: 1.25rem; flex-shrink: 0; }
.photo-info { flex: 1; min-width: 180px; }
.photo-info h3 { font-weight: 700; margin: 0 0 .2rem; }
.photo-info p { color: #6b7280; font-size: .8rem; margin: 0; }
.photo-stats { display: flex; gap: 1.5rem; }
.photo-stat { text-align: center; }
.photo-stat .val { font-weight: 800; font-size: 1.1rem; }
.photo-stat .lbl { font-size: .7rem; color: #9ca3af; }
.status-badge { display: inline-block; padding: .2rem .65rem; border-radius: 20px; font-size: .75rem; font-weight: 700; }
.status-pending { background: #fef3c7; color: #92400e; }
.status-approved { background: #d1fae5; color: #065f46; }
.status-rejected { background: #fee2e2; color: #991b1b; }
.filter-tabs { display: flex; gap: .5rem; flex-wrap: wrap; margin-bottom: 1.5rem; }
.filter-tab { padding: .4rem .9rem; border-radius: 20px; border: 1.5px solid #e5e7eb; background: #fff; text-decoration: none; font-size: .82rem; font-weight: 600; color: #374151; }
.filter-tab.active { background: var(--primary); color: #fff; border-color: var(--primary); }
.action-btns { display: flex; gap: .5rem; flex-wrap: wrap; }
.btn-approve { background: #d1fae5; color: #065f46; border: none; padding: .4rem .9rem; border-radius: 6px; font-weight: 700; font-size: .8rem; cursor: pointer; }
.btn-reject { background: #fee2e2; color: #991b1b; border: none; padding: .4rem .9rem; border-radius: 6px; font-weight: 700; font-size: .8rem; cursor: pointer; }
.btn-pending { background: #fef3c7; color: #92400e; border: none; padding: .4rem .9rem; border-radius: 6px; font-weight: 700; font-size: .8rem; cursor: pointer; }
</style>

<div class="admin-wrap">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;flex-wrap:wrap;gap:1rem;">
    <h1 style="font-size:1.5rem;font-weight:800;">Manage Photographers</h1>
    <a href="/admin/index.php" style="color:var(--primary);font-weight:700;text-decoration:none;">← Dashboard</a>
  </div>

  <?php if (isset($_GET['updated'])): ?>
  <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:.75rem 1rem;margin-bottom:1.5rem;color:#16a34a;font-weight:600;">✅ Status updated successfully.</div>
  <?php endif; ?>

  <div class="filter-tabs">
    <?php foreach (['all'=>'All','pending'=>'Pending Approval','approved'=>'Approved','rejected'=>'Rejected'] as $k=>$v): ?>
    <a href="/admin/photographers.php?status=<?= $k ?>" class="filter-tab <?= $filter===$k?'active':'' ?>"><?= $v ?></a>
    <?php endforeach; ?>
  </div>

  <?php if ($photographers): ?>
  <?php foreach ($photographers as $p): ?>
  <div class="photo-card">
    <div class="photo-avatar"><?= strtoupper(substr($p['name'], 0, 1)) ?></div>
    <div class="photo-info">
      <h3><?= htmlspecialchars($p['name']) ?></h3>
      <p><?= htmlspecialchars($p['email']) ?> <?= $p['phone'] ? '| '.$p['phone'] : '' ?></p>
      <?php if ($p['studio_name']): ?><p style="color:var(--primary);font-weight:600;"><?= htmlspecialchars($p['studio_name']) ?><?= $p['city'] ? ', '.htmlspecialchars($p['city']) : '' ?></p><?php endif; ?>
      <p>Joined: <?= date('d M Y', strtotime($p['created_at'])) ?></p>
    </div>
    <div class="photo-stats">
      <div class="photo-stat"><div class="val"><?= $p['order_count'] ?></div><div class="lbl">Orders</div></div>
      <div class="photo-stat"><div class="val">₹<?= number_format($p['total_spent']) ?></div><div class="lbl">Spent</div></div>
    </div>
    <span class="status-badge status-<?= $p['status'] ?>"><?= ucfirst($p['status']) ?></span>
    <form method="POST" class="action-btns">
      <input type="hidden" name="user_id" value="<?= $p['id'] ?>">
      <?php if ($p['status'] !== 'approved'): ?>
      <button type="submit" name="update_status" value="1" onclick="this.form.status.value='approved'" class="btn-approve">✅ Approve</button>
      <?php endif; ?>
      <?php if ($p['status'] !== 'rejected'): ?>
      <button type="submit" name="update_status" value="1" onclick="this.form.status.value='rejected'" class="btn-reject">✕ Reject</button>
      <?php endif; ?>
      <?php if ($p['status'] !== 'pending'): ?>
      <button type="submit" name="update_status" value="1" onclick="this.form.status.value='pending'" class="btn-pending">⏳ Set Pending</button>
      <?php endif; ?>
      <input type="hidden" name="status" value="">
    </form>
  </div>
  <?php endforeach; ?>
  <?php else: ?>
  <div style="background:#fff;border-radius:12px;padding:3rem;text-align:center;color:#9ca3af;box-shadow:0 2px 12px rgba(0,0,0,.06);">No photographers found.</div>
  <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
