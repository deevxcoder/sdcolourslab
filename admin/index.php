<?php
$pageTitle = 'Admin Dashboard – SD Colours';
require_once '../includes/auth.php';
requireAdmin();
require_once '../includes/db.php';

$db = getDB();

$totalOrders = $db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$pendingOrders = $db->query("SELECT COUNT(*) FROM orders WHERE status='pending'")->fetchColumn();
$totalPhotographers = $db->query("SELECT COUNT(*) FROM users WHERE role='photographer'")->fetchColumn();
$pendingPhotographers = $db->query("SELECT COUNT(*) FROM users WHERE role='photographer' AND status='pending'")->fetchColumn();
$totalRevenue = $db->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE status != 'cancelled'")->fetchColumn();
$totalProducts = $db->query("SELECT COUNT(*) FROM products WHERE active=1")->fetchColumn();

$recentOrders = $db->query("SELECT o.*, u.name as photographer_name FROM orders o JOIN users u ON o.photographer_id=u.id ORDER BY o.created_at DESC LIMIT 8")->fetchAll();

require_once '../includes/header.php';
?>
<style>
.admin-wrap { max-width: 1200px; margin: 0 auto; padding: 2rem; padding-top: 5.5rem; }
.admin-header { background: linear-gradient(135deg,#1a1a2e,#16213e); color: #fff; border-radius: 16px; padding: 2rem; margin-bottom: 2rem; }
.admin-header h1 { font-size: 1.6rem; font-weight: 800; margin: 0; }
.admin-header p { color: #9ca3af; margin: .25rem 0 0; }
.stats-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 1.5rem; margin-bottom: 2rem; }
.stat-card { background: #fff; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 12px rgba(0,0,0,.06); display: flex; align-items: center; gap: 1rem; }
.stat-icon { font-size: 2.5rem; }
.stat-val { font-size: 1.75rem; font-weight: 800; color: var(--primary); }
.stat-lbl { font-size: .8rem; color: #6b7280; }
.stat-sub { font-size: .75rem; color: #ef4444; font-weight: 600; }
.nav-cards { display: grid; grid-template-columns: repeat(4,1fr); gap: 1rem; margin-bottom: 2rem; }
.nav-card { background: #fff; border-radius: 12px; padding: 1.25rem; text-align: center; text-decoration: none; color: #1f2937; box-shadow: 0 2px 12px rgba(0,0,0,.06); font-weight: 700; font-size: .9rem; }
.nav-card:hover { background: var(--primary); color: #fff; }
.section-card { background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,.06); overflow: hidden; }
.section-head { padding: 1.25rem 1.5rem; border-bottom: 1px solid #f3f4f6; display: flex; justify-content: space-between; align-items: center; }
.section-head h2 { font-size: 1rem; font-weight: 700; margin: 0; }
.admin-table { width: 100%; border-collapse: collapse; font-size: .875rem; }
.admin-table th { background: #f9fafb; padding: .75rem 1rem; text-align: left; font-weight: 600; color: #6b7280; font-size: .75rem; text-transform: uppercase; }
.admin-table td { padding: .85rem 1rem; border-top: 1px solid #f3f4f6; }
.status-badge { display: inline-block; padding: .2rem .65rem; border-radius: 20px; font-size: .75rem; font-weight: 700; }
.status-pending { background: #fef3c7; color: #92400e; }
.status-processing { background: #dbeafe; color: #1e40af; }
.status-shipped { background: #ede9fe; color: #5b21b6; }
.status-delivered { background: #d1fae5; color: #065f46; }
.status-cancelled { background: #fee2e2; color: #991b1b; }
@media(max-width:768px){.stats-grid,.nav-cards{grid-template-columns:1fr 1fr!important;}}
@media(max-width:480px){.stats-grid,.nav-cards{grid-template-columns:1fr!important;}}
</style>

<div class="admin-wrap">
  <div class="admin-header">
    <h1>Admin Dashboard</h1>
    <p>SD Colours Photobook Lab – Management Panel</p>
  </div>

  <div class="stats-grid">
    <div class="stat-card"><div class="stat-icon">📋</div><div><div class="stat-val"><?= $totalOrders ?></div><div class="stat-lbl">Total Orders</div><?php if ($pendingOrders): ?><div class="stat-sub"><?= $pendingOrders ?> pending</div><?php endif; ?></div></div>
    <div class="stat-card"><div class="stat-icon">👨‍📷</div><div><div class="stat-val"><?= $totalPhotographers ?></div><div class="stat-lbl">Photographers</div><?php if ($pendingPhotographers): ?><div class="stat-sub"><?= $pendingPhotographers ?> awaiting approval</div><?php endif; ?></div></div>
    <div class="stat-card"><div class="stat-icon">💰</div><div><div class="stat-val">₹<?= number_format($totalRevenue) ?></div><div class="stat-lbl">Total Revenue</div></div></div>
  </div>

  <div class="nav-cards" style="margin-bottom:2rem;">
    <a href="/admin/orders.php" class="nav-card">📋 Manage Orders</a>
    <a href="/admin/photographers.php" class="nav-card">👨‍📷 Photographers<?php if ($pendingPhotographers): ?> <span style="background:#ef4444;color:#fff;border-radius:10px;font-size:.65rem;padding:1px 5px;"><?= $pendingPhotographers ?></span><?php endif; ?></a>
    <a href="/admin/products.php" class="nav-card">📦 Products</a>
    <a href="/admin/products.php?action=add" class="nav-card">➕ Add Product</a>
  </div>

  <div class="section-card">
    <div class="section-head">
      <h2>Recent Orders</h2>
      <a href="/admin/orders.php" style="font-size:.8rem;color:var(--primary);font-weight:700;text-decoration:none;">View All →</a>
    </div>
    <?php if ($recentOrders): ?>
    <table class="admin-table">
      <thead><tr><th>#</th><th>Photographer</th><th>Date</th><th>Total</th><th>Status</th><th>Action</th></tr></thead>
      <tbody>
        <?php foreach ($recentOrders as $o): ?>
        <tr>
          <td>#<?= $o['id'] ?></td>
          <td><?= htmlspecialchars($o['photographer_name']) ?></td>
          <td><?= date('d M Y', strtotime($o['created_at'])) ?></td>
          <td>₹<?= number_format($o['total']) ?></td>
          <td><span class="status-badge status-<?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span></td>
          <td><a href="/admin/orders.php?id=<?= $o['id'] ?>" style="color:var(--primary);font-weight:700;font-size:.8rem;text-decoration:none;">Manage →</a></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php else: ?>
    <div style="padding:3rem;text-align:center;color:#9ca3af;">No orders yet.</div>
    <?php endif; ?>
  </div>
</div>

<?php require_once '../includes/footer.php'; ?>
