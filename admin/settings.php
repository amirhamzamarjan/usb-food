<?php
/**
 * US BENGOL - Admin Account Settings & Security
 */

$pageTitle = 'Admin Profile & Security';
$activeNav = 'settings';

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';

$pdo = Database::getConnection();
$adminId = $_SESSION['admin_id'] ?? 1;

// Fetch current admin
$stmt = $pdo->prepare("SELECT id, username, full_name, email FROM admins WHERE id = ?");
$stmt->execute([$adminId]);
$admin = $stmt->fetch();

$errors = [];

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid security token.';
    } else {
        $fullName = sanitize_input($_POST['full_name'] ?? '');
        $email = sanitize_input($_POST['email'] ?? '');
        $username = sanitize_input($_POST['username'] ?? '');
        $currentPass = $_POST['current_password'] ?? '';
        $newPass = $_POST['new_password'] ?? '';
        $confirmPass = $_POST['confirm_password'] ?? '';

        if (empty($fullName) || empty($username) || empty($email)) {
            $errors[] = 'Name, username, and email are required.';
        }

        // Password change logic
        if (!empty($newPass)) {
            if (empty($currentPass)) {
                $errors[] = 'Please enter your current password to set a new password.';
            } else {
                // Verify current password
                $stmt = $pdo->prepare("SELECT password_hash FROM admins WHERE id = ?");
                $stmt->execute([$adminId]);
                $hash = $stmt->fetchColumn();

                if (!password_verify($currentPass, $hash)) {
                    $errors[] = 'Current password is incorrect.';
                } elseif (strlen($newPass) < 6) {
                    $errors[] = 'New password must be at least 6 characters long.';
                } elseif ($newPass !== $confirmPass) {
                    $errors[] = 'New password and confirmation do not match.';
                }
            }
        }

        if (empty($errors)) {
            if (!empty($newPass)) {
                $newHash = password_hash($newPass, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE admins SET full_name = ?, username = ?, email = ?, password_hash = ? WHERE id = ?");
                $stmt->execute([$fullName, $username, $email, $newHash, $adminId]);
            } else {
                $stmt = $pdo->prepare("UPDATE admins SET full_name = ?, username = ?, email = ? WHERE id = ?");
                $stmt->execute([$fullName, $username, $email, $adminId]);
            }

            $_SESSION['admin_name'] = $fullName;
            $_SESSION['admin_username'] = $username;

            set_flash_message('success', 'Admin profile and security settings updated successfully.');
            header("Location: settings.php");
            exit;
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="admin-card" style="max-width: 700px;">
    <div class="admin-card-header">
        <h3 class="admin-card-title">Administrator Profile & Security</h3>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul style="margin-left: 20px;">
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="settings.php">
        <input type="hidden" name="csrf_token" value="<?= get_csrf_token() ?>">

        <div class="admin-form-group">
            <label class="admin-form-label">Administrator Full Name</label>
            <input type="text" name="full_name" value="<?= htmlspecialchars($admin['full_name'] ?? '') ?>" class="admin-input" required>
        </div>

        <div class="admin-grid-2">
            <div class="admin-form-group">
                <label class="admin-form-label">Username</label>
                <input type="text" name="username" value="<?= htmlspecialchars($admin['username'] ?? '') ?>" class="admin-input" required>
            </div>

            <div class="admin-form-group">
                <label class="admin-form-label">Email Address</label>
                <input type="email" name="email" value="<?= htmlspecialchars($admin['email'] ?? '') ?>" class="admin-input" required>
            </div>
        </div>

        <h4 style="color:var(--primary); margin: 32px 0 16px 0; border-top:1px solid var(--border); padding-top:20px;">
            Change Password (Optional)
        </h4>

        <div class="admin-form-group">
            <label class="admin-form-label">Current Password</label>
            <input type="password" name="current_password" class="admin-input" placeholder="Enter current password to change it">
        </div>

        <div class="admin-grid-2">
            <div class="admin-form-group">
                <label class="admin-form-label">New Password</label>
                <input type="password" name="new_password" class="admin-input" placeholder="Minimum 6 characters">
            </div>

            <div class="admin-form-group">
                <label class="admin-form-label">Confirm New Password</label>
                <input type="password" name="confirm_password" class="admin-input" placeholder="Re-type new password">
            </div>
        </div>

        <div style="margin-top: 28px;">
            <button type="submit" class="btn-admin btn-admin-primary" style="padding:12px 28px;">
                Save Profile & Password
            </button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
