<?php

require_once __DIR__ . '/../../bootstrap.php';

use Models\Run;
use Models\Session;

session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    // Handle GET request
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Get runs for a specific session if session_id is provided
        if (isset($_GET['session_id'])) {
            $session = Session::where('id', $_GET['session_id'])
                ->where('user_id', $_SESSION['user_id'])
                ->first();

            if (!$session) {
                http_response_code(404);
                echo json_encode(['error' => 'Session not found']);
                exit;
            }

            $runs = Run::where('session_id', $session->id)
                ->orderBy('date', 'desc')
                ->get();
        } 
        // Otherwise, get runs for active session
        else {
            if (!isset($_SESSION['active_session_id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'No active session']);
                exit;
            }

            $activeSession = Session::find($_SESSION['active_session_id']);
            if (!$activeSession || $activeSession->status !== 'active') {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid or inactive session']);
                exit;
            }

            $runs = Run::where('session_id', $activeSession->id)
                ->orderBy('date', 'desc')
                ->get();
        }

        // Format dates for frontend
        foreach ($runs as $run) {
            $run->formatted_date = (new DateTime($run->date))->format('d.m.Y');
        }

        echo json_encode($runs);
        exit;
    }

    // For non-GET requests, require active session
    if (!isset($_SESSION['active_session_id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'No active session']);
        exit;
    }

    $activeSession = Session::find($_SESSION['active_session_id']);
    if (!$activeSession || $activeSession->status !== 'active') {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid or inactive session']);
        exit;
    }

    // Handle DELETE request
    if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input || !isset($input['id'])) {
            throw new Exception('Invalid input data');
        }

        $run = Run::where('id', $input['id'])
            ->where('session_id', $activeSession->id)
            ->first();
            
        if (!$run) {
            throw new Exception('Run not found in current session');
        }

        $run->delete();
        echo json_encode(['success' => true]);
        exit;
    }

    // Handle POST request
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid input data');
    }

    // Validate required fields
    if (!isset($input['date']) || !isset($input['kilometers'])) {
        throw new Exception('Missing required fields');
    }

    // Validate date is within session period
    $runDate = new DateTime($input['date']);
    $sessionStart = new DateTime($activeSession->start_date);
    $sessionEnd = new DateTime($activeSession->end_date);

    if ($runDate < $sessionStart || $runDate > $sessionEnd) {
        throw new Exception('Run date must be within the session period');
    }

    // Update existing run
    if (isset($input['id'])) {
        $run = Run::where('id', $input['id'])
            ->where('session_id', $activeSession->id)
            ->first();
            
        if (!$run) {
            throw new Exception('Run not found in current session');
        }
        
        $run->date = $input['date'];
        $run->kilometers = $input['kilometers'];
        $run->save();
    } 
    // Create new run
    else {
        $run = new Run();
        $run->date = $input['date'];
        $run->kilometers = $input['kilometers'];
        $run->session_id = $activeSession->id;
        $run->save();
    }

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
