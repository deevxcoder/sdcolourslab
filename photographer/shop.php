<?php
$pageTitle = 'Shop – SD Colours';
require_once '../includes/auth.php';
requirePhotographer();
require_once '../includes/db.php';

startSession();
$db = getDB();

$cat = $_GET['cat'] ?? 'all';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $productId = (int)$_POST['product_id'];
    $size = trim($_POST['size'] ?? '');
    $qty = max(1, (int)($_POST['quantity'] ?? 1));
    $notes = trim($_POST['notes'] ?? '');

    $stmt = $db->prepare('SELECT * FROM products WHERE id=? AND active=1');
    $stmt->execute([$productId]);
    $product = $stmt->fetch();

    if ($product) {
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        $cartKey = $productId . '_' . $size;
        if (isset($_SESSION['cart'][$cartKey])) {
            $_SESSION['cart'][$cartKey]['quantity'] += $qty;
        } else {
            $_SESSION['cart'][$cartKey] = [
                'product_id' => $productId,
                'name' => $product['name'],
                'price' => (float)$product['price'],
                'size' => $size,
                'quantity' => $qty,
                'notes' => $notes,
            ];
        }
        $message = 'success';
    }
}

$categories = ['all' => 'All Products', 'combo' => 'Combo Pads', 'album' => 'Albums', 'led_frame' => 'LED Frames', 'wall_acrylic' => 'Wall Acrylic'];
$where = $cat !== 'all' ? "AND category=?" : "";
$params = $cat !== 'all' ? [$cat] : [];
$stmt = $db->prepare("SELECT * FROM products WHERE active=1 $where ORDER BY sort_order");
$stmt->execute($params);
$products = $stmt->fetchAll();

require_once '../includes/header.php';
?>
<style>
.shop-wrap { max-width: 1200px; margin: 0 auto; padding: 2rem; padding-top: 5.5rem; }
.shop-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem; }
.cat-tabs { display: flex; gap: .5rem; flex-wrap: wrap; }
.cat-tab { padding: .45rem 1rem; border-radius: 20px; border: 1.5px solid #e5e7eb; background: #fff; cursor: pointer; font-size: .85rem; font-weight: 600; text-decoration: none; color: #374151; }
.cat-tab.active { background: var(--primary); color: #fff; border-color: var(--primary); }
.shop-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px,1fr)); gap: 1.5rem; }
.shop-card { background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,.07); overflow: hidden; display: flex; flex-direction: column; }
.shop-card-img { background: linear-gradient(135deg,#1a1a2e,#16213e); padding: 2rem; display: flex; align-items: center; justify-content: center; font-size: 3rem; height: 140px; }
.shop-card-body { padding: 1.25rem; flex: 1; display: flex; flex-direction: column; }
.shop-card-body h3 { font-weight: 700; margin-bottom: .5rem; }
.shop-card-price { font-size: 1.25rem; font-weight: 800; color: var(--primary); margin-bottom: .75rem; }
.add-form select, .add-form input[type=number], .add-form input[type=text] { width: 100%; padding: .5rem .75rem; border: 1.5px solid #e5e7eb; border-radius: 8px; margin-bottom: .5rem; font-size: .85rem; box-sizing: border-box; }
.add-form select:focus, .add-form input:focus { outline: none; border-color: var(--primary); }
.btn-add { width: 100%; background: var(--primary); color: #fff; border: none; padding: .65rem; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: .9rem; margin-top: auto; }
.btn-add:hover { opacity: .9; }
.alert-success { background: #d1fae5; border: 1px solid #6ee7b7; color: #065f46; padding: .75rem 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-weight: 600; }
</style>

<div class="shop-wrap">
  <div class="shop-header">
    <h1 style="font-size:1.5rem;font-weight:800;">Browse Products</h1>
    <a href="/photographer/cart.php" style="background:var(--primary);color:#fff;padding:.5rem 1.25rem;border-radius:8px;text-decoration:none;font-weight:700;">🛒 Cart (<?= getCartCount() ?>)</a>
  </div>

  <?php if ($message === 'success'): ?>
  <div class="alert-success">✅ Product added to cart! <a href="/photographer/cart.php" style="color:var(--primary);">View Cart →</a></div>
  <?php endif; ?>

  <div class="cat-tabs" style="margin-bottom:2rem;">
    <?php foreach ($categories as $key => $label): ?>
    <a href="/photographer/shop.php?cat=<?= $key ?>" class="cat-tab <?= $cat === $key ? 'active' : '' ?>"><?= $label ?></a>
    <?php endforeach; ?>
  </div>

  <div class="shop-grid">
    <?php foreach ($products as $p):
      $sizes = json_decode($p['sizes'], true);
      $features = json_decode($p['features'], true);
      $emojis = ['combo'=>'📦','album'=>'📖','led_frame'=>'💡','wall_acrylic'=>'✨'];
      $emoji = $emojis[$p['category']] ?? '🖼️';
      $hasImg = !empty($p['image']);
    ?>
    <div class="shop-card">
      <?php if ($hasImg): ?>
      <div class="shop-card-img" style="padding:0;height:200px;"><img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" style="width:100%;height:100%;object-fit:cover;" loading="lazy" /></div>
      <?php else: ?>
      <div class="shop-card-img"><?= $emoji ?></div>
      <?php endif; ?>
      <div class="shop-card-body">
        <?php if ($p['tag']): ?><span style="background:<?= $p['tag']==='Premium'?'#7c3aed':'var(--primary)' ?>;color:#fff;font-size:.65rem;padding:2px 8px;border-radius:10px;font-weight:700;"><?= htmlspecialchars($p['tag']) ?></span><br><br><?php endif; ?>
        <h3><?= htmlspecialchars($p['name']) ?></h3>
        <div class="shop-card-price">₹<?= number_format($p['price']) ?><?= $p['price_alt'] ? ' – ₹'.number_format($p['price_alt']) : '' ?></div>
        <?php if ($features): ?><ul style="color:#6b7280;font-size:.8rem;margin-bottom:.75rem;padding-left:1rem;"><?php foreach ($features as $f): ?><li><?= htmlspecialchars($f) ?></li><?php endforeach; ?></ul><?php endif; ?>
        <form method="POST" class="add-form">
          <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
          <?php if ($sizes && count($sizes) > 0): ?>
          <select name="size" required>
            <option value="">Select Size</option>
            <?php foreach ($sizes as $s): ?><option value="<?= htmlspecialchars($s) ?>"><?= htmlspecialchars($s) ?></option><?php endforeach; ?>
          </select>
          <?php endif; ?>
          <input type="number" name="quantity" value="1" min="1" max="100" placeholder="Qty">
          <input type="text" name="notes" placeholder="Special instructions (optional)">
          <button type="submit" name="add_to_cart" class="btn-add">Add to Cart</button>
        </form>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<?php require_once '../includes/footer.php'; ?>
