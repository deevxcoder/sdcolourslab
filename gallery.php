<?php
$pageTitle = 'Gallery – SD Colours Photobook Lab';
$pageDesc = 'Showcase of premium wedding albums and photo products by SD Colours.';
require_once 'includes/header.php';
?>
<div class="page-hero">
  <h1 class="font-serif text-gradient">Our Gallery</h1>
  <p>A showcase of our finest work — premium wedding albums and photography print products.</p>
</div>
<div class="section" style="background:#f8f9fa;">
  <div class="container">
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1.5rem;">
      <?php
      $items = [
        ['emoji'=>'📖','title'=>'Premium Wedding Album','desc'=>'12x18 Metallic Velvet finish'],
        ['emoji'=>'📦','title'=>'Leather Combo Pad','desc'=>'18x24 with luxury bag'],
        ['emoji'=>'💡','title'=>'LED Backlit Frame','desc'=>'12x18 backlit display'],
        ['emoji'=>'✨','title'=>'Acrylic Wall Print','desc'=>'16x20 high-gloss acrylic'],
        ['emoji'=>'🖼️','title'=>'Canvas Wall Art','desc'=>'20x30 premium canvas'],
        ['emoji'=>'📖','title'=>'Metallic Album','desc'=>'12x15 Pearl finish'],
        ['emoji'=>'📦','title'=>'Gold Series Combo','desc'=>'12x30 with premium bag'],
        ['emoji'=>'💡','title'=>'LED Mini Frame','desc'=>'8x12 backlit display'],
        ['emoji'=>'✨','title'=>'Acrylic Print','desc'=>'24x36 monument size'],
      ];
      foreach ($items as $item): ?>
      <div style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.06);">
        <div style="background:linear-gradient(135deg,#1a1a2e,#16213e);height:200px;display:flex;align-items:center;justify-content:center;font-size:4rem;"><?= $item['emoji'] ?></div>
        <div style="padding:1.25rem;">
          <h3 style="font-weight:700;margin-bottom:.25rem;"><?= htmlspecialchars($item['title']) ?></h3>
          <p style="color:#6b7280;font-size:.875rem;"><?= htmlspecialchars($item['desc']) ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div style="margin-top:3rem;text-align:center;">
      <p style="color:#6b7280;margin-bottom:1rem;">Want to see more? Reach out to us for a full product catalogue.</p>
      <a href="https://wa.me/918895838987" target="_blank" class="btn-wa">Request Catalogue on WhatsApp</a>
    </div>
  </div>
</div>
<?php require_once 'includes/footer.php'; ?>
