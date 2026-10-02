<?php
/**
 * US BENGOL - Admin Dynamic Website Content Management
 */

$pageTitle = 'Website Content & Copy Management';
$activeNav = 'content';

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';

$pdo = Database::getConnection();

// Save content updates
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash_message('error', 'Invalid security token.');
    } else {
        $fields = [
            'hero_headline',
            'hero_subtitle',
            'story_title',
            'story_subtitle',
            'why_us_headline',
            'how_we_prepare_headline',
            'bulk_order_headline',
            'delivery_headline',
            'contact_address',
            'contact_phone',
            'contact_whatsapp'
        ];

        $stmt = $pdo->prepare("
            INSERT INTO site_content (key_name, content_value) VALUES (:key, :val)
            ON DUPLICATE KEY UPDATE content_value = :val2
        ");

        // Use fallback if SQLite
        $isSqlite = (Database::getDriverType() === 'sqlite');
        if ($isSqlite) {
            $stmt = $pdo->prepare("INSERT OR REPLACE INTO site_content (key_name, content_value) VALUES (:key, :val)");
        }

        foreach ($fields as $field) {
            $val = sanitize_input($_POST[$field] ?? '');
            if ($isSqlite) {
                $stmt->execute(['key' => $field, 'val' => $val]);
            } else {
                $stmt->execute(['key' => $field, 'val' => $val, 'val2' => $val]);
            }
        }

        set_flash_message('success', 'Website content updated successfully! Changes are live on the homepage.');
    }
    header("Location: content.php");
    exit;
}

// Fetch current content
$stmt = $pdo->query("SELECT key_name, content_value FROM site_content");
$content = [];
while ($row = $stmt->fetch()) {
    $content[$row['key_name']] = $row['content_value'];
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="admin-card" style="max-width: 900px;">
    <div class="admin-card-header">
        <div>
            <h3 class="admin-card-title">Edit Website Headings & Copy</h3>
            <p style="font-size:0.88rem; color:var(--text-muted); margin-top:4px;">
                You can customize the main messages, addresses, and headlines without modifying any HTML files.
            </p>
        </div>
    </div>

    <form method="POST" action="content.php">
        <input type="hidden" name="csrf_token" value="<?= get_csrf_token() ?>">

        <h4 style="color:var(--primary); margin-bottom:16px; border-bottom:1px solid var(--border); padding-bottom:8px;">
            1. Hero Section & Main Tagline
        </h4>

        <div class="admin-form-group">
            <label class="admin-form-label">Hero Main Headline</label>
            <input type="text" name="hero_headline" value="<?= htmlspecialchars($content['hero_headline'] ?? 'Traditional Taste, Made with Care.') ?>" class="admin-input" required>
        </div>

        <div class="admin-form-group">
            <label class="admin-form-label">Hero Supporting Subtitle</label>
            <textarea name="hero_subtitle" rows="3" class="admin-textarea" required><?= htmlspecialchars($content['hero_subtitle'] ?? '') ?></textarea>
        </div>

        <h4 style="color:var(--primary); margin: 28px 0 16px 0; border-bottom:1px solid var(--border); padding-bottom:8px;">
            2. Brand Story ("Rooted in Tradition")
        </h4>

        <div class="admin-form-group">
            <label class="admin-form-label">Story Section Title</label>
            <input type="text" name="story_title" value="<?= htmlspecialchars($content['story_title'] ?? 'Rooted in Tradition') ?>" class="admin-input">
        </div>

        <div class="admin-form-group">
            <label class="admin-form-label">Story Lead Summary</label>
            <textarea name="story_subtitle" rows="2" class="admin-textarea"><?= htmlspecialchars($content['story_subtitle'] ?? '') ?></textarea>
        </div>

        <h4 style="color:var(--primary); margin: 28px 0 16px 0; border-bottom:1px solid var(--border); padding-bottom:8px;">
            3. Business Contact, Location & WhatsApp
        </h4>

        <div class="admin-form-group">
            <label class="admin-form-label">Official Address (Shown in Contact & Footer)</label>
            <input type="text" name="contact_address" value="<?= htmlspecialchars($content['contact_address'] ?? 'Bawgan, Uttarkhan, Dhaka 1230, Bangladesh') ?>" class="admin-input" required>
        </div>

        <div class="admin-grid-2">
            <div class="admin-form-group">
                <label class="admin-form-label">Display Phone / WhatsApp Number</label>
                <input type="text" name="contact_phone" value="<?= htmlspecialchars($content['contact_phone'] ?? '01921692008') ?>" class="admin-input" required>
            </div>

            <div class="admin-form-group">
                <label class="admin-form-label">WhatsApp International Number (for wa.me links)</label>
                <input type="text" name="contact_whatsapp" value="<?= htmlspecialchars($content['contact_whatsapp'] ?? '8801921692008') ?>" class="admin-input" required>
            </div>
        </div>

        <div style="margin-top: 32px;">
            <button type="submit" class="btn-admin btn-admin-primary" style="padding:14px 28px; font-size:1rem;">
                Save Content Changes
            </button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
