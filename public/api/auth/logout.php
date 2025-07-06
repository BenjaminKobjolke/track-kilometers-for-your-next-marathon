<?php

require_once __DIR__ . '/../../../bootstrap.php';

use App\Models\User;
use App\Utils\Logger;

$logger = new Logger('auth');

header('Content-Type: application/json');

try {
    $logger->info('Logout attempt initiated');
    session_start();
    
    // Clear remember me cookie if exists
    if (isset($_COOKIE['remember_token'])) {
        $user = User::where('remember_token', $_COOKIE['remember_token'])->first();
        if ($user) {
            $user->clearRememberToken();
        }
        
        setcookie('remember_token', '', [
            'expires' => time() - 3600,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
    }

    // Clear session
    session_destroy();

    $logger->info('Logout successful');
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $logger->error('Logout failed', [
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
