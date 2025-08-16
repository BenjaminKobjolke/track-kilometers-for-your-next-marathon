<?php
/**
 * @var Models\TranslationManager $translator
 */
?>
<div class="modal fade" id="probabilityInfoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= $translator->get('probability_info_title') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><?= $translator->get('probability_info_description') ?></p>
                
                <div class="mb-3">
                    <h6><?= $translator->get('probability_current_progress') ?></h6>
                    <p id="currentProgressText"></p>
                </div>

                <div class="mb-3">
                    <h6><?= $translator->get('probability_estimated_progress') ?></h6>
                    <p id="estimatedProgressText"></p>
                </div>

                <div class="mb-3">
                    <h6><?= $translator->get('probability_special_cases') ?></h6>
                    <p><?= $translator->get('probability_special_cases_text') ?></p>
                </div>

                <div class="alert alert-info">
                    <strong id="finalProbabilityText"></strong>
                </div>
            </div>
        </div>
    </div>
</div>
