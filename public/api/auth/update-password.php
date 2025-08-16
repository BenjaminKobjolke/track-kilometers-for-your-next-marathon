<?php

require_once __DIR__ . '/../../../bootstrap.php';

use Models\User;
use Models\TranslationManager;
use Models\Logger;

$translator = new TranslationManager();
$logger = new Logger();

header('Content-Type: application/json');

try {
    $logger->info('Password reset attempt started');
    
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['token']) || !isset($input['password'])) {
        $logger->error('Invalid input provided for password reset', ['input_keys' => $input ? array_keys($input) : 'null']);
        throw new Exception($translator->get('error_invalid_input'));
    }

    $logger->info('Searching for user with reset token');
    
    // Find user by reset token
    $user = User::where('reset_token', $input['token'])->first();
    if (!$user) {
        $logger->error('No user found with provided reset token');
        throw new Exception($translator->get('error_reset_token_invalid'));
    }

    $logger->info('User found, validating token expiration', ['user_id' => $user->id]);

    // Validate token expiration
    if (!$user->isResetTokenValid()) {
        $logger->error('Reset token expired', ['user_id' => $user->id, 'token_expires_at' => $user->reset_token_expires_at]);
        throw new Exception($translator->get('error_reset_token_expired'));
    }

    $logger->info('Updating password and activating account if needed', ['user_id' => $user->id]);
    
    // Update password and activate account if needed first
    $wasActivated = $user->activateOnPasswordReset();
    $user->password = password_hash($input['password'], PASSWORD_DEFAULT);
    
    $logger->info('Saving password update to database', ['user_id' => $user->id]);
    
    // Save password update
    if (!$user->save()) {
        $logger->error('Failed to save password update to database', ['user_id' => $user->id]);
        throw new Exception($translator->get('error_database_save_failed'));
    }
    
    $logger->info('Password saved successfully, clearing reset token', ['user_id' => $user->id]);
    
    // Clear reset token only after successful password save
    $user->clearResetToken();
    
    // Clear any existing remember token
    $user->clearRememberToken();

    $logger->info('Password reset completed successfully', ['user_id' => $user->id, 'was_activated' => $wasActivated]);

    echo json_encode([
        'success' => true,
        'message' => $wasActivated 
            ? $translator->get('message_password_updated_activated')
            : $translator->get('message_password_updated')
    ]);
} catch (Exception $e) {
    $logger->error('Password reset failed', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
