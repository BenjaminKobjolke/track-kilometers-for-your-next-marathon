<?php
/**
 * @var array $config
 * @var Models\Settings $settings
 * @var Models\TranslationManager $translator
 */
?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.appConfig = {
            baseUrl: '<?= $config['base_url'] ?>',
            translations: <?= json_encode($translator->getAll()) ?>,
            language: '<?= $settings->language ?>',
            session: <?= $activeSession ? json_encode([
                'unit' => $activeSession->unit ?? 'Kilometers',
                'unit_short' => $activeSession->unit_short ?? 'km'
            ]) : json_encode(['unit' => 'Kilometers', 'unit_short' => 'km']) ?>
        };
    </script>
    <script type="module" src="js/app.js"></script>
</body>
</html>
