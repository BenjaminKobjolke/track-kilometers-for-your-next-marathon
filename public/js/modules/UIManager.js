import translationManager from './TranslationManager.js';

export class UIManager {
    constructor() {
        this.runsTableBody = document.querySelector('tbody');
        this.currentSessionInfo = document.getElementById('currentSessionInfo');
    }

    refreshDynamicText() {
        // Update floating action button text
        const floatingBtn = document.querySelector('.floating-add-btn');
        if (floatingBtn) {
            floatingBtn.title = translationManager.translate('button_add_run');
        }

        // Update navbar add button text
        const navAddBtn = document.querySelector('[data-bs-target="#addRunModal"]');
        if (navAddBtn && navAddBtn.textContent.trim() !== '+') {
            navAddBtn.textContent = translationManager.translate('button_add_run');
        }

        // Update modal titles and labels (they will be updated when opened)
        // Update any other dynamic text elements as needed
    }

    updateSessionInfo(session) {
        if (this.currentSessionInfo) {
            if (!session) {
                this.currentSessionInfo.innerHTML = '';
                return;
            }
            this.currentSessionInfo.innerHTML = translationManager.translate('label_session', { name: session.name });
        }
    }

    updateRunsTable(runs) {
        if (!this.runsTableBody) return;

        this.runsTableBody.innerHTML = runs.map(run => `
            <tr>
                <td>${run.formatted_date}</td>
                <td>${Number(run.amount || run.kilometers).toFixed(1)}</td>
                <td class="actions-cell text-end">
                    <button class="btn btn-sm btn-primary edit-run me-2" 
                            data-id="${run.id}"
                            data-date="${run.formatted_date}"
                            data-amount="${run.amount || run.kilometers}">
                        ${translationManager.translate('button_edit')}
                    </button>
                    <button class="btn btn-sm btn-danger delete-run"
                            data-id="${run.id}"
                            data-date="${run.formatted_date}">
                        ${translationManager.translate('button_delete')}
                    </button>
                </td>
            </tr>
        `).join('');
    }

    async updateUI(session, runManager, statsManager) {
        // Update translation context with new session
        if (session) {
            translationManager.updateSessionContext(session);
        }
        
        this.updateSessionInfo(session);
        this.refreshDynamicText(); // Update all dynamic text with new session context

        try {
            const runsResponse = await fetch('api/runs.php');
            if (runsResponse.ok) {
                const runs = await runsResponse.json();
                this.updateRunsTable(runs);
                await statsManager.updateStats(session, runs);
            } else {
                this.updateRunsTable([]);
                statsManager.clearStats();
            }

            // Re-bind event handlers for the new buttons
            runManager.rebindEvents();
        } catch (error) {
            console.error(translationManager.translate('error_generic', { message: 'updating UI' }));
            this.updateRunsTable([]);
            statsManager.clearStats();
        }
    }

    clearUI(statsManager) {
        this.updateSessionInfo(null);
        this.updateRunsTable([]);
        statsManager.clearStats();
    }

    initializeDateInputs() {
        const dateInputs = document.querySelectorAll('input[data-type="date"]');
        dateInputs.forEach(input => {
            input.addEventListener('blur', (e) => {
                DateFormatter.validateDateInput(e.target);
            });
        });
    }
}
