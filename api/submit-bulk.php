<?php
/**
 * US BENGOL - Bulk Order Submission API Endpoint
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    json_response(['success' => false, 'error' => 'Method not allowed.'], 405);
}

try {
    // Read input either from form-urlencoded / FormData or raw JSON
    $input = $_POST;
    if (empty($input)) {
        $raw = file_get_contents('php://input');
        $json = json_decode($raw, true);
        if (is_array($json)) {
            $input = $json;
        }
    }

    $fullName = sanitize_input($input['full_name'] ?? '');
    $organization = sanitize_input($input['organization'] ?? '');
    $phone = sanitize_input($input['phone'] ?? '');
    $email = sanitize_input($input['email'] ?? '');
    $productName = sanitize_input($input['product_name'] ?? '');
    $quantity = sanitize_input($input['estimated_quantity'] ?? '');
    $location = sanitize_input($input['delivery_location'] ?? '');
    $schedule = sanitize_input($input['preferred_schedule'] ?? '');
    $message = sanitize_input($input['message'] ?? '');

    // Validation
    $errors = [];
    if (empty($fullName)) {
        $errors['full_name'] = 'Please provide your full name.';
    }
    if (empty($organization)) {
        $errors['organization'] = 'Please provide your organization / business name.';
    }
    if (empty($phone)) {
        $errors['phone'] = 'Please provide a valid contact phone number.';
    } elseif (!preg_match('/^(\+?88)?01[3-9]\d{8}$/', preg_replace('/[\s\-]/', '', $phone))) {
        // Soft Bangladeshi phone check
        $errors['phone'] = 'Please enter a valid 11-digit Bangladeshi phone number.';
    }
    if (empty($productName)) {
        $errors['product_name'] = 'Please select a product of interest.';
    }
    if (empty($quantity)) {
        $errors['estimated_quantity'] = 'Please specify an estimated quantity.';
    }
    if (empty($location)) {
        $errors['delivery_location'] = 'Please provide your delivery location / district.';
    }

    if (!empty($errors)) {
        json_response([
            'success' => false,
            'message' => 'Please correct the highlighted errors.',
            'errors' => $errors
        ], 422);
    }

    $pdo = Database::getConnection();
    $stmt = $pdo->prepare("
        INSERT INTO bulk_orders (full_name, organization, phone, email, product_name, estimated_quantity, delivery_location, preferred_schedule, message, status, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'new', NOW())
    ");

    $stmt->execute([
        $fullName,
        $organization,
        $phone,
        $email,
        $productName,
        $quantity,
        $location,
        $schedule,
        $message
    ]);

    json_response([
        'success' => true,
        'message' => 'Thank you. Your request has been received. Our team will review the details and contact you shortly.',
        'order_id' => $pdo->lastInsertId()
    ]);

} catch (Exception $e) {
    error_log("Bulk Order Submission Error: " . $e->getMessage());
    json_response([
        'success' => false,
        'message' => 'An unexpected error occurred while processing your request. Please call us directly at 01921692008.'
    ], 500);
}
