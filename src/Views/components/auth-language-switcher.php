<?php
/**
 * Language switcher component for authentication screens
 * @var Models\TranslationManager $translator
 */

// Get current language from URL parameter or default to 'en'
$currentLang = $_GET['lang'] ?? 'en';
$supportedLangs = ['en' => 'English', 'de' => 'Deutsch'];
?>
<div class="language-switcher">
    <div class="dropdown">
        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-globe"></i> <?= $supportedLangs[$currentLang] ?? 'English' ?>
        </button>
        <ul class="dropdown-menu">
            <?php foreach ($supportedLangs as $langCode => $langName): ?>
                <li>
                    <a class="dropdown-item <?= $currentLang === $langCode ? 'active' : '' ?>" 
                       href="?lang=<?= $langCode ?><?= isset($_GET['token']) ? '&token=' . urlencode($_GET['token']) : '' ?>">
                        <?= $langName ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<style>
.language-switcher {
    position: absolute;
    top: 20px;
    right: 20px;
}

.language-switcher .btn {
    border-color: #dee2e6;
}

.language-switcher .dropdown-item.active {
    background-color: #e9ecef;
    font-weight: bold;
}
</style>