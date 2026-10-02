<?php
/**
 * US BENGOL - Products Public API Endpoint
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';

try {
    $pdo = Database::getConnection();

    $category = isset($_GET['category']) ? sanitize_input($_GET['category']) : null;
    $slug = isset($_GET['slug']) ? sanitize_input($_GET['slug']) : null;
    $id = isset($_GET['id']) ? (int)$_GET['id'] : null;

    if ($id) {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND is_active = 1 LIMIT 1");
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        if (!$product) {
            json_response(['success' => false, 'error' => 'Product not found.'], 404);
        }
        $product['characteristics'] = json_decode($product['characteristics'] ?? '[]', true);
        $product['formatted_price'] = format_price($product['price']);
        $product['whatsapp_link'] = get_whatsapp_order_link($product['name'], 1, (float)$product['price']);
        json_response(['success' => true, 'product' => $product]);
    }

    if ($slug) {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE slug = ? AND is_active = 1 LIMIT 1");
        $stmt->execute([$slug]);
        $product = $stmt->fetch();
        if (!$product) {
            json_response(['success' => false, 'error' => 'Product not found.'], 404);
        }
        $product['characteristics'] = json_decode($product['characteristics'] ?? '[]', true);
        $product['formatted_price'] = format_price($product['price']);
        $product['whatsapp_link'] = get_whatsapp_order_link($product['name'], 1, (float)$product['price']);
        json_response(['success' => true, 'product' => $product]);
    }

    $sql = "SELECT * FROM products WHERE is_active = 1";
    $params = [];

    if ($category && $category !== 'all') {
        $sql .= " AND category = ?";
        $params[] = $category;
    }

    $sql .= " ORDER BY sort_order ASC, id ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll();

    foreach ($products as &$p) {
        $p['characteristics'] = json_decode($p['characteristics'] ?? '[]', true);
        $p['formatted_price'] = format_price($p['price']);
        $p['whatsapp_link'] = get_whatsapp_order_link($p['name'], 1, (float)$p['price']);
    }

    json_response([
        'success' => true,
        'count' => count($products),
        'products' => $products
    ]);

} catch (Exception $e) {
    error_log("API Products Error: " . $e->getMessage());
    json_response([
        'success' => false,
        'error' => 'Failed to load products.'
    ], 500);
}
