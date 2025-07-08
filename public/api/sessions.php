<?php

require_once '../../bootstrap.php';

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

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            if (isset($_GET['id'])) {
                // Get specific session
                $session = Session::where('user_id', $userId)
                    ->where('id', $_GET['id'])
                    ->first();
                
                if (!$session) {
                    http_response_code(404);
                    echo json_encode(['error' => 'Session not found']);
                    exit;
                }
                
                echo json_encode($session);
            } else if (isset($_GET['status'])) {
                // Get sessions by status
                $sessions = Session::where('user_id', $userId)
                    ->where('status', $_GET['status'])
                    ->orderBy('created_at', 'desc')
                    ->get();
                echo json_encode($sessions);
            } else {
                // Get all sessions
                $sessions = Session::where('user_id', $userId)
                    ->orderBy('created_at', 'desc')
                    ->get();
                echo json_encode($sessions);
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['name']) || !isset($data['start_date']) || !isset($data['end_date']) || !isset($data['target_kilometers'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing required fields: name, start_date, end_date, and target_kilometers are required']);
                exit;
            }

            $session = new Session([
                'user_id' => $userId,
                'name' => $data['name'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'target_kilometers' => $data['target_kilometers'],
                'status' => 'active'
            ]);
            
            $session->save();
            echo json_encode($session);
            break;

        case 'PUT':
            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Session ID required']);
                exit;
            }

            $session = Session::where('user_id', $userId)
                ->where('id', $_GET['id'])
                ->first();

            if (!$session) {
                http_response_code(404);
                echo json_encode(['error' => 'Session not found']);
                exit;
            }

            $data = json_decode(file_get_contents('php://input'), true);
            
            if (isset($data['name'])) {
                $session->name = $data['name'];
            }
            if (isset($data['start_date'])) {
                $session->start_date = $data['start_date'];
            }
            if (isset($data['end_date'])) {
                $session->end_date = $data['end_date'];
            }
            if (isset($data['status'])) {
                $session->status = $data['status'];
            }

            $session->save();
            echo json_encode($session);
            break;

        case 'DELETE':
            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Session ID required']);
                exit;
            }

            $session = Session::where('user_id', $userId)
                ->where('id', $_GET['id'])
                ->first();

            if (!$session) {
                http_response_code(404);
                echo json_encode(['error' => 'Session not found']);
                exit;
            }

            $session->delete();
            echo json_encode(['success' => true]);
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
} catch (Exception $e) {
    $logger = new Logger();
    $logger->error('Session API Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}
