<?php
$pageTitle = 'About Us – SD Colours Photobook Lab';
$pageDesc = 'Learn about SD Colours Photobook Lab, your trusted printing partner in Rourkela, India.';
require_once 'includes/header.php';
?>
<div class="page-hero">
  <h1 class="font-serif text-gradient">About SD Colours</h1>
  <p>Your trusted Creativity Photobook Company in India.</p>
</div>
<div class="section" style="background:#fff;">
  <div class="container">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:4rem;align-items:center;max-width:1000px;margin:0 auto;" class="about-grid">
      <div>
        <h2 class="font-serif" style="font-size:1.75rem;font-weight:800;margin-bottom:1rem;">Who We Are</h2>
        <p style="color:#6b7280;line-height:1.8;margin-bottom:1rem;">SD Colours Photobook Lab is a premium print lab based in Rourkela, Odisha, India. We specialise in producing high-quality wedding albums, combo photo pads, LED frames, acrylic prints, and wall canvases for professional photographers across India.</p>
        <p style="color:#6b7280;line-height:1.8;margin-bottom:1rem;">We use HP Indigo commercial printing presses to ensure every print meets the highest standard of colour accuracy and longevity. Our team of skilled craftsmen hand-assemble each album with meticulous attention to detail.</p>
        <p style="color:#6b7280;line-height:1.8;">From single pieces to bulk orders, we ship across India with fast and secure delivery — helping photographers deliver unforgettable memories to their clients.</p>
      </div>
      <div style="background:linear-gradient(135deg,#1a1a2e,#16213e);border-radius:16px;padding:2.5rem;text-align:center;">
        <img src="/images/logo.png" alt="SD Colours Logo" style="max-width:180px;margin-bottom:1.5rem;" />
        <p style="color:#d1d5db;font-size:1rem;line-height:1.7;">"Turning your captured moments into timeless masterpieces."</p>
      </div>
    </div>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:2rem;margin-top:4rem;" class="stats-grid">
      <div style="text-align:center;padding:2rem;background:#f8f9fa;border-radius:12px;">
        <div style="font-size:2.5rem;font-weight:800;color:var(--primary);">500+</div>
        <div style="font-weight:600;margin-top:.5rem;">Photographers Served</div>
      </div>
      <div style="text-align:center;padding:2rem;background:#f8f9fa;border-radius:12px;">
        <div style="font-size:2.5rem;font-weight:800;color:var(--primary);">10,000+</div>
        <div style="font-weight:600;margin-top:.5rem;">Albums Delivered</div>
      </div>
      <div style="text-align:center;padding:2rem;background:#f8f9fa;border-radius:12px;">
        <div style="font-size:2.5rem;font-weight:800;color:var(--primary);">Pan India</div>
        <div style="font-weight:600;margin-top:.5rem;">Shipping Coverage</div>
      </div>
    </div>
  </div>
</div>
<style>@media(max-width:768px){.about-grid{grid-template-columns:1fr!important;gap:2rem!important;}.stats-grid{grid-template-columns:1fr!important;}}</style>
<?php require_once 'includes/footer.php'; ?>
