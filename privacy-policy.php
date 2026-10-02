<?php
/**
 * US BENGOL - Privacy Policy
 */

define('USBENGOL_INIT', true);
require_once __DIR__ . '/config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy • US BENGOL</title>
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
                <h1 style="font-size: 2.2rem; margin-bottom: 12px;">Privacy Policy</h1>
                <p style="color:var(--color-text-muted); font-size:0.95rem; margin-bottom: 32px;">Last updated: <?= date('F j, Y') ?></p>

                <div style="color:var(--color-text-body); line-height: 1.8; font-size: 1.05rem;">
                    <h3 style="font-size: 1.3rem; color:var(--color-text-main); margin: 24px 0 10px 0;">1. Introduction</h3>
                    <p>
                        At <strong>US BENGOL</strong>, we respect the privacy of our website visitors, customers, and corporate partners. This Privacy Policy explains what information we collect and how it is used to serve you with fresh, traditional food products and reliable delivery across Bangladesh.
                    </p>

                    <h3 style="font-size: 1.3rem; color:var(--color-text-main); margin: 24px 0 10px 0;">2. Information We Collect</h3>
                    <p>
                        When you contact us, request a product quotation, submit a bulk order form, or communicate with us through WhatsApp, we may collect:
                    </p>
                    <ul style="list-style: disc; margin-left: 24px; margin-bottom: 20px;">
                        <li>Your full name and organization / business name</li>
                        <li>Contact phone number and email address</li>
                        <li>Delivery location and preferred delivery schedule</li>
                        <li>Order quantities and specific product preferences</li>
                    </ul>

                    <h3 style="font-size: 1.3rem; color:var(--color-text-main); margin: 24px 0 10px 0;">3. How We Use Your Information</h3>
                    <p>
                        Any information submitted through our website or direct channels is used solely for legitimate business purposes, including:
                    </p>
                    <ul style="list-style: disc; margin-left: 24px; margin-bottom: 20px;">
                        <li>Processing and delivering your personal or institutional orders</li>
                        <li>Contacting you regarding product inquiries, batch schedules, or pricing quotes</li>
                        <li>Providing customer support and order fulfillment updates</li>
                    </ul>

                    <h3 style="font-size: 1.3rem; color:var(--color-text-main); margin: 24px 0 10px 0;">4. Data Protection</h3>
                    <p>
                        We do not sell, rent, or trade your personal information to third parties. Your information is securely stored within our internal databases and accessible only to authorized US BENGOL staff for order fulfillment.
                    </p>

                    <h3 style="font-size: 1.3rem; color:var(--color-text-main); margin: 24px 0 10px 0;">5. Contact Us</h3>
                    <p>
                        If you have any questions about this Privacy Policy or wish to update your contact information, please contact us at:
                    </p>
                    <p style="background:var(--color-wheat-100); padding:16px; border-radius:10px; border:1px solid var(--color-wheat-200);">
                        <strong>US BENGOL</strong><br>
                        Bawgan, Uttarkhan, Dhaka 1230, Bangladesh<br>
                        Phone / WhatsApp: 01921692008<br>
                        Email: info@usbengol.com
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
