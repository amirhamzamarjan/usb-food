<?php
/**
 * US BENGOL - Admin Product Management
 */

$pageTitle = 'Product & Price Management';
$activeNav = 'products';

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';

$pdo = Database::getConnection();

// Handle Delete Product
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    if (verify_csrf_token($_GET['csrf_token'] ?? '')) {
        $deleteId = (int)$_GET['id'];
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$deleteId]);
        set_flash_message('success', 'Product removed successfully.');
    } else {
        set_flash_message('error', 'Invalid security token.');
    }
    header("Location: products.php");
    exit;
}

// Handle Quick Price Update Form
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['quick_price_update'])) {
    if (verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $prices = $_POST['prices'] ?? [];
        $availabilities = $_POST['availabilities'] ?? [];

        $updateStmt = $pdo->prepare("UPDATE products SET price = :price, availability = :avail WHERE id = :id");

        foreach ($prices as $id => $priceVal) {
            $id = (int)$id;
            $price = (float)$priceVal;
            $avail = $availabilities[$id] ?? 'in_stock';
            $updateStmt->execute([
                'price' => $price,
                'avail' => $avail,
                'id' => $id
            ]);
        }

        set_flash_message('success', 'Product prices and availability updated successfully!');
    } else {
        set_flash_message('error', 'Invalid security token.');
    }
    header("Location: products.php");
    exit;
}

// Fetch all products
$stmt = $pdo->query("SELECT * FROM products ORDER BY sort_order ASC, id ASC");
$allProducts = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h3 class="admin-card-title">Manage Products & Prices</h3>
            <p style="font-size:0.88rem; color:var(--text-muted); margin-top:4px;">
                You can change product prices and availability directly here, or click Edit to modify images, descriptions, and badges.
            </p>
        </div>
        <a href="product-edit.php" class="btn-admin btn-admin-primary">+ Add New Product</a>
    </div>

    <form method="POST" action="products.php">
        <input type="hidden" name="csrf_token" value="<?= get_csrf_token() ?>">
        <input type="hidden" name="quick_price_update" value="1">

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 70px;">Image</th>
                        <th>Product Details</th>
                        <th>Category & Badge</th>
                        <th style="width: 140px;">Price (৳ BDT)</th>
                        <th style="width: 150px;">Availability</th>
                        <th style="width: 80px;">Active</th>
                        <th style="width: 160px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allProducts as $p): ?>
                        <tr>
                            <td>
                                <img src="../<?= htmlspecialchars($p['image'] ?: 'images/ataruti.png') ?>" alt="" class="product-thumb">
                            </td>
                            <td>
                                <div style="font-weight: 800; font-size: 1rem; color: var(--text-main);">
                                    <?= htmlspecialchars($p['name']) ?>
                                </div>
                                <?php if (!empty($p['name_bn'])): ?>
                                    <div style="font-family:'Noto Sans Bengali'; font-size:0.85rem; color:#8C651F;"><?= htmlspecialchars($p['name_bn']) ?></div>
                                <?php endif; ?>
                                <small style="color:var(--text-muted);"><?= htmlspecialchars($p['unit'] ?? 'pack') ?></small>
                            </td>
                            <td>
                                <div style="font-weight:600; text-transform:capitalize;"><?= htmlspecialchars($p['category']) ?></div>
                                <?php if (!empty($p['badge'])): ?>
                                    <span style="display:inline-block; font-size:0.75rem; background:#FAF6EE; border:1px solid var(--border); padding:2px 8px; border-radius:999px; margin-top:4px; font-weight:700; color:#6B4515;">
                                        <?= htmlspecialchars($p['badge']) ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="display:flex; align-items:center; gap:4px;">
                                    <span style="font-weight:700; color:var(--text-muted);">৳</span>
                                    <input type="number" step="0.50" min="0" name="prices[<?= $p['id'] ?>]" value="<?= number_format((float)$p['price'], 2, '.', '') ?>" class="admin-input" style="padding:6px 10px; font-weight:700; width:100px;">
                                </div>
                            </td>
                            <td>
                                <select name="availabilities[<?= $p['id'] ?>]" class="admin-select" style="padding:6px 10px; font-size:0.88rem;">
                                    <option value="in_stock" <?= $p['availability'] === 'in_stock' ? 'selected' : '' ?>>In Stock</option>
                                    <option value="pre_order" <?= $p['availability'] === 'pre_order' ? 'selected' : '' ?>>Pre-Order</option>
                                    <option value="out_of_stock" <?= $p['availability'] === 'out_of_stock' ? 'selected' : '' ?>>Out of Stock</option>
                                </select>
                            </td>
                            <td>
                                <span class="status-pill <?= $p['is_active'] ? 'status-in_stock' : 'status-out_of_stock' ?>">
                                    <?= $p['is_active'] ? 'Yes' : 'Hidden' ?>
                                </span>
                            </td>
                            <td>
                                <div style="display:flex; gap:6px;">
                                    <a href="product-edit.php?id=<?= $p['id'] ?>" class="btn-admin btn-admin-secondary btn-admin-sm">Edit</a>
                                    <a href="products.php?action=delete&id=<?= $p['id'] ?>&csrf_token=<?= get_csrf_token() ?>" onclick="return confirm('Are you sure you want to delete <?= addslashes($p['name']) ?>?');" class="btn-admin btn-admin-danger btn-admin-sm">Del</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div style="margin-top: 24px; display:flex; justify-content:flex-end;">
            <button type="submit" class="btn-admin btn-admin-primary" style="padding:12px 24px;">
                Save Price & Availability Changes
            </button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
