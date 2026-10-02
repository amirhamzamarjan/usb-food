<?php
/**
 * US BENGOL - Admin Login Handler
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: index.php");
    exit;
}

$error = '';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = "Session expired or invalid form submission. Please try again.";
    } else {
        $username = sanitize_input($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $error = "Please enter both username and password.";
        } else {
            try {
                $pdo = Database::getConnection();
                $stmt = $pdo->prepare("SELECT id, username, password_hash, full_name, email FROM admins WHERE username = ? LIMIT 1");
                $stmt->execute([$username]);
                $admin = $stmt->fetch();

                if ($admin && password_verify($password, $admin['password_hash'])) {
                    // Login Success
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_username'] = $admin['username'];
                    $_SESSION['admin_name'] = $admin['full_name'];

                    // Prevent session fixation
                    session_regenerate_id(true);

                    header("Location: index.php");
                    exit;
                } else {
                    $error = "Invalid username or password.";
                    // Emulate timing for security
                    password_verify('dummy', '$2y$10$dummyhashdummyhashdummyhashdummyhashdummyhash');
                }
            } catch (Exception $e) {
                error_log("Admin Login Error: " . $e->getMessage());
                $error = "A system error occurred. Please try again later.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login • US BENGOL</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #233D2D;
            --bg: #FAF7F2;
            --border: #E8DEC8;
            --text-main: #2A1810;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: var(--bg); display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 20px; }
        .login-card { background: #fff; width: 100%; max-width: 420px; border-radius: 20px; padding: 48px 40px; box-shadow: 0 15px 35px rgba(35, 20, 12, 0.08); border: 1px solid var(--border); }
        .brand-logo { display: block; margin: 0 auto 24px auto; height: 50px; }
        h1 { text-align: center; color: var(--text-main); font-size: 1.5rem; font-weight: 700; margin-bottom: 8px; }
        p.sub { text-align: center; color: #7A675B; font-size: 0.95rem; margin-bottom: 32px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-size: 0.9rem; font-weight: 600; color: var(--text-main); margin-bottom: 8px; }
        input[type="text"], input[type="password"] { width: 100%; padding: 12px 16px; border: 1.5px solid var(--border); border-radius: 8px; font-size: 1rem; transition: border-color 0.2s; background: #FAF8F5; }
        input:focus { outline: none; border-color: var(--primary); background: #fff; }
        .btn { display: block; width: 100%; background: var(--primary); color: #fff; border: none; padding: 14px; border-radius: 8px; font-size: 1rem; font-weight: 700; cursor: pointer; transition: background 0.2s; margin-top: 10px; }
        .btn:hover { background: #172A1F; }
        .error-msg { background: #FFEBEE; color: #C62828; padding: 12px; border-radius: 8px; font-size: 0.9rem; text-align: center; margin-bottom: 20px; font-weight: 500; }
        .back-link { display: block; text-align: center; margin-top: 24px; color: #7A675B; text-decoration: none; font-size: 0.9rem; font-weight: 500; transition: color 0.2s; }
        .back-link:hover { color: var(--text-main); }
    </style>
</head>
<body>
    <div class="login-card">
        <img src="../images/logo.png" alt="US BENGOL" class="brand-logo">
        <h1>Admin Secure Login</h1>
        <p class="sub">Manage products & website content.</p>

        <?php if (!empty($error)): ?>
            <div class="error-msg"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php
            $flash = get_flash_message();
            if ($flash && $flash['type'] === 'error'):
        ?>
            <div class="error-msg"><?= htmlspecialchars($flash['message']) ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <input type="hidden" name="csrf_token" value="<?= get_csrf_token() ?>">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required autocomplete="username" autofocus>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn">Login to Dashboard</button>
        </form>

        <a href="../index.php" class="back-link">&larr; Return to Website</a>

        <div style="font-size: 11px; color:#A39185; text-align:center; margin-top:30px;">
            Default test setup: admin / admin123<br>
            Protected by US BENGOL
        </div>
    </div>
</body>
</html>
