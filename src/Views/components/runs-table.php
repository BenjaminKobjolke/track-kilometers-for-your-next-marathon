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
                <th><?= $translator->get('table_header_kilometers') ?></th>
                <th><?= $translator->get('table_header_actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($stats['runs'] as $run): ?>
            <tr>
                <td><?= $run->formatted_date ?></td>
                <td><?= number_format($run->kilometers, 1) ?></td>
                <td>
                    <button class="btn btn-sm btn-primary edit-run me-2" 
                            data-id="<?= $run->id ?>"
                            data-date="<?= $run->formatted_date ?>"
                            data-km="<?= $run->kilometers ?>">
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
