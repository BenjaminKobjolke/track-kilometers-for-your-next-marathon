export class PastSessionsManager {
    constructor(sessionManager, modalManager, uiManager, statsManager) {
        this.sessionManager = sessionManager;
        this.modalManager = modalManager;
        this.uiManager = uiManager;
        this.statsManager = statsManager;
        this.runManager = null; // Will be set in refreshUI
        this.initializeEventListeners();
    }

    initializeEventListeners() {
        const pastSessionsBtn = document.querySelector('[data-bs-target="#pastSessionsModal"]');
        if (pastSessionsBtn) {
            pastSessionsBtn.addEventListener('click', () => this.handlePastSessionsClick());
        }
    }

    async handlePastSessionsClick() {
        try {
            const sessions = await this.sessionManager.getCompletedSessions();
            
            // Get stats for each session
            const sessionsWithStats = await Promise.all(sessions.map(async (session) => {
                const stats = await this.sessionManager.getSessionStats(session.id);
                const totalDays = Math.ceil((new Date(session.end_date) - new Date(session.start_date)) / (1000 * 60 * 60 * 24)) + 1;
                const dailyAverage = totalDays > 0 ? stats.totalKilometers / totalDays : 0;
                return { ...session, stats, dailyAverage };
            }));

            this.modalManager.showPastSessionsModal(
                sessionsWithStats, 
                this.sessionManager,
                async () => {
                    // Refresh the list of completed sessions
                    const updatedSessions = await this.sessionManager.getCompletedSessions();
                    const updatedSessionsWithStats = await Promise.all(updatedSessions.map(async (session) => {
                        const stats = await this.sessionManager.getSessionStats(session.id);
                        const totalDays = Math.ceil((new Date(session.end_date) - new Date(session.start_date)) / (1000 * 60 * 60 * 24)) + 1;
                        const dailyAverage = totalDays > 0 ? stats.totalKilometers / totalDays : 0;
                        return { ...session, stats, dailyAverage };
                    }));
                    this.modalManager.showPastSessionsModal(
                        updatedSessionsWithStats,
                        this.sessionManager,
                        async () => {
                            await this.refreshUI();
                            return true; // Return a value to prevent undefined output
                        }
                    );
                    return true; // Return a value to prevent undefined output
                }
            );
        } catch (error) {
            console.error('Error loading past sessions:', error);
            document.getElementById('pastSessionsList').innerHTML = 
                '<tr><td colspan="6" class="text-danger">Error loading sessions</td></tr>';
        }
    }

    async refreshUI() {
        const currentSession = await this.sessionManager.getCurrentSession();
        if (currentSession) {
            await this.uiManager.updateUI(currentSession, this.runManager, this.statsManager);
        } else {
            this.uiManager.clearUI(this.statsManager);
            this.modalManager.showCreateSessionModal();
        }
        return true; // Return a value to prevent undefined output
    }
}
