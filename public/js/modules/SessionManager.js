import { DateFormatter } from './DateFormatter.js';

export class SessionManager {
    constructor() {
        this.baseUrl = window.appConfig.baseUrl;
        this.currentSession = null;
        this.initialized = false;
        this.initPromise = this.initializeCurrentSession();
    }

    async waitForInitialization() {
        if (!this.initialized) {
            await this.initPromise;
            this.initialized = true;
        }
    }

    async initializeCurrentSession() {
        try {
            console.log('Initializing current session...');
            
            // First try to get the current session from PHP session
            const currentResponse = await fetch(`${this.baseUrl}/api/sessions/current.php`);
            console.log('Current session response status:', currentResponse.status);

            if (currentResponse.ok) {
                const data = await currentResponse.json();
                if (data.success && data.session) {
                    this.currentSession = data.session;
                    console.log('Loaded current session:', this.currentSession);
                    return;
                }
            }

            // If no current session, fall back to getting first active session
            const response = await fetch(`${this.baseUrl}/api/sessions/active.php`);
            console.log('Active sessions response status:', response.status);

            if (!response.ok) {
                const errorText = await response.text();
                console.error('Failed to get active sessions:', errorText);
                throw new Error('Failed to get active sessions');
            }

            const data = await response.json();
            console.log('Active sessions response:', data);

            if (data.sessions && data.sessions.length > 0) {
                this.currentSession = data.sessions[0];
                console.log('Set current session:', this.currentSession);
            } else {
                console.log('No active sessions found during initialization');
            }
        } catch (error) {
            console.error('Failed to initialize current session:', error);
            this.currentSession = null;
        }
    }

    async createSession(name, startDate, endDate, targetKilometers, unit = 'Kilometers', unitShort = 'km') {
        const response = await fetch(`${this.baseUrl}/api/sessions.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                name,
                start_date: DateFormatter.germanToIsoDate(startDate),
                end_date: DateFormatter.germanToIsoDate(endDate),
                target_kilometers: targetKilometers,
                unit: unit,
                unit_short: unitShort
            })
        });

        const data = await response.json();
        if (!response.ok) {
            throw new Error(data.error || 'Failed to create session');
        }

        await this.setActiveSession(data.id);
        return data;
    }

    async getActiveSessions() {
        try {
            console.log('Fetching active sessions...');
            const response = await fetch(`${this.baseUrl}/api/sessions/active.php`);
            console.log('Active sessions response status:', response.status);
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('Failed to get active sessions:', errorText);
                throw new Error('Failed to get active sessions');
            }

            const data = await response.json();
            console.log('Active sessions API response:', data);

            if (!data.sessions) {
                console.error('Invalid response format:', data);
                throw new Error('Invalid response format');
            }

            console.log('Found', data.sessions.length, 'active sessions');
            return data;
        } catch (error) {
            console.error('Error getting active sessions:', error);
            throw error;
        }
    }

    async setActiveSession(sessionId) {
        try {
            console.log('Setting active session:', sessionId);
            const response = await fetch(`${this.baseUrl}/api/sessions/active.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    session_id: sessionId
                })
            });
            console.log('Set active session response status:', response.status);

            if (!response.ok) {
                const errorText = await response.text();
                console.error('Failed to set active session:', errorText);
                throw new Error('Failed to set active session');
            }

            const data = await response.json();
            console.log('Set active session response:', data);

            if (!data.session) {
                console.error('Invalid response format:', data);
                throw new Error('Invalid response format');
            }

            this.currentSession = data.session;
            console.log('Current session updated:', this.currentSession);
            return data.session;
        } catch (error) {
            console.error('Error setting active session:', error);
            throw error;
        }
    }

    async getAllSessions() {
        const response = await fetch(`${this.baseUrl}/api/sessions.php`);
        
        if (!response.ok) {
            throw new Error('Failed to get sessions');
        }

        return await response.json();
    }

    async getSession(id) {
        const response = await fetch(`${this.baseUrl}/api/sessions.php?id=${id}`);
        
        if (!response.ok) {
            throw new Error('Failed to get session');
        }

        return await response.json();
    }

    async updateSession(id, data) {
        // Convert dates to ISO format if present
        const updatedData = { ...data };
        if (updatedData.start_date) {
            updatedData.start_date = DateFormatter.germanToIsoDate(updatedData.start_date);
        }
        if (updatedData.end_date) {
            updatedData.end_date = DateFormatter.germanToIsoDate(updatedData.end_date);
        }

        const response = await fetch(`${this.baseUrl}/api/sessions.php?id=${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(updatedData)
        });

        if (!response.ok) {
            throw new Error('Failed to update session');
        }

        return await response.json();
    }

    async completeSession(sessionId) {
        const response = await fetch(`${this.baseUrl}/api/sessions/complete.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                session_id: sessionId
            })
        });

        if (!response.ok) {
            throw new Error('Failed to complete session');
        }

        return await response.json();
    }

    async deleteSession(id) {
        const response = await fetch(`${this.baseUrl}/api/sessions.php?id=${id}`, {
            method: 'DELETE'
        });

        if (!response.ok) {
            throw new Error('Failed to delete session');
        }

        return await response.json();
    }

    async reopenSession(id) {
        const response = await fetch(`${this.baseUrl}/api/sessions.php?id=${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                status: 'active'
            })
        });

        if (!response.ok) {
            throw new Error('Failed to reopen session');
        }

        const session = await response.json();
        await this.setActiveSession(session.id);
        return session;
    }

    async getCurrentSession() {
        await this.waitForInitialization();
        return this.currentSession;
    }

    async getActiveSession() {
        await this.waitForInitialization();
        return this.currentSession;
    }

    async getCompletedSessions() {
        try {
            const response = await fetch(`${this.baseUrl}/api/sessions.php?status=completed`);
            if (!response.ok) {
                throw new Error('Failed to get completed sessions');
            }

            const sessions = await response.json();
            console.log('Completed sessions:', sessions);
            return sessions;
        } catch (error) {
            console.error('Error getting completed sessions:', error);
            throw error;
        }
    }

    async getSessionStats(sessionId) {
        try {
            const response = await fetch(`${this.baseUrl}/api/runs.php?session_id=${sessionId}`);
            if (!response.ok) {
                throw new Error('Failed to get session runs');
            }

            const runs = await response.json();
            const totalKilometers = runs.reduce((sum, run) => sum + parseFloat(run.kilometers), 0);
            return {
                totalKilometers,
                runs
            };
        } catch (error) {
            console.error('Error getting session stats:', error);
            throw error;
        }
    }
}
