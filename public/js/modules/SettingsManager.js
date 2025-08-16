import translationManager from './TranslationManager.js';

export class SettingsManager {
    constructor() {
        this.bindEvents();
    }

    bindEvents() {
        const saveButton = document.getElementById('saveSettings');
        if (saveButton) {
            saveButton.addEventListener('click', () => this.saveSettings());
        }

        // Handle theme preview
        const themeSelect = document.getElementById('theme');
        if (themeSelect) {
            themeSelect.addEventListener('change', (e) => {
                document.body.dataset.theme = e.target.value;
            });
        }
    }

    async saveSettings() {
        const formData = {
            theme: document.getElementById('theme').value,
            language: document.getElementById('language').value
        };

        // Apply theme immediately
        document.body.dataset.theme = formData.theme;

        try {
            const response = await fetch('api/settings.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });
            const data = await response.json();
            
            if (data.success) {
                // Always reload page to get new translations
                location.reload();
            } else {
                alert(translationManager.translate('error_saving_settings', { message: data.message }));
            }
        } catch (error) {
            console.error('Error:', error);
            alert(translationManager.translate('error_saving_settings', { message: error.message }));
        }
    }
}
