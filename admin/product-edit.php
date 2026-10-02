<?php
/**
 * US BENGOL - Admin Add / Edit Product (with Image Upload)
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';

$pdo = Database::getConnection();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = null;

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();
    if (!$product) {
        set_flash_message('error', 'Product not found.');
        header("Location: products.php");
        exit;
    }
}

$pageTitle = $product ? 'Edit Product: ' . $product['name'] : 'Add New Product';
$activeNav = 'products';

$errors = [];

// Form submission handling
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid CSRF security token.';
    } else {
        $name = sanitize_input($_POST['name'] ?? '');
        $nameBn = sanitize_input($_POST['name_bn'] ?? '');
        $category = sanitize_input($_POST['category'] ?? 'staple');
        $shortDesc = sanitize_input($_POST['short_description'] ?? '');
        $desc = sanitize_input($_POST['description'] ?? '');
        $price = (float)($_POST['price'] ?? 0.00);
        $unit = sanitize_input($_POST['unit'] ?? 'pack of 10 pcs');
        $badge = sanitize_input($_POST['badge'] ?? '');
        $availability = sanitize_input($_POST['availability'] ?? 'in_stock');
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
        $sortOrder = (int)($_POST['sort_order'] ?? 0);

        // Process characteristics list (lines to json array)
        $charLines = explode("\n", $_POST['characteristics'] ?? '');
        $characteristics = [];
        foreach ($charLines as $line) {
            $line = trim($line);
            if (!empty($line)) $characteristics[] = sanitize_input($line);
        }
        $charsJson = json_encode($characteristics, JSON_UNESCAPED_UNICODE);

        if (empty($name)) {
            $errors[] = 'Product Name is required.';
        }

        // Slug generation
        $slugBase = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name));
        $slug = trim($slugBase, '-');
        if (empty($slug)) $slug = 'product-' . time();

        // Image Handling
        $imagePath = $product['image'] ?? 'images/ataruti.png';

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = upload_product_image($_FILES['image']);
            if ($uploadResult['success']) {
                $imagePath = $uploadResult['path'];
            } else {
                $errors[] = 'Image upload error: ' . $uploadResult['error'];
            }
        }

        if (empty($errors)) {
            if ($product) {
                // Update
                $updateSql = "
                    UPDATE products SET
                        name = :name,
                        name_bn = :name_bn,
                        category = :category,
                        short_description = :short_description,
                        description = :description,
                        price = :price,
                        unit = :unit,
                        image = :image,
                        badge = :badge,
                        availability = :availability,
                        is_active = :is_active,
                        is_featured = :is_featured,
                        sort_order = :sort_order,
                        characteristics = :characteristics
                    WHERE id = :id
                ";
                $stmt = $pdo->prepare($updateSql);
                $stmt->execute([
                    'name' => $name,
                    'name_bn' => $nameBn,
                    'category' => $category,
                    'short_description' => $shortDesc,
                    'description' => $desc,
                    'price' => $price,
                    'unit' => $unit,
                    'image' => $imagePath,
                    'badge' => $badge,
                    'availability' => $availability,
                    'is_active' => $isActive,
                    'is_featured' => $isFeatured,
                    'sort_order' => $sortOrder,
                    'characteristics' => $charsJson,
                    'id' => $id
                ]);

                set_flash_message('success', 'Product updated successfully.');
            } else {
                // Create
                $insertSql = "
                    INSERT INTO products
                    (name, name_bn, slug, category, short_description, description, price, unit, image, badge, availability, is_active, is_featured, sort_order, characteristics)
                    VALUES
                    (:name, :name_bn, :slug, :category, :short_description, :description, :price, :unit, :image, :badge, :availability, :is_active, :is_featured, :sort_order, :characteristics)
                ";
                $stmt = $pdo->prepare($insertSql);
                $stmt->execute([
                    'name' => $name,
                    'name_bn' => $nameBn,
                    'slug' => $slug,
                    'category' => $category,
                    'short_description' => $shortDesc,
                    'description' => $desc,
                    'price' => $price,
                    'unit' => $unit,
                    'image' => $imagePath,
                    'badge' => $badge,
                    'availability' => $availability,
                    'is_active' => $isActive,
                    'is_featured' => $isFeatured,
                    'sort_order' => $sortOrder,
                    'characteristics' => $charsJson
                ]);

                set_flash_message('success', 'New product added successfully.');
            }

            header("Location: products.php");
            exit;
        }
    }
}

// Format existing characteristics for textarea
$existingChars = '';
if ($product && !empty($product['characteristics'])) {
    $arr = json_decode($product['characteristics'], true);
    if (is_array($arr)) {
        $existingChars = implode("\n", $arr);
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="admin-card" style="max-width: 900px;">
    <div class="admin-card-header">
        <h3 class="admin-card-title"><?= $product ? 'Edit Product Details' : 'Add New Product' ?></h3>
        <a href="products.php" class="btn-admin btn-admin-secondary btn-admin-sm">&larr; Back to Products</a>
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

    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= get_csrf_token() ?>">

        <div class="admin-grid-2">
            <div class="admin-form-group">
                <label class="admin-form-label">Product Name (English) *</label>
                <input type="text" name="name" value="<?= htmlspecialchars($product['name'] ?? '') ?>" class="admin-input" required placeholder="e.g. Atta Roti">
            </div>

            <div class="admin-form-group">
                <label class="admin-form-label">Product Name (Bangla)</label>
                <input type="text" name="name_bn" value="<?= htmlspecialchars($product['name_bn'] ?? '') ?>" class="admin-input" placeholder="e.g. আটা রুটি">
            </div>
        </div>

        <div class="admin-grid-2">
            <div class="admin-form-group">
                <label class="admin-form-label">Price (৳ BDT) *</label>
                <input type="number" step="0.50" min="0" name="price" value="<?= htmlspecialchars($product['price'] ?? '0.00') ?>" class="admin-input" required>
                <small style="color:var(--text-muted);">Set 0.00 if pricing is flexible or pending final update.</small>
            </div>

            <div class="admin-form-group">
                <label class="admin-form-label">Packaging Unit</label>
                <input type="text" name="unit" value="<?= htmlspecialchars($product['unit'] ?? 'pack of 10 pcs') ?>" class="admin-input" placeholder="e.g. pack of 10 pcs / 500g Jar">
            </div>
        </div>

        <div class="admin-grid-2">
            <div class="admin-form-group">
                <label class="admin-form-label">Category</label>
                <select name="category" class="admin-select">
                    <option value="roti" <?= ($product['category'] ?? '') === 'roti' ? 'selected' : '' ?>>Roti & Bread</option>
                    <option value="parota" <?= ($product['category'] ?? '') === 'parota' ? 'selected' : '' ?>>Parota</option>
                    <option value="staple" <?= ($product['category'] ?? '') === 'staple' ? 'selected' : '' ?>>Ghee & Staples</option>
                    <option value="dietary" <?= ($product['category'] ?? '') === 'dietary' ? 'selected' : '' ?>>Special Dietary</option>
                    <option value="flour" <?= ($product['category'] ?? '') === 'flour' ? 'selected' : '' ?>>Flour & Grains</option>
                </select>
            </div>

            <div class="admin-form-group">
                <label class="admin-form-label">Badge Label (Optional)</label>
                <input type="text" name="badge" value="<?= htmlspecialchars($product['badge'] ?? '') ?>" class="admin-input" placeholder="e.g. Daily Essential / Pure & Aromatic">
            </div>
        </div>

        <!-- Image Upload Section -->
        <div class="admin-form-group" style="background:#FAF8F5; padding:20px; border-radius:12px; border:1.5px dashed var(--border);">
            <label class="admin-form-label">Product Image</label>
            <?php if (!empty($product['image'])): ?>
                <div style="display:flex; align-items:center; gap:16px; margin-bottom:12px;">
                    <img src="../<?= htmlspecialchars($product['image']) ?>" alt="Current Product Image" style="height:64px; width:64px; object-fit:contain; background:#FFF; border-radius:8px; border:1px solid var(--border);">
                    <div style="font-size:0.88rem; color:var(--text-muted);">
                        Current image: <code><?= htmlspecialchars($product['image']) ?></code>
                    </div>
                </div>
            <?php endif; ?>

            <input type="file" name="image" accept="image/png, image/jpeg, image/webp" class="admin-input" style="background:#FFF;">
            <small style="color:var(--text-muted); display:block; margin-top:4px;">
                Supported formats: PNG, JPG, WEBP. Max size: 5MB.
            </small>
        </div>

        <div class="admin-form-group">
            <label class="admin-form-label">Short Description *</label>
            <textarea name="short_description" rows="2" class="admin-textarea" required placeholder="Brief tagline shown on product cards..."><?= htmlspecialchars($product['short_description'] ?? '') ?></textarea>
        </div>

        <div class="admin-form-group">
            <label class="admin-form-label">Full Detailed Description</label>
            <textarea name="description" rows="4" class="admin-textarea" placeholder="Detailed product description shown inside quick view modal..."><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
        </div>

        <div class="admin-form-group">
            <label class="admin-form-label">Key Characteristics (One per line)</label>
            <textarea name="characteristics" rows="3" class="admin-textarea" placeholder="100% Whole Wheat&#10;Soft & Pliable&#10;No Preservatives"><?= htmlspecialchars($existingChars) ?></textarea>
        </div>

        <div class="admin-grid-2">
            <div class="admin-form-group">
                <label class="admin-form-label">Availability Status</label>
                <select name="availability" class="admin-select">
                    <option value="in_stock" <?= ($product['availability'] ?? '') === 'in_stock' ? 'selected' : '' ?>>In Stock</option>
                    <option value="pre_order" <?= ($product['availability'] ?? '') === 'pre_order' ? 'selected' : '' ?>>Pre-Order</option>
                    <option value="out_of_stock" <?= ($product['availability'] ?? '') === 'out_of_stock' ? 'selected' : '' ?>>Out of Stock</option>
                </select>
            </div>

            <div class="admin-form-group">
                <label class="admin-form-label">Sort Display Order</label>
                <input type="number" name="sort_order" value="<?= htmlspecialchars($product['sort_order'] ?? '0') ?>" class="admin-input">
            </div>
        </div>

        <div style="display:flex; gap:24px; margin-bottom:28px;">
            <label style="display:flex; align-items:center; gap:8px; font-weight:600; cursor:pointer;">
                <input type="checkbox" name="is_active" value="1" <?= (!isset($product) || $product['is_active']) ? 'checked' : '' ?>>
                Visible on Website
            </label>

            <label style="display:flex; align-items:center; gap:8px; font-weight:600; cursor:pointer;">
                <input type="checkbox" name="is_featured" value="1" <?= (isset($product) && $product['is_featured']) ? 'checked' : '' ?>>
                Featured Product
            </label>
        </div>

        <div style="display:flex; gap:12px;">
            <button type="submit" class="btn-admin btn-admin-primary" style="padding:14px 28px; font-size:1rem;">
                <?= $product ? 'Save Changes' : 'Create Product' ?>
            </button>
            <a href="products.php" class="btn-admin btn-admin-secondary" style="padding:14px 24px;">Cancel</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
