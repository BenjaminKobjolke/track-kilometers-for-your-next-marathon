<?php
/**
 * @var Models\TranslationManager $translator
 */
?>
<div class="modal fade" id="pastSessionsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= $translator->get('modal_title_past_sessions') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th><?= $translator->get('table_header_name') ?></th>
                                <th><?= $translator->get('table_header_period') ?></th>
                                <th><?= $translator->get('table_header_total_km') ?></th>
                                <th><?= $translator->get('table_header_daily_average') ?></th>
                                <th><?= $translator->get('table_header_status') ?></th>
                                <th><?= $translator->get('table_header_actions') ?></th>
                            </tr>
                        </thead>
                        <tbody id="pastSessionsList">
                            <!-- Past sessions will be populated here by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
