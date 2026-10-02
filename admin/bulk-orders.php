<?php
/**
 * US BENGOL - Admin Bulk Orders Management
 */

$pageTitle = 'Bulk & Institutional Order Requests';
$activeNav = 'bulk_orders';

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';

$pdo = Database::getConnection();

// Handle Status or Notes Update
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['update_order_status'])) {
    if (verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $orderId = (int)$_POST['order_id'];
        $status = sanitize_input($_POST['status'] ?? 'new');
        $notes = sanitize_input($_POST['admin_notes'] ?? '');

        $stmt = $pdo->prepare("UPDATE bulk_orders SET status = ?, admin_notes = ? WHERE id = ?");
        $stmt->execute([$status, $notes, $orderId]);

        set_flash_message('success', 'Order status updated successfully.');
    } else {
        set_flash_message('error', 'Invalid security token.');
    }
    header("Location: bulk-orders.php" . ($orderId ? "?id=" . $orderId : ""));
    exit;
}

// Single order detail view check
$selectedId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$selectedOrder = null;
if ($selectedId > 0) {
    $stmt = $pdo->prepare("SELECT * FROM bulk_orders WHERE id = ?");
    $stmt->execute([$selectedId]);
    $selectedOrder = $stmt->fetch();
}

// Fetch all orders
$stmt = $pdo->query("SELECT * FROM bulk_orders ORDER BY id DESC");
$orders = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<?php if ($selectedOrder): ?>
    <!-- Single Order Detail View -->
    <div class="admin-card" style="max-width: 800px;">
        <div class="admin-card-header">
            <div>
                <h3 class="admin-card-title">Bulk Request #<?= $selectedOrder['id'] ?> &bull; <?= htmlspecialchars($selectedOrder['organization']) ?></h3>
                <small style="color:var(--text-muted);">Submitted on <?= date('F j, Y, g:i a', strtotime($selectedOrder['created_at'])) ?></small>
            </div>
            <a href="bulk-orders.php" class="btn-admin btn-admin-secondary btn-admin-sm">&larr; Back to All Orders</a>
        </div>

        <div style="background:#FAF8F5; border:1px solid var(--border); border-radius:12px; padding:24px; margin-bottom:28px;">
            <div class="admin-grid-2" style="margin-bottom:16px;">
                <div>
                    <label style="font-size:0.8rem; text-transform:uppercase; color:var(--text-muted); font-weight:700;">Contact Name</label>
                    <div style="font-size:1.1rem; font-weight:700;"><?= htmlspecialchars($selectedOrder['full_name']) ?></div>
                </div>
                <div>
                    <label style="font-size:0.8rem; text-transform:uppercase; color:var(--text-muted); font-weight:700;">Organization</label>
                    <div style="font-size:1.1rem; font-weight:700;"><?= htmlspecialchars($selectedOrder['organization']) ?></div>
                </div>
            </div>

            <div class="admin-grid-2" style="margin-bottom:16px;">
                <div>
                    <label style="font-size:0.8rem; text-transform:uppercase; color:var(--text-muted); font-weight:700;">Phone Number</label>
                    <div>
                        <a href="tel:<?= htmlspecialchars($selectedOrder['phone']) ?>" style="font-size:1.1rem; font-weight:800; color:var(--primary);">
                            <?= htmlspecialchars($selectedOrder['phone']) ?>
                        </a>
                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $selectedOrder['phone']) ?>" target="_blank" class="btn-admin btn-admin-sm" style="background:#25D366; color:#FFF; margin-left:8px;">
                            Chat WhatsApp
                        </a>
                    </div>
                </div>
                <div>
                    <label style="font-size:0.8rem; text-transform:uppercase; color:var(--text-muted); font-weight:700;">Email Address</label>
                    <div style="font-size:1rem;"><?= htmlspecialchars($selectedOrder['email'] ?: 'Not provided') ?></div>
                </div>
            </div>

            <hr style="border:none; border-top:1px solid var(--border); margin:16px 0;">

            <div class="admin-grid-2" style="margin-bottom:16px;">
                <div>
                    <label style="font-size:0.8rem; text-transform:uppercase; color:var(--text-muted); font-weight:700;">Product Requested</label>
                    <div style="font-size:1.15rem; font-weight:800; color:var(--text-main);"><?= htmlspecialchars($selectedOrder['product_name']) ?></div>
                </div>
                <div>
                    <label style="font-size:0.8rem; text-transform:uppercase; color:var(--text-muted); font-weight:700;">Estimated Quantity</label>
                    <div style="font-size:1.15rem; font-weight:800; color:var(--primary);"><?= htmlspecialchars($selectedOrder['estimated_quantity']) ?></div>
                </div>
            </div>

            <div class="admin-grid-2" style="margin-bottom:16px;">
                <div>
                    <label style="font-size:0.8rem; text-transform:uppercase; color:var(--text-muted); font-weight:700;">Delivery Location</label>
                    <div style="font-size:1rem; font-weight:600;"><?= htmlspecialchars($selectedOrder['delivery_location']) ?></div>
                </div>
                <div>
                    <label style="font-size:0.8rem; text-transform:uppercase; color:var(--text-muted); font-weight:700;">Preferred Schedule</label>
                    <div style="font-size:1rem;"><?= htmlspecialchars($selectedOrder['preferred_schedule'] ?: 'Standard Delivery') ?></div>
                </div>
            </div>

            <?php if (!empty($selectedOrder['message'])): ?>
                <div style="margin-top:16px;">
                    <label style="font-size:0.8rem; text-transform:uppercase; color:var(--text-muted); font-weight:700;">Additional Message / Requirements</label>
                    <p style="background:#FFF; padding:12px; border-radius:8px; border:1px solid var(--border); font-size:0.95rem; margin-top:4px;">
                        <?= nl2br(htmlspecialchars($selectedOrder['message'])) ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Status update form -->
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= get_csrf_token() ?>">
            <input type="hidden" name="update_order_status" value="1">
            <input type="hidden" name="order_id" value="<?= $selectedOrder['id'] ?>">

            <div class="admin-grid-2">
                <div class="admin-form-group">
                    <label class="admin-form-label">Processing Status</label>
                    <select name="status" class="admin-select">
                        <option value="new" <?= $selectedOrder['status'] === 'new' ? 'selected' : '' ?>>New / Unaddressed</option>
                        <option value="contacted" <?= $selectedOrder['status'] === 'contacted' ? 'selected' : '' ?>>Contacted & Negotiating</option>
                        <option value="completed" <?= $selectedOrder['status'] === 'completed' ? 'selected' : '' ?>>Completed / Active Contract</option>
                        <option value="cancelled" <?= $selectedOrder['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled / Inactive</option>
                    </select>
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label">Internal Admin Notes</label>
                    <input type="text" name="admin_notes" value="<?= htmlspecialchars($selectedOrder['admin_notes'] ?? '') ?>" class="admin-input" placeholder="e.g. Quoted ৳12/pc, weekly batch delivery.">
                </div>
            </div>

            <button type="submit" class="btn-admin btn-admin-primary">Save Status & Notes</button>
        </form>
    </div>
<?php endif; ?>

<!-- All Orders Table -->
<div class="admin-card">
    <div class="admin-card-header">
        <h3 class="admin-card-title">Bulk Order Submissions (<?= count($orders) ?>)</h3>
    </div>

    <?php if (empty($orders)): ?>
        <p style="color:var(--text-muted); font-size:0.95rem; text-align:center; padding: 24px 0;">No bulk order requests received yet.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#ID</th>
                        <th>Date</th>
                        <th>Organization</th>
                        <th>Contact</th>
                        <th>Phone</th>
                        <th>Product & Qty</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $o): ?>
                        <tr style="<?= ($selectedId === (int)$o['id']) ? 'background:#FAF4E8;' : '' ?>">
                            <td>#<?= $o['id'] ?></td>
                            <td><?= date('d M Y', strtotime($o['created_at'])) ?></td>
                            <td><strong><?= htmlspecialchars($o['organization']) ?></strong></td>
                            <td><?= htmlspecialchars($o['full_name']) ?></td>
                            <td>
                                <a href="tel:<?= htmlspecialchars($o['phone']) ?>" style="color:var(--primary); font-weight:700;">
                                    <?= htmlspecialchars($o['phone']) ?>
                                </a>
                            </td>
                            <td>
                                <div><?= htmlspecialchars($o['product_name']) ?></div>
                                <small style="color:var(--text-muted); font-weight:600;">Qty: <?= htmlspecialchars($o['estimated_quantity']) ?></small>
                            </td>
                            <td><?= htmlspecialchars($o['delivery_location']) ?></td>
                            <td>
                                <span class="status-pill status-<?= htmlspecialchars($o['status']) ?>">
                                    <?= ucfirst($o['status']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="bulk-orders.php?id=<?= $o['id'] ?>" class="btn-admin btn-admin-secondary btn-admin-sm">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
