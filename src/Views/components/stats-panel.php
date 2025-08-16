<?php
/**
 * @var Models\Session $activeSession
 * @var Models\TranslationManager $translator
 * @var array $stats
 */

use Models\DateFormatter;
?>
<div class="row mt-4">
    <div class="col-md-3">
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
                        <p class="card-text" id="total-amount"><?= number_format(is_nan($stats['totalKilometers']) ? 0 : $stats['totalKilometers'], 1) ?> <?= $activeSession ? $activeSession->unit_short : 'units' ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="card-info flex-grow-1 pe-3">
                        <h5 class="card-title"><?= $translator->get('stats_estimated_total') ?></h5>
                        <p class="card-subtext" id="daily-average"><?= $translator->get('stats_daily_average', ['amount' => number_format(is_nan($stats['averageKilometers']) ? 0 : $stats['averageKilometers'], 1)], $activeSession) ?></p>
                    </div>
                    <div class="card-value text-end">
                        <p class="card-text" id="estimated-total"><?= number_format(is_nan($stats['estimatedTotal']) ? 0 : $stats['estimatedTotal'], 1) ?> <?= $activeSession ? $activeSession->unit_short : 'units' ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="card-info flex-grow-1 pe-3">
                        <h5 class="card-title"><?= $translator->get('stats_remaining_days') ?></h5>
                        <?php if ($activeSession): ?>
                        <p class="card-subtext"><?= $translator->get('stats_until', ['date' => DateFormatter::isoToGermanDate($activeSession->end_date)]) ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="card-value text-end">
                        <p class="card-text" id="remaining-days"><?= $translator->get('stats_days', ['count' => $stats['remainingDays']]) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card probability-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="card-info flex-grow-1 pe-3">
                        <h5 class="card-title"><?= $translator->get('stats_target_probability') ?></h5>
                        <p class="card-subtext" id="target-info"><?= $translator->get('stats_target_of', ['amount' => number_format($activeSession ? $activeSession->target_kilometers : 0, 1)], $activeSession) ?></p>
                    </div>
                    <div class="card-value text-end">
                        <p class="card-text" id="target-probability"><?= number_format(is_nan($stats['probability']) ? 0 : $stats['probability'], 1) ?>%</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
