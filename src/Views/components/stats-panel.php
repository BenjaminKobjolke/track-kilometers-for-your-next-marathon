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
                        <p class="card-text" id="total-amount"><?= NumberFormatter::format(is_nan($stats['totalKilometers']) ? 0 : $stats['totalKilometers'], 1, $translator->getCurrentLanguage()) ?> <?= $activeSession ? $activeSession->unit_short : 'units' ?></p>
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
                        <p class="card-subtext" id="daily-average"><?= $translator->get('stats_daily_average', ['amount' => NumberFormatter::format(is_nan($stats['averageKilometers']) ? 0 : $stats['averageKilometers'], 1, $translator->getCurrentLanguage())], $activeSession) ?></p>
                    </div>
                    <div class="card-value text-end">
                        <p class="card-text" id="estimated-total"><?= NumberFormatter::format(is_nan($stats['estimatedTotal']) ? 0 : $stats['estimatedTotal'], 1, $translator->getCurrentLanguage()) ?> <?= $activeSession ? $activeSession->unit_short : 'units' ?></p>
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
                        <h5 class="card-title"><?= $translator->get('stats_remaining_days') ?></h5>
                        <?php if ($activeSession): ?>
                        <p class="card-subtext"><?= $translator->get('stats_date_range', [
                            'start_date' => DateFormatter::isoToGermanDate($activeSession->start_date),
                            'end_date' => DateFormatter::isoToGermanDate($activeSession->end_date)
                        ]) ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="card-value text-end">
                        <p class="card-text" id="remaining-days"><?= $translator->get('stats_days', ['count' => $stats['remainingDays']]) ?></p>
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
                        <p class="card-subtext" id="target-info"><?= $translator->get('stats_target_of', ['amount' => NumberFormatter::format($activeSession ? $activeSession->target_kilometers : 0, 1, $translator->getCurrentLanguage())], $activeSession) ?></p>
                    </div>
                    <div class="card-value text-end">
                        <p class="card-text" id="target-probability"><?= NumberFormatter::format(is_nan($stats['probability']) ? 0 : $stats['probability'], 1, $translator->getCurrentLanguage()) ?>%</p>
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
                        <h5 class="card-title"><?= $translator->get('stats_training_period') ?></h5>
                        <p class="card-subtext" id="elapsed-days"><?= $translator->get('stats_day_of', ['current' => $stats['elapsedDays'], 'total' => $stats['trainingDays']]) ?></p>
                    </div>
                    <div class="card-value text-end">
                        <p class="card-text" id="training-days"><?= $translator->get('stats_days', ['count' => $stats['trainingDays']]) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
