<?php
/**
 * @var Models\Settings $settings
 * @var Models\TranslationManager $translator
 */
?>
<!DOCTYPE html>
<html lang="<?= $settings->language ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $translator->get('page_title_main') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body data-theme="<?= $settings->theme ?>">
    <div class="container mt-4">
