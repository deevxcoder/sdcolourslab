<?php
$pageTitle = 'My Cart – SD Colours';
require_once '../includes/auth.php';
requirePhotographer();
require_once '../includes/db.php';

startSession();

if (isset($_POST['remove'])) {
    $key = $_POST['remove'];
    unset($_SESSION['cart'][$key]);
    header('Location: /photographer/cart.php');
    exit;
}
if (isset($_POST['update_qty'])) {
    $key = $_POST['cart_key'];
    $qty = max(1, (int)$_POST['quantity']);
    if (isset($_SESSION['cart'][$key])) {
        $_SESSION['cart'][$key]['quantity'] = $qty;
    }
    header('Location: /photographer/cart.php');
    exit;
}

$cart = $_SESSION['cart'] ?? [];
$total = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $cart));

require_once '../includes/header.php';
?>
<style>
.cart-wrap { max-width: 900px; margin: 0 auto; padding: 2rem; padding-top: 5.5rem; }
.cart-table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,.06); margin-bottom: 2rem; }
.cart-table th { background: #f9fafb; padding: .75rem 1rem; text-align: left; font-weight: 600; color: #6b7280; font-size: .75rem; text-transform: uppercase; }
.cart-table td { padding: 1rem; border-top: 1px solid #f3f4f6; vertical-align: middle; }
.qty-input { width: 60px; padding: .4rem; border: 1.5px solid #e5e7eb; border-radius: 6px; text-align: center; }
.btn-remove { background: #fee2e2; color: #dc2626; border: none; padding: .35rem .75rem; border-radius: 6px; cursor: pointer; font-size: .8rem; font-weight: 700; }
.cart-summary { background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,.06); padding: 1.5rem; }
.cart-total { font-size: 1.5rem; font-weight: 800; color: var(--primary); margin: 1rem 0; }
</style>

<div class="cart-wrap">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;">
    <h1 style="font-size:1.6rem;font-weight:800;">My Cart</h1>
    <a href="/photographer/shop.php" style="color:var(--primary);font-weight:700;text-decoration:none;">← Continue Shopping</a>
  </div>

  <?php if (empty($cart)): ?>
  <div style="background:#fff;border-radius:12px;padding:4rem;text-align:center;box-shadow:0 2px 12px rgba(0,0,0,.06);">
    <div style="font-size:4rem;margin-bottom:1rem;">🛒</div>
    <h2 style="font-weight:700;margin-bottom:.5rem;">Your cart is empty</h2>
    <p style="color:#6b7280;margin-bottom:1.5rem;">Browse our products and add items to get started.</p>
    <a href="/photographer/shop.php" class="btn-primary">Browse Products</a>
  </div>
  <?php else: ?>
  <table class="cart-table">
    <thead><tr><th>Product</th><th>Size</th><th>Price</th><th>Qty</th><th>Subtotal</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($cart as $key => $item): ?>
      <tr>
        <td><strong><?= htmlspecialchars($item['name']) ?></strong><?php if ($item['notes']): ?><br><small style="color:#9ca3af;"><?= htmlspecialchars($item['notes']) ?></small><?php endif; ?></td>
        <td><?= htmlspecialchars($item['size'] ?: '—') ?></td>
        <td>₹<?= number_format($item['price']) ?></td>
        <td>
          <form method="POST" style="display:inline;">
            <input type="hidden" name="cart_key" value="<?= htmlspecialchars($key) ?>">
            <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" max="100" class="qty-input">
            <button type="submit" name="update_qty" style="background:none;border:none;color:var(--primary);cursor:pointer;font-weight:600;font-size:.8rem;">Update</button>
          </form>
        </td>
        <td><strong>₹<?= number_format($item['price'] * $item['quantity']) ?></strong></td>
        <td>
          <form method="POST">
            <input type="hidden" name="remove" value="<?= htmlspecialchars($key) ?>">
            <button type="submit" class="btn-remove">✕ Remove</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div class="cart-summary">
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;">
      <div>
        <div style="color:#6b7280;font-size:.875rem;">Order Total</div>
        <div class="cart-total">₹<?= number_format($total) ?></div>
        <div style="color:#9ca3af;font-size:.8rem;">* Shipping calculated on order confirmation</div>
      </div>
      <div style="display:flex;gap:1rem;flex-wrap:wrap;">
        <a href="/photographer/shop.php" style="padding:.75rem 1.5rem;border:1.5px solid #e5e7eb;border-radius:8px;font-weight:700;text-decoration:none;color:#374151;">← Shop More</a>
        <a href="/photographer/checkout.php" class="btn-primary" style="padding:.75rem 2rem;">Place Order →</a>
      </div>
    </div>
  </div>
  <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
