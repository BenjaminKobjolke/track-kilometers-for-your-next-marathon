<?php

require_once __DIR__ . '/../../../bootstrap.php';
$config = require __DIR__ . '/../../../config.php';

use Models\User;
use Models\Logger;
use Models\TranslationManager;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$logger = new Logger('auth');
$translator = new TranslationManager();

header('Content-Type: application/json');

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['email'])) {
        throw new Exception($translator->get('error_invalid_input'));
    }

    // Find user by email
    $user = User::where('email', $input['email'])->first();
    if (!$user) {
        throw new Exception($translator->get('error_email_not_found'));
    }

    // Generate reset token
    $user->generateResetToken();

    // Create reset link
    $resetLink = 'http://' . $_SERVER['HTTP_HOST'] . 
                 $config['base_url'] . '/api/auth/reset.php?token=' . 
                 $user->reset_token;

    // Configure PHPMailer to use local mail server
    $mail = new PHPMailer(true);
    $mail->isMail(); // Use PHP's mail() function

    // Set email content
    $mail->setFrom($config['email']['from_address'], $config['email']['from_name']);
    $mail->addAddress($user->email);
    $mail->isHTML(true);
    $mail->Subject = $translator->get('email_subject_reset');
    $mail->Body = "
        <h2>" . $translator->get('email_reset_heading') . "</h2>
        <p>" . $translator->get('email_reset_instruction') . "</p>
        <p><a href='{$resetLink}'>{$resetLink}</a></p>
        <p>" . $translator->get('email_reset_expiry') . "</p>
        <p>" . $translator->get('email_reset_disclaimer') . "</p>
    ";

    $mail->send();
    
    $logger->info('Password reset link sent', [
        'email' => $user->email,
        'token_expires' => $user->reset_token_expires_at
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => $translator->get('success_reset_link_sent')
    ]);
} catch (Exception $e) {
    $logger->error('Password reset failed', [
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $translator->get('error_reset_failed', ['message' => $e->getMessage()])
    ]);
}
