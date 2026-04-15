<?php
$pageTitle = 'Our Products – SD Colours Photobook Lab';
$pageDesc = 'Explore wedding albums, combo photo pads, LED frames, and wall acrylic prints.';
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
  <p>Explore our curated selection of premium wedding albums, combo photo pads, LED frames, and wall acrylic prints.</p>
</div>

<div class="section bg-accent-light">
  <div class="container">

    <section id="albums" style="margin-bottom:5rem;">
      <div class="section-sep"></div>
      <h2 class="font-serif" style="font-size:1.75rem;font-weight:800;margin-bottom:.5rem;">Photo Album Printing</h2>
      <p style="color:#6b7280;font-size:.9rem;margin-bottom:2rem;">All prices are per page. Select your album type, size, and preferred paper finish.</p>
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:1.5rem;">
        <?php foreach ($albums as $p):
          $sizes = json_decode($p['sizes'], true);
          $features = json_decode($p['features'], true);
          $isPremium = $p['tag'] === 'Premium';
        ?>
        <div class="album-card" style="<?= $isPremium ? 'border-top:3px solid #7c3aed;' : '' ?>">
          <?php if ($isPremium): ?><span style="background:#7c3aed;color:#fff;font-size:.65rem;padding:2px 8px;border-radius:10px;font-weight:700;display:inline-block;margin-bottom:.5rem;">Premium</span><?php endif; ?>
          <h3><?= htmlspecialchars($p['name']) ?></h3>
          <?php if ($p['description']): ?><p style="color:#6b7280;font-size:.78rem;margin-bottom:.75rem;"><?= htmlspecialchars($p['description']) ?></p><?php endif; ?>
          <div class="label-sm">Available Sizes</div>
          <p style="font-size:.85rem;margin-bottom:.75rem;"><?= implode(', ', array_map('htmlspecialchars', $sizes)) ?></p>
          <div class="label-sm mt-4" style="margin-top:.75rem;">Paper Types &amp; Per-Page Pricing</div>
          <ul style="font-size:.8rem;margin-top:.4rem;"><?php foreach ($features as $f): ?><li><?= htmlspecialchars($f) ?></li><?php endforeach; ?></ul>
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
      <style>
        .combo-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.75rem; }
        .combo-card { background: #fff; border-radius: 14px; overflow: hidden; box-shadow: 0 3px 16px rgba(0,0,0,.08); display: flex; flex-direction: column; transition: transform .2s, box-shadow .2s; }
        .combo-card:hover { transform: translateY(-3px); box-shadow: 0 8px 28px rgba(0,0,0,.13); }
        .combo-card-img { position: relative; height: 220px; overflow: hidden; background: #f0f0f0; }
        .combo-card-img img { width: 100%; height: 100%; object-fit: cover; object-position: center; }
        .combo-tag { position: absolute; top: 10px; left: 10px; padding: .25rem .75rem; border-radius: 20px; font-size: .72rem; font-weight: 800; color: #fff; letter-spacing: .03em; }
        .combo-tag-bestseller { background: var(--primary); }
        .combo-tag-premium { background: #7c3aed; }
        .combo-card-body { padding: 1.25rem; flex: 1; display: flex; flex-direction: column; }
        .combo-card-body h3 { font-weight: 800; font-size: 1rem; margin-bottom: .2rem; line-height: 1.35; }
        .combo-card-desc { font-size: .78rem; color: #6b7280; margin-bottom: .75rem; }
        .combo-price { font-size: 1.4rem; font-weight: 800; color: var(--primary); margin-bottom: .75rem; }
        .combo-features { list-style: none; padding: 0; margin: 0 0 .75rem; display: flex; flex-direction: column; gap: .2rem; }
        .combo-features li { font-size: .78rem; color: #4b5563; display: flex; align-items: center; gap: .4rem; }
        .combo-features li::before { content: '✓'; color: var(--primary); font-weight: 800; flex-shrink: 0; }
        .combo-sizes { display: flex; flex-wrap: wrap; gap: .3rem; margin-bottom: .9rem; }
        .combo-size-pill { background: #f3f4f6; color: #374151; padding: .15rem .55rem; border-radius: 4px; font-size: .72rem; font-weight: 700; }
        .combo-card-body .btn-action { margin-top: auto; display: block; text-align: center; padding: .6rem; border-radius: 8px; font-weight: 700; font-size: .85rem; text-decoration: none; }
        .btn-order-now { background: var(--primary); color: #fff; }
        .btn-whatsapp { background: #25D366; color: #fff; }
      </style>
      <div class="combo-grid">
        <?php foreach ($combos as $p):
          $sizes = json_decode($p['sizes'], true);
          $features = json_decode($p['features'], true);
          $imgSrc = $p['image'] ?: '/images/monogram.png';
          $tagClass = $p['tag'] === 'Premium' ? 'combo-tag-premium' : 'combo-tag-bestseller';
        ?>
        <div class="combo-card">
          <div class="combo-card-img">
            <img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($p['name']) ?>" loading="lazy" />
            <?php if ($p['tag']): ?><span class="combo-tag <?= $tagClass ?>"><?= htmlspecialchars($p['tag']) ?></span><?php endif; ?>
          </div>
          <div class="combo-card-body">
            <h3><?= htmlspecialchars($p['name']) ?></h3>
            <?php if ($p['description']): ?><div class="combo-card-desc"><?= htmlspecialchars($p['description']) ?></div><?php endif; ?>
            <div class="combo-price">₹<?= number_format($p['price']) ?><?= $p['price_alt'] ? ' / ₹'.number_format($p['price_alt']) : '' ?></div>
            <ul class="combo-features"><?php foreach (array_slice($features, 0, 5) as $f): ?><li><?= htmlspecialchars($f) ?></li><?php endforeach; ?></ul>
            <div class="combo-sizes"><?php foreach ($sizes as $s): ?><span class="combo-size-pill"><?= htmlspecialchars($s) ?></span><?php endforeach; ?></div>
            <?php if (isPhotographer()): ?>
              <a href="/photographer/shop.php?cat=combo" class="btn-action btn-order-now">Order Now</a>
            <?php else: ?>
              <a href="https://wa.me/918895838987?text=<?= urlencode($p['name']) ?>" target="_blank" class="btn-action btn-whatsapp">Order on WhatsApp</a>
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

    <section id="wall-acrylic">
      <div class="section-sep"></div>
      <h2 class="font-serif" style="font-size:1.75rem;font-weight:800;margin-bottom:.5rem;">Wall Acrylic Photo</h2>
      <p style="color:#6b7280;font-size:.9rem;margin-bottom:2rem;">Premium 5mm thick crystal clear acrylic wall prints for home and studio display.</p>
      <div class="table-wrap">
        <table>
          <thead><tr><th>Product</th><th>Thickness</th><th>Price (₹)</th><?php if (isPhotographer()): ?><th>Action</th><?php endif; ?></tr></thead>
          <tbody>
            <?php foreach ($acrylic as $p):
              $sizes = json_decode($p['sizes'], true);
            ?>
            <tr>
              <td style="font-weight:600;"><?= htmlspecialchars($p['name']) ?></td>
              <td><span style="background:#f3f4f6;padding:2px 8px;border-radius:4px;font-size:.78rem;font-weight:700;">5mm</span></td>
              <td style="font-weight:700;color:var(--primary);">₹<?= number_format($p['price']) ?></td>
              <?php if (isPhotographer()): ?>
              <td><a href="/photographer/shop.php?cat=wall_acrylic" style="color:var(--primary);font-weight:600;font-size:.85rem;">Order</a></td>
              <?php endif; ?>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php if (isPhotographer()): ?>
        <a href="/photographer/shop.php?cat=wall_acrylic" class="btn-primary" style="margin-top:1.5rem;display:inline-block;">Order Wall Acrylic</a>
      <?php endif; ?>
    </section>

  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
