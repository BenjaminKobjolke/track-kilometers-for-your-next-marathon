import { RunManager } from './modules/RunManager.js';
import { SettingsManager } from './modules/SettingsManager.js';
import { AuthManager } from './modules/AuthManager.js';
import { SessionManager } from './modules/SessionManager.js';
import { StatsManager } from './modules/StatsManager.js';
import { ModalManager } from './modules/ModalManager.js';
import { UIManager } from './modules/UIManager.js';
import { PastSessionsManager } from './modules/PastSessionsManager.js';
import { DateFormatter } from './modules/DateFormatter.js';
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

        // Handle session creation and editing
        const createSessionForm = document.getElementById('createSessionForm');
        if (createSessionForm) {
            createSessionForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const sessionId = document.getElementById('sessionId').value;
                const name = document.getElementById('sessionName').value;
                const startDate = document.getElementById('sessionStartDate').value;
                const endDate = document.getElementById('sessionEndDate').value;
                const targetKm = document.getElementById('sessionTargetKm').value;

                try {
                    let session;
                    if (sessionId) {
                        // Edit existing session
                        session = await sessionManager.updateSession(sessionId, {
                            name: name,
                            start_date: startDate,
                            end_date: endDate,
                            target_kilometers: parseFloat(targetKm)
                        });
                    } else {
                        // Create new session
                        session = await sessionManager.createSession(name, startDate, endDate, parseFloat(targetKm));
                    }
                    await uiManager.updateUI(session, runManager, statsManager);
                    modalManager.hideModal(document.getElementById('createSessionModal'));
                } catch (error) {
                    console.error('Error saving session:', error);
                    if (error.message) {
                        alert(error.message);
                    } else {
                        alert(translationManager.translate('error_generic', { message: 'saving session' }));
                    }
                }
            });
        }

        // Handle edit session button
        const editSessionBtn = document.getElementById('editSession');
        if (editSessionBtn) {
            editSessionBtn.addEventListener('click', async () => {
                try {
                    const activeSession = await sessionManager.getActiveSession();
                    if (activeSession) {
                        // Populate form with current session data
                        document.getElementById('sessionId').value = activeSession.id;
                        document.getElementById('sessionName').value = activeSession.name;
                        document.getElementById('sessionStartDate').value = DateFormatter.isoToGermanDate(activeSession.start_date);
                        document.getElementById('sessionEndDate').value = DateFormatter.isoToGermanDate(activeSession.end_date);
                        document.getElementById('sessionTargetKm').value = activeSession.target_kilometers;
                        
                        // Update modal title and button text
                        document.getElementById('sessionModalTitle').textContent = translationManager.translate('modal_title_edit_session');
                        document.getElementById('sessionSubmitBtn').textContent = translationManager.translate('button_save');
                        
                        // Show modal
                        modalManager.showModal(document.getElementById('createSessionModal'));
                    }
                } catch (error) {
                    console.error('Error loading session for edit:', error);
                    alert(translationManager.translate('error_generic', { message: 'loading session' }));
                }
            });
        }

        // Reset form when creating new session (triggered by "New Session" button)
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-bs-target="#createSessionModal"]')) {
                // Reset form for new session creation
                document.getElementById('sessionId').value = '';
                document.getElementById('sessionName').value = '';
                document.getElementById('sessionStartDate').value = '';
                document.getElementById('sessionEndDate').value = '';
                document.getElementById('sessionTargetKm').value = '500';
                
                // Reset modal title and button text
                document.getElementById('sessionModalTitle').textContent = translationManager.translate('modal_title_create_session');
                document.getElementById('sessionSubmitBtn').textContent = translationManager.translate('button_create');
            }
        });

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
