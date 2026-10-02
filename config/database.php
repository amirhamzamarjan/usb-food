<?php
/**
 * US BENGOL - Database Connection Wrapper
 * Uses PDO with Prepared Statements for maximum security.
 * Supports MySQL (standard for cPanel) with seamless auto-setup.
 */

require_once __DIR__ . '/config.php';

class Database {
    private static ?PDO $instance = null;
    private static string $driverType = 'mysql';

    public static function getConnection(): PDO {
        if (self::$instance !== null) {
            return self::$instance;
        }

        // Attempt MySQL connection first
        try {
            $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ];

            self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            self::$driverType = 'mysql';
            return self::$instance;
        } catch (PDOException $e) {
            // If MySQL database doesn't exist yet, try creating it if user has privileges
            if (str_contains($e->getMessage(), "Unknown database") || str_contains($e->getMessage(), "1049")) {
                try {
                    $rootDsn = sprintf('mysql:host=%s;charset=%s', DB_HOST, DB_CHARSET);
                    $rootPdo = new PDO($rootDsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
                    $rootPdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

                    self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
                    self::$driverType = 'mysql';
                    // Initialize tables
                    self::initSchema(self::$instance, 'mysql');
                    return self::$instance;
                } catch (Exception $innerEx) {
                    // Fall back to SQLite for local development preview
                }
            }

            // Fallback for seamless local preview without active MySQL server
            $sqlitePath = BASE_DIR . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'usbengol_local.sqlite';
            try {
                $dsn = 'sqlite:' . $sqlitePath;
                self::$instance = new PDO($dsn, null, null, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
                self::$driverType = 'sqlite';
                self::initSchema(self::$instance, 'sqlite');
                return self::$instance;
            } catch (PDOException $sqliteEx) {
                // If everything fails, log and output user-friendly error
                error_log("Database connection failure: " . $e->getMessage());
                die("A temporary database issue occurred. Please check database configuration in config/config.php.");
            }
        }
    }

    public static function getDriverType(): string {
        return self::$driverType;
    }

    public static function initSchema(PDO $pdo, string $driver = 'mysql'): void {
        if ($driver === 'mysql') {
            $sql = "
            CREATE TABLE IF NOT EXISTS `admins` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `username` VARCHAR(60) NOT NULL UNIQUE,
                `password_hash` VARCHAR(255) NOT NULL,
                `full_name` VARCHAR(100) NOT NULL,
                `email` VARCHAR(120) NOT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE IF NOT EXISTS `products` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(150) NOT NULL,
                `name_bn` VARCHAR(150) DEFAULT NULL,
                `slug` VARCHAR(160) NOT NULL UNIQUE,
                `category` VARCHAR(60) DEFAULT 'staple',
                `short_description` TEXT NOT NULL,
                `description` TEXT DEFAULT NULL,
                `price` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
                `unit` VARCHAR(50) DEFAULT 'pack',
                `image` VARCHAR(255) DEFAULT NULL,
                `badge` VARCHAR(50) DEFAULT NULL,
                `availability` ENUM('in_stock', 'pre_order', 'out_of_stock') DEFAULT 'in_stock',
                `is_active` TINYINT(1) DEFAULT 1,
                `is_featured` TINYINT(1) DEFAULT 0,
                `sort_order` INT DEFAULT 0,
                `characteristics` TEXT DEFAULT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE IF NOT EXISTS `bulk_orders` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `full_name` VARCHAR(150) NOT NULL,
                `organization` VARCHAR(200) NOT NULL,
                `phone` VARCHAR(30) NOT NULL,
                `email` VARCHAR(150) DEFAULT NULL,
                `product_name` VARCHAR(150) NOT NULL,
                `estimated_quantity` VARCHAR(100) NOT NULL,
                `delivery_location` VARCHAR(255) NOT NULL,
                `preferred_schedule` VARCHAR(100) DEFAULT NULL,
                `message` TEXT DEFAULT NULL,
                `status` ENUM('new', 'contacted', 'completed', 'cancelled') DEFAULT 'new',
                `admin_notes` TEXT DEFAULT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE IF NOT EXISTS `contact_messages` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(150) NOT NULL,
                `phone` VARCHAR(30) NOT NULL,
                `email` VARCHAR(150) DEFAULT NULL,
                `subject` VARCHAR(200) DEFAULT NULL,
                `message` TEXT NOT NULL,
                `status` ENUM('new', 'replied', 'archived') DEFAULT 'new',
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE IF NOT EXISTS `site_content` (
                `key_name` VARCHAR(100) PRIMARY KEY,
                `content_value` TEXT NOT NULL,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";
        } else {
            // SQLite Syntax
            $sql = "
            CREATE TABLE IF NOT EXISTS admins (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT NOT NULL UNIQUE,
                password_hash TEXT NOT NULL,
                full_name TEXT NOT NULL,
                email TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS products (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                name_bn TEXT,
                slug TEXT NOT NULL UNIQUE,
                category TEXT DEFAULT 'staple',
                short_description TEXT NOT NULL,
                description TEXT,
                price REAL NOT NULL DEFAULT 0.00,
                unit TEXT DEFAULT 'pack',
                image TEXT,
                badge TEXT,
                availability TEXT DEFAULT 'in_stock',
                is_active INTEGER DEFAULT 1,
                is_featured INTEGER DEFAULT 0,
                sort_order INTEGER DEFAULT 0,
                characteristics TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS bulk_orders (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                full_name TEXT NOT NULL,
                organization TEXT NOT NULL,
                phone TEXT NOT NULL,
                email TEXT,
                product_name TEXT NOT NULL,
                estimated_quantity TEXT NOT NULL,
                delivery_location TEXT NOT NULL,
                preferred_schedule TEXT,
                message TEXT,
                status TEXT DEFAULT 'new',
                admin_notes TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS contact_messages (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                phone TEXT NOT NULL,
                email TEXT,
                subject TEXT,
                message TEXT NOT NULL,
                status TEXT DEFAULT 'new',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS site_content (
                key_name TEXT PRIMARY KEY,
                content_value TEXT NOT NULL,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            ";
        }

        try {
            $pdo->exec($sql);
            self::seedDefaultData($pdo);
        } catch (Exception $e) {
            error_log("Schema initialization error: " . $e->getMessage());
        }
    }

    public static function seedDefaultData(PDO $pdo): void {
        // Check if admin exists
        $stmt = $pdo->query("SELECT COUNT(*) FROM admins");
        if ($stmt->fetchColumn() == 0) {
            // Default admin: admin / admin123 (Secured with BCRYPT)
            $passHash = password_hash('admin123', PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO admins (username, password_hash, full_name, email) VALUES (?, ?, ?, ?)");
            $stmt->execute(['admin', $passHash, 'US BENGOL Administrator', 'admin@usbengol.com']);
        }

        // Check if default products exist
        $stmt = $pdo->query("SELECT COUNT(*) FROM products");
        if ($stmt->fetchColumn() == 0) {
            $defaultProducts = [
                [
                    'name' => 'Atta Roti',
                    'name_bn' => 'আটা রুটি',
                    'slug' => 'atta-roti',
                    'category' => 'roti',
                    'short_description' => 'Soft, wholesome whole wheat rotis made with traditional care and authentic homemade texture.',
                    'description' => 'Our traditional Atta Roti is prepared from carefully selected high-grade whole wheat flour. Each roti is rolled to standard thickness and lightly puffed to preserve its natural softness and aroma, making it the perfect staple for everyday family breakfasts and dinners.',
                    'price' => 0.00,
                    'unit' => 'pack of 10 pcs',
                    'image' => 'images/ataruti.png',
                    'badge' => 'Daily Essential',
                    'availability' => 'in_stock',
                    'is_active' => 1,
                    'is_featured' => 1,
                    'sort_order' => 1,
                    'characteristics' => json_encode(['100% Whole Wheat', 'Soft & Pliable Texture', 'Hygienically Prepared', 'No Artificial Preservatives'])
                ],
                [
                    'name' => 'Maida Roti',
                    'name_bn' => 'ময়দা রুটি',
                    'slug' => 'maida-roti',
                    'category' => 'roti',
                    'short_description' => 'Silky smooth, tender traditional maida rotis crafted for a delicate and comforting meal.',
                    'description' => 'Prepared with finely sifted flour following timeless Bangladeshi kitchen traditions. Soft, pliable, and satisfying, our Maida Rotis pair wonderfully with rich gravies, slow-cooked meats, daal, or traditional bhaji.',
                    'price' => 0.00,
                    'unit' => 'pack of 10 pcs',
                    'image' => 'images/moydaruti.png',
                    'badge' => 'Classic Taste',
                    'availability' => 'in_stock',
                    'is_active' => 1,
                    'is_featured' => 1,
                    'sort_order' => 2,
                    'characteristics' => json_encode(['Silky Tender Texture', 'Evenly Rolled', 'Authentic Recipe', 'Fresh Daily Batches'])
                ],
                [
                    'name' => 'Parota',
                    'name_bn' => 'পরোটা',
                    'slug' => 'parota',
                    'category' => 'parota',
                    'short_description' => 'Delicately layered, golden, and flaky traditional parotas with comforting homemade warmth.',
                    'description' => 'Layered with precision and prepared with the warmth of traditional culinary care. Crisp on the exterior and tender within, our Parotas bring the beloved aroma and comfort of an authentic Bangladeshi morning table.',
                    'price' => 0.00,
                    'unit' => 'pack of 10 pcs',
                    'image' => 'images/porota.png',
                    'badge' => 'Layered & Crisp',
                    'availability' => 'in_stock',
                    'is_active' => 1,
                    'is_featured' => 1,
                    'sort_order' => 3,
                    'characteristics' => json_encode(['Multi-Layered Flakiness', 'Golden Crust', 'Comforting Taste', 'Ready to Warm & Serve'])
                ],
                [
                    'name' => 'Diabetes-Friendly Roti',
                    'name_bn' => 'ডায়াবেটিস ফ্রেন্ডলি রুটি',
                    'slug' => 'diabetes-friendly-roti',
                    'category' => 'dietary',
                    'short_description' => 'A thoughtfully positioned option for customers who pay closer attention to their dietary choices.',
                    'description' => 'Carefully formulated for individuals seeking a balanced, health-conscious staple for their daily meals. Prepared with selected fiber-rich grains and traditional care without compromising on softness, aroma, or authentic taste.',
                    'price' => 0.00,
                    'unit' => 'pack of 10 pcs',
                    'image' => 'images/diabetes-roti.svg',
                    'badge' => 'Special Dietary',
                    'availability' => 'in_stock',
                    'is_active' => 1,
                    'is_featured' => 1,
                    'sort_order' => 4,
                    'characteristics' => json_encode(['High Fiber Grain Blend', 'Thoughtful Formulation', 'Wholesome & Filling', 'Zero Added Sugars'])
                ],
                [
                    'name' => 'Ghee',
                    'name_bn' => 'ঘি',
                    'slug' => 'ghee',
                    'category' => 'staple',
                    'short_description' => 'Rich, golden, and authentically aromatic traditional ghee for elevated everyday cooking.',
                    'description' => 'Crafted through traditional slow-clarification methods to capture a deep, comforting aroma and granular texture. Perfect for drizzling over warm rotis, fragrant rice, khichuri, or special celebratory dishes.',
                    'price' => 0.00,
                    'unit' => '500g Jar',
                    'image' => 'images/ghee.svg',
                    'badge' => 'Pure & Aromatic',
                    'availability' => 'in_stock',
                    'is_active' => 1,
                    'is_featured' => 1,
                    'sort_order' => 5,
                    'characteristics' => json_encode(['Traditional Slow-Clarified', 'Granular Danedar Texture', 'Rich Natural Aroma', 'Pure Staple Quality'])
                ],
                [
                    'name' => 'Atta',
                    'name_bn' => 'আটা',
                    'slug' => 'atta',
                    'category' => 'flour',
                    'short_description' => 'Stone-ground style whole wheat flour preserving natural bran and wholesome nutrients.',
                    'description' => 'Premium whole grain wheat carefully milled to standard consistency. Yields wonderfully soft, pliable rotis with rich natural sweetness and wholesome dietary fiber.',
                    'price' => 0.00,
                    'unit' => '2kg Pack',
                    'image' => 'images/atta.svg',
                    'badge' => 'Stone-Ground Style',
                    'availability' => 'in_stock',
                    'is_active' => 1,
                    'is_featured' => 0,
                    'sort_order' => 6,
                    'characteristics' => json_encode(['Selected Whole Wheat', 'Rich Natural Bran', 'Ideal Water Absorption', 'Consistent Fineness'])
                ],
                [
                    'name' => 'Maida',
                    'name_bn' => 'ময়দা',
                    'slug' => 'maida',
                    'category' => 'flour',
                    'short_description' => 'Superfine, silky refined wheat flour for traditional snacks, parotas, and festive cooking.',
                    'description' => 'Milled to an exquisite fine texture from high-grade wheat. Provides ideal elasticity and texture for flaky parotas, luchis, traditional sweets, and baking.',
                    'price' => 0.00,
                    'unit' => '2kg Pack',
                    'image' => 'images/maida.svg',
                    'badge' => 'Superfine Quality',
                    'availability' => 'in_stock',
                    'is_active' => 1,
                    'is_featured' => 0,
                    'sort_order' => 7,
                    'characteristics' => json_encode(['Superfine Sifted', 'Superior Dough Elasticity', 'Ideal for Parotas & Sweets', 'Clean & Unadulterated'])
                ]
            ];

            $insertStmt = $pdo->prepare("
                INSERT INTO products (name, name_bn, slug, category, short_description, description, price, unit, image, badge, availability, is_active, is_featured, sort_order, characteristics)
                VALUES (:name, :name_bn, :slug, :category, :short_description, :description, :price, :unit, :image, :badge, :availability, :is_active, :is_featured, :sort_order, :characteristics)
            ");

            foreach ($defaultProducts as $p) {
                $insertStmt->execute($p);
            }
        }

        // Check site_content
        $stmt = $pdo->query("SELECT COUNT(*) FROM site_content");
        if ($stmt->fetchColumn() == 0) {
            $defaultContent = [
                'hero_headline' => 'Traditional Taste, Made with Care.',
                'hero_subtitle' => 'US BENGOL brings traditional Bangladeshi food staples, homemade care, and authentic village-inspired warmth to modern family tables across Bangladesh.',
                'story_title' => 'Rooted in Tradition',
                'story_subtitle' => 'A commitment to authentic taste, simple ingredients, and the timeless comfort of home-cooked meals.',
                'why_us_headline' => 'Why Choose US BENGOL?',
                'how_we_prepare_headline' => 'How We Prepare',
                'bulk_order_headline' => 'Need a Regular Supply for Your Business?',
                'delivery_headline' => 'Nationwide Delivery Across Bangladesh',
                'contact_address' => BUSINESS_ADDRESS,
                'contact_phone' => PHONE_NUMBER,
                'contact_whatsapp' => WHATSAPP_NUMBER
            ];

            $contentStmt = $pdo->prepare("INSERT INTO site_content (key_name, content_value) VALUES (?, ?)");
            foreach ($defaultContent as $key => $val) {
                $contentStmt->execute([$key, $val]);
            }
        }
    }
}
