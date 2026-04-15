<?php
$pageTitle = 'Manage Orders – Admin';
require_once '../includes/auth.php';
requireAdmin();
require_once '../includes/db.php';

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order'])) {
    $orderId = (int)$_POST['order_id'];
    $status = $_POST['status'];
    $adminNotes = trim($_POST['admin_notes'] ?? '');
    $allowed = ['pending','processing','shipped','delivered','cancelled'];
    if (in_array($status, $allowed)) {
        $stmt = $db->prepare("UPDATE orders SET status=?, admin_notes=?, updated_at=NOW() WHERE id=?");
        $stmt->execute([$status, $adminNotes, $orderId]);
    }
    header("Location: /admin/orders.php?id=$orderId&updated=1");
    exit;
}

$viewId = isset($_GET['id']) ? (int)$_GET['id'] : null;
$statusFilter = $_GET['status'] ?? 'all';
$order = null;
$items = [];

if ($viewId) {
    $stmt = $db->prepare("SELECT o.*, u.name as photographer_name, u.email as photographer_email, u.phone as photographer_phone, u.studio_name FROM orders o JOIN users u ON o.photographer_id=u.id WHERE o.id=?");
    $stmt->execute([$viewId]);
    $order = $stmt->fetch();
    if ($order) {
        $iStmt = $db->prepare("SELECT * FROM order_items WHERE order_id=?");
        $iStmt->execute([$viewId]);
        $items = $iStmt->fetchAll();
    }
}

$where = $statusFilter !== 'all' ? "WHERE o.status=?" : "WHERE 1=1";
$params = $statusFilter !== 'all' ? [$statusFilter] : [];
$stmt = $db->prepare("SELECT o.*, u.name as photographer_name, COUNT(oi.id) as item_count FROM orders o JOIN users u ON o.photographer_id=u.id LEFT JOIN order_items oi ON o.id=oi.order_id $where GROUP BY o.id, u.name ORDER BY o.created_at DESC");
$stmt->execute($params);
$orders = $stmt->fetchAll();

require_once '../includes/header.php';
?>
<style>
.admin-wrap { max-width: 1200px; margin: 0 auto; padding: 2rem; padding-top: 5.5rem; }
.admin-table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,.06); font-size: .875rem; }
.admin-table th { background: #f9fafb; padding: .75rem 1rem; text-align: left; font-weight: 600; color: #6b7280; font-size: .75rem; text-transform: uppercase; }
.admin-table td { padding: .85rem 1rem; border-top: 1px solid #f3f4f6; }
.status-badge { display: inline-block; padding: .2rem .65rem; border-radius: 20px; font-size: .75rem; font-weight: 700; }
.status-pending { background: #fef3c7; color: #92400e; }
.status-processing { background: #dbeafe; color: #1e40af; }
.status-shipped { background: #ede9fe; color: #5b21b6; }
.status-delivered { background: #d1fae5; color: #065f46; }
.status-cancelled { background: #fee2e2; color: #991b1b; }
.filter-tabs { display: flex; gap: .5rem; flex-wrap: wrap; margin-bottom: 1.5rem; }
.filter-tab { padding: .4rem .9rem; border-radius: 20px; border: 1.5px solid #e5e7eb; background: #fff; text-decoration: none; font-size: .82rem; font-weight: 600; color: #374151; }
.filter-tab.active { background: var(--primary); color: #fff; border-color: var(--primary); }
.detail-card { background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,.06); overflow: hidden; margin-bottom: 1.5rem; }
.detail-head { background: linear-gradient(135deg,#1a1a2e,#16213e); color: #fff; padding: 1.5rem; display: flex; justify-content: space-between; align-items: center; }
</style>

<div class="admin-wrap">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;flex-wrap:wrap;gap:1rem;">
    <h1 style="font-size:1.5rem;font-weight:800;"><?= $order ? 'Order #'.$order['id'] : 'All Orders' ?></h1>
    <a href="/admin/index.php" style="color:var(--primary);font-weight:700;text-decoration:none;">← Dashboard</a>
  </div>

  <?php if ($order): ?>
  <?php if (isset($_GET['updated'])): ?>
  <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:.75rem 1rem;margin-bottom:1.5rem;color:#16a34a;font-weight:600;">✅ Order updated successfully.</div>
  <?php endif; ?>

  <div class="detail-card">
    <div class="detail-head">
      <div>
        <div style="font-size:.8rem;color:#9ca3af;">Order #<?= $order['id'] ?></div>
        <div style="font-weight:700;"><?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></div>
      </div>
      <span class="status-badge status-<?= $order['status'] ?>" style="font-size:.9rem;padding:.35rem .9rem;"><?= ucfirst($order['status']) ?></span>
    </div>
    <div style="padding:1.5rem;display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;" class="order-info-grid">
      <div>
        <div style="font-size:.75rem;color:#6b7280;text-transform:uppercase;font-weight:700;margin-bottom:.5rem;">Photographer</div>
        <div style="font-weight:700;"><?= htmlspecialchars($order['photographer_name']) ?></div>
        <?php if ($order['studio_name']): ?><div style="color:#6b7280;"><?= htmlspecialchars($order['studio_name']) ?></div><?php endif; ?>
        <div style="color:#6b7280;font-size:.875rem;"><?= htmlspecialchars($order['photographer_email']) ?></div>
        <?php if ($order['photographer_phone']): ?><div style="color:#6b7280;font-size:.875rem;"><?= htmlspecialchars($order['photographer_phone']) ?></div><?php endif; ?>
      </div>
      <div>
        <div style="font-size:.75rem;color:#6b7280;text-transform:uppercase;font-weight:700;margin-bottom:.5rem;">Order Total</div>
        <div style="font-size:1.75rem;font-weight:800;color:var(--primary);">₹<?= number_format($order['total']) ?></div>
      </div>
    </div>
    <div style="border-top:1px solid #f3f4f6;">
      <?php foreach ($items as $item): ?>
      <div style="display:flex;justify-content:space-between;align-items:center;padding:.85rem 1.5rem;border-bottom:1px solid #f9fafb;">
        <div>
          <strong><?= htmlspecialchars($item['product_name']) ?></strong>
          <?php if ($item['size']): ?><span style="color:#6b7280;"> – <?= htmlspecialchars($item['size']) ?></span><?php endif; ?>
          <?php if ($item['notes']): ?><br><small style="color:#9ca3af;"><?= htmlspecialchars($item['notes']) ?></small><?php endif; ?>
          <br><small style="color:#9ca3af;">Qty: <?= $item['quantity'] ?> × ₹<?= number_format($item['unit_price']) ?></small>
        </div>
        <strong>₹<?= number_format($item['unit_price'] * $item['quantity']) ?></strong>
      </div>
      <?php endforeach; ?>
    </div>
    <?php if ($order['notes']): ?>
    <div style="padding:1rem 1.5rem;background:#f9fafb;border-top:1px solid #f3f4f6;">
      <strong style="font-size:.75rem;color:#6b7280;">PHOTOGRAPHER NOTE:</strong><br><?= htmlspecialchars($order['notes']) ?>
    </div>
    <?php endif; ?>
  </div>

  <div class="detail-card">
    <div style="padding:1.5rem;">
      <h2 style="font-weight:700;margin-bottom:1.5rem;">Update Order Status</h2>
      <form method="POST">
        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1rem;" class="update-grid">
          <div>
            <label style="font-weight:600;font-size:.875rem;display:block;margin-bottom:.4rem;">Status</label>
            <select name="status" style="width:100%;padding:.65rem;border:1.5px solid #e5e7eb;border-radius:8px;">
              <?php foreach (['pending','processing','shipped','delivered','cancelled'] as $s): ?>
              <option value="<?= $s ?>" <?= $order['status']===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label style="font-weight:600;font-size:.875rem;display:block;margin-bottom:.4rem;">Admin Note to Photographer</label>
            <input type="text" name="admin_notes" value="<?= htmlspecialchars($order['admin_notes'] ?? '') ?>" placeholder="e.g. Dispatched via Blue Dart, Tracking: XXXX" style="width:100%;padding:.65rem;border:1.5px solid #e5e7eb;border-radius:8px;box-sizing:border-box;">
          </div>
        </div>
        <button type="submit" name="update_order" class="btn-primary" style="border:none;cursor:pointer;">Save Changes</button>
      </form>
    </div>
  </div>
  <a href="/admin/orders.php" style="color:var(--primary);font-weight:700;text-decoration:none;">← Back to All Orders</a>
  <style>@media(max-width:600px){.order-info-grid,.update-grid{grid-template-columns:1fr!important;}}</style>

  <?php else: ?>
  <div class="filter-tabs">
    <?php foreach (['all'=>'All','pending'=>'Pending','processing'=>'Processing','shipped'=>'Shipped','delivered'=>'Delivered','cancelled'=>'Cancelled'] as $k=>$v): ?>
    <a href="/admin/orders.php?status=<?= $k ?>" class="filter-tab <?= $statusFilter===$k?'active':'' ?>"><?= $v ?></a>
    <?php endforeach; ?>
  </div>
  <?php if ($orders): ?>
  <table class="admin-table">
    <thead><tr><th>#</th><th>Photographer</th><th>Date</th><th>Items</th><th>Total</th><th>Status</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($orders as $o): ?>
      <tr>
        <td><strong>#<?= $o['id'] ?></strong></td>
        <td><?= htmlspecialchars($o['photographer_name']) ?></td>
        <td><?= date('d M Y', strtotime($o['created_at'])) ?></td>
        <td><?= $o['item_count'] ?></td>
        <td>₹<?= number_format($o['total']) ?></td>
        <td><span class="status-badge status-<?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span></td>
        <td><a href="/admin/orders.php?id=<?= $o['id'] ?>" style="color:var(--primary);font-weight:700;font-size:.8rem;text-decoration:none;">Manage →</a></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php else: ?>
  <div style="background:#fff;border-radius:12px;padding:3rem;text-align:center;color:#9ca3af;box-shadow:0 2px 12px rgba(0,0,0,.06);">No orders found.</div>
  <?php endif; ?>
  <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
