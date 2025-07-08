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
    $logger->info('Request method: ' . $_SERVER['REQUEST_METHOD']);
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            // Get all active sessions
            $logger->info('Getting active sessions for user: ' . $userId);
            $query = Session::where('user_id', $userId)
                ->where('status', '=', 'active')
                ->orderBy('created_at', 'desc');
            
            $logger->info('Query SQL: ' . $query->toSql());
            $logger->info('Query bindings: ' . json_encode($query->getBindings()));
            
            $activeSessions = $query->get();
            $logger->info('Found ' . count($activeSessions) . ' active sessions');
            $logger->info('Active sessions: ' . json_encode($activeSessions));

            $response = [
                'sessions' => $activeSessions,
                'count' => count($activeSessions)
            ];

            echo json_encode($response);
            break;

        case 'POST':
            // Set active session in user's session
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['session_id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Session ID required']);
                exit;
            }

            $session = Session::where('user_id', $userId)
                ->where('id', $data['session_id'])
                ->where('status', '=', 'active')
                ->first();

            if (!$session) {
                http_response_code(404);
                echo json_encode(['error' => 'Active session not found']);
                exit;
            }

            // Store active session ID in user's session
            $_SESSION['active_session_id'] = $session->id;
            
            echo json_encode([
                'success' => true,
                'session' => $session
            ]);
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
} catch (Exception $e) {
    $logger->error('Active Sessions API Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}
