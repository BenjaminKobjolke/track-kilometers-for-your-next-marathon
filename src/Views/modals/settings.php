<?php
/**
 * @var Models\Settings $settings
 * @var Models\TranslationManager $translator
 */
?>
<div class="modal fade" id="settingsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= $translator->get('modal_title_settings') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="settingsForm">
                    <div class="mb-3">
                        <label for="theme" class="form-label"><?= $translator->get('settings_theme') ?></label>
                        <select class="form-control" id="theme" name="theme">
                            <option value="light" <?= $settings->theme === 'light' ? 'selected' : '' ?>><?= $translator->get('settings_theme_light') ?></option>
                            <option value="dark" <?= $settings->theme === 'dark' ? 'selected' : '' ?>><?= $translator->get('settings_theme_dark') ?></option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="language" class="form-label"><?= $translator->get('settings_language') ?></label>
                        <select class="form-control" id="language" name="language">
                            <option value="en" <?= $settings->language === 'en' ? 'selected' : '' ?>><?= $translator->get('settings_language_en') ?></option>
                            <option value="de" <?= $settings->language === 'de' ? 'selected' : '' ?>><?= $translator->get('settings_language_de') ?></option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= $translator->get('button_close') ?></button>
                <button type="button" class="btn btn-primary" id="saveSettings"><?= $translator->get('button_save') ?></button>
            </div>
        </div>
    </div>
</div>
