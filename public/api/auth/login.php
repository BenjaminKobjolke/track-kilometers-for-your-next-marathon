<?php

require_once __DIR__ . '/../../../bootstrap.php';

use Models\User;
use Models\Logger;

$logger = new Logger('auth');

header('Content-Type: application/json');

// Check database file permissions
$dbPath = __DIR__ . '/../../../database/database.sqlite';
if (!file_exists($dbPath)) {
    $logger->error('Database file does not exist', ['path' => $dbPath]);
    throw new Exception('Database file not found');
}
if (!is_writable($dbPath)) {
    $logger->error('Database not writable', [
        'path' => $dbPath,
        'permissions' => decoct(fileperms($dbPath) & 0777)
    ]);
    throw new Exception('Database file not writable');
}

try {
    // Test database connection
    try {
        User::count();
    } catch (PDOException $e) {
        $logger->error('Database connection error', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        throw $e;
    }

    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid input data');
    }

    // Validate required fields
    if (!isset($input['email']) || !isset($input['password'])) {
        throw new Exception('Missing required fields');
    }

    // Find user by email
    $user = User::where('email', $input['email'])->first();

    // If no user exists, create first user
    if (!$user && User::count() === 0) {
        $user = new User();
        $user->email = $input['email'];
        $user->password = password_hash($input['password'], PASSWORD_DEFAULT);
        $user->save();
    }
    // Otherwise verify password
    else if (!$user || !password_verify($input['password'], $user->password)) {
        throw new Exception('Invalid email or password');
    }

    // Check if account is activated
    if (!$user->isActive()) {
        throw new Exception('Please activate your account. Check your email for activation instructions.');
    }

    // Start session
    session_start();
    $_SESSION['user_id'] = $user->id;

    // Handle remember me
    if (isset($input['remember_me']) && $input['remember_me']) {
        $user->generateRememberToken();
        setcookie(
            'remember_token',
            $user->remember_token,
            [
                'expires' => strtotime('+30 days'),
                'path' => '/',
                'httponly' => true,
                'samesite' => 'Strict'
            ]
        );
    }

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $logger->error('Login error', [
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
