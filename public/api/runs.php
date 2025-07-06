<?php

require_once __DIR__ . '/../../bootstrap.php';

use App\Models\Run;

header('Content-Type: application/json');

try {
    // Handle DELETE request
    if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input || !isset($input['id'])) {
            throw new Exception('Invalid input data');
        }

        $run = Run::find($input['id']);
        if (!$run) {
            throw new Exception('Run not found');
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

    // Update existing run
    if (isset($input['id'])) {
        $run = Run::find($input['id']);
        if (!$run) {
            throw new Exception('Run not found');
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
