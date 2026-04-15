<?php
$pageTitle = 'My Dashboard – SD Colours';
require_once '../includes/auth.php';
requirePhotographer();
require_once '../includes/db.php';

$db = getDB();
$userId = $_SESSION['user_id'];

$orders = $db->prepare("SELECT o.*, COUNT(oi.id) as item_count FROM orders o LEFT JOIN order_items oi ON o.id=oi.order_id WHERE o.photographer_id=? GROUP BY o.id ORDER BY o.created_at DESC LIMIT 5");
$orders->execute([$userId]);
$recentOrders = $orders->fetchAll();

$totalOrders = $db->prepare("SELECT COUNT(*) FROM orders WHERE photographer_id=?");
$totalOrders->execute([$userId]);
$orderCount = $totalOrders->fetchColumn();

$totalSpent = $db->prepare("SELECT COALESCE(SUM(total),0) FROM orders WHERE photographer_id=? AND status != 'cancelled'");
$totalSpent->execute([$userId]);
$spent = $totalSpent->fetchColumn();

$user = getCurrentUser();

require_once '../includes/header.php';
?>
<style>
.dash-wrap { max-width: 1100px; margin: 0 auto; padding: 2rem; }
.dash-header { background: linear-gradient(135deg,#1a1a2e,#16213e); color: #fff; border-radius: 16px; padding: 2rem; margin-bottom: 2rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; }
.dash-header h1 { font-size: 1.6rem; font-weight: 800; margin: 0; }
.dash-header p { color: #9ca3af; margin: .25rem 0 0; }
.stats-row { display: grid; grid-template-columns: repeat(3,1fr); gap: 1.5rem; margin-bottom: 2rem; }
.stat-card { background: #fff; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 12px rgba(0,0,0,.06); text-align: center; }
.stat-card .val { font-size: 2rem; font-weight: 800; color: var(--primary); }
.stat-card .lbl { color: #6b7280; font-size: .875rem; margin-top: .25rem; }
.section-card { background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,.06); overflow: hidden; margin-bottom: 1.5rem; }
.section-card-head { padding: 1.25rem 1.5rem; border-bottom: 1px solid #f3f4f6; display: flex; justify-content: space-between; align-items: center; }
.section-card-head h2 { font-size: 1rem; font-weight: 700; margin: 0; }
.order-table { width: 100%; border-collapse: collapse; font-size: .875rem; }
.order-table th { background: #f9fafb; padding: .75rem 1rem; text-align: left; font-weight: 600; color: #6b7280; font-size: .75rem; text-transform: uppercase; }
.order-table td { padding: .85rem 1rem; border-top: 1px solid #f3f4f6; }
.status-badge { display: inline-block; padding: .2rem .65rem; border-radius: 20px; font-size: .75rem; font-weight: 700; }
.status-pending { background: #fef3c7; color: #92400e; }
.status-processing { background: #dbeafe; color: #1e40af; }
.status-shipped { background: #ede9fe; color: #5b21b6; }
.status-delivered { background: #d1fae5; color: #065f46; }
.status-cancelled { background: #fee2e2; color: #991b1b; }
.quick-links { display: grid; grid-template-columns: repeat(3,1fr); gap: 1rem; margin-bottom: 2rem; }
.quick-link { background: #fff; border-radius: 12px; padding: 1.5rem; text-align: center; text-decoration: none; color: #1f2937; box-shadow: 0 2px 12px rgba(0,0,0,.06); transition: transform .2s, box-shadow .2s; }
.quick-link:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,.1); }
.quick-link .icon { font-size: 2rem; margin-bottom: .75rem; }
.quick-link .label { font-weight: 700; font-size: .9rem; }
@media(max-width:640px){
  .stats-row,.quick-links{grid-template-columns:1fr!important;}
  .dash-wrap{padding:1rem;}
  .order-table thead{display:none;}
  .order-table tr{display:block;background:#fff;border:1px solid #f3f4f6;border-radius:10px;margin-bottom:.75rem;box-shadow:0 1px 4px rgba(0,0,0,.05);}
  .order-table td{display:flex;justify-content:space-between;align-items:center;padding:.6rem 1rem;border-top:1px solid #f9fafb;font-size:.85rem;}
  .order-table td::before{content:attr(data-label);font-weight:600;color:#6b7280;font-size:.72rem;text-transform:uppercase;flex-shrink:0;margin-right:.5rem;}
}
</style>

<div class="dash-wrap" style="padding-top:5.5rem;">
  <div class="dash-header">
    <div>
      <h1>Welcome back, <?= htmlspecialchars($user['name']) ?>!</h1>
      <p><?= htmlspecialchars($user['studio_name'] ?: 'SD Colours Photographer Portal') ?></p>
    </div>
    <a href="/photographer/shop.php" class="btn-primary">+ Place New Order</a>
  </div>

  <div class="stats-row">
    <div class="stat-card"><div class="val"><?= $orderCount ?></div><div class="lbl">Total Orders</div></div>
    <div class="stat-card"><div class="val">₹<?= number_format($spent) ?></div><div class="lbl">Total Spent</div></div>
    <div class="stat-card"><div class="val"><?= getCartCount() ?></div><div class="lbl">Items in Cart</div></div>
  </div>

  <div class="quick-links">
    <a href="/photographer/shop.php" class="quick-link"><div class="icon">🛍️</div><div class="label">Browse & Order</div></a>
    <a href="/photographer/cart.php" class="quick-link"><div class="icon">🛒</div><div class="label">My Cart</div></a>
    <a href="/photographer/orders.php" class="quick-link"><div class="icon">📋</div><div class="label">My Orders</div></a>
  </div>

  <div class="section-card">
    <div class="section-card-head">
      <h2>Recent Orders</h2>
      <a href="/photographer/orders.php" style="font-size:.8rem;color:var(--primary);font-weight:700;text-decoration:none;">View All →</a>
    </div>
    <?php if ($recentOrders): ?>
    <table class="order-table">
      <thead><tr><th>Order #</th><th>Date</th><th>Items</th><th>Total</th><th>Status</th></tr></thead>
      <tbody>
        <?php foreach ($recentOrders as $o): ?>
        <tr>
          <td data-label="Order"><a href="/photographer/orders.php?id=<?= $o['id'] ?>" style="color:var(--primary);font-weight:700;">#<?= $o['id'] ?></a></td>
          <td data-label="Date"><?= date('d M Y', strtotime($o['created_at'])) ?></td>
          <td data-label="Items"><?= $o['item_count'] ?> item<?= $o['item_count'] != 1 ? 's' : '' ?></td>
          <td data-label="Total">₹<?= number_format($o['total']) ?></td>
          <td data-label="Status"><span class="status-badge status-<?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php else: ?>
    <div style="padding:3rem;text-align:center;color:#9ca3af;">
      <div style="font-size:3rem;margin-bottom:1rem;">📦</div>
      <p>No orders yet. <a href="/photographer/shop.php" style="color:var(--primary);font-weight:700;">Browse products</a> to get started.</p>
    </div>
    <?php endif; ?>
  </div>
</div>

<?php require_once '../includes/footer.php'; ?>
