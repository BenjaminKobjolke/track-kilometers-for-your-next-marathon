<?php
require_once __DIR__ . '/../bootstrap.php';
$config = require_once __DIR__ . '/../config.php';

use Models\User;

// Check if user is already logged in
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Check remember me cookie
if (isset($_COOKIE['remember_token'])) {
    $user = User::where('remember_token', $_COOKIE['remember_token'])->first();
    if ($user && $user->isRememberTokenValid()) {
        $_SESSION['user_id'] = $user->id;
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Marathon Training Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h2 class="text-center mb-4">Login</h2>
                        <?php if (isset($_GET['error'])): ?>
                            <div class="alert alert-danger">
                                <?= htmlspecialchars($_GET['error']) ?>
                            </div>
                        <?php endif; ?>
                        <form id="loginForm">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="rememberMe" name="remember_me">
                                <label class="form-check-label" for="rememberMe">Remember me</label>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                            <div class="text-center mt-3">
                                <a href="#" id="forgotPassword" data-bs-toggle="modal" data-bs-target="#resetPasswordModal">Forgot Password?</a>
                                <div class="mt-2">
                                    <a href="<?= $config['base_url'] ?>/register.php">Don't have an account? Register here</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'reset_form.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.appConfig = {
            baseUrl: '<?= $config['base_url'] ?>'
        };
    </script>
    <script type="module" src="js/app.js"></script>
</body>
</html>
