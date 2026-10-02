<?php
/**
 * US BENGOL - General Contact Message API Endpoint
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
    $input = $_POST;
    if (empty($input)) {
        $raw = file_get_contents('php://input');
        $json = json_decode($raw, true);
        if (is_array($json)) {
            $input = $json;
        }
    }

    $name = sanitize_input($input['name'] ?? '');
    $phone = sanitize_input($input['phone'] ?? '');
    $email = sanitize_input($input['email'] ?? '');
    $subject = sanitize_input($input['subject'] ?? 'General Inquiry');
    $message = sanitize_input($input['message'] ?? '');

    $errors = [];
    if (empty($name)) {
        $errors['name'] = 'Please enter your name.';
    }
    if (empty($phone)) {
        $errors['phone'] = 'Please enter your phone number.';
    }
    if (empty($message)) {
        $errors['message'] = 'Please write your message.';
    }

    if (!empty($errors)) {
        json_response([
            'success' => false,
            'message' => 'Please fill out all required fields.',
            'errors' => $errors
        ], 422);
    }

    $pdo = Database::getConnection();
    $stmt = $pdo->prepare("
        INSERT INTO contact_messages (name, phone, email, subject, message, status, created_at)
        VALUES (?, ?, ?, ?, ?, 'new', NOW())
    ");

    $stmt->execute([$name, $phone, $email, $subject, $message]);

    json_response([
        'success' => true,
        'message' => 'Thank you for reaching out. We have received your message and will respond promptly.'
    ]);

} catch (Exception $e) {
    error_log("Contact API Error: " . $e->getMessage());
    json_response([
        'success' => false,
        'message' => 'Unable to send message at this time. Please reach out via WhatsApp at 01921692008.'
    ], 500);
}
