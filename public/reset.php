<?php
require_once __DIR__ . '/../bootstrap.php';

use Models\User;
use Models\Logger;

$logger = new Logger('auth');

// Check for token
$token = $_GET['token'] ?? null;
if (!$token) {
    $logger->error('Password reset attempted without token');
    header('Location: login.php?error=' . urlencode('No token provided'));
    exit;
}

// Find user by reset token
$user = User::where('reset_token', $token)->first();

// Validate token
if (!$user || !$user->isResetTokenValid()) {
    $logger->error('Invalid or expired reset token used', [
        'token' => $token,
        'user_found' => (bool)$user,
        'token_valid' => $user ? $user->isResetTokenValid() : false
    ]);
    header('Location: login.php?error=' . urlencode('Invalid or expired token'));
    exit;
}

$logger->info('Valid reset token used', ['email' => $user->email]);
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Marathon Training Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h2 class="text-center mb-4">Reset Password</h2>
                        <form id="resetForm">
                            <input type="hidden" id="resetToken" value="<?= htmlspecialchars($token) ?>">
                            <div class="mb-3">
                                <label for="newPassword" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="newPassword" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirmPassword" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="confirmPassword" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Reset Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('resetForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const token = document.getElementById('resetToken').value;

            if (newPassword !== confirmPassword) {
                alert('Passwords do not match');
                return;
            }

            try {
                const requestData = {
                    token,
                    password: newPassword
                };
                
                const response = await fetch('api/auth/update-password.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(requestData)
                });

                const data = await response.json();
                
                if (data.success) {
                    alert('Password has been reset successfully');
                    window.location.href = 'login.php';
                } else {
                    alert(data.message || 'Failed to reset password');
                }
            } catch (error) {
                console.error('Error resetting password:', error);
                alert('Error resetting password');
            }
        });
    </script>
</body>
</html>
