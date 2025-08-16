<?php
require_once __DIR__ . '/../bootstrap.php';
$config = require_once __DIR__ . '/../config.php';

use Models\TranslationManager;
$translator = new TranslationManager();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $translator->get('page_title_register') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0"><?= $translator->get('heading_register') ?></h4>
                    </div>
                    <div class="card-body">
                        <form id="registerForm">
                            <div class="mb-3">
                                <label for="email" class="form-label"><?= $translator->get('label_email_address') ?></label>
                                <input type="email" class="form-control" id="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label"><?= $translator->get('label_password') ?></label>
                                <input type="password" class="form-control" id="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirmPassword" class="form-label"><?= $translator->get('label_confirm_password') ?></label>
                                <input type="password" class="form-control" id="confirmPassword" required>
                            </div>
                            <div class="alert alert-danger d-none" id="errorMessage"></div>
                            <div class="alert alert-success d-none" id="successMessage"></div>
                            <button type="submit" class="btn btn-primary"><?= $translator->get('button_register') ?></button>
                        </form>
                        <div class="mt-3">
                            <a href="login.php"><?= $translator->get('link_login') ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.appConfig = {
            baseUrl: '<?php echo $config['base_url']; ?>'
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script type="module" src="js/modules/RegisterManager.js"></script>
</body>
</html>
