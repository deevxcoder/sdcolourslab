<?php
$pageTitle = 'Checkout – SD Colours';
require_once '../includes/auth.php';
requirePhotographer();
require_once '../includes/db.php';

startSession();

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header('Location: /photographer/cart.php');
    exit;
}

$db = getDB();
$total = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $cart));
$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $notes = trim($_POST['notes'] ?? '');
    try {
        $db->beginTransaction();
        $stmt = $db->prepare("INSERT INTO orders (photographer_id, total, notes, status) VALUES (?, ?, ?, 'pending') RETURNING id");
        $stmt->execute([$_SESSION['user_id'], $total, $notes]);
        $orderId = $stmt->fetchColumn();
        foreach ($cart as $item) {
            $is = $db->prepare("INSERT INTO order_items (order_id, product_id, product_name, size, quantity, unit_price, notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $is->execute([$orderId, $item['product_id'], $item['name'], $item['size'], $item['quantity'], $item['price'], $item['notes']]);
        }
        $db->commit();
        unset($_SESSION['cart']);
        $success = $orderId;
    } catch (Exception $e) {
        $db->rollBack();
        $error = 'Failed to place order. Please try again.';
    }
}

$user = getCurrentUser();
require_once '../includes/header.php';
?>
<style>
.checkout-wrap { max-width: 800px; margin: 0 auto; padding: 2rem; padding-top: 5.5rem; }
.order-summary { background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,.06); overflow: hidden; margin-bottom: 2rem; }
.order-summary-head { background: #f9fafb; padding: 1rem 1.5rem; font-weight: 700; border-bottom: 1px solid #f3f4f6; }
.order-summary-body { padding: 1.5rem; }
.order-item { display: flex; justify-content: space-between; padding: .6rem 0; border-bottom: 1px solid #f9fafb; font-size: .9rem; }
.form-group { margin-bottom: 1.25rem; }
.form-group label { display: block; font-weight: 600; font-size: .875rem; margin-bottom: .4rem; }
.form-group textarea { width: 100%; padding: .65rem .9rem; border: 1.5px solid #e5e7eb; border-radius: 8px; resize: vertical; box-sizing: border-box; }
.success-box { background: #f0fdf4; border: 1px solid #86efac; border-radius: 12px; padding: 2.5rem; text-align: center; }
.alert-error { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: .75rem 1rem; border-radius: 8px; margin-bottom: 1rem; }
</style>

<div class="checkout-wrap">
  <?php if ($success): ?>
  <div class="success-box">
    <div style="font-size:3.5rem;margin-bottom:1rem;">✅</div>
    <h1 style="font-size:1.75rem;font-weight:800;margin-bottom:.5rem;">Order Placed!</h1>
    <p style="color:#6b7280;margin-bottom:1.5rem;">Your order <strong>#<?= $success ?></strong> has been received. We'll process it shortly and update the status in your dashboard.</p>
    <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
      <a href="/photographer/orders.php" class="btn-primary">View My Orders</a>
      <a href="/photographer/shop.php" style="padding:.75rem 1.5rem;border:1.5px solid #e5e7eb;border-radius:8px;font-weight:700;text-decoration:none;color:#374151;">Shop More</a>
    </div>
  </div>
  <?php else: ?>
  <h1 style="font-size:1.6rem;font-weight:800;margin-bottom:2rem;">Review & Place Order</h1>
  <?php if ($error): ?><div class="alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

  <div class="order-summary">
    <div class="order-summary-head">Order Summary</div>
    <div class="order-summary-body">
      <?php foreach ($cart as $item): ?>
      <div class="order-item">
        <div>
          <strong><?= htmlspecialchars($item['name']) ?></strong>
          <?php if ($item['size']): ?> – <span style="color:#6b7280;"><?= htmlspecialchars($item['size']) ?></span><?php endif; ?>
          <span style="color:#9ca3af;margin-left:.5rem;">× <?= $item['quantity'] ?></span>
        </div>
        <div><strong>₹<?= number_format($item['price'] * $item['quantity']) ?></strong></div>
      </div>
      <?php endforeach; ?>
      <div style="display:flex;justify-content:space-between;padding-top:1rem;font-size:1.25rem;font-weight:800;">
        <span>Total</span><span style="color:var(--primary);">₹<?= number_format($total) ?></span>
      </div>
    </div>
  </div>

  <div style="background:#fff;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,.06);padding:1.5rem;">
    <h2 style="font-weight:700;margin-bottom:1.5rem;">Delivery Details</h2>
    <div style="background:#f9fafb;padding:1rem;border-radius:8px;margin-bottom:1.5rem;font-size:.875rem;">
      <strong><?= htmlspecialchars($user['name']) ?></strong><?= $user['studio_name'] ? ' – '.htmlspecialchars($user['studio_name']) : '' ?><br>
      <span style="color:#6b7280;"><?= htmlspecialchars($user['phone'] ?: 'No phone on file') ?> | <?= htmlspecialchars($user['city'] ?: '') ?></span>
    </div>
    <form method="POST">
      <div class="form-group">
        <label>Order Notes / Special Instructions (optional)</label>
        <textarea name="notes" rows="3" placeholder="Any specific requirements, delivery instructions, etc."></textarea>
      </div>
      <div style="display:flex;gap:1rem;justify-content:flex-end;flex-wrap:wrap;">
        <a href="/photographer/cart.php" style="padding:.75rem 1.5rem;border:1.5px solid #e5e7eb;border-radius:8px;font-weight:700;text-decoration:none;color:#374151;">← Edit Cart</a>
        <button type="submit" class="btn-primary" style="border:none;cursor:pointer;font-size:1rem;padding:.75rem 2rem;">✅ Confirm Order</button>
      </div>
    </form>
  </div>
  <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
