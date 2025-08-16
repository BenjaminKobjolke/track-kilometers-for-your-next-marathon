<?php

require_once __DIR__ . '/../../../bootstrap.php';

use Models\User;
use Models\Logger;
use Models\TranslationManager;
use Controllers\SessionController;

$logger = new Logger('auth');
$translator = new TranslationManager();

header('Content-Type: application/json');

// Check database file permissions
$dbPath = __DIR__ . '/../../../database/database.sqlite';
if (!file_exists($dbPath)) {
    $logger->error('Database file does not exist', ['path' => $dbPath]);
    throw new Exception($translator->get('error_database_not_found'));
}
if (!is_writable($dbPath)) {
    $logger->error('Database not writable', [
        'path' => $dbPath,
        'permissions' => decoct(fileperms($dbPath) & 0777)
    ]);
    throw new Exception($translator->get('error_database_not_writable'));
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
        throw new Exception($translator->get('error_invalid_input'));
    }

    // Validate required fields
    if (!isset($input['email']) || !isset($input['password'])) {
        throw new Exception($translator->get('error_missing_fields'));
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
        throw new Exception($translator->get('error_invalid_credentials'));
    }

    // Check if account is activated
    if (!$user->isActive()) {
        throw new Exception($translator->get('error_account_not_activated'));
    }

    // Start session
    session_start();
    $_SESSION['user_id'] = $user->id;

    // Restore last active session
    try {
        $sessionController = new SessionController();
        $sessionController->getActiveSession(); // This will automatically restore the last active session
    } catch (Exception $e) {
        // Log but don't fail login if session restoration fails
        $logger->info('Could not restore last active session: ' . $e->getMessage());
    }

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
        'message' => $translator->get('error_login_failed', ['message' => $e->getMessage()])
    ]);
}
