-- =======================================================
-- US BENGOL - Complete Production MySQL Database Schema
-- Brand: US BENGOL (Traditional Bangladeshi Food Products)
-- Address: Bawgan, Uttarkhan, Dhaka 1230, Bangladesh
-- WhatsApp: 01921692008
-- =======================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+06:00";

-- --------------------------------------------------------
-- Table: admins
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default Admin Account (Username: admin, Password: admin123)
-- Please change your password in the admin panel settings upon first login!
INSERT INTO `admins` (`id`, `username`, `password_hash`, `full_name`, `email`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$7R.p520zI8tFh.7vjRk5y.yU/N.aZ7V8cK0lH/wX3K0rL6HwAeVQy', 'US BENGOL Administrator', 'admin@usbengol.com', NOW(), NOW())
ON DUPLICATE KEY UPDATE `username`=`username`;

-- --------------------------------------------------------
-- Table: products
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_bn` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT 'staple',
  `short_description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `unit` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'pack',
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `badge` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `availability` enum('in_stock','pre_order','out_of_stock') COLLATE utf8mb4_unicode_ci DEFAULT 'in_stock',
  `is_active` tinyint(1) DEFAULT 1,
  `is_featured` tinyint(1) DEFAULT 0,
  `sort_order` int(11) DEFAULT 0,
  `characteristics` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Initial Seed Products (Prices set to 0.00 placeholder - fully editable in admin panel)
INSERT INTO `products` (`id`, `name`, `name_bn`, `slug`, `category`, `short_description`, `description`, `price`, `unit`, `image`, `badge`, `availability`, `is_active`, `is_featured`, `sort_order`, `characteristics`, `created_at`, `updated_at`) VALUES
(1, 'Atta Roti', 'আটা রুটি', 'atta-roti', 'roti', 'Soft, wholesome whole wheat rotis made with traditional care and authentic homemade texture.', 'Our traditional Atta Roti is prepared from carefully selected high-grade whole wheat flour. Each roti is rolled to standard thickness and lightly puffed to preserve its natural softness and aroma, making it the perfect staple for everyday family breakfasts and dinners.', 0.00, 'pack of 10 pcs', 'images/ataruti.png', 'Daily Essential', 'in_stock', 1, 1, 1, '[\"100% Whole Wheat\", \"Soft & Pliable Texture\", \"Hygienically Prepared\", \"No Artificial Preservatives\"]', NOW(), NOW()),
(2, 'Maida Roti', 'ময়দা রুটি', 'maida-roti', 'roti', 'Silky smooth, tender traditional maida rotis crafted for a delicate and comforting meal.', 'Prepared with finely sifted flour following timeless Bangladeshi kitchen traditions. Soft, pliable, and satisfying, our Maida Rotis pair wonderfully with rich gravies, slow-cooked meats, daal, or traditional bhaji.', 0.00, 'pack of 10 pcs', 'images/moydaruti.png', 'Classic Taste', 'in_stock', 1, 1, 2, '[\"Silky Tender Texture\", \"Evenly Rolled\", \"Authentic Recipe\", \"Fresh Daily Batches\"]', NOW(), NOW()),
(3, 'Parota', 'পরোটা', 'parota', 'parota', 'Delicately layered, golden, and flaky traditional parotas with comforting homemade warmth.', 'Layered with precision and prepared with the warmth of traditional culinary care. Crisp on the exterior and tender within, our Parotas bring the beloved aroma and comfort of an authentic Bangladeshi morning table.', 0.00, 'pack of 10 pcs', 'images/porota.png', 'Layered & Crisp', 'in_stock', 1, 1, 3, '[\"Multi-Layered Flakiness\", \"Golden Crust\", \"Comforting Taste\", \"Ready to Warm & Serve\"]', NOW(), NOW()),
(4, 'Diabetes-Friendly Roti', 'ডায়াবেটিস ফ্রেন্ডলি রুটি', 'diabetes-friendly-roti', 'dietary', 'A thoughtfully positioned option for customers who pay closer attention to their dietary choices.', 'Carefully formulated for individuals seeking a balanced, health-conscious staple for their daily meals. Prepared with selected fiber-rich grains and traditional care without compromising on softness, aroma, or authentic taste.', 0.00, 'pack of 10 pcs', 'images/diabetes-roti.svg', 'Special Dietary', 'in_stock', 1, 1, 4, '[\"High Fiber Grain Blend\", \"Thoughtful Formulation\", \"Wholesome & Filling\", \"Zero Added Sugars\"]', NOW(), NOW()),
(5, 'Ghee', 'ঘি', 'ghee', 'staple', 'Rich, golden, and authentically aromatic traditional ghee for elevated everyday cooking.', 'Crafted through traditional slow-clarification methods to capture a deep, comforting aroma and granular texture. Perfect for drizzling over warm rotis, fragrant rice, khichuri, or special celebratory dishes.', 0.00, '500g Jar', 'images/ghee.svg', 'Pure & Aromatic', 'in_stock', 1, 1, 5, '[\"Traditional Slow-Clarified\", \"Granular Danedar Texture\", \"Rich Natural Aroma\", \"Pure Staple Quality\"]', NOW(), NOW()),
(6, 'Atta', 'আটা', 'atta', 'flour', 'Stone-ground style whole wheat flour preserving natural bran and wholesome nutrients.', 'Premium whole grain wheat carefully milled to standard consistency. Yields wonderfully soft, pliable rotis with rich natural sweetness and wholesome dietary fiber.', 0.00, '2kg Pack', 'images/atta.svg', 'Stone-Ground Style', 'in_stock', 1, 0, 6, '[\"Selected Whole Wheat\", \"Rich Natural Bran\", \"Ideal Water Absorption\", \"Consistent Fineness\"]', NOW(), NOW()),
(7, 'Maida', 'ময়দা', 'maida', 'flour', 'Superfine, silky refined wheat flour for traditional snacks, parotas, and festive cooking.', 'Milled to an exquisite fine texture from high-grade wheat. Provides ideal elasticity and texture for flaky parotas, luchis, traditional sweets, and baking.', 0.00, '2kg Pack', 'images/maida.svg', 'Superfine Quality', 'in_stock', 1, 0, 7, '[\"Superfine Sifted\", \"Superior Dough Elasticity\", \"Ideal for Parotas & Sweets\", \"Clean & Unadulterated\"]', NOW(), NOW())
ON DUPLICATE KEY UPDATE `slug`=`slug`;

-- --------------------------------------------------------
-- Table: bulk_orders
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `bulk_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `organization` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estimated_quantity` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `delivery_location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `preferred_schedule` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('new','contacted','completed','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'new',
  `admin_notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: contact_messages
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('new','replied','archived') COLLATE utf8mb4_unicode_ci DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: site_content
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `site_content` (
  `key_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content_value` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`key_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `site_content` (`key_name`, `content_value`, `updated_at`) VALUES
('hero_headline', 'Traditional Taste, Made with Care.', NOW()),
('hero_subtitle', 'US BENGOL brings traditional Bangladeshi food staples, homemade care, and authentic village-inspired warmth to modern family tables across Bangladesh.', NOW()),
('story_title', 'Rooted in Tradition', NOW()),
('story_subtitle', 'A commitment to authentic taste, simple ingredients, and the timeless comfort of home-cooked meals.', NOW()),
('why_us_headline', 'Why Choose US BENGOL?', NOW()),
('how_we_prepare_headline', 'How We Prepare', NOW()),
('bulk_order_headline', 'Need a Regular Supply for Your Business?', NOW()),
('delivery_headline', 'Nationwide Delivery Across Bangladesh', NOW()),
('contact_address', 'Bawgan, Uttarkhan, Dhaka 1230, Bangladesh', NOW()),
('contact_phone', '01921692008', NOW()),
('contact_whatsapp', '8801921692008', NOW())
ON DUPLICATE KEY UPDATE `key_name`=`key_name`;

COMMIT;
