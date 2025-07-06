<?php

require_once __DIR__ . '/../../../bootstrap.php';
$config = require_once __DIR__ . '/../../../config.php';

use App\Models\User;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['email'])) {
        throw new Exception('Invalid input data');
    }

    // Find user by email
    $user = User::where('email', $input['email'])->first();
    if (!$user) {
        throw new Exception('Email not found');
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
    $mail->Subject = 'Password Reset Request';
    $mail->Body = "
        <h2>Password Reset Request</h2>
        <p>Click the link below to reset your password:</p>
        <p><a href='{$resetLink}'>{$resetLink}</a></p>
        <p>This link will expire in 24 hours.</p>
        <p>If you didn't request this, please ignore this email.</p>
    ";

    $mail->send();
    
    echo json_encode([
        'success' => true,
        'message' => 'Password reset link has been sent to your email'
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to send reset link: ' . $e->getMessage()
    ]);
}
