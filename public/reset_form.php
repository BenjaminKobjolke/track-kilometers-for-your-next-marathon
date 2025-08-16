<?php
use Models\TranslationManager;
if (!isset($translator)) {
    // Get language from URL parameter, localStorage will be handled by JavaScript
    $language = $_GET['lang'] ?? 'en';
    $translator = new TranslationManager($language);
}
?>
<!-- Password Reset Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resetPasswordModalLabel"><?= $translator->get('modal_title_reset_password') ?></h5>
                <div class="d-flex align-items-center">
                    <div class="dropdown me-2">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-globe"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="?lang=en<?= isset($_GET['token']) ? '&token=' . urlencode($_GET['token']) : '' ?>">English</a></li>
                            <li><a class="dropdown-item" href="?lang=de<?= isset($_GET['token']) ? '&token=' . urlencode($_GET['token']) : '' ?>">Deutsch</a></li>
                        </ul>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div class="modal-body">
                <form id="resetPasswordForm">
                    <div class="mb-3">
                        <label for="resetEmail" class="form-label"><?= $translator->get('label_email') ?></label>
                        <input type="email" class="form-control" id="resetEmail" name="email" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= $translator->get('button_cancel') ?></button>
                <button type="button" class="btn btn-primary" id="sendResetLink"><?= $translator->get('button_send_reset_link') ?></button>
            </div>
        </div>
    </div>
</div>
