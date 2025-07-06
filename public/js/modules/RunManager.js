import { DateFormatter } from './DateFormatter.js';

export class RunManager {
    constructor() {
        this.bindEvents();
    }

    bindEvents() {
        // Save Run
        const saveButton = document.getElementById('saveRun');
        if (saveButton) {
            saveButton.addEventListener('click', () => this.saveRun());
        }

        // Edit Run
        const editButtons = document.querySelectorAll('.edit-run');
        if (editButtons.length > 0) {
            editButtons.forEach(button => {
                button.addEventListener('click', (e) => this.editRun(e));
            });
        }

        // Delete Run
        const deleteButtons = document.querySelectorAll('.delete-run');
        if (deleteButtons.length > 0) {
            deleteButtons.forEach(button => {
                button.addEventListener('click', (e) => this.deleteRun(e));
            });
        }

        // Reset form when adding new run
        const addButton = document.querySelector('[data-bs-target="#addRunModal"]');
        if (addButton) {
            addButton.addEventListener('click', () => {
                this.resetForm();
                this.setDefaultDate();
            });
        }

        // Add date validation
        const dateInput = document.querySelector('input[name="date"]');
        if (dateInput) {
            dateInput.addEventListener('blur', (e) => {
                DateFormatter.validateDateInput(e.target);
            });
        }
    }

    setDefaultDate() {
        document.getElementById('runDate').value = DateFormatter.getCurrentGermanDate();
    }

    resetForm() {
        document.getElementById('runForm').reset();
        document.getElementById('runId').value = '';
        document.querySelector('#addRunModal .modal-title').textContent = 'Add Run';
    }

    async saveRun() {
        const runDate = document.getElementById('runDate').value;
        
        if (!DateFormatter.isValidGermanDate(runDate)) {
            alert('Please enter a valid date in DD.MM.YYYY format');
            return;
        }

        const runId = document.getElementById('runId').value;
        const formData = {
            date: DateFormatter.germanToIsoDate(runDate),
            kilometers: document.getElementById('kilometers').value
        };

        if (runId) {
            formData.id = runId;
        }

        try {
            const response = await fetch('api/runs.php', {
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
                alert('Error saving run: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error saving run');
        }
    }

    editRun(event) {
        const button = event.currentTarget;
        const modal = new bootstrap.Modal(document.getElementById('addRunModal'));
        document.getElementById('runId').value = button.dataset.id;
        document.getElementById('runDate').value = button.dataset.date;
        document.getElementById('kilometers').value = button.dataset.km;
        document.querySelector('#addRunModal .modal-title').textContent = 'Edit Run';
        modal.show();
    }

    async deleteRun(event) {
        const button = event.currentTarget;
        const runId = button.dataset.id;
        const date = button.dataset.date;

        if (!confirm(`Are you sure you want to delete the run from ${date}?`)) {
            return;
        }

        try {
            const response = await fetch('api/runs.php', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id: runId })
            });
            const data = await response.json();
            
            if (data.success) {
                location.reload();
            } else {
                alert('Error deleting run: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error deleting run');
        }
    }
}
