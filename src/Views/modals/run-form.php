<?php
/**
 * @var Models\TranslationManager $translator
 */
?>
<div class="modal fade" id="addRunModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= $translator->getWithSession('modal_title_add_run', $activeSession) ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="runForm">
                    <input type="hidden" id="runId">
                    <div class="mb-3">
                        <label for="runDate" class="form-label"><?= $translator->get('label_date') ?></label>
                        <input type="text" class="form-control" id="runDate" name="date" required placeholder="DD.MM.YYYY">
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label"><?= $translator->getWithSession('label_amount', $activeSession) ?></label>
                        <input type="number" step="0.1" class="form-control" id="amount" name="amount" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= $translator->get('button_close') ?></button>
                <button type="button" class="btn btn-primary" id="saveRun"><?= $translator->get('button_save') ?></button>
            </div>
        </div>
    </div>
</div>
