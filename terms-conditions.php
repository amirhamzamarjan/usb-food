<?php
/**
 * US BENGOL - Terms and Conditions
 */

define('USBENGOL_INIT', true);
require_once __DIR__ . '/config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms & Conditions • US BENGOL</title>
    <link rel="icon" type="image/svg+xml" href="images/favicon.svg">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/components.css">
    <link rel="stylesheet" href="css/responsive.css">
</head>
<body>
    <header class="site-header scrolled" style="position: sticky;">
        <div class="container">
            <div class="nav-wrapper" style="background:transparent; border-color:transparent; box-shadow:none;">
                <a href="index.php" class="brand-logo-link">
                    <img src="images/logo.png" alt="US BENGOL" class="brand-logo-img">
                </a>
                <a href="index.php" class="btn btn-outline btn-sm">&larr; Back to Home</a>
            </div>
        </div>
    </header>

    <main class="section">
        <div class="container" style="max-width: 800px;">
            <div style="background:#FFFFFF; border:1px solid var(--color-border); border-radius:var(--radius-xl); padding:48px; box-shadow:var(--shadow-sm);">
                <span class="badge-tag gold">Legal Information</span>
                <h1 style="font-size: 2.2rem; margin-bottom: 12px;">Terms & Conditions</h1>
                <p style="color:var(--color-text-muted); font-size:0.95rem; margin-bottom: 32px;">Last updated: <?= date('F j, Y') ?></p>

                <div style="color:var(--color-text-body); line-height: 1.8; font-size: 1.05rem;">
                    <h3 style="font-size: 1.3rem; color:var(--color-text-main); margin: 24px 0 10px 0;">1. Agreement to Terms</h3>
                    <p>
                        By visiting our website, exploring products, submitting bulk order requests, or placing orders with <strong>US BENGOL</strong>, you agree to comply with the terms and practices described herein.
                    </p>

                    <h3 style="font-size: 1.3rem; color:var(--color-text-main); margin: 24px 0 10px 0;">2. Products & Pricing</h3>
                    <p>
                        US BENGOL specializes in traditional Bangladeshi food staples, including Atta Roti, Maida Roti, Parota, Ghee, and related products. Product availability, packaging specifications, and prices displayed on the website or quoted directly are subject to confirmation at the time of order booking.
                    </p>

                    <h3 style="font-size: 1.3rem; color:var(--color-text-main); margin: 24px 0 10px 0;">3. Ordering & Confirmation</h3>
                    <p>
                        Retail and household orders may be coordinated via WhatsApp, phone, or direct communication. Bulk and institutional order requests submitted via our website form are reviewed by our operations team, who will confirm quantities, batch schedules, delivery routes, and finalized pricing before fulfillment.
                    </p>

                    <h3 style="font-size: 1.3rem; color:var(--color-text-main); margin: 24px 0 10px 0;">4. Delivery Coverage</h3>
                    <p>
                        We coordinate deliveries across Dhaka and nationwide throughout Bangladesh. Delivery timelines, transport arrangements, and handling depend on delivery location and order volume.
                    </p>

                    <h3 style="font-size: 1.3rem; color:var(--color-text-main); margin: 24px 0 10px 0;">5. Quality & Freshness</h3>
                    <p>
                        All products are prepared with attention to hygiene, standard consistency, and traditional taste. If you encounter any concerns regarding product condition upon receiving delivery, please notify our team within 24 hours of receipt.
                    </p>

                    <h3 style="font-size: 1.3rem; color:var(--color-text-main); margin: 24px 0 10px 0;">6. Contact Details</h3>
                    <p>
                        For any queries regarding commercial terms, contracts, or orders:
                    </p>
                    <p style="background:var(--color-wheat-100); padding:16px; border-radius:10px; border:1px solid var(--color-wheat-200);">
                        <strong>US BENGOL</strong><br>
                        Bawgan, Uttarkhan, Dhaka 1230, Bangladesh<br>
                        Phone / WhatsApp: 01921692008
                    </p>
                </div>
            </div>
        </div>
    </main>

    <footer class="site-footer" style="padding: 40px 0;">
        <div class="container text-center" style="text-align: center;">
            <p>&copy; <?= date('Y') ?> US BENGOL. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
