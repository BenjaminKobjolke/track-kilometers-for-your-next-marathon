<?php
/**
 * @var Models\Session $activeSession
 * @var Models\TranslationManager $translator
 * @var array $stats
 */

use Models\DateFormatter;
use Utils\NumberFormatter;
?>
<div class="row mt-4">
    <div class="col mb-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="card-info flex-grow-1 pe-3">
                        <h5 class="card-title"><?= $translator->get('stats_total_amount', [], $activeSession) ?></h5>
                        <?php if ($activeSession): ?>
                        <p class="card-subtext"><?= $translator->get('stats_since', ['date' => DateFormatter::isoToGermanDate($activeSession->start_date)]) ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="card-value text-end">
                        <p class="card-text" id="total-amount">-</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col mb-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="card-info flex-grow-1 pe-3">
                        <h5 class="card-title"><?= $translator->get('stats_estimated_total') ?></h5>
                        <p class="card-subtext" id="daily-average">-</p>
                    </div>
                    <div class="card-value text-end">
                        <p class="card-text" id="estimated-total">-</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col mb-3">
        <div class="card probability-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="card-info flex-grow-1 pe-3">
                        <h5 class="card-title"><?= $translator->get('stats_target_probability') ?></h5>
                        <p class="card-subtext" id="target-info">-</p>
                    </div>
                    <div class="card-value text-end">
                        <p class="card-text" id="target-probability">-</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col mb-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><?= $translator->get('stats_training_period') ?></h5>
                <div class="small">
                    <div id="date-range">-</div>
                    <div id="day-progress">-</div>
                    <div id="remaining-info">-</div>
                </div>
            </div>
        </div>
    </div>
</div>
