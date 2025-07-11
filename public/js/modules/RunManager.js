import { DateFormatter } from './DateFormatter.js';

export class RunManager {
    constructor(sessionManager, uiManager, statsManager) {
        this.uiManager = uiManager;
        this.statsManager = statsManager;
        this.sessionManager = sessionManager;
        this.boundEventHandlers = new Map();
        this.bindEvents();
        // Store instance for rebinding
        RunManager.instance = this;
    }

    rebindEvents() {
        this.removeEventListeners();
        this.bindEvents();
    }

    static rebindEvents() {
        if (RunManager.instance) {
            RunManager.instance.rebindEvents();
        }
    }

    removeEventListeners() {
        // Remove save button listener
        const saveButton = document.getElementById('saveRun');
        if (saveButton && this.boundEventHandlers.has('save')) {
            saveButton.removeEventListener('click', this.boundEventHandlers.get('save'));
        }

        // Remove edit button listeners
        const editButtons = document.querySelectorAll('.edit-run');
        if (editButtons.length > 0 && this.boundEventHandlers.has('edit')) {
            editButtons.forEach(button => {
                button.removeEventListener('click', this.boundEventHandlers.get('edit'));
            });
        }

        // Remove delete button listeners
        const deleteButtons = document.querySelectorAll('.delete-run');
        if (deleteButtons.length > 0 && this.boundEventHandlers.has('delete')) {
            deleteButtons.forEach(button => {
                button.removeEventListener('click', this.boundEventHandlers.get('delete'));
            });
        }

        // Remove add button listener
        const addButton = document.querySelector('[data-bs-target="#addRunModal"]');
        if (addButton && this.boundEventHandlers.has('add')) {
            addButton.removeEventListener('click', this.boundEventHandlers.get('add'));
        }

        // Remove date input listener
        const dateInput = document.querySelector('input[name="date"]');
        if (dateInput && this.boundEventHandlers.has('date')) {
            dateInput.removeEventListener('blur', this.boundEventHandlers.get('date'));
        }

        // Clear the map
        this.boundEventHandlers.clear();
    }

    bindEvents() {
        // Save Run
        const saveButton = document.getElementById('saveRun');
        if (saveButton) {
            const handler = () => this.saveRun();
            this.boundEventHandlers.set('save', handler);
            saveButton.addEventListener('click', handler);
        }

        // Edit Run
        const editButtons = document.querySelectorAll('.edit-run');
        if (editButtons.length > 0) {
            const handler = (e) => this.editRun(e);
            this.boundEventHandlers.set('edit', handler);
            editButtons.forEach(button => {
                button.addEventListener('click', handler);
            });
        }

        // Delete Run
        const deleteButtons = document.querySelectorAll('.delete-run');
        if (deleteButtons.length > 0) {
            const handler = (e) => this.deleteRun(e);
            this.boundEventHandlers.set('delete', handler);
            deleteButtons.forEach(button => {
                button.addEventListener('click', handler);
            });
        }

        // Reset form when adding new run
        const addButton = document.querySelector('[data-bs-target="#addRunModal"]');
        if (addButton) {
            const handler = () => {
                this.resetForm();
                this.setDefaultDate();
            };
            this.boundEventHandlers.set('add', handler);
            addButton.addEventListener('click', handler);
        }

        // Add date validation
        const dateInput = document.querySelector('input[name="date"]');
        if (dateInput) {
            const handler = (e) => DateFormatter.validateDateInput(e.target);
            this.boundEventHandlers.set('date', handler);
            dateInput.addEventListener('blur', handler);
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

    async updateUIWithRuns(runs, session) {
        this.uiManager.updateRunsTable(runs);
        await this.statsManager.updateStats(session, runs);
        this.rebindEvents();
    }

    async saveRun() {
        const runDate = document.getElementById('runDate').value;
        
        if (!DateFormatter.isValidGermanDate(runDate)) {
            alert('Please enter a valid date in DD.MM.YYYY format');
            return;
        }

        const runId = document.getElementById('runId').value;
        // Check if there's an active session
        const currentSession = await this.sessionManager.getCurrentSession();
        if (!currentSession) {
            alert('Please select or create a session first');
            return;
        }

        const formData = {
            date: DateFormatter.germanToIsoDate(runDate),
            kilometers: document.getElementById('kilometers').value,
            session_id: currentSession.id
        };

        if (runId) {
            formData.id = runId;
        }

        // Validate date is within session period
        const runDateObj = new Date(formData.date);
        const sessionStart = new Date(currentSession.start_date);
        const sessionEnd = new Date(currentSession.end_date);

        if (runDateObj < sessionStart || runDateObj > sessionEnd) {
            alert(`Run date must be between ${DateFormatter.isoToGermanDate(currentSession.start_date)} and ${DateFormatter.isoToGermanDate(currentSession.end_date)}`);
            return;
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
                // Update runs table and stats
                const runsResponse = await fetch('api/runs.php');
                if (runsResponse.ok) {
                    const runs = await runsResponse.json();
                    await this.updateUIWithRuns(runs, currentSession);
                } else {
                    await this.updateUIWithRuns([], currentSession);
                }
                // Close modal if it's open
                const modal = bootstrap.Modal.getInstance(document.getElementById('addRunModal'));
                if (modal) modal.hide();
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
                // Update runs table and stats
                const runsResponse = await fetch('api/runs.php');
                const currentSession = await this.sessionManager.getCurrentSession();
                if (runsResponse.ok) {
                    const runs = await runsResponse.json();
                    await this.updateUIWithRuns(runs, currentSession);
                } else {
                    await this.updateUIWithRuns([], currentSession);
                }
            } else {
                alert('Error deleting run: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error deleting run');
        }
    }
}
