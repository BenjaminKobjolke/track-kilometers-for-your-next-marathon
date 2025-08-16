<?php

require_once '../../../bootstrap.php';

// Start session
session_start();

use Models\Session;
use Models\Logger;
use Controllers\SessionController;

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
    $sessionController = new SessionController();
    $session = $sessionController->getActiveSession();

    if (!$session) {
        http_response_code(404);
        echo json_encode(['error' => 'No active session']);
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
