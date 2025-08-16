<?php
require_once __DIR__ . '/../../../bootstrap.php';

use Models\User;
use Models\Logger;
use Models\TranslationManager;
use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as Capsule;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$logger = new Logger('auth');
$translator = new TranslationManager();

header('Content-Type: application/json');

try {
    // Get JSON input
    $json = file_get_contents('php://input');
    $data = json_decode($json);

    if (!$data || !isset($data->email) || !isset($data->password)) {
        throw new Exception($translator->get('error_invalid_input'));
    }

    // Validate email
    if (!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception($translator->get('error_invalid_email'));
    }

    // Check if email already exists
    if (User::where('email', $data->email)->exists()) {
        throw new Exception($translator->get('error_email_registered'));
    }

    // Get client IP and create hash
    $ip = $_SERVER['REMOTE_ADDR'];
    $ipHash = hash('sha256', $ip);

    // Check for recent registrations from this IP
    $recentAttempt = Capsule::table('register_log')
        ->where('ip_hash', $ipHash)
        ->where('created_at', '>', Carbon::now()->subHour())
        ->first();

    if ($recentAttempt) {
        throw new Exception($translator->get('error_registration_rate_limit'));
    }

    // Log the registration attempt
    Capsule::table('register_log')->insert([
        'ip_hash' => $ipHash,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now()
    ]);

    // Create user
    $user = new User();
    $user->email = $data->email;
    $user->password = password_hash($data->password, PASSWORD_DEFAULT);
    $user->activation_token = bin2hex(random_bytes(32));
    $user->is_active = false;
    $user->save();

    // Get config
    $config = require_once __DIR__ . '/../../../config.php';

    // Create activation link
    $activationLink = sprintf(
        "%s://%s%s/api/auth/activate.php?token=%s",
        isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
        $_SERVER['SERVER_NAME'],
        $config['base_url'],
        $user->activation_token
    );

    // Configure PHPMailer to use local mail server
    $mail = new PHPMailer(true);
    $mail->isMail(); // Use PHP's mail() function

    // Set email content
    $mail->setFrom($config['email']['from_address'], $config['email']['from_name']);
    $mail->addAddress($user->email);
    $mail->isHTML(true);
    $mail->Subject = $translator->get('email_subject_activate');
    $mail->Body = "
        <h2>" . $translator->get('email_welcome') . "</h2>
        <p>" . $translator->get('email_activation_instruction') . "</p>
        <p><a href='{$activationLink}'>{$activationLink}</a></p>
        <p>" . $translator->get('email_activation_disclaimer') . "</p>
    ";

    $mail->send();
    
    $logger->info('Activation link sent', [
        'email' => $user->email
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => $translator->get('message_activation_sent')
    ]);

} catch (Exception $e) {
    $logger->error('Registration failed', [
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
