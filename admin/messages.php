<?php
/**
 * US BENGOL - Admin Contact Messages Management
 */

$pageTitle = 'Contact Messages & Feedback';
$activeNav = 'messages';

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';

$pdo = Database::getConnection();

// Delete message
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    if (verify_csrf_token($_GET['csrf_token'] ?? '')) {
        $delId = (int)$_GET['id'];
        $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
        $stmt->execute([$delId]);
        set_flash_message('success', 'Message deleted.');
    }
    header("Location: messages.php");
    exit;
}

// Fetch all messages
$stmt = $pdo->query("SELECT * FROM contact_messages ORDER BY id DESC");
$messages = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="admin-card">
    <div class="admin-card-header">
        <h3 class="admin-card-title">General Contact Inquiries (<?= count($messages) ?>)</h3>
    </div>

    <?php if (empty($messages)): ?>
        <p style="color:var(--text-muted); font-size:0.95rem; text-align:center; padding: 24px 0;">No contact messages received yet.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Sender Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($messages as $m): ?>
                        <tr>
                            <td><?= date('d M, Y', strtotime($m['created_at'])) ?></td>
                            <td><strong><?= htmlspecialchars($m['name']) ?></strong></td>
                            <td>
                                <a href="tel:<?= htmlspecialchars($m['phone']) ?>" style="color:var(--primary); font-weight:700;">
                                    <?= htmlspecialchars($m['phone']) ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($m['email'] ?: '—') ?></td>
                            <td><?= htmlspecialchars($m['subject'] ?: 'General Inquiry') ?></td>
                            <td style="max-width:320px;">
                                <div style="font-size:0.9rem; line-height:1.4; color:var(--text-body);">
                                    <?= nl2br(htmlspecialchars($m['message'])) ?>
                                </div>
                            </td>
                            <td>
                                <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $m['phone']) ?>" target="_blank" class="btn-admin btn-admin-sm" style="background:#25D366; color:#FFF; margin-bottom:4px;">
                                    WhatsApp
                                </a>
                                <a href="messages.php?action=delete&id=<?= $m['id'] ?>&csrf_token=<?= get_csrf_token() ?>" onclick="return confirm('Delete this message?');" class="btn-admin btn-admin-danger btn-admin-sm">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
