export default class RegisterManager {
    constructor() {
        this.form = document.getElementById('registerForm');
        this.emailInput = document.getElementById('email');
        this.passwordInput = document.getElementById('password');
        this.confirmPasswordInput = document.getElementById('confirmPassword');
        this.errorMessage = document.getElementById('errorMessage');
        this.successMessage = document.getElementById('successMessage');

        this.bindEvents();
    }

    bindEvents() {
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
    }

    showError(message) {
        this.errorMessage.textContent = message;
        this.errorMessage.classList.remove('d-none');
        this.successMessage.classList.add('d-none');
    }

    showSuccess(message) {
        this.successMessage.textContent = message;
        this.successMessage.classList.remove('d-none');
        this.errorMessage.classList.add('d-none');
    }

    validateForm() {
        if (this.passwordInput.value !== this.confirmPasswordInput.value) {
            this.showError('Passwords do not match');
            return false;
        }

        if (this.passwordInput.value.length < 8) {
            this.showError('Password must be at least 8 characters long');
            return false;
        }

        return true;
    }

    async handleSubmit(e) {
        e.preventDefault();

        if (!this.validateForm()) {
            return;
        }

        try {
            const response = await fetch(`${window.appConfig.baseUrl}/api/auth/register.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    email: this.emailInput.value,
                    password: this.passwordInput.value
                })
            });

            const data = await response.json();

            if (!data.success) {
                this.showError(data.error || 'Registration failed');
                return;
            }

            this.showSuccess(data.message);
            this.form.reset();

        } catch (error) {
            this.showError('An error occurred during registration');
            console.error('Registration error:', error);
        }
    }
}

// Initialize the manager
new RegisterManager();
