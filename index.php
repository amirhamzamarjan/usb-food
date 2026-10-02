<?php
/**
 * US BENGOL - Main Homepage (Production Ready)
 * Brand: US BENGOL (Traditional Bangladeshi Food Products)
 */

define('USBENGOL_INIT', true);
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/helpers.php';

// Fetch products & site content dynamically
$products = [];
$content = [];

try {
    $pdo = Database::getConnection();

    // Fetch site content
    $contentStmt = $pdo->query("SELECT key_name, content_value FROM site_content");
    while ($row = $contentStmt->fetch()) {
        $content[$row['key_name']] = $row['content_value'];
    }

    // Fetch active products
    $prodStmt = $pdo->query("SELECT * FROM products WHERE is_active = 1 ORDER BY sort_order ASC, id ASC");
    $products = $prodStmt->fetchAll();

} catch (Exception $e) {
    error_log("Homepage Database fetch error: " . $e->getMessage());
}

// Fallback content if database was empty
$heroHeadline = $content['hero_headline'] ?? 'Traditional Taste, Made with Care.';
$heroSubtitle = $content['hero_subtitle'] ?? 'US BENGOL brings traditional Bangladeshi food staples, homemade care, and authentic village-inspired warmth to modern family tables across Bangladesh.';
$storyTitle   = $content['story_title'] ?? 'Rooted in Tradition';
$storySub     = $content['story_subtitle'] ?? 'A commitment to authentic taste, simple ingredients, and the timeless comfort of home-cooked meals.';
$address      = $content['contact_address'] ?? BUSINESS_ADDRESS;
$phone        = $content['contact_phone'] ?? PHONE_NUMBER;
$whatsapp     = $content['contact_whatsapp'] ?? WHATSAPP_NUMBER;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">

    <!-- Primary Meta Tags -->
    <title>US BENGOL • Traditional Taste, Made with Care | Premium Bangladeshi Food</title>
    <meta name="title" content="US BENGOL • Traditional Taste, Made with Care">
    <meta name="description" content="US BENGOL specializes in traditional Bangladeshi food products including Atta Roti, Maida Roti, Parota, Ghee, Flour, and Diabetes-Friendly Roti. Homemade care with nationwide delivery across Bangladesh.">
    <meta name="keywords" content="US BENGOL, atta roti, maida roti, parota, ghee, diabetes friendly roti, bangladeshi traditional food, dhaka food delivery, bulk food supply">
    <meta name="author" content="US BENGOL">
    <link rel="canonical" href="https://usbengol.com/">
    <link rel="icon" type="image/svg+xml" href="images/favicon.svg">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://usbengol.com/">
    <meta property="og:title" content="US BENGOL • Traditional Taste, Made with Care">
    <meta property="og:description" content="Authentic Bangladeshi food staples prepared with traditional care. Available for retail and bulk delivery across Bangladesh.">
    <meta property="og:image" content="images/logo.png">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/components.css">
    <link rel="stylesheet" href="css/responsive.css">
</head>
<body>

    <!-- =========================================================
         NAVIGATION BAR (FLOATING & STICKY)
         ========================================================= -->
    <header class="site-header" id="navbar">
        <div class="container">
            <div class="nav-wrapper">
                <a href="index.php" class="brand-logo-link" aria-label="US BENGOL Homepage">
                    <img src="images/logo.png" alt="US BENGOL Logo" class="brand-logo-img">
                </a>

                <nav class="nav-menu" aria-label="Primary Navigation">
                    <a href="#home" class="nav-link active">Home</a>
                    <a href="#products" class="nav-link">Our Products</a>
                    <a href="#story" class="nav-link">Our Story</a>
                    <a href="#how-we-prepare" class="nav-link">How We Prepare</a>
                    <a href="#bulk-orders" class="nav-link">Bulk Orders</a>
                    <a href="#contact" class="nav-link">Contact</a>
                </nav>

                <div class="nav-actions">
                    <a href="https://wa.me/<?= htmlspecialchars($whatsapp) ?>?text=<?= urlencode('Hello US BENGOL, I would like to place an order.') ?>" target="_blank" rel="noopener" class="btn btn-whatsapp btn-sm">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0012.04 2z"/></svg>
                        <span>Order Now</span>
                    </a>

                    <button class="mobile-toggle" aria-label="Open Navigation Menu">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Mobile Drawer Menu -->
    <div class="drawer-backdrop"></div>
    <div class="mobile-drawer" role="dialog" aria-modal="true">
        <div>
            <div class="drawer-header">
                <img src="images/logo.png" alt="US BENGOL" style="height: 38px;">
                <button class="drawer-close" aria-label="Close Menu">&times;</button>
            </div>
            <nav class="drawer-menu">
                <a href="#home" class="drawer-link">Home</a>
                <a href="#products" class="drawer-link">Our Products</a>
                <a href="#story" class="drawer-link">Our Story</a>
                <a href="#how-we-prepare" class="drawer-link">How We Prepare</a>
                <a href="#bulk-orders" class="drawer-link">Bulk Orders</a>
                <a href="#contact" class="drawer-link">Contact</a>
            </nav>
        </div>
        <div class="drawer-footer">
            <a href="https://wa.me/<?= htmlspecialchars($whatsapp) ?>?text=<?= urlencode('Hello US BENGOL, I would like to order.') ?>" target="_blank" class="btn btn-whatsapp" style="width: 100%;">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0012.04 2z"/></svg>
                Order on WhatsApp
            </a>
            <p style="font-size: 0.82rem; color: var(--color-text-muted); text-align: center; margin-top: 14px;">
                Bawgan, Uttarkhan, Dhaka 1230
            </p>
        </div>
    </div>


    <!-- =========================================================
         HERO SECTION (EDITORIAL BRAND COMPOSITION)
         ========================================================= -->
    <section class="hero-section" id="home">
        <div class="container">
            <div class="hero-grid">
                <div class="hero-content">
                    <div class="hero-tag">
                        <span class="hero-tag-dot"></span>
                        <span>Traditional Bangladeshi Food Heritage</span>
                    </div>

                    <h1 class="hero-title">
                        Traditional Taste, <br>
                        <span class="title-accent">Made with Care.</span>
                    </h1>

                    <div class="hero-bn-pill">
                        ঐতিহ্যবাহী স্বাদ ও মমতা • খাঁটি খাদ্যপণ্য
                    </div>

                    <p class="hero-description">
                        <?= htmlspecialchars($heroSubtitle) ?>
                    </p>

                    <div class="hero-cta-group">
                        <a href="#products" class="btn btn-primary btn-lg">
                            Explore Products
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </a>
                        <a href="https://wa.me/<?= htmlspecialchars($whatsapp) ?>?text=<?= urlencode('Hello US BENGOL, I would like to explore your products.') ?>" target="_blank" rel="noopener" class="btn btn-whatsapp btn-lg">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0012.04 2z"/></svg>
                            Order on WhatsApp
                        </a>
                    </div>

                    <div class="hero-location-cue">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s-8-4.5-8-11.8A8 8 0 0 1 12 2a8 8 0 0 1 8 8.2c0 7.3-8 11.8-8 11.8z"/><circle cx="12" cy="10" r="3"/></svg>
                        <span>Prepared in Uttarkhan, Dhaka • Delivered Across Bangladesh</span>
                    </div>
                </div>

                <div class="hero-visual">
                    <div class="hero-visual-card">
                        <!-- Floating Micro Badge (Top) -->
                        <div class="floating-badge badge-top">
                            <div class="badge-icon-box">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" style="width:20px; height:20px;"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                            </div>
                            <div>
                                <div class="badge-text-primary">Daily Fresh</div>
                                <div class="badge-text-secondary">Authentic Layers</div>
                            </div>
                        </div>

                        <!-- Main Showcase Product (Parota / Roti) -->
                        <img src="images/porota.png" alt="US BENGOL Traditional Parota" class="hero-main-img">

                        <!-- Floating Micro Badge (Bottom) -->
                        <div class="floating-badge badge-bottom">
                            <div class="badge-icon-box">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" style="width:20px; height:20px;"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                            </div>
                            <div>
                                <div class="badge-text-primary">Homemade Care</div>
                                <div class="badge-text-secondary">Pure Traditional Taste</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- =========================================================
         TRUST & VALUE STRIP (COMPACT & HONEST)
         ========================================================= -->
    <section class="trust-strip">
        <div class="container">
            <div class="trust-grid">
                <div class="trust-card">
                    <div class="trust-icon-box">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:24px; height:24px;"><circle cx="12" cy="12" r="10"></circle><path d="M12 6v6l4 2"></path></svg>
                    </div>
                    <div>
                        <h4 class="trust-card-title">Traditional Taste</h4>
                        <p class="trust-card-desc">Rooted in familiar Bangladeshi food traditions.</p>
                    </div>
                </div>

                <div class="trust-card">
                    <div class="trust-icon-box">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:24px; height:24px;"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    </div>
                    <div>
                        <h4 class="trust-card-title">Made with Care</h4>
                        <p class="trust-card-desc">Prepared with attention to quality and consistency.</p>
                    </div>
                </div>

                <div class="trust-card">
                    <div class="trust-icon-box">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:24px; height:24px;"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                    </div>
                    <div>
                        <h4 class="trust-card-title">Nationwide Delivery</h4>
                        <p class="trust-card-desc">Available for customers across Bangladesh.</p>
                    </div>
                </div>

                <div class="trust-card">
                    <div class="trust-icon-box">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:24px; height:24px;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    </div>
                    <div>
                        <h4 class="trust-card-title">Retail & Bulk Orders</h4>
                        <p class="trust-card-desc">For everyday households and institutional supply.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- =========================================================
         PRODUCT SHOWCASE ("OUR PRODUCTS")
         ========================================================= -->
    <section class="section" id="products">
        <div class="container">
            <div class="section-header">
                <span class="badge-tag gold">Pure Staple Foods</span>
                <h2 class="section-title">Our Traditional Products</h2>
                <p class="section-subtitle">
                    Crafted with natural ingredients, traditional techniques, and the warmth of homemade cooking.
                </p>
            </div>

            <!-- Category Tabs -->
            <div class="product-filter-tabs">
                <button type="button" class="filter-tab-btn active" data-category="all">All Products</button>
                <button type="button" class="filter-tab-btn" data-category="roti">Roti & Parota</button>
                <button type="button" class="filter-tab-btn" data-category="staple">Ghee & Staples</button>
                <button type="button" class="filter-tab-btn" data-category="dietary">Special Dietary</button>
                <button type="button" class="filter-tab-btn" data-category="flour">Flour & Grains</button>
            </div>

            <!-- Dynamic Product Grid Container -->
            <div class="products-grid" id="products-grid-container">
                <?php foreach ($products as $product):
                    $isDietary = ($product['category'] === 'dietary');
                    $badgeClass = $isDietary ? 'dietary' : (str_contains(strtolower($product['badge'] ?? ''), 'pure') ? 'gold' : '');
                    $priceFormatted = ((float)$product['price'] > 0) ? format_price($product['price']) : '৳0.00';
                    $waLink = get_whatsapp_order_link($product['name'], 1, (float)$product['price']);
                ?>
                <div class="product-card" data-id="<?= $product['id'] ?>" data-slug="<?= htmlspecialchars($product['slug']) ?>">
                    <div class="product-card-media">
                        <?php if (!empty($product['badge'])): ?>
                            <span class="product-card-badge <?= $badgeClass ?>"><?= htmlspecialchars($product['badge']) ?></span>
                        <?php endif; ?>
                        <img src="<?= htmlspecialchars($product['image'] ?: 'images/ataruti.png') ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-card-img" loading="lazy">
                    </div>
                    <div class="product-card-body">
                        <div class="product-header-row">
                            <div>
                                <h3 class="product-title"><?= htmlspecialchars($product['name']) ?></h3>
                                <?php if (!empty($product['name_bn'])): ?>
                                    <span class="product-title-bn"><?= htmlspecialchars($product['name_bn']) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="product-price-box">
                                <span class="product-price"><?= $priceFormatted ?></span>
                                <span class="product-unit"><?= htmlspecialchars($product['unit'] ?? 'pack') ?></span>
                            </div>
                        </div>
                        <p class="product-desc"><?= htmlspecialchars($product['short_description']) ?></p>
                        <div class="product-card-footer">
                            <button type="button" class="btn btn-outline btn-sm btn-quick-view" data-id="<?= $product['id'] ?>">
                                View Details
                            </button>
                            <a href="<?= $waLink ?>" target="_blank" rel="noopener" class="btn btn-whatsapp btn-sm">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0012.04 2z"/></svg>
                                Order
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Special Spotlight: Diabetes-Friendly Roti -->
            <div class="dietary-spotlight-card">
                <div>
                    <span class="badge-tag green">Thoughtful Formulation</span>
                    <h3 style="font-size: 2rem; font-weight: 800; color: var(--color-brown-900); margin-bottom: 12px;">
                        Diabetes-Friendly Roti
                    </h3>
                    <p style="font-size: 1.05rem; color: var(--color-text-body); line-height: 1.65; margin-bottom: 20px;">
                        A thoughtfully positioned option for customers who pay closer attention to their dietary choices. Prepared with deliberate care, high-fiber natural grains, and traditional rolling techniques.
                    </p>

                    <div class="dietary-features-list">
                        <div class="dietary-feature-item">
                            <span class="dietary-feature-icon">✓</span>
                            <span>Selected High-Fiber Grain Blend</span>
                        </div>
                        <div class="dietary-feature-item">
                            <span class="dietary-feature-icon">✓</span>
                            <span>Natural Soft Homemade Texture</span>
                        </div>
                        <div class="dietary-feature-item">
                            <span class="dietary-feature-icon">✓</span>
                            <span>Zero Added Sugars or Fillers</span>
                        </div>
                        <div class="dietary-feature-item">
                            <span class="dietary-feature-icon">✓</span>
                            <span>Hygienically Rolled & Packaged</span>
                        </div>
                    </div>

                    <div style="display: flex; gap: 14px; flex-wrap: wrap;">
                        <a href="https://wa.me/<?= htmlspecialchars($whatsapp) ?>?text=<?= urlencode('Hello US BENGOL, I would like to inquire about the Diabetes-Friendly Roti.') ?>" target="_blank" class="btn btn-primary">
                            Inquire on WhatsApp
                        </a>
                        <button type="button" class="btn btn-outline" onclick="openProductModalById(4)">
                            View Product Details
                        </button>
                    </div>
                </div>

                <div style="text-align: center;">
                    <img src="images/diabetes-roti.svg" alt="US BENGOL Diabetes-Friendly Roti" style="max-height: 320px; margin: 0 auto; filter: drop-shadow(0 16px 30px rgba(42, 24, 16, 0.12));">
                </div>
            </div>
        </div>
    </section>


    <!-- =========================================================
         WHY CHOOSE US BENGOL (EMOTIONAL & TRUSTWORTHY)
         ========================================================= -->
    <section class="section section-alt" id="why-us">
        <div class="container">
            <div class="section-header">
                <span class="badge-tag">Our Promise</span>
                <h2 class="section-title">Why Choose US BENGOL?</h2>
                <p class="section-subtitle">
                    We believe everyday food should feel honest, nourishing, and true to traditional Bangladeshi roots.
                </p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 28px;">
                <div style="background: #FFFFFF; border: 1px solid var(--color-border); border-radius: var(--radius-lg); padding: 36px 28px; box-shadow: var(--shadow-sm);">
                    <div style="width: 52px; height: 52px; border-radius: 14px; background: #FAF6EF; border: 1px solid var(--color-border); display: flex; align-items: center; justify-content: center; color: var(--color-green-deep); margin-bottom: 20px;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:26px; height:26px;"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                    </div>
                    <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 10px;">Familiar Traditional Taste</h3>
                    <p style="color: var(--color-text-body); font-size: 0.95rem; line-height: 1.6;">
                        Our recipes follow authentic Bangladeshi kitchen traditions, delivering the comforting taste you grew up loving at the family morning table.
                    </p>
                </div>

                <div style="background: #FFFFFF; border: 1px solid var(--color-border); border-radius: var(--radius-lg); padding: 36px 28px; box-shadow: var(--shadow-sm);">
                    <div style="width: 52px; height: 52px; border-radius: 14px; background: #FAF6EF; border: 1px solid var(--color-border); display: flex; align-items: center; justify-content: center; color: var(--color-green-deep); margin-bottom: 20px;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:26px; height:26px;"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                    </div>
                    <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 10px;">Homemade-Inspired Care</h3>
                    <p style="color: var(--color-text-body); font-size: 0.95rem; line-height: 1.6;">
                        Prepared with small-batch attention, clean handling, and respectful culinary standards just like food made by caring hands at home.
                    </p>
                </div>

                <div style="background: #FFFFFF; border: 1px solid var(--color-border); border-radius: var(--radius-lg); padding: 36px 28px; box-shadow: var(--shadow-sm);">
                    <div style="width: 52px; height: 52px; border-radius: 14px; background: #FAF6EF; border: 1px solid var(--color-border); display: flex; align-items: center; justify-content: center; color: var(--color-green-deep); margin-bottom: 20px;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:26px; height:26px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    </div>
                    <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 10px;">Consistent Quality & Texture</h3>
                    <p style="color: var(--color-text-body); font-size: 0.95rem; line-height: 1.6;">
                        Every pack is evenly rolled, soft, and dependable. No unexpected dryness, no artificial aftertaste—just pure everyday satisfaction.
                    </p>
                </div>

                <div style="background: #FFFFFF; border: 1px solid var(--color-border); border-radius: var(--radius-lg); padding: 36px 28px; box-shadow: var(--shadow-sm);">
                    <div style="width: 52px; height: 52px; border-radius: 14px; background: #FAF6EF; border: 1px solid var(--color-border); display: flex; align-items: center; justify-content: center; color: var(--color-green-deep); margin-bottom: 20px;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:26px; height:26px;"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                    </div>
                    <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 10px;">Family-Friendly Food Choices</h3>
                    <p style="color: var(--color-text-body); font-size: 0.95rem; line-height: 1.6;">
                        From classic whole wheat rotis to rich layered parotas and mindful dietary formulations, we offer nutritious staples for all ages.
                    </p>
                </div>

                <div style="background: #FFFFFF; border: 1px solid var(--color-border); border-radius: var(--radius-lg); padding: 36px 28px; box-shadow: var(--shadow-sm);">
                    <div style="width: 52px; height: 52px; border-radius: 14px; background: #FAF6EF; border: 1px solid var(--color-border); display: flex; align-items: center; justify-content: center; color: var(--color-green-deep); margin-bottom: 20px;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:26px; height:26px;"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                    </div>
                    <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 10px;">Nationwide Reach</h3>
                    <p style="color: var(--color-text-body); font-size: 0.95rem; line-height: 1.6;">
                        Conveniently accessible across Dhaka and all 64 districts in Bangladesh with reliable courier and transport coordination.
                    </p>
                </div>

                <div style="background: #FFFFFF; border: 1px solid var(--color-border); border-radius: var(--radius-lg); padding: 36px 28px; box-shadow: var(--shadow-sm);">
                    <div style="width: 52px; height: 52px; border-radius: 14px; background: #FAF6EF; border: 1px solid var(--color-border); display: flex; align-items: center; justify-content: center; color: var(--color-green-deep); margin-bottom: 20px;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:26px; height:26px;"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    </div>
                    <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 10px;">Retail & Bulk Supply Capability</h3>
                    <p style="color: var(--color-text-body); font-size: 0.95rem; line-height: 1.6;">
                        Seamless supply capability for individual households as well as restaurants, hotels, canteens, and catering partners.
                    </p>
                </div>
            </div>
        </div>
    </section>


    <!-- =========================================================
         BRAND STORY ("ROOTED IN TRADITION")
         ========================================================= -->
    <section class="section" id="story">
        <div class="container">
            <div class="story-grid">
                <div class="story-visual-frame">
                    <div class="story-card-inner">
                        <div class="story-quote-mark">“</div>
                        <p class="story-quote-text">
                            Food is at its finest when it carries the quiet warmth of home, simple ingredients, and genuine care.
                        </p>
                        <div class="story-quote-author">US BENGOL • আমাদের ঐতিহ্য</div>
                    </div>
                </div>

                <div class="story-paragraphs">
                    <span class="badge-tag gold">Brand Heritage</span>
                    <h2 class="section-title"><?= htmlspecialchars($storyTitle) ?></h2>
                    <p class="lead" style="color: var(--color-brown-700); margin-bottom: 24px;">
                        <?= htmlspecialchars($storySub) ?>
                    </p>
                    <p>
                        In every Bangladeshi home, the morning aroma of freshly puffed rotis, golden flaky parotas on the tawa, and the rich richness of pure ghee represent more than just breakfast—they symbolize family togetherness, warmth, and care.
                    </p>
                    <p>
                        At <strong>US BENGOL</strong>, we bridge the nostalgia of traditional village-kitchen authenticity with the quality and hygienic consistency expected by modern families and foodservice businesses.
                    </p>
                    <p>
                        From our location in Uttarkhan, Dhaka, we take pride in carefully selecting pure wheat flours, respecting traditional dough-kneading wisdom, and delivering consistent everyday staples across Bangladesh.
                    </p>
                    <div style="margin-top: 28px;">
                        <a href="#how-we-prepare" class="btn btn-outline">
                            Discover How We Prepare
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- =========================================================
         HOW WE PREPARE (5-STEP JOURNEY TIMELINE)
         ========================================================= -->
    <section class="section section-alt" id="how-we-prepare">
        <div class="container">
            <div class="section-header">
                <span class="badge-tag">Our Process</span>
                <h2 class="section-title">How We Prepare</h2>
                <p class="section-subtitle">
                    A careful, step-by-step journey focused on purity, consistency, and freshness.
                </p>
            </div>

            <div class="prepare-timeline">
                <div class="timeline-step">
                    <div class="step-number-circle">01</div>
                    <div>
                        <h3 class="step-title">Carefully Selected</h3>
                        <p class="step-desc">Quality-focused raw materials and premium grains are chosen for preparation.</p>
                    </div>
                </div>

                <div class="timeline-step">
                    <div class="step-number-circle">02</div>
                    <div>
                        <h3 class="step-title">Traditional Preparation</h3>
                        <p class="step-desc">The process is inspired by familiar, time-tested Bangladeshi food traditions.</p>
                    </div>
                </div>

                <div class="timeline-step">
                    <div class="step-number-circle">03</div>
                    <div>
                        <h3 class="step-title">Prepared with Care</h3>
                        <p class="step-desc">Products are handled and prepared with close attention to texture and consistency.</p>
                    </div>
                </div>

                <div class="timeline-step">
                    <div class="step-number-circle">04</div>
                    <div>
                        <h3 class="step-title">Freshness in Focus</h3>
                        <p class="step-desc">Special attention is given to preserving natural aroma and freshness.</p>
                    </div>
                </div>

                <div class="timeline-step">
                    <div class="step-number-circle">05</div>
                    <div>
                        <h3 class="step-title">Ready for Your Table</h3>
                        <p class="step-desc">Finished products are packaged cleanly for customers and reliable delivery.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- =========================================================
         HOMEMADE / VILLAGE ATMOSPHERE SECTION
         ========================================================= -->
    <section class="section">
        <div class="container">
            <div style="background: radial-gradient(circle at 80% 20%, #FAF5ED 0%, #EFE5D4 100%); border: 1px solid var(--color-border); border-radius: var(--radius-xl); padding: 56px 48px; position: relative; overflow: hidden;">
                <div style="max-width: 640px;">
                    <span class="badge-tag">Familiar Kitchen Warmth</span>
                    <h2 style="font-size: 2.4rem; font-weight: 800; color: var(--color-brown-900); margin-bottom: 16px;">
                        From a Familiar Kitchen to Your Table
                    </h2>
                    <p style="font-size: 1.12rem; color: var(--color-text-body); line-height: 1.7; margin-bottom: 28px;">
                        There is something irreplaceable about simple food made with devotion. Whether it is a quiet family breakfast before a busy workday or a festive weekend meal with loved ones, US BENGOL brings the comfort of authentic homemade staples straight to your home.
                    </p>
                    <div style="display: flex; gap: 16px; flex-wrap: wrap;">
                        <a href="https://wa.me/<?= htmlspecialchars($whatsapp) ?>?text=<?= urlencode('Hello US BENGOL, I would like to order fresh rotis and parotas.') ?>" target="_blank" class="btn btn-primary">
                            Order for Home
                        </a>
                        <a href="#bulk-orders" class="btn btn-outline">
                            Inquire for Business Supply
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- =========================================================
         BULK & INSTITUTIONAL ORDERS SECTION
         ========================================================= -->
    <section class="section section-alt" id="bulk-orders">
        <div class="container">
            <div class="bulk-section-card">
                <div class="section-header">
                    <span class="badge-tag gold">Commercial & Institutional Supply</span>
                    <h2 class="section-title">Need a Regular Supply?</h2>
                    <p class="section-subtitle">
                        We partner with food businesses, organizations, and institutions that require consistent, high-grade traditional staples delivered on schedule.
                    </p>
                </div>

                <div class="bulk-targets-grid">
                    <div class="bulk-target-box">
                        <svg class="bulk-target-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8h1a4 4 0 0 1 0 8h-1"></path><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path><line x1="6" y1="1" x2="6" y2="4"></line><line x1="10" y1="1" x2="10" y2="4"></line><line x1="14" y1="1" x2="14" y2="4"></line></svg>
                        <h4 class="bulk-target-name">Restaurants & Cafés</h4>
                    </div>

                    <div class="bulk-target-box">
                        <svg class="bulk-target-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                        <h4 class="bulk-target-name">Hotels & Resorts</h4>
                    </div>

                    <div class="bulk-target-box">
                        <svg class="bulk-target-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path></svg>
                        <h4 class="bulk-target-name">Catering & Events</h4>
                    </div>

                    <div class="bulk-target-box">
                        <svg class="bulk-target-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                        <h4 class="bulk-target-name">Corporate & Canteens</h4>
                    </div>
                </div>

                <div style="text-align: center;">
                    <button type="button" class="btn btn-primary btn-lg trigger-bulk-modal">
                        Request a Bulk Order
                    </button>
                    <p style="font-size: 0.88rem; color: var(--color-text-muted); margin-top: 14px;">
                        Custom packaging, scheduled daily deliveries, and flexible batch quantities available.
                    </p>
                </div>
            </div>
        </div>
    </section>


    <!-- =========================================================
         CONTACT & DELIVERY SECTION
         ========================================================= -->
    <section class="section" id="contact">
        <div class="container">
            <div class="section-header">
                <span class="badge-tag">Get in Touch</span>
                <h2 class="section-title">Contact & Orders</h2>
                <p class="section-subtitle">
                    Traditional favorites, delivered across Bangladesh. Reach out directly for personal orders or business inquiries.
                </p>
            </div>

            <div class="contact-grid">
                <div class="contact-info-card">
                    <div class="contact-item">
                        <div class="contact-icon-circle">
                            <svg viewBox="0 0 24 24" fill="currentColor" style="width:24px; height:24px;"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0012.04 2z"/></svg>
                        </div>
                        <div>
                            <div class="contact-item-title">WhatsApp & Phone</div>
                            <div class="contact-item-value">
                                <a href="https://wa.me/<?= htmlspecialchars($whatsapp) ?>" target="_blank" rel="noopener" style="color:var(--color-green-deep);">
                                    <?= htmlspecialchars($phone) ?>
                                </a>
                            </div>
                            <div style="font-size: 0.85rem; color: var(--color-text-muted); margin-top: 2px;">
                                Fast response on WhatsApp for quick inquiries & orders
                            </div>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="contact-icon-circle">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:24px; height:24px;"><path d="M12 22s-8-4.5-8-11.8A8 8 0 0 1 12 2a8 8 0 0 1 8 8.2c0 7.3-8 11.8-8 11.8z"/><circle cx="12" cy="10" r="3"/></svg>
                        </div>
                        <div>
                            <div class="contact-item-title">Location & Facility</div>
                            <div class="contact-item-value"><?= htmlspecialchars($address) ?></div>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="contact-icon-circle">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:24px; height:24px;"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                        </div>
                        <div>
                            <div class="contact-item-title">Delivery Reach</div>
                            <div class="contact-item-value"><?= DELIVERY_COVERAGE ?></div>
                            <div style="font-size: 0.85rem; color: var(--color-text-muted); margin-top: 2px;">
                                Serving retail customers & corporate clients in all districts
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; gap: 12px; margin-top: 8px;">
                        <a href="https://wa.me/<?= htmlspecialchars($whatsapp) ?>?text=<?= urlencode('Hello US BENGOL, I would like to place an order.') ?>" target="_blank" rel="noopener" class="btn btn-whatsapp" style="flex:1;">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0012.04 2z"/></svg>
                            Order on WhatsApp
                        </a>
                        <button type="button" class="btn btn-outline trigger-bulk-modal" style="flex:1;">
                            Bulk Order Form
                        </button>
                    </div>
                </div>

                <!-- Contact Message Form -->
                <div style="background: #FFFFFF; border: 1px solid var(--color-border); border-radius: var(--radius-xl); padding: 40px; box-shadow: var(--shadow-sm);">
                    <h3 style="font-size: 1.4rem; font-weight: 800; margin-bottom: 20px;">Send a Direct Message</h3>

                    <form id="general-contact-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Your Name <span class="req">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Tanvir Ahmed" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Phone Number <span class="req">*</span></label>
                                <input type="tel" name="phone" class="form-control" placeholder="01XXXXXXXXX" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Email Address (Optional)</label>
                                <input type="email" name="email" class="form-control" placeholder="name@example.com">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Subject</label>
                                <input type="text" name="subject" class="form-control" placeholder="Inquiry / Feedback">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Message <span class="req">*</span></label>
                            <textarea name="message" class="form-control" rows="4" placeholder="How can we assist you?" required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>


    <!-- =========================================================
         FOOTER
         ========================================================= -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-grid">
                <div>
                    <img src="images/logo.png" alt="US BENGOL" class="footer-logo">
                    <p class="footer-about">
                        US BENGOL is a premium traditional food brand dedicated to bringing authentic Bangladeshi staples, homemade warmth, and consistent everyday freshness to families and businesses across Bangladesh.
                    </p>
                    <div style="font-size: 0.88rem; color: #EBD6A7; font-family: var(--font-bn);">
                        ঐতিহ্য ও মমতায় তৈরি খাঁটি খাদ্যপণ্য
                    </div>
                </div>

                <div>
                    <h4 class="footer-heading">Quick Links</h4>
                    <div class="footer-links">
                        <a href="#home" class="footer-link">Home</a>
                        <a href="#products" class="footer-link">Our Products</a>
                        <a href="#story" class="footer-link">Brand Story</a>
                        <a href="#how-we-prepare" class="footer-link">How We Prepare</a>
                        <a href="#bulk-orders" class="footer-link">Bulk Orders</a>
                        <a href="#contact" class="footer-link">Contact</a>
                    </div>
                </div>

                <div>
                    <h4 class="footer-heading">Products</h4>
                    <div class="footer-links">
                        <a href="#products" class="footer-link">Atta Roti</a>
                        <a href="#products" class="footer-link">Maida Roti</a>
                        <a href="#products" class="footer-link">Traditional Parota</a>
                        <a href="#products" class="footer-link">Diabetes-Friendly Roti</a>
                        <a href="#products" class="footer-link">Pure Ghee</a>
                        <a href="#products" class="footer-link">Stone-Ground Flour</a>
                    </div>
                </div>

                <div>
                    <h4 class="footer-heading">Contact & Delivery</h4>
                    <p style="font-size: 0.9rem; color: #BBA99B; line-height: 1.6; margin-bottom: 12px;">
                        <strong>Address:</strong><br>
                        <?= htmlspecialchars($address) ?>
                    </p>
                    <p style="font-size: 0.9rem; color: #BBA99B; line-height: 1.6; margin-bottom: 16px;">
                        <strong>WhatsApp:</strong> <?= htmlspecialchars($phone) ?><br>
                        <strong>Coverage:</strong> Nationwide Bangladesh
                    </p>
                    <a href="https://wa.me/<?= htmlspecialchars($whatsapp) ?>" target="_blank" class="btn btn-whatsapp btn-sm">
                        Chat on WhatsApp
                    </a>
                </div>
            </div>

            <div class="footer-bottom">
                <div>
                    &copy; <?= date('Y') ?> US BENGOL. All rights reserved. Traditional Taste, Made with Care.
                </div>
                <div style="display: flex; gap: 20px;">
                    <a href="privacy-policy.php" style="color: inherit;">Privacy Policy</a>
                    <a href="terms-conditions.php" style="color: inherit;">Terms & Conditions</a>
                    <a href="admin/login.php" style="color: inherit; opacity: 0.7;">Admin Access</a>
                </div>
            </div>
        </div>
    </footer>


    <!-- =========================================================
         PRODUCT QUICK VIEW MODAL
         ========================================================= -->
    <div class="modal-overlay" id="product-modal" role="dialog" aria-modal="true">
        <div class="modal-container modal-lg">
            <button type="button" class="modal-close-btn" aria-label="Close Modal">&times;</button>
            <div class="modal-body">
                <div class="product-modal-grid">
                    <div class="product-modal-media">
                        <img src="images/ataruti.png" alt="Product" id="modal-product-img" class="product-modal-img">
                    </div>

                    <div>
                        <span class="badge-tag gold" id="modal-product-badge">Daily Essential</span>
                        <h2 style="font-size: 1.8rem; font-weight: 800; color: var(--color-brown-900); margin-bottom: 2px;" id="modal-product-title">
                            Product Title
                        </h2>
                        <span style="font-family: var(--font-bn); font-size: 1rem; color: var(--color-gold-hover); font-weight: 600; display: block; margin-bottom: 12px;" id="modal-product-title-bn"></span>

                        <div style="display: flex; align-items: baseline; gap: 8px; margin-bottom: 16px;">
                            <span style="font-size: 1.8rem; font-weight: 800; color: var(--color-green-deep);" id="modal-product-price">৳0.00</span>
                            <span style="font-size: 0.9rem; color: var(--color-text-muted);" id="modal-product-unit">/ pack</span>
                        </div>

                        <p style="font-size: 0.96rem; color: var(--color-text-body); line-height: 1.6; margin-bottom: 20px;" id="modal-product-desc">
                            Product description goes here.
                        </p>

                        <div id="modal-product-tags" style="display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 24px;"></div>

                        <!-- Quantity Selector -->
                        <div class="qty-control-wrapper">
                            <span style="font-size: 0.92rem; font-weight: 700; color: var(--color-text-main);">Quantity:</span>
                            <div class="qty-btn-box">
                                <button type="button" class="qty-btn" id="qty-dec-btn" aria-label="Decrease quantity">-</button>
                                <input type="number" id="modal-qty-input" class="qty-input" value="1" min="1">
                                <button type="button" class="qty-btn" id="qty-inc-btn" aria-label="Increase quantity">+</button>
                            </div>
                        </div>

                        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                            <a href="#" id="modal-whatsapp-btn" target="_blank" rel="noopener" class="btn btn-whatsapp" style="flex:1;">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0012.04 2z"/></svg>
                                Order on WhatsApp
                            </a>
                            <button type="button" class="btn btn-outline trigger-bulk-modal" onclick="closeModal(productModal);">
                                Bulk Inquiry
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- =========================================================
         BULK ORDER REQUEST MODAL FORM
         ========================================================= -->
    <div class="modal-overlay" id="bulk-modal" role="dialog" aria-modal="true">
        <div class="modal-container">
            <button type="button" class="modal-close-btn" aria-label="Close Modal">&times;</button>
            <div class="modal-body">
                <div style="text-align: center; margin-bottom: 24px;">
                    <span class="badge-tag gold">Commercial Supply</span>
                    <h3 style="font-size: 1.6rem; font-weight: 800; color: var(--color-brown-900);">Request a Bulk Order</h3>
                    <p style="font-size: 0.92rem; color: var(--color-text-muted);">
                        For restaurants, hotels, catering, canteens, and institutional orders.
                    </p>
                </div>

                <form id="bulk-order-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Full Name <span class="req">*</span></label>
                            <input type="text" name="full_name" class="form-control" placeholder="e.g. Rafiqul Islam" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Organization Name <span class="req">*</span></label>
                            <input type="text" name="organization" class="form-control" placeholder="e.g. Royal Bengal Hotel / Caterer" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Phone Number <span class="req">*</span></label>
                            <input type="tel" name="phone" class="form-control" placeholder="01XXXXXXXXX" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email Address (Optional)</label>
                            <input type="email" name="email" class="form-control" placeholder="procurement@organization.com">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Product of Interest <span class="req">*</span></label>
                            <select name="product_name" id="bulk-product-select" class="form-control" required>
                                <option value="">Select a Product</option>
                                <option value="Atta Roti">Atta Roti (আটা রুটি)</option>
                                <option value="Maida Roti">Maida Roti (ময়দা রুটি)</option>
                                <option value="Parota">Parota (পরোটা)</option>
                                <option value="Diabetes-Friendly Roti">Diabetes-Friendly Roti (ডায়াবেটিস ফ্রেন্ডলি রুটি)</option>
                                <option value="Ghee">Pure Ghee (ঘি)</option>
                                <option value="Atta">Stone-Ground Atta (আটা)</option>
                                <option value="Maida">Superfine Maida (ময়দা)</option>
                                <option value="Multiple Products / Custom Batch">Multiple Products / Custom Batch</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Estimated Quantity <span class="req">*</span></label>
                            <input type="text" name="estimated_quantity" class="form-control" placeholder="e.g. 50 packs daily / 200 pcs" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Delivery Location <span class="req">*</span></label>
                            <input type="text" name="delivery_location" class="form-control" placeholder="e.g. Banani, Dhaka / Chittagong" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Preferred Schedule</label>
                            <input type="text" name="preferred_schedule" class="form-control" placeholder="e.g. Daily Morning 7:00 AM">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Special Requirements / Message</label>
                        <textarea name="message" class="form-control" rows="3" placeholder="Tell us more about your recurring supply requirements..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                        Submit Bulk Request
                    </button>
                </form>
            </div>
        </div>
    </div>


    <!-- =========================================================
         STICKY MOBILE ACTION BAR
         ========================================================= -->
    <div class="mobile-sticky-bar">
        <a href="https://wa.me/<?= htmlspecialchars($whatsapp) ?>?text=<?= urlencode('Hello US BENGOL, I would like to order.') ?>" target="_blank" rel="noopener" class="btn btn-whatsapp">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0012.04 2z"/></svg>
            WhatsApp Order
        </a>
        <button type="button" class="btn btn-primary trigger-bulk-modal">
            Bulk Order
        </button>
    </div>

    <!-- Scripts -->
    <script src="js/app.js"></script>
    <script src="js/animations.js"></script>
    <script src="js/forms.js"></script>
</body>
</html>
