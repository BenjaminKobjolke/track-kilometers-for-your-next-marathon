<?php
/**
 * @var Models\TranslationManager $translator
 * @var array $stats
 */
?>
<div class="table-responsive mt-4">
    <table class="table">
        <thead>
            <tr>
                <th><?= $translator->get('table_header_date') ?></th>
                <th><?= $translator->get('table_header_amount', [], $activeSession ?? null) ?></th>
                <th class="text-end"><?= $translator->get('table_header_actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($stats['runs'] as $run): ?>
            <tr>
                <td><?= $run->formatted_date ?></td>
                <td><?= number_format($run->amount, 1) ?></td>
                <td class="actions-cell text-end">
                    <button class="btn btn-sm btn-primary edit-run me-2" 
                            data-id="<?= $run->id ?>"
                            data-date="<?= $run->formatted_date ?>"
                            data-amount="<?= $run->amount ?>">
                        <?= $translator->get('button_edit') ?>
                    </button>
                    <button class="btn btn-sm btn-danger delete-run"
                            data-id="<?= $run->id ?>"
                            data-date="<?= $run->formatted_date ?>">
                        <?= $translator->get('button_delete') ?>
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
