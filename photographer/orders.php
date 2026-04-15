<?php
$pageTitle = 'My Orders – SD Colours';
require_once '../includes/auth.php';
requirePhotographer();
require_once '../includes/db.php';

$db = getDB();
$userId = $_SESSION['user_id'];

$viewId = isset($_GET['id']) ? (int)$_GET['id'] : null;
$order = null;
$items = [];

if ($viewId) {
    $stmt = $db->prepare("SELECT * FROM orders WHERE id=? AND photographer_id=?");
    $stmt->execute([$viewId, $userId]);
    $order = $stmt->fetch();
    if ($order) {
        $iStmt = $db->prepare("SELECT * FROM order_items WHERE order_id=?");
        $iStmt->execute([$viewId]);
        $items = $iStmt->fetchAll();
    }
}

$stmt = $db->prepare("SELECT o.*, COUNT(oi.id) as item_count FROM orders o LEFT JOIN order_items oi ON o.id=oi.order_id WHERE o.photographer_id=? GROUP BY o.id ORDER BY o.created_at DESC");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll();

require_once '../includes/header.php';
?>
<style>
.orders-wrap { max-width: 1000px; margin: 0 auto; padding: 2rem; padding-top: 5.5rem; }
.orders-table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,.06); }
.orders-table th { background: #f9fafb; padding: .75rem 1rem; text-align: left; font-weight: 600; color: #6b7280; font-size: .75rem; text-transform: uppercase; }
.orders-table td { padding: 1rem; border-top: 1px solid #f3f4f6; }
.status-badge { display: inline-block; padding: .2rem .65rem; border-radius: 20px; font-size: .75rem; font-weight: 700; }
.status-pending { background: #fef3c7; color: #92400e; }
.status-processing { background: #dbeafe; color: #1e40af; }
.status-shipped { background: #ede9fe; color: #5b21b6; }
.status-delivered { background: #d1fae5; color: #065f46; }
.status-cancelled { background: #fee2e2; color: #991b1b; }
.detail-card { background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,.06); overflow: hidden; margin-bottom: 2rem; }
.detail-head { background: linear-gradient(135deg,#1a1a2e,#16213e); color: #fff; padding: 1.5rem; display: flex; justify-content: space-between; align-items: center; }
.item-row { display: flex; justify-content: space-between; align-items: center; padding: .85rem 1.5rem; border-bottom: 1px solid #f3f4f6; }
</style>

<div class="orders-wrap">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;">
    <h1 style="font-size:1.6rem;font-weight:800;"><?= $order ? 'Order #'.$order['id'] : 'My Orders' ?></h1>
    <?php if ($order): ?>
    <a href="/photographer/orders.php" style="color:var(--primary);font-weight:700;text-decoration:none;">← All Orders</a>
    <?php else: ?>
    <a href="/photographer/shop.php" class="btn-primary">+ New Order</a>
    <?php endif; ?>
  </div>

  <?php if ($order): ?>
  <div class="detail-card">
    <div class="detail-head">
      <div>
        <div style="font-size:.8rem;color:#9ca3af;">Order Date</div>
        <div style="font-weight:700;"><?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></div>
      </div>
      <span class="status-badge status-<?= $order['status'] ?>" style="font-size:.9rem;padding:.35rem .9rem;"><?= ucfirst($order['status']) ?></span>
    </div>
    <div>
      <?php foreach ($items as $item): ?>
      <div class="item-row">
        <div>
          <strong><?= htmlspecialchars($item['product_name']) ?></strong>
          <?php if ($item['size']): ?><span style="color:#6b7280;"> – <?= htmlspecialchars($item['size']) ?></span><?php endif; ?>
          <?php if ($item['notes']): ?><br><small style="color:#9ca3af;"><?= htmlspecialchars($item['notes']) ?></small><?php endif; ?>
          <br><small style="color:#9ca3af;">Qty: <?= $item['quantity'] ?> × ₹<?= number_format($item['unit_price']) ?></small>
        </div>
        <strong>₹<?= number_format($item['unit_price'] * $item['quantity']) ?></strong>
      </div>
      <?php endforeach; ?>
      <div style="display:flex;justify-content:space-between;padding:1.25rem 1.5rem;font-size:1.1rem;">
        <strong>Total</strong><strong style="color:var(--primary);">₹<?= number_format($order['total']) ?></strong>
      </div>
    </div>
    <?php if ($order['notes']): ?>
    <div style="padding:1rem 1.5rem;background:#f9fafb;border-top:1px solid #f3f4f6;">
      <strong style="font-size:.8rem;color:#6b7280;">YOUR NOTE:</strong><br>
      <span style="font-size:.9rem;"><?= htmlspecialchars($order['notes']) ?></span>
    </div>
    <?php endif; ?>
    <?php if ($order['admin_notes']): ?>
    <div style="padding:1rem 1.5rem;background:#eff6ff;border-top:1px solid #bfdbfe;">
      <strong style="font-size:.8rem;color:#1e40af;">ADMIN NOTE:</strong><br>
      <span style="font-size:.9rem;"><?= htmlspecialchars($order['admin_notes']) ?></span>
    </div>
    <?php endif; ?>
  </div>

  <?php elseif (empty($orders)): ?>
  <div style="background:#fff;border-radius:12px;padding:4rem;text-align:center;box-shadow:0 2px 12px rgba(0,0,0,.06);">
    <div style="font-size:4rem;margin-bottom:1rem;">📋</div>
    <h2 style="font-weight:700;margin-bottom:.5rem;">No orders yet</h2>
    <p style="color:#6b7280;margin-bottom:1.5rem;">Start shopping to place your first order.</p>
    <a href="/photographer/shop.php" class="btn-primary">Browse Products</a>
  </div>

  <?php else: ?>
  <table class="orders-table">
    <thead><tr><th>Order #</th><th>Date</th><th>Items</th><th>Total</th><th>Status</th><th>Action</th></tr></thead>
    <tbody>
      <?php foreach ($orders as $o): ?>
      <tr>
        <td><strong>#<?= $o['id'] ?></strong></td>
        <td><?= date('d M Y', strtotime($o['created_at'])) ?></td>
        <td><?= $o['item_count'] ?> item<?= $o['item_count'] != 1 ? 's' : '' ?></td>
        <td>₹<?= number_format($o['total']) ?></td>
        <td><span class="status-badge status-<?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span></td>
        <td><a href="/photographer/orders.php?id=<?= $o['id'] ?>" style="color:var(--primary);font-weight:700;font-size:.85rem;text-decoration:none;">View Details →</a></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
