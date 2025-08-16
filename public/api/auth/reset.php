<?php
require_once __DIR__ . '/../../../bootstrap.php';

use Models\User;
use Models\TranslationManager;

$translator = new TranslationManager();

// Check for token
$token = $_GET['token'] ?? null;
if (!$token) {
    header('Location: ../../login.php?error=' . urlencode($translator->get('error_no_token')));
    exit;
}

// Find user by reset token
$user = User::where('reset_token', $token)->first();

// Validate token
if (!$user || !$user->isResetTokenValid()) {
    header('Location: ../../login.php?error=' . urlencode($translator->get('error_invalid_token')));
    exit;
}


?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $translator->get('page_title_reset_password') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h2 class="text-center mb-4"><?= $translator->get('heading_reset_password') ?></h2>
                        <form id="resetForm">
                            <input type="hidden" id="resetToken" value="<?= htmlspecialchars($token) ?>">
                            <div class="mb-3">
                                <label for="newPassword" class="form-label"><?= $translator->get('label_new_password') ?></label>
                                <input type="password" class="form-control" id="newPassword" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirmPassword" class="form-label"><?= $translator->get('label_confirm_password') ?></label>
                                <input type="password" class="form-control" id="confirmPassword" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100"><?= $translator->get('button_reset_password') ?></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.appConfig = {
            translations: <?= json_encode($translator->getAll()) ?>,
            language: '<?= $translator->getCurrentLanguage() ?>'
        };
    </script>
    <script type="module">
        import translationManager from '../../js/modules/TranslationManager.js';
        document.getElementById('resetForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const token = document.getElementById('resetToken').value;

            if (newPassword !== confirmPassword) {
                alert(translationManager.translate('error_password_mismatch'));
                return;
            }

            try {
                const requestData = {
                    token,
                    password: newPassword
                };
                
                console.log('Submitting password reset request...');
                
                const response = await fetch('update-password.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(requestData)
                });

                console.log('Response received:', response.status, response.statusText);

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const data = await response.json();
                console.log('Response data:', data);
                
                if (data.success) {
                    alert(translationManager.translate('success_password_reset'));
                    window.location.href = '../../login.php';
                } else {
                    alert(data.message || translationManager.translate('error_reset_failed_generic'));
                }
            } catch (error) {
                console.error('Password reset error:', error);
                alert(translationManager.translate('error_reset_password') + ' (' + error.message + ')');
            }
        });
    </script>
</body>
</html>
