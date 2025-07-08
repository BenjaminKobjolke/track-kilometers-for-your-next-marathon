<?php
require_once __DIR__ . '/../../../bootstrap.php';
$config = require_once __DIR__ . '/../../../config.php';

use Models\User;

try {
    $token = $_GET['token'] ?? '';

    if (empty($token)) {
        http_response_code(400);
        echo 'Invalid activation token';
        exit;
    }

    $user = User::where('activation_token', $token)->first();

    if (!$user) {
        http_response_code(400);
        echo 'Invalid or expired activation token';
        exit;
    }

    $user->is_active = true;
    $user->activation_token = null;
    $user->save();

    // Redirect to login page with success message
    header('Location: ' . $config['base_url'] . '/login.php?message=' . urlencode('Account activated successfully. You can now log in.'));
    exit;

} catch (Exception $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo 'An error occurred during account activation';
}
