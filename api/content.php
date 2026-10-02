<?php
/**
 * US BENGOL - Site Content API Endpoint
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';

try {
    $pdo = Database::getConnection();
    $stmt = $pdo->query("SELECT key_name, content_value FROM site_content");
    $content = [];
    while ($row = $stmt->fetch()) {
        $content[$row['key_name']] = $row['content_value'];
    }

    json_response([
        'success' => true,
        'content' => $content
    ]);
} catch (Exception $e) {
    json_response(['success' => false, 'error' => 'Failed to retrieve site content.'], 500);
}
