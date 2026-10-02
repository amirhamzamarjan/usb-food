<?php
/**
 * US BENGOL - Admin Dashboard Overview
 */

$pageTitle = 'Dashboard Overview';
$activeNav = 'dashboard';

require_once __DIR__ . '/includes/header.php';

try {
    $pdo = Database::getConnection();

    // Stats
    $totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    $totalBulkOrders = $pdo->query("SELECT COUNT(*) FROM bulk_orders")->fetchColumn();
    $newBulkOrders = $pdo->query("SELECT COUNT(*) FROM bulk_orders WHERE status = 'new'")->fetchColumn();
    $totalMessages = $pdo->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn();

    // Recent Bulk Orders
    $recentOrdersStmt = $pdo->query("SELECT * FROM bulk_orders ORDER BY id DESC LIMIT 5");
    $recentOrders = $recentOrdersStmt->fetchAll();

    // Recent Products
    $productsStmt = $pdo->query("SELECT * FROM products ORDER BY sort_order ASC, id ASC LIMIT 5");
    $recentProducts = $productsStmt->fetchAll();

} catch (Exception $e) {
    error_log("Dashboard Load Error: " . $e->getMessage());
    $totalProducts = 0; $totalBulkOrders = 0; $newBulkOrders = 0; $totalMessages = 0;
    $recentOrders = []; $recentProducts = [];
}
?>

<!-- Metric Stats Row -->
<div class="dash-stats-grid">
    <div class="dash-stat-card">
        <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
        </div>
        <div>
            <div class="stat-val"><?= $totalProducts ?></div>
            <div class="stat-label">Active Products</div>
        </div>
    </div>

    <div class="dash-stat-card">
        <div class="stat-icon" style="color:#E65100; background:#FFF3E0;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
        </div>
        <div>
            <div class="stat-val"><?= $newBulkOrders ?></div>
            <div class="stat-label">New Bulk Inquiries</div>
        </div>
    </div>

    <div class="dash-stat-card">
        <div class="stat-icon" style="color:#0D47A1; background:#E3F2FD;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
        </div>
        <div>
            <div class="stat-val"><?= $totalBulkOrders ?></div>
            <div class="stat-label">Total Bulk Requests</div>
        </div>
    </div>

    <div class="dash-stat-card">
        <div class="stat-icon" style="color:#7B1FA2; background:#F3E5F5;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
        </div>
        <div>
            <div class="stat-val"><?= $totalMessages ?></div>
            <div class="stat-label">Contact Messages</div>
        </div>
    </div>
</div>

<!-- Recent Bulk Orders -->
<div class="admin-card">
    <div class="admin-card-header">
        <h3 class="admin-card-title">Recent Bulk Order Submissions</h3>
        <a href="bulk-orders.php" class="btn-admin btn-admin-secondary btn-admin-sm">View All Submissions &rarr;</a>
    </div>

    <?php if (empty($recentOrders)): ?>
        <p style="color:var(--text-muted); font-size:0.95rem; text-align:center; padding: 20px 0;">No bulk order requests received yet.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Organization</th>
                        <th>Contact Person</th>
                        <th>Phone</th>
                        <th>Product & Quantity</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentOrders as $order): ?>
                        <tr>
                            <td><?= date('d M, Y', strtotime($order['created_at'])) ?></td>
                            <td><strong><?= htmlspecialchars($order['organization']) ?></strong></td>
                            <td><?= htmlspecialchars($order['full_name']) ?></td>
                            <td>
                                <a href="tel:<?= htmlspecialchars($order['phone']) ?>" style="color:var(--primary); font-weight:700;">
                                    <?= htmlspecialchars($order['phone']) ?>
                                </a>
                            </td>
                            <td>
                                <div><?= htmlspecialchars($order['product_name']) ?></div>
                                <small style="color:var(--text-muted); font-weight:600;">Qty: <?= htmlspecialchars($order['estimated_quantity']) ?></small>
                            </td>
                            <td><?= htmlspecialchars($order['delivery_location']) ?></td>
                            <td>
                                <span class="status-pill status-<?= htmlspecialchars($order['status']) ?>">
                                    <?= ucfirst($order['status']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="bulk-orders.php?id=<?= $order['id'] ?>" class="btn-admin btn-admin-secondary btn-admin-sm">Details</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Quick Product Management Strip -->
<div class="admin-card">
    <div class="admin-card-header">
        <h3 class="admin-card-title">Current Product Catalog (Quick Price View)</h3>
        <a href="products.php" class="btn-admin btn-admin-primary btn-admin-sm">+ Manage Products & Prices</a>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Name (English / Bangla)</th>
                    <th>Category</th>
                    <th>Current Price</th>
                    <th>Availability</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentProducts as $prod): ?>
                    <tr>
                        <td>
                            <img src="../<?= htmlspecialchars($prod['image'] ?: 'images/ataruti.png') ?>" alt="" class="product-thumb">
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($prod['name']) ?></strong>
                            <?php if (!empty($prod['name_bn'])): ?>
                                <div style="font-family:'Noto Sans Bengali'; font-size:0.85rem; color:#8C651F;"><?= htmlspecialchars($prod['name_bn']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td><?= ucfirst($prod['category']) ?></td>
                        <td>
                            <span style="font-size:1.1rem; font-weight:800; color:var(--primary);">
                                <?= format_price($prod['price']) ?>
                            </span>
                            <small style="color:var(--text-muted);">/ <?= htmlspecialchars($prod['unit']) ?></small>
                        </td>
                        <td>
                            <span class="status-pill status-<?= htmlspecialchars($prod['availability']) ?>">
                                <?= str_replace('_', ' ', ucfirst($prod['availability'])) ?>
                            </span>
                        </td>
                        <td>
                            <a href="product-edit.php?id=<?= $prod['id'] ?>" class="btn-admin btn-admin-secondary btn-admin-sm">Edit Product</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
