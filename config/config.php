<?php
/**
 * US BENGOL - Global Configuration File
 * Production-ready configuration for standard shared hosting & cPanel
 */

// Prevent direct script access
if (!defined('USBENGOL_INIT')) {
    define('USBENGOL_INIT', true);
}

// Environment settings
define('APP_NAME', 'US BENGOL');
define('APP_TAGLINE', 'Traditional Taste, Made with Care');
define('APP_TAGLINE_BN', 'ঐতিহ্যবাহী স্বাদ ও মমতা');
define('APP_VERSION', '1.0.0');

// Contact & Location Settings
define('PHONE_NUMBER', '01921692008');
define('WHATSAPP_NUMBER', '8801921692008'); // International format for wa.me links
define('CONTACT_EMAIL', 'info@usbengol.com');
define('BUSINESS_ADDRESS', 'Bawgan, Uttarkhan, Dhaka 1230, Bangladesh');
define('DELIVERY_COVERAGE', 'Delivery available across Bangladesh');
define('CURRENCY_SYMBOL', '৳');

// Database Configuration (Default credentials for standard shared hosting / cPanel)
// Update these to match your cPanel MySQL database details
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'usbengol_db');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_CHARSET', 'utf8mb4');

// Base Paths
define('BASE_DIR', dirname(__DIR__));
define('UPLOADS_DIR', BASE_DIR . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'products');
define('UPLOADS_URL', 'uploads/products/');

// Upload constraints
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5 MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'image/jpg']);

// Session settings
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    // Enable secure cookies if HTTPS
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        ini_set('session.cookie_secure', 1);
    }
    session_start();
}
