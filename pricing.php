<?php
$pageTitle = 'Pricing – SD Colours Photobook Lab';
$pageDesc = 'Transparent pricing for all our photo products.';
require_once 'includes/header.php';
require_once 'includes/db.php';

$db = getDB();
$combos = $db->query("SELECT * FROM products WHERE category='combo' AND active=true ORDER BY sort_order")->fetchAll();
$led = $db->query("SELECT * FROM products WHERE category='led_frame' AND active=true ORDER BY sort_order")->fetchAll();
$acrylic = $db->query("SELECT * FROM products WHERE category='wall_acrylic' AND active=true ORDER BY sort_order")->fetchAll();
?>
<div class="page-hero">
  <h1 class="font-serif text-gradient">Our Pricing</h1>
  <p>Transparent pricing for all products. No hidden fees.</p>
</div>

<div class="section bg-accent-light">
  <div class="container">

    <section style="margin-bottom:4rem;">
      <div class="section-sep"></div>
      <h2 class="font-serif" style="font-size:1.75rem;font-weight:800;margin-bottom:2rem;">Combo Photo Pads</h2>
      <div class="table-wrap">
        <table>
          <thead><tr><th>Product</th><th>Price (₹)</th><th>Sizes</th><th>Includes</th></tr></thead>
          <tbody>
            <?php foreach ($combos as $p):
              $sizes = json_decode($p['sizes'], true);
              $features = json_decode($p['features'], true);
            ?>
            <tr>
              <td><strong><?= htmlspecialchars($p['name']) ?></strong><?php if ($p['tag']): ?> <span style="background:var(--primary);color:#fff;font-size:.65rem;padding:2px 6px;border-radius:4px;font-weight:700;"><?= htmlspecialchars($p['tag']) ?></span><?php endif; ?></td>
              <td>₹<?= number_format($p['price']) ?><?= $p['price_alt'] ? ' / ₹'.number_format($p['price_alt']) : '' ?></td>
              <td><?= implode(', ', $sizes) ?></td>
              <td><?= implode(', ', $features) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

    <section style="margin-bottom:4rem;">
      <div class="section-sep"></div>
      <h2 class="font-serif" style="font-size:1.75rem;font-weight:800;margin-bottom:2rem;">LED Frames</h2>
      <div class="table-wrap">
        <table>
          <thead><tr><th>Product</th><th>Price (₹)</th></tr></thead>
          <tbody>
            <?php foreach ($led as $p): ?>
            <tr><td><?= htmlspecialchars($p['name']) ?></td><td>₹<?= number_format($p['price']) ?></td></tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

    <section>
      <div class="section-sep"></div>
      <h2 class="font-serif" style="font-size:1.75rem;font-weight:800;margin-bottom:.5rem;">Wall Acrylic Photo</h2>
      <p style="color:#6b7280;font-size:.9rem;margin-bottom:1.5rem;">Premium 5mm crystal clear acrylic wall prints.</p>
      <div class="table-wrap">
        <table>
          <thead><tr><th>Product</th><th>Thickness</th><th>Price (₹)</th></tr></thead>
          <tbody>
            <?php foreach ($acrylic as $p): ?>
            <tr>
              <td style="font-weight:600;"><?= htmlspecialchars($p['name']) ?></td>
              <td><span style="background:#f3f4f6;padding:2px 8px;border-radius:4px;font-size:.78rem;font-weight:700;">5mm</span></td>
              <td style="font-weight:700;color:var(--primary);">₹<?= number_format($p['price']) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

    <div style="margin-top:3rem;background:#fff;border-radius:12px;padding:2rem;text-align:center;">
      <h3 style="font-weight:800;margin-bottom:.5rem;">Need a Custom Quote?</h3>
      <p style="color:#6b7280;margin-bottom:1.5rem;">For bulk orders or custom sizes, contact us directly on WhatsApp.</p>
      <a href="https://wa.me/918895838987" target="_blank" class="btn-wa">Chat on WhatsApp</a>
    </div>

  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
