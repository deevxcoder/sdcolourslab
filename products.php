<?php
$pageTitle = 'Our Products – SD Colours Photobook Lab';
$pageDesc = 'Explore wedding albums, combo photo pads, LED frames, acrylic prints and wall canvas.';
require_once 'includes/header.php';
require_once 'includes/db.php';

$db = getDB();
$combos = $db->query("SELECT * FROM products WHERE category='combo' AND active=true ORDER BY sort_order")->fetchAll();
$albums = $db->query("SELECT * FROM products WHERE category='album' AND active=true ORDER BY sort_order")->fetchAll();
$led = $db->query("SELECT * FROM products WHERE category='led_frame' AND active=true ORDER BY sort_order")->fetchAll();
$acrylic = $db->query("SELECT * FROM products WHERE category='wall_acrylic' AND active=true ORDER BY sort_order")->fetchAll();
?>

<div class="page-hero">
  <h1 class="font-serif text-gradient">Our Products</h1>
  <p>Explore our curated selection of premium wedding albums, combo photo pads, LED frames, and wall canvases.</p>
</div>

<div class="section bg-accent-light">
  <div class="container">

    <section id="albums" style="margin-bottom:5rem;">
      <div class="section-sep"></div>
      <h2 class="font-serif" style="font-size:1.75rem;font-weight:800;margin-bottom:2rem;">Photo Album Printing</h2>
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:1.5rem;">
        <?php foreach ($albums as $p):
          $sizes = json_decode($p['sizes'], true);
          $features = json_decode($p['features'], true);
        ?>
        <div class="album-card">
          <h3><?= htmlspecialchars($p['name']) ?></h3>
          <div class="label-sm">Sizes</div>
          <p><?= implode(', ', $sizes) ?></p>
          <div class="label-sm mt-4">Paper Types</div>
          <ul><?php foreach ($features as $f): ?><li><?= htmlspecialchars($f) ?></li><?php endforeach; ?></ul>
          <?php if (isPhotographer()): ?>
            <a href="/photographer/shop.php?cat=album" class="btn-wa" style="margin-top:1rem;display:block;text-align:center;">Order Now</a>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
    </section>

    <section id="combos" style="margin-bottom:5rem;">
      <div class="section-sep"></div>
      <h2 class="font-serif" style="font-size:1.75rem;font-weight:800;margin-bottom:2rem;">Combo Photo Pad Products</h2>
      <div class="products-grid">
        <?php foreach ($combos as $p):
          $sizes = json_decode($p['sizes'], true);
          $features = json_decode($p['features'], true);
          $tagStyle = ($p['tag'] === 'Premium') ? 'background:#7c3aed;' : '';
        ?>
        <div class="product-card">
          <?php if ($p['tag']): ?><span class="product-tag" style="<?= $tagStyle ?>"><?= htmlspecialchars($p['tag']) ?></span><?php endif; ?>
          <div class="product-card-img"><img src="/images/monogram.png" alt="<?= htmlspecialchars($p['name']) ?>" /></div>
          <div class="product-card-body">
            <h3><?= htmlspecialchars($p['name']) ?></h3>
            <div class="product-price">₹<?= number_format($p['price']) ?><?= $p['price_alt'] ? ' / ₹'.number_format($p['price_alt']) : '' ?></div>
            <ul class="product-features"><?php foreach ($features as $f): ?><li><?= htmlspecialchars($f) ?></li><?php endforeach; ?></ul>
            <div class="size-pills"><?php foreach ($sizes as $s): ?><span class="size-pill"><?= htmlspecialchars($s) ?></span><?php endforeach; ?></div>
            <?php if (isPhotographer()): ?>
              <a href="/photographer/shop.php?cat=combo" class="btn-primary" style="margin-top:1rem;display:block;text-align:center;font-size:.85rem;">Order Now</a>
            <?php else: ?>
              <a href="https://wa.me/918895838987?text=<?= urlencode($p['name']) ?>" target="_blank" class="btn-wa">Order on WhatsApp</a>
            <?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </section>

    <section id="led-frames" style="margin-bottom:5rem;">
      <div class="section-sep"></div>
      <h2 class="font-serif" style="font-size:1.75rem;font-weight:800;margin-bottom:2rem;">LED Frames</h2>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;max-width:600px;">
        <?php foreach ($led as $p): ?>
        <div class="led-card">
          <div><h3><?= htmlspecialchars($p['name']) ?></h3><p class="sub">Premium Backlit Frame</p></div>
          <div class="price-big">₹<?= number_format($p['price']) ?></div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php if (isPhotographer()): ?>
        <a href="/photographer/shop.php?cat=led_frame" class="btn-primary" style="margin-top:1.5rem;display:inline-block;">Order LED Frames</a>
      <?php endif; ?>
    </section>

    <section id="wall-decor">
      <div class="section-sep"></div>
      <h2 class="font-serif" style="font-size:1.75rem;font-weight:800;margin-bottom:2rem;">Wall Acrylic &amp; Canvas</h2>
      <div class="table-wrap">
        <table>
          <thead><tr><th>Size (Inches)</th><th>Price (₹)</th><?php if (isPhotographer()): ?><th>Action</th><?php endif; ?></tr></thead>
          <tbody>
            <?php foreach ($acrylic as $p):
              $sizes = json_decode($p['sizes'], true);
            ?>
            <tr>
              <td><?= htmlspecialchars($sizes[0] ?? '') ?></td>
              <td>₹<?= number_format($p['price']) ?></td>
              <?php if (isPhotographer()): ?>
              <td><a href="/photographer/shop.php?cat=wall_acrylic" style="color:var(--primary);font-weight:600;font-size:.85rem;">Order</a></td>
              <?php endif; ?>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
