<?php
/**
 * US BENGOL - Security & Utility Helper Functions
 */

require_once __DIR__ . '/config.php';

/**
 * Generate or get current CSRF Token
 */
function get_csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF Token
 */
function verify_csrf_token(?string $token): bool {
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Sanitize text input
 */
function sanitize_input(?string $data): string {
    if ($data === null) return '';
    $data = trim($data);
    $data = stripslashes($data);
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Format Currency in BDT (৳)
 */
function format_price(float|int|string $price): string {
    $num = (float)$price;
    return CURRENCY_SYMBOL . number_format($num, 2);
}

/**
 * Generate international WhatsApp Link
 */
function get_whatsapp_order_link(string $productName = '', int $quantity = 1, float $price = 0.0): string {
    $basePhone = WHATSAPP_NUMBER;

    if (empty($productName)) {
        $message = "Hello US BENGOL,\nI would like to inquire about your traditional food products and place an order.";
    } else {
        $priceText = ($price > 0) ? " (Estimated: " . format_price($price * $quantity) . ")" : "";
        $message = "Hello US BENGOL,\nI would like to place an order for:\n" .
                   "• Product: {$productName}\n" .
                   "• Quantity: {$quantity}{$priceText}\n\n" .
                   "Please let me know the total price and delivery details to my location.";
    }

    return 'https://wa.me/' . $basePhone . '?text=' . urlencode($message);
}

/**
 * Secure Product Image Upload Handler
 */
function upload_product_image(array $file): array {
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['success' => false, 'error' => 'Invalid file upload parameters.'];
    }

    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['success' => false, 'error' => 'No file was uploaded.'];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Upload error code: ' . $file['error']];
    }

    if ($file['size'] > MAX_UPLOAD_SIZE) {
        return ['success' => false, 'error' => 'File size exceeds maximum allowed limit (5MB).'];
    }

    // Check MIME type using finfo
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);

    if (!in_array($mimeType, ALLOWED_IMAGE_TYPES, true)) {
        return ['success' => false, 'error' => 'Invalid file format. Allowed formats: JPG, PNG, WEBP.'];
    }

    // Determine extension
    $extensionMap = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/jpg'  => 'jpg'
    ];
    $ext = $extensionMap[$mimeType] ?? 'jpg';

    // Ensure uploads directory exists
    if (!is_dir(UPLOADS_DIR)) {
        mkdir(UPLOADS_DIR, 0755, true);
    }

    // Generate cryptographically random secure filename
    $filename = 'product_' . bin2hex(random_bytes(12)) . '.' . $ext;
    $targetPath = UPLOADS_DIR . DIRECTORY_SEPARATOR . $filename;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['success' => false, 'error' => 'Failed to save uploaded image on server.'];
    }

    return [
        'success' => true,
        'path' => UPLOADS_URL . $filename,
        'filename' => $filename
    ];
}

/**
 * Return JSON response
 */
function json_response(array $data, int $statusCode = 200): void {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/**
 * Set Flash Message
 */
function set_flash_message(string $type, string $message): void {
    $_SESSION['flash_message'] = [
        'type' => $type, // success, error, warning, info
        'message' => $message
    ];
}

/**
 * Get and Clear Flash Message
 */
function get_flash_message(): ?array {
    if (isset($_SESSION['flash_message'])) {
        $msg = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $msg;
    }
    return null;
}
