<?php

require_once __DIR__ . '/../../bootstrap.php';

use App\Models\Settings;

header('Content-Type: application/json');

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid input data');
    }

    // Validate required fields
    if (!isset($input['start_date']) || !isset($input['end_date']) || !isset($input['target_kilometers'])) {
        throw new Exception('Missing required fields');
    }

    // Validate dates
    if (strtotime($input['end_date']) <= strtotime($input['start_date'])) {
        throw new Exception('End date must be after start date');
    }

    // Validate target kilometers
    if (!is_numeric($input['target_kilometers']) || $input['target_kilometers'] <= 0) {
        throw new Exception('Target kilometers must be a positive number');
    }

    // Get or create settings
    $settings = Settings::first();
    if (!$settings) {
        $settings = new Settings();
    }

    // Update settings
    $settings->start_date = $input['start_date'];
    $settings->end_date = $input['end_date'];
    $settings->target_kilometers = $input['target_kilometers'];
    $settings->theme = isset($input['theme']) ? $input['theme'] : 'light';
    $settings->save();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
