<?php
/**
 * US BENGOL - 1-Click Database Setup & Health Check
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';

$message = '';
$status = 'info';

try {
    $pdo = Database::getConnection();
    $driver = Database::getDriverType();

    if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['action']) && $_POST['action'] === 'install') {
        Database::initSchema($pdo, $driver);
        $message = "Database schema initialized and default products & admin seeded successfully!";
        $status = "success";
    }

    // Check table counts
    $stmt = $pdo->query("SELECT COUNT(*) FROM products");
    $productCount = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM admins");
    $adminCount = $stmt->fetchColumn();

} catch (Exception $e) {
    $message = "Error connecting to database: " . $e->getMessage();
    $status = "error";
    $productCount = 0;
    $adminCount = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>US BENGOL • Database & System Setup</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #FAF7F2;
            --card-bg: #FFFFFF;
            --text-main: #2A1810;
            --text-muted: #6E5A4E;
            --primary: #233D2D;
            --accent: #C89B48;
            --border: #E8DEC8;
            --success-bg: #E8F5E9;
            --success-text: #1B5E20;
            --error-bg: #FFEBEE;
            --error-text: #B71C1C;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bg);
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 24px;
        }
        .setup-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 40px;
            max-width: 580px;
            width: 100%;
            box-shadow: 0 20px 40px rgba(42, 24, 16, 0.06);
        }
        .brand-header {
            text-align: center;
            margin-bottom: 28px;
        }
        .brand-logo {
            max-width: 130px;
            height: auto;
            margin-bottom: 12px;
        }
        h1 {
            font-size: 24px;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 6px;
        }
        p.subtitle {
            color: var(--text-muted);
            font-size: 14px;
        }
        .status-alert {
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-size: 14px;
            font-weight: 500;
        }
        .status-alert.success { background: var(--success-bg); color: var(--success-text); }
        .status-alert.error { background: var(--error-bg); color: var(--error-text); }
        .status-alert.info { background: #FFF8E1; color: #795548; }

        .stat-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 28px;
        }
        .stat-box {
            background: #FAF6EF;
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 18px;
            text-align: center;
        }
        .stat-num {
            font-size: 28px;
            font-weight: 800;
            color: var(--primary);
        }
        .stat-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 4px;
        }
        .btn-action {
            display: block;
            width: 100%;
            background: var(--primary);
            color: #FFFFFF;
            border: none;
            padding: 16px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .btn-action:hover {
            background: #172A1F;
            transform: translateY(-2px);
        }
        .btn-secondary {
            background: #FAF6EF;
            color: var(--text-main);
            border: 1px solid var(--border);
            margin-top: 12px;
        }
        .btn-secondary:hover {
            background: #F2E8D8;
        }
        .credentials-box {
            margin-top: 24px;
            padding: 16px;
            background: #FAF6EF;
            border-radius: 12px;
            font-size: 13px;
            color: var(--text-muted);
            line-height: 1.6;
        }
        .credentials-box strong { color: var(--text-main); }
    </style>
</head>
<body>
    <div class="setup-card">
        <div class="brand-header">
            <img src="../images/logo.png" alt="US BENGOL Logo" class="brand-logo">
            <h1>System & Database Setup</h1>
            <p class="subtitle">US BENGOL • Traditional Taste, Made with Care</p>
        </div>

        <?php if (!empty($message)): ?>
            <div class="status-alert <?= htmlspecialchars($status) ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <div class="stat-grid">
            <div class="stat-box">
                <div class="stat-num"><?= $productCount ?></div>
                <div class="stat-label">Products Active</div>
            </div>
            <div class="stat-box">
                <div class="stat-num"><?= $adminCount ?></div>
                <div class="stat-label">Admin Accounts</div>
            </div>
        </div>

        <form method="POST">
            <input type="hidden" name="action" value="install">
            <button type="submit" class="btn-action">Re-Seed / Initialize Database</button>
        </form>

        <a href="../admin/index.php" class="btn-action btn-secondary">Open Admin Panel</a>
        <a href="../index.php" class="btn-action btn-secondary" style="border:none; background:transparent;">Back to Website</a>

        <div class="credentials-box">
            <strong>Default Admin Credentials:</strong><br>
            Username: <code>admin</code><br>
            Password: <code>admin123</code><br>
            <em>(You can update credentials securely inside the Admin Panel Settings)</em>
        </div>
    </div>
</body>
</html>
