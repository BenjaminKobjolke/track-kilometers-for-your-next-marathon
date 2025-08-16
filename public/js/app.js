import { RunManager } from './modules/RunManager.js';
import { SettingsManager } from './modules/SettingsManager.js';
import { AuthManager } from './modules/AuthManager.js';
import { SessionManager } from './modules/SessionManager.js';
import { StatsManager } from './modules/StatsManager.js';
import { ModalManager } from './modules/ModalManager.js';
import { UIManager } from './modules/UIManager.js';
import { PastSessionsManager } from './modules/PastSessionsManager.js';
import translationManager from './modules/TranslationManager.js';

// Initialize app
async function initializeApp() {
    try {
        if (document.getElementById('runForm')) {
            const sessionManager = new SessionManager();
        const statsManager = new StatsManager();
        const modalManager = new ModalManager();
        const uiManager = new UIManager();
        const runManager = new RunManager(sessionManager, uiManager, statsManager);
        const settingsManager = new SettingsManager();
        const authManager = new AuthManager();
        const pastSessionsManager = new PastSessionsManager(sessionManager, modalManager, uiManager, statsManager);
        pastSessionsManager.runManager = runManager; // Set runManager after initialization

        // Initialize date inputs
        uiManager.initializeDateInputs();

        // Handle new session creation
        const createSessionForm = document.getElementById('createSessionForm');
        if (createSessionForm) {
            createSessionForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const name = document.getElementById('sessionName').value;
                const startDate = document.getElementById('sessionStartDate').value;
                const endDate = document.getElementById('sessionEndDate').value;
                const targetKm = document.getElementById('sessionTargetKm').value;

                try {
                    const session = await sessionManager.createSession(name, startDate, endDate, parseFloat(targetKm));
                    await uiManager.updateUI(session, runManager, statsManager);
                    modalManager.hideModal(document.getElementById('createSessionModal'));
                } catch (error) {
                    console.error('Error creating session:', error);
                    if (error.message) {
                        alert(error.message);
                    } else {
                        alert(translationManager.translate('error_generic', { message: 'creating session' }));
                    }
                }
            });
        }

        // Handle session completion
        const completeSessionBtn = document.getElementById('completeSession');
        if (completeSessionBtn) {
            completeSessionBtn.addEventListener('click', async () => {
                const currentSession = await sessionManager.getCurrentSession();
                if (!currentSession) return;

                if (confirm(translationManager.translate('message_confirm_complete_session'))) {
                    try {
                        await sessionManager.completeSession(currentSession.id);
                        uiManager.clearUI(statsManager);
                        modalManager.showCreateSessionModal();
                    } catch (error) {
                        console.error('Error completing session:', error);
                        alert(translationManager.translate('error_generic', { message: 'completing session' }));
                    }
                }
            });
        }

        // Handle session switching
        const switchSessionBtn = document.querySelector('#switchSession');
        if (switchSessionBtn) {
            switchSessionBtn.addEventListener('click', async () => {
                try {
                    const response = await sessionManager.getActiveSessions();
                    modalManager.showSessionSelectionModal(
                        response.sessions,
                        sessionManager,
                        async (session) => {
                            await uiManager.updateUI(session, runManager, statsManager);
                        }
                    );
                } catch (error) {
                    console.error('Error getting active sessions:', error);
                    alert(translationManager.translate('error_generic', { message: 'loading sessions' }));
                }
            });
        }

        // Check for active sessions on load
        checkActiveSessions();

        async function checkActiveSessions() {
            try {
                const currentSession = await sessionManager.getCurrentSession();
                if (currentSession) {
                    await uiManager.updateUI(currentSession, runManager, statsManager);
                    return;
                }

                const response = await sessionManager.getActiveSessions();
                
                if (!response.sessions || response.sessions.length === 0) {
                    modalManager.showCreateSessionModal();
                } else if (response.sessions.length === 1) {
                    const session = await sessionManager.setActiveSession(response.sessions[0].id);
                    await uiManager.updateUI(session, runManager, statsManager);
                } else {
                    modalManager.showSessionSelectionModal(
                        response.sessions,
                        sessionManager,
                        async (session) => {
                            await uiManager.updateUI(session, runManager, statsManager);
                        }
                    );
                }
            } catch (error) {
                console.error('Error checking active sessions:', error);
                alert(translationManager.translate('error_generic', { message: 'loading sessions' }));
            }
        }
    } else if (document.getElementById('loginForm')) {
        // Initialize auth manager after translations are loaded
        new AuthManager();
    }
    } catch (error) {
        console.error('Error initializing app:', error);
    }
}

// Start the application
initializeApp();
