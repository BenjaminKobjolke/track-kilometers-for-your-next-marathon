export class AuthManager {
    constructor() {
        console.log('AuthManager initialized');
        this.bindEvents();
    }

    bindEvents() {
        const loginForm = document.getElementById('loginForm');
        const forgotPassword = document.getElementById('forgotPassword');
        const sendResetLink = document.getElementById('sendResetLink');
        const logoutButton = document.getElementById('logoutButton');
        const resetForm = document.getElementById('resetPasswordForm');

        if (loginForm) {
            loginForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.login();
            });
        }

        if (forgotPassword) {
            forgotPassword.addEventListener('click', (e) => {
                e.preventDefault();
                this.showResetModal();
            });
        }

        if (sendResetLink) {
            console.log('Adding click handler to send reset link button');
            sendResetLink.addEventListener('click', () => {
                console.log('Send reset link clicked');
                this.requestPasswordReset();
            });
        }

        if (resetForm) {
            resetForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.requestPasswordReset();
            });
        }

        if (logoutButton) {
            console.log('Adding logout button handler');
            logoutButton.addEventListener('click', () => {
                console.log('Logout button clicked');
                this.logout();
            });
        }
    }

    async logout() {
        try {
            console.log('Attempting logout...');
            const response = await fetch(`${window.appConfig.baseUrl}/api/auth/logout.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            const data = await response.json();
            
            console.log('Logout response:', data);
            if (data.success) {
                window.location.href = `${window.appConfig.baseUrl}/login.php`;
            } else {
                console.error('Logout failed:', data.message);
                alert('Logout failed: ' + data.message);
            }
        } catch (error) {
            console.error('Logout error:', error);
            alert('Error during logout: ' + error.message);
        }
    }

    showResetModal() {
        const modalElement = document.getElementById('resetPasswordModal');
        if (!modalElement) {
            console.error('Reset modal element not found');
            return;
        }
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    }

    async login() {
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const rememberMe = document.getElementById('rememberMe').checked;

        try {
            const response = await fetch(`${window.appConfig.baseUrl}/api/auth/login.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    email,
                    password,
                    remember_me: rememberMe
                })
            });

            const data = await response.json();
            
            if (data.success) {
                window.location.href = `${window.appConfig.baseUrl}/index.php`;
            } else {
                alert(data.message || 'Login failed');
            }
        } catch (error) {
            alert('Error during login');
        }
    }


    async requestPasswordReset() {
        console.log('Requesting password reset...');
        const email = document.getElementById('resetEmail').value;
        console.log('Email:', email);

        try {
            const response = await fetch(`${window.appConfig.baseUrl}/api/auth/reset-password.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ email })
            });

            const data = await response.json();
            
            if (data.success) {
                alert('Password reset link has been sent to your email');
                bootstrap.Modal.getInstance(document.getElementById('resetPasswordModal')).hide();
            } else {
                alert(data.message || 'Failed to send reset link');
            }
        } catch (error) {
            alert('Error requesting password reset');
        }
    }
}
