<?php

require_once '../../bootstrap.php';

use Models\Settings;
use Models\Logger;

session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$logger = new Logger();

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            $settings = Settings::getDefault();
            echo json_encode($settings);
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['theme'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Theme is required']);
                exit;
            }

            $settings = Settings::getDefault();
            $settings->theme = $data['theme'];
            $settings->save();

            echo json_encode([
                'success' => true,
                'settings' => $settings
            ]);
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
} catch (Exception $e) {
    $logger->error('Settings API Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}
