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
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['session_id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Session ID required']);
        exit;
    }

    $session = Session::where('user_id', $userId)
        ->where('id', $data['session_id'])
        ->whereRaw('status = ?', ['active'])
        ->first();

    if (!$session) {
        http_response_code(404);
        echo json_encode(['error' => 'Active session not found']);
        exit;
    }

    // Complete the session
    $session->status = 'completed';
    $session->save();

    // If this was the active session, remove it from user's session
    if (isset($_SESSION['active_session_id']) && $_SESSION['active_session_id'] === $session->id) {
        unset($_SESSION['active_session_id']);
    }

    echo json_encode([
        'success' => true,
        'session' => $session
    ]);

} catch (Exception $e) {
    $logger->error('Complete Session API Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}
