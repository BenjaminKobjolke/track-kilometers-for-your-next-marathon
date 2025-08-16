<?php
/**
 * @var Models\TranslationManager $translator
 */
?>
<div class="modal fade" id="createSessionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sessionModalTitle"><?= $translator->get('modal_title_create_session') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="createSessionForm">
                    <input type="hidden" id="sessionId">
                    <div class="mb-3">
                        <label for="sessionName" class="form-label"><?= $translator->get('label_session_name') ?></label>
                        <input type="text" class="form-control" id="sessionName" required>
                    </div>
                    <div class="mb-3">
                        <label for="sessionStartDate" class="form-label"><?= $translator->get('label_start_date') ?></label>
                        <input type="text" class="form-control" id="sessionStartDate" data-type="date" required placeholder="DD.MM.YYYY">
                    </div>
                    <div class="mb-3">
                        <label for="sessionEndDate" class="form-label"><?= $translator->get('label_end_date') ?></label>
                        <input type="text" class="form-control" id="sessionEndDate" data-type="date" required placeholder="DD.MM.YYYY">
                    </div>
                    <div class="mb-3">
                        <label for="sessionTargetKm" class="form-label"><?= $translator->get('label_target_amount', [], null) ?></label>
                        <input type="number" step="0.1" class="form-control" id="sessionTargetKm" required value="500">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sessionUnit" class="form-label"><?= $translator->get('label_unit') ?></label>
                                <input type="text" class="form-control" id="sessionUnit" required value="Kilometers" placeholder="e.g. Kilometers, Books, Hours">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sessionUnitShort" class="form-label"><?= $translator->get('label_unit_short') ?></label>
                                <input type="text" class="form-control" id="sessionUnitShort" required value="km" placeholder="e.g. km, books, hrs">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= $translator->get('button_close') ?></button>
                <button type="submit" class="btn btn-primary" form="createSessionForm" id="sessionSubmitBtn"><?= $translator->get('button_create') ?></button>
            </div>
        </div>
    </div>
</div>
