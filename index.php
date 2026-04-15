<?php
$pageTitle = 'SD Colours Photobook Lab – Wedding Album Printing India';
$pageDesc = 'Your Fast & Professional Photobook Printing Partner in Rourkela. Premium wedding albums, acrylic combos, LED frames shipped across India.';
require_once 'includes/header.php';
require_once 'includes/db.php';

$db = getDB();
$featured = $db->query("SELECT * FROM products WHERE active = true AND tag IS NOT NULL ORDER BY sort_order LIMIT 3")->fetchAll();
?>

  <section class="hero">
    <div class="hero-bg"></div>
    <div class="hero-overlay"></div>
    <div class="hero-content">
      <div class="hero-text">
        <h1><span class="text-gradient">Creativity Photobook</span><br>Company in India</h1>
        <p>Your Fast &amp; Professional Printing Partner based in Rourkela. Elevate your memories with premium wedding albums, acrylic combos, and LED frames designed to last a lifetime.</p>
        <div class="hero-buttons">
          <a href="/register.php" class="btn-primary">Get Started – Register</a>
          <a href="/pricing.php" class="btn-outline-white">View Price List →</a>
          <a href="tel:8260754410" class="btn-call">📞 Call Us: 8260754410</a>
        </div>
      </div>
      <div class="hero-monogram">
        <img src="/images/monogram.png" alt="SD Colours Monogram" />
      </div>
    </div>
  </section>

  <div class="trust-bar">
    <div class="trust-grid">
      <div class="trust-card"><div class="trust-icon">🚚</div><div><h3>Shipping All Over India</h3><p>Fast, secure delivery across the nation.</p></div></div>
      <div class="trust-card"><div class="trust-icon">✅</div><div><h3>High Quality Printing</h3><p>HP Indigo commercial printing presses.</p></div></div>
      <div class="trust-card"><div class="trust-icon">🏆</div><div><h3>Premium Wedding Albums</h3><p>Crafted with attention to detail.</p></div></div>
    </div>
  </div>

  <section class="cats-section">
    <div class="container">
      <h2 class="font-serif text-gradient">Our Core Offerings</h2>
      <p class="subtitle">Everything a professional photographer needs to present their best work.</p>
      <div class="cats-grid">
        <a href="/products.php#albums" class="cat-item"><div class="cat-circle">📖</div><span>Wedding Albums</span></a>
        <a href="/products.php#combos" class="cat-item"><div class="cat-circle">📦</div><span>Combo Photo Pads</span></a>
        <a href="/products.php#wall-decor" class="cat-item"><div class="cat-circle">✨</div><span>Acrylic Prints</span></a>
        <a href="/products.php#led-frames" class="cat-item"><div class="cat-circle">💡</div><span>LED Frames</span></a>
        <a href="/products.php" class="cat-item"><div class="cat-circle">🖼️</div><span>Wall Canvas</span></a>
      </div>
    </div>
  </section>

  <section class="section" style="background:#fff;">
    <div class="container">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2.5rem;">
        <h2 class="font-serif" style="font-size:1.8rem;font-weight:800;">Featured Collections</h2>
        <a href="/products.php" style="font-size:.875rem;font-weight:700;color:var(--primary);text-decoration:none;">View all →</a>
      </div>
      <div class="products-grid">
        <?php foreach ($featured as $p):
          $sizes = json_decode($p['sizes'] ?? '[]', true);
          $features = json_decode($p['features'] ?? '[]', true);
          $tagStyle = ($p['tag'] === 'Premium') ? 'background:#7c3aed;' : '';
        ?>
        <div class="product-card">
          <?php if ($p['tag']): ?><span class="product-tag" style="<?= $tagStyle ?>"><?= htmlspecialchars($p['tag']) ?></span><?php endif; ?>
          <div class="product-card-img"><img src="/images/monogram.png" alt="<?= htmlspecialchars($p['name']) ?>" /></div>
          <div class="product-card-body">
            <h3><?= htmlspecialchars($p['name']) ?></h3>
            <div class="product-price">₹<?= number_format($p['price']) ?><?= $p['price_alt'] ? ' / ₹' . number_format($p['price_alt']) : '' ?></div>
            <ul class="product-features"><?php foreach ($features as $f): ?><li><?= htmlspecialchars($f) ?></li><?php endforeach; ?></ul>
            <div class="size-pills"><?php foreach ($sizes as $s): ?><span class="size-pill"><?= htmlspecialchars($s) ?></span><?php endforeach; ?></div>
            <?php if (isPhotographer()): ?>
              <a href="/photographer/shop.php" class="btn-primary" style="margin-top:1rem;display:block;text-align:center;">Order Now</a>
            <?php else: ?>
              <a href="https://wa.me/918895838987?text=Hi!%20I'm%20interested%20in%20<?= urlencode($p['name']) ?>." target="_blank" class="btn-wa">Order on WhatsApp</a>
            <?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <?php if (!isLoggedIn()): ?>
  <section style="background:linear-gradient(135deg,#1a1a2e,#16213e);padding:5rem 0;text-align:center;">
    <div class="container">
      <h2 class="font-serif" style="color:#fff;font-size:2rem;margin-bottom:1rem;">Are You a Professional Photographer?</h2>
      <p style="color:#d1d5db;margin-bottom:2rem;font-size:1.05rem;">Register for a photographer account and order prints directly from our online portal — no WhatsApp needed.</p>
      <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
        <a href="/register.php" class="btn-primary" style="font-size:1rem;padding:.875rem 2.5rem;">Create Photographer Account</a>
        <a href="/login.php" class="btn-outline-white" style="font-size:1rem;padding:.875rem 2.5rem;">Sign In</a>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <section class="cta-section">
    <h2 class="font-serif">Start Your Wedding Album Today</h2>
    <p>Join hundreds of professional photographers who trust SD Colours Photobook Lab for their premium printing needs.</p>
    <a href="https://wa.me/918895838987" target="_blank" rel="noopener noreferrer" class="btn-primary" style="font-size:1rem;padding:.875rem 2.5rem;">Chat with us on WhatsApp</a>
  </section>

<?php require_once 'includes/footer.php'; ?>
