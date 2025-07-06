import { DateFormatter } from './DateFormatter.js';

export class SettingsManager {
    constructor() {
        this.bindEvents();
    }

    bindEvents() {
        const saveButton = document.getElementById('saveSettings');
        if (saveButton) {
            saveButton.addEventListener('click', () => this.saveSettings());
        }

        // Add date validation
        const dateInputs = document.querySelectorAll('input[name="start_date"], input[name="end_date"]');
        if (dateInputs.length > 0) {
            dateInputs.forEach(input => {
                input.addEventListener('blur', (e) => {
                    DateFormatter.validateDateInput(e.target);
                });
            });
        }
    }

    async saveSettings() {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;

        if (!DateFormatter.isValidGermanDate(startDate)) {
            alert('Please enter a valid start date in DD.MM.YYYY format');
            return;
        }
        if (!DateFormatter.isValidGermanDate(endDate)) {
            alert('Please enter a valid end date in DD.MM.YYYY format');
            return;
        }

        const formData = {
            start_date: DateFormatter.germanToIsoDate(startDate),
            end_date: DateFormatter.germanToIsoDate(endDate),
            target_kilometers: document.getElementById('targetKm').value,
            theme: document.getElementById('theme').value
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
                location.reload();
            } else {
                alert('Error saving settings: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error saving settings');
        }
    }
}
