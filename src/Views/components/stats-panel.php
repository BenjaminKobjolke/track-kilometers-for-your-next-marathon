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
                    <div class="card-info">
                        <h5 class="card-title"><?= $translator->get('stats_total_kilometers') ?></h5>
                        <?php if ($activeSession): ?>
                        <p class="card-subtext"><?= $translator->get('stats_since', ['date' => DateFormatter::isoToGermanDate($activeSession->start_date)]) ?></p>
                        <?php endif; ?>
                    </div>
                    <p class="card-text"><?= number_format($stats['totalKilometers'], 1) ?> km</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="card-info">
                        <h5 class="card-title"><?= $translator->get('stats_estimated_total') ?></h5>
                        <p class="card-subtext"><?= $translator->get('stats_daily_average', ['km' => number_format($stats['averageKilometers'], 1)]) ?></p>
                    </div>
                    <p class="card-text"><?= number_format($stats['estimatedTotal'], 1) ?> km</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="card-info">
                        <h5 class="card-title"><?= $translator->get('stats_remaining_days') ?></h5>
                        <?php if ($activeSession): ?>
                        <p class="card-subtext"><?= $translator->get('stats_until', ['date' => DateFormatter::isoToGermanDate($activeSession->end_date)]) ?></p>
                        <?php endif; ?>
                    </div>
                    <p class="card-text"><?= $translator->get('stats_days', ['count' => $stats['remainingDays']]) ?></p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card probability-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="card-info">
                        <h5 class="card-title"><?= $translator->get('stats_target_probability') ?></h5>
                        <p class="card-subtext"><?= $translator->get('stats_target_of', ['km' => number_format($activeSession ? $activeSession->target_kilometers : 0, 1)]) ?></p>
                    </div>
                    <p class="card-text"><?= number_format($stats['probability'], 1) ?>%</p>
                </div>
            </div>
        </div>
    </div>
</div>
