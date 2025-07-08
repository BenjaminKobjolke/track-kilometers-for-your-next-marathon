<?php

require_once __DIR__ . '/../../../bootstrap.php';

use Models\User;

header('Content-Type: application/json');

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['token']) || !isset($input['password'])) {
        throw new Exception('Invalid input data');
    }

    // Find user by reset token
    $user = User::where('reset_token', $input['token'])->first();
    if (!$user) {
        throw new Exception('Invalid reset token');
    }

    // Validate token expiration
    if (!$user->isResetTokenValid()) {
        throw new Exception('Reset token has expired');
    }

    // Update password
    $user->password = password_hash($input['password'], PASSWORD_DEFAULT);
    $user->save();
    
    // Clear reset token
    $user->clearResetToken();
    
    // Clear any existing remember token
    $user->clearRememberToken();

    echo json_encode([
        'success' => true,
        'message' => 'Password has been updated successfully'
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
