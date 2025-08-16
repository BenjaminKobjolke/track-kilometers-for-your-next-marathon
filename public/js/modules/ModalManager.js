import { DateFormatter } from './DateFormatter.js';

export class ModalManager {
    constructor() {
        this.initializeModalCleanup();
    }

    initializeModalCleanup() {
        // Initialize settings modal cleanup
        const settingsModalElement = document.getElementById('settingsModal');
        if (settingsModalElement) {
            settingsModalElement.addEventListener('hidden.bs.modal', () => {
                this.cleanupModal(settingsModalElement);
            });
        }
    }

    cleanupModal(modalElement) {
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) {
            modal.dispose();
            document.body.classList.remove('modal-open');
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
        }
    }

    showModal(modalElement) {
        if (!modalElement) {
            console.error('Modal element not found');
            return;
        }

        // Remove any existing modal instance
        const existingModal = bootstrap.Modal.getInstance(modalElement);
        if (existingModal) {
            existingModal.dispose();
            document.body.classList.remove('modal-open');
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
        }

        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    }

    hideModal(modalElement) {
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) {
            modal.hide();
        }
    }

    showCreateSessionModal() {
        this.showModal(document.getElementById('createSessionModal'));
    }

    showSessionSelectionModal(sessions, sessionManager, updateUI) {
        const modalElement = document.getElementById('selectSessionModal');
        if (!modalElement) {
            console.error('Modal element not found');
            return;
        }

        const container = document.getElementById('sessionList');
        if (!container) {
            console.error('Session list container not found');
            return;
        }
        
        if (!Array.isArray(sessions)) {
            console.error('Sessions is not an array:', sessions);
            container.innerHTML = '<div class="alert alert-danger">Error loading sessions</div>';
            return;
        }

        container.innerHTML = sessions.map(session => `
            <div class="session-item mb-3 p-3 border rounded">
                <h5>${session.name}</h5>
                <p>Period: ${DateFormatter.isoToGermanDate(session.start_date)} - ${DateFormatter.isoToGermanDate(session.end_date)}</p>
                <button class="btn btn-primary select-session" data-id="${session.id}">Select</button>
            </div>
        `).join('');

        // Add click handlers
        container.querySelectorAll('.select-session').forEach(button => {
            button.addEventListener('click', async () => {
                try {
                    await sessionManager.setActiveSession(button.dataset.id);
                    // Reload page to ensure all translations and units are updated properly
                    window.location.reload();
                } catch (error) {
                    console.error('Error selecting session:', error);
                    alert('Error selecting session');
                }
            });
        });

        this.showModal(modalElement);
    }

    showPastSessionsModal(sessions, sessionManager, updateUI) {
        const modalElement = document.getElementById('pastSessionsModal');
        const container = document.getElementById('pastSessionsList');
        
        if (!sessions || !Array.isArray(sessions)) {
            container.innerHTML = '<tr><td colspan="6">No completed sessions found</td></tr>';
            return;
        }

        container.innerHTML = sessions.map(session => `
            <tr>
                <td>${session.name}</td>
                <td>${DateFormatter.isoToGermanDate(session.start_date)} - ${DateFormatter.isoToGermanDate(session.end_date)}</td>
                <td>${session.stats.totalKilometers.toFixed(1)} ${session.unit_short || 'units'}</td>
                <td>${session.dailyAverage.toFixed(1)} ${session.unit_short || 'units'}</td>
                <td>${session.status}</td>
                <td>
                    <button class="btn btn-sm btn-primary reopen-session me-2" data-id="${session.id}">
                        Reopen
                    </button>
                    <button class="btn btn-sm btn-danger delete-session" data-id="${session.id}">
                        Delete
                    </button>
                </td>
            </tr>
        `).join('');

        // Add click handlers
        container.querySelectorAll('.reopen-session').forEach(button => {
            button.addEventListener('click', async () => {
                try {
                    await sessionManager.reopenSession(button.dataset.id);
                    // Reload page to ensure all translations and units are updated properly
                    window.location.reload();
                } catch (error) {
                    console.error('Error reopening session:', error);
                    alert('Error reopening session');
                }
            });
        });

        container.querySelectorAll('.delete-session').forEach(button => {
            button.addEventListener('click', async () => {
                if (confirm('Are you sure you want to delete this session? This cannot be undone.')) {
                    try {
                        await sessionManager.deleteSession(button.dataset.id);
                        await updateUI();
                    } catch (error) {
                        console.error('Error deleting session:', error);
                        alert('Error deleting session');
                    }
                }
            });
        });

        this.showModal(modalElement);
    }
}
