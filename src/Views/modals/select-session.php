<?php
/**
 * @var Models\TranslationManager $translator
 */
?>
<div class="modal fade" id="selectSessionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= $translator->get('modal_title_select_session') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="sessionList">
                    <!-- Sessions will be populated here by JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>
