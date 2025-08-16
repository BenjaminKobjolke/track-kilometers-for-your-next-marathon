<?php
use Models\TranslationManager;
if (!isset($translator)) {
    $translator = new TranslationManager();
}
?>
<!-- Password Reset Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resetPasswordModalLabel"><?= $translator->get('modal_title_reset_password') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
