<?php

require_once '../../../bootstrap.php';

// Start session
session_start();

use Models\Session;
use Models\Logger;

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user_id'];
$logger = new Logger();

try {
    // Check if there's an active session ID in the PHP session
    if (!isset($_SESSION['active_session_id'])) {
        http_response_code(404);
        echo json_encode(['error' => 'No active session']);
        exit;
    }

    // Get the active session
    $session = Session::where('id', $_SESSION['active_session_id'])
        ->where('user_id', $userId)
        ->where('status', '=', 'active')
        ->first();

    if (!$session) {
        // Clear invalid session ID
        unset($_SESSION['active_session_id']);
        http_response_code(404);
        echo json_encode(['error' => 'Active session not found']);
        exit;
    }

    echo json_encode([
        'success' => true,
        'session' => $session
    ]);
} catch (Exception $e) {
    $logger->error('Current Session API Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}
