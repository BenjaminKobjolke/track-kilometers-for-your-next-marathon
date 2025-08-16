<?php

require_once __DIR__ . '/../../bootstrap.php';

use Models\Run;
use Models\Session;
use Models\TranslationManager;

$translator = new TranslationManager();

session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => $translator->get('error_unauthorized')]);
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
                echo json_encode(['error' => $translator->get('error_session_not_found')]);
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
                echo json_encode(['error' => $translator->get('error_no_active_session')]);
                exit;
            }

            $activeSession = Session::find($_SESSION['active_session_id']);
            if (!$activeSession || $activeSession->status !== 'active') {
                http_response_code(400);
                echo json_encode(['error' => $translator->get('error_invalid_session')]);
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

    // For non-GET requests, determine which session to use
    $sessionId = null;
    
    // Check if session_id is provided in input data (for POST/DELETE)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        if ($input && isset($input['session_id'])) {
            $sessionId = $input['session_id'];
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $input = json_decode(file_get_contents('php://input'), true);
        if ($input && isset($input['session_id'])) {
            $sessionId = $input['session_id'];
        }
    }
    
    // Fall back to active session if no session_id provided
    if (!$sessionId) {
        if (!isset($_SESSION['active_session_id'])) {
            http_response_code(400);
            echo json_encode(['error' => $translator->get('error_no_active_session')]);
            exit;
        }
        $sessionId = $_SESSION['active_session_id'];
    }

    // Validate session exists and belongs to current user
    $activeSession = Session::where('id', $sessionId)
        ->where('user_id', $_SESSION['user_id'])
        ->first();
        
    if (!$activeSession) {
        http_response_code(400);
        echo json_encode(['error' => $translator->get('error_session_not_found')]);
        exit;
    }

    // Handle DELETE request
    if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input || !isset($input['id'])) {
            throw new Exception($translator->get('error_invalid_input'));
        }

        $run = Run::where('id', $input['id'])
            ->where('session_id', $activeSession->id)
            ->first();
            
        if (!$run) {
            throw new Exception($translator->get('error_run_not_found'));
        }

        $run->delete();
        echo json_encode(['success' => true]);
        exit;
    }

    // Handle POST request - input already decoded above
    if (!$input) {
        throw new Exception($translator->get('error_invalid_input'));
    }

    // Validate required fields
    if (!isset($input['date']) || !isset($input['amount'])) {
        throw new Exception($translator->get('error_missing_fields'));
    }

    // Validate date is within session period
    $runDate = new DateTime($input['date']);
    $sessionStart = new DateTime($activeSession->start_date);
    $sessionEnd = new DateTime($activeSession->end_date);

    if ($runDate < $sessionStart || $runDate > $sessionEnd) {
        throw new Exception($translator->get('error_date_out_of_range'));
    }

    // Update existing run
    if (isset($input['id'])) {
        $run = Run::where('id', $input['id'])
            ->where('session_id', $activeSession->id)
            ->first();
            
        if (!$run) {
            throw new Exception($translator->get('error_run_not_found'));
        }
        
        $run->date = $input['date'];
        $run->amount = $input['amount'];
        $run->save();
    } 
    // Create new run
    else {
        $run = new Run();
        $run->date = $input['date'];
        $run->amount = $input['amount'];
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
