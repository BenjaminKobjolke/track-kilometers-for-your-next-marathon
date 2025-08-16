<?php

namespace Controllers;

use Models\User;

class AuthController {
    public function checkAuth() {
        if (!isset($_SESSION['user_id'])) {
            // Check remember me cookie
            if (isset($_COOKIE['remember_token'])) {
                $user = User::where('remember_token', $_COOKIE['remember_token'])->first();
                if ($user && $user->isRememberTokenValid()) {
                    $_SESSION['user_id'] = $user->id;
                } else {
                    $this->redirectToLogin();
                }
            } else {
                $this->redirectToLogin();
            }
        }
    }

    private function redirectToLogin() {
        header('Location: login.php');
        exit;
    }
}
