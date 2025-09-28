<?php
require_once __DIR__ . '/../../../bootstrap.php';
$config = require __DIR__ . '/../../../config.php';

use Models\User;
use Models\TranslationManager;

$translator = new TranslationManager();

try {
    $token = $_GET['token'] ?? '';

    if (empty($token)) {
        http_response_code(400);
        echo $translator->get('error_invalid_activation_token');
        exit;
    }

    $user = User::where('activation_token', $token)->first();

    if (!$user) {
        http_response_code(400);
        echo $translator->get('error_token_expired');
        exit;
    }

    $user->is_active = true;
    $user->activation_token = null;
    $user->save();

    // Redirect to login page with success message
    header('Location: ' . $config['base_url'] . '/login.php?message=' . urlencode($translator->get('message_activation_success')));
    exit;

} catch (Exception $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo $translator->get('error_activation_failed');
}
