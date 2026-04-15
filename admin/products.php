<?php
$pageTitle = 'Manage Products – Admin';
require_once '../includes/auth.php';
requireAdmin();
require_once '../includes/db.php';

$db = getDB();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_product'])) {
        $name = trim($_POST['name']);
        $category = $_POST['category'];
        $description = trim($_POST['description'] ?? '');
        $price = (float)$_POST['price'];
        $priceAlt = $_POST['price_alt'] ? (float)$_POST['price_alt'] : null;
        $sizes = json_encode(array_filter(array_map('trim', explode(',', $_POST['sizes'] ?? ''))));
        $features = json_encode(array_filter(array_map('trim', explode(',', $_POST['features'] ?? ''))));
        $tag = trim($_POST['tag'] ?? '') ?: null;
        $active = isset($_POST['active']) ? true : false;
        $sortOrder = (int)($_POST['sort_order'] ?? 0);

        if ($_POST['product_id']) {
            $stmt = $db->prepare("UPDATE products SET name=?,category=?,description=?,price=?,price_alt=?,sizes=?,features=?,tag=?,active=?,sort_order=? WHERE id=?");
            $stmt->execute([$name,$category,$description,$price,$priceAlt,$sizes,$features,$tag,$active?1:0,$sortOrder,(int)$_POST['product_id']]);
            $message = 'Product updated.';
        } else {
            $stmt = $db->prepare("INSERT INTO products (name,category,description,price,price_alt,sizes,features,tag,active,sort_order) VALUES (?,?,?,?,?,?,?,?,?,?)");
            $stmt->execute([$name,$category,$description,$price,$priceAlt,$sizes,$features,$tag,$active?1:0,$sortOrder]);
            $message = 'Product added.';
        }
    }
    if (isset($_POST['toggle_active'])) {
        $stmt = $db->prepare("UPDATE products SET active = NOT active WHERE id=?");
        $stmt->execute([(int)$_POST['product_id']]);
        $message = 'Product status toggled.';
    }
    if (isset($_POST['delete_product'])) {
        $stmt = $db->prepare("DELETE FROM products WHERE id=?");
        $stmt->execute([(int)$_POST['product_id']]);
        $message = 'Product deleted.';
    }
    header("Location: /admin/products.php" . ($message ? "?msg=".urlencode($message) : ""));
    exit;
}

$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : null;
$editProduct = null;
if ($editId) {
    $stmt = $db->prepare("SELECT * FROM products WHERE id=?");
    $stmt->execute([$editId]);
    $editProduct = $stmt->fetch();
}
$action = isset($_GET['action']) ? $_GET['action'] : ($editProduct ? 'edit' : 'list');

$products = $db->query("SELECT * FROM products ORDER BY sort_order, id")->fetchAll();
$cats = ['combo'=>'Combo Pad','album'=>'Album','led_frame'=>'LED Frame','wall_acrylic'=>'Wall Acrylic'];

require_once '../includes/header.php';
?>
<style>
.admin-wrap { max-width: 1200px; margin: 0 auto; padding: 2rem; padding-top: 5.5rem; }
.admin-table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,.06); font-size: .875rem; }
.admin-table th { background: #f9fafb; padding: .75rem 1rem; text-align: left; font-weight: 600; color: #6b7280; font-size: .75rem; text-transform: uppercase; }
.admin-table td { padding: .85rem 1rem; border-top: 1px solid #f3f4f6; vertical-align: middle; }
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
.form-group { margin-bottom: 0; }
.form-group label { display: block; font-weight: 600; font-size: .875rem; margin-bottom: .4rem; color: #374151; }
.form-group input, .form-group select, .form-group textarea { width: 100%; padding: .65rem .9rem; border: 1.5px solid #e5e7eb; border-radius: 8px; box-sizing: border-box; font-size: .9rem; }
.form-group input:focus, .form-group select:focus { outline: none; border-color: var(--primary); }
.product-form { background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,.06); padding: 2rem; }
.cat-badge { display: inline-block; padding: .15rem .5rem; border-radius: 4px; font-size: .7rem; font-weight: 700; background: #dbeafe; color: #1e40af; }
.active-pill { display: inline-block; padding: .15rem .5rem; border-radius: 10px; font-size: .7rem; font-weight: 700; }
@media(max-width:640px){.form-grid{grid-template-columns:1fr!important;}}
</style>

<div class="admin-wrap">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;flex-wrap:wrap;gap:1rem;">
    <h1 style="font-size:1.5rem;font-weight:800;"><?= ($action === 'add' || $editProduct) ? ($editProduct ? 'Edit Product' : 'Add Product') : 'All Products' ?></h1>
    <div style="display:flex;gap:.75rem;">
      <?php if ($action !== 'add' && !$editProduct): ?>
      <a href="/admin/products.php?action=add" class="btn-primary" style="font-size:.85rem;">+ Add Product</a>
      <?php endif; ?>
      <a href="/admin/index.php" style="color:var(--primary);font-weight:700;text-decoration:none;">← Dashboard</a>
    </div>
  </div>

  <?php if (isset($_GET['msg'])): ?>
  <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:.75rem 1rem;margin-bottom:1.5rem;color:#16a34a;font-weight:600;">✅ <?= htmlspecialchars($_GET['msg']) ?></div>
  <?php endif; ?>

  <?php if ($action === 'add' || $editProduct): ?>
  <div class="product-form">
    <h2 style="font-weight:700;margin-bottom:1.5rem;"><?= $editProduct ? 'Edit: '.htmlspecialchars($editProduct['name']) : 'Add New Product' ?></h2>
    <form method="POST">
      <input type="hidden" name="product_id" value="<?= $editProduct ? $editProduct['id'] : '' ?>">
      <div class="form-grid" style="margin-bottom:1.25rem;">
        <div class="form-group">
          <label>Product Name *</label>
          <input type="text" name="name" required value="<?= htmlspecialchars($editProduct['name'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Category *</label>
          <select name="category">
            <?php foreach ($cats as $k=>$v): ?><option value="<?= $k ?>" <?= ($editProduct['category']??'')===$k?'selected':'' ?>><?= $v ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Price (₹) *</label>
          <input type="number" step="0.01" name="price" required value="<?= $editProduct['price'] ?? '' ?>">
        </div>
        <div class="form-group">
          <label>Alt Price (₹) — optional</label>
          <input type="number" step="0.01" name="price_alt" value="<?= $editProduct['price_alt'] ?? '' ?>">
        </div>
        <div class="form-group">
          <label>Sizes (comma-separated)</label>
          <input type="text" name="sizes" value="<?= htmlspecialchars(implode(', ', json_decode($editProduct['sizes'] ?? '[]', true))) ?>" placeholder="12x24, 12x30, 18x24">
        </div>
        <div class="form-group">
          <label>Features (comma-separated)</label>
          <input type="text" name="features" value="<?= htmlspecialchars(implode(', ', json_decode($editProduct['features'] ?? '[]', true))) ?>" placeholder="Premium Finish, Includes Bag">
        </div>
        <div class="form-group">
          <label>Tag (e.g. Best Seller, Premium)</label>
          <input type="text" name="tag" value="<?= htmlspecialchars($editProduct['tag'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Sort Order</label>
          <input type="number" name="sort_order" value="<?= $editProduct['sort_order'] ?? 0 ?>">
        </div>
      </div>
      <div class="form-group" style="margin-bottom:1.25rem;">
        <label>Description</label>
        <textarea name="description" rows="2"><?= htmlspecialchars($editProduct['description'] ?? '') ?></textarea>
      </div>
      <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem;">
        <label style="display:flex;align-items:center;gap:.5rem;font-weight:600;cursor:pointer;">
          <input type="checkbox" name="active" <?= ($editProduct['active'] ?? true) ? 'checked' : '' ?> style="width:auto;"> Active (visible to photographers)
        </label>
      </div>
      <div style="display:flex;gap:1rem;flex-wrap:wrap;">
        <button type="submit" name="save_product" class="btn-primary" style="border:none;cursor:pointer;"><?= $editProduct ? 'Save Changes' : 'Add Product' ?></button>
        <a href="/admin/products.php" style="padding:.65rem 1.5rem;border:1.5px solid #e5e7eb;border-radius:8px;font-weight:700;text-decoration:none;color:#374151;">Cancel</a>
      </div>
    </form>
  </div>

  <?php else: ?>
  <table class="admin-table">
    <thead><tr><th>Product</th><th>Category</th><th>Price</th><th>Tag</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach ($products as $p): ?>
      <tr style="<?= !$p['active'] ? 'opacity:.5;' : '' ?>">
        <td><strong><?= htmlspecialchars($p['name']) ?></strong></td>
        <td><span class="cat-badge"><?= $cats[$p['category']] ?? $p['category'] ?></span></td>
        <td>₹<?= number_format($p['price']) ?><?= $p['price_alt'] ? ' / ₹'.number_format($p['price_alt']) : '' ?></td>
        <td><?= $p['tag'] ? htmlspecialchars($p['tag']) : '—' ?></td>
        <td><span class="active-pill" style="background:<?= $p['active']?'#d1fae5':'#f3f4f6' ?>;color:<?= $p['active']?'#065f46':'#9ca3af' ?>;"><?= $p['active']?'Active':'Hidden' ?></span></td>
        <td>
          <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
            <a href="/admin/products.php?edit=<?= $p['id'] ?>" style="background:#eff6ff;color:#1e40af;padding:.3rem .7rem;border-radius:6px;font-size:.75rem;font-weight:700;text-decoration:none;">Edit</a>
            <form method="POST" style="display:inline;">
              <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
              <button type="submit" name="toggle_active" style="background:<?= $p['active']?'#fef3c7':'#d1fae5' ?>;color:<?= $p['active']?'#92400e':'#065f46' ?>;border:none;padding:.3rem .7rem;border-radius:6px;font-size:.75rem;font-weight:700;cursor:pointer;"><?= $p['active']?'Hide':'Show' ?></button>
            </form>
            <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this product?')">
              <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
              <button type="submit" name="delete_product" style="background:#fee2e2;color:#991b1b;border:none;padding:.3rem .7rem;border-radius:6px;font-size:.75rem;font-weight:700;cursor:pointer;">Delete</button>
            </form>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
