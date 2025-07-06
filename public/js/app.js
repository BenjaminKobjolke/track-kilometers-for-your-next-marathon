import { RunManager } from './modules/RunManager.js';
import { SettingsManager } from './modules/SettingsManager.js';
import { AuthManager } from './modules/AuthManager.js';

// Initialize managers based on page elements
if (document.getElementById('runForm')) {
    new RunManager();
    new SettingsManager();
    new AuthManager(); // Initialize AuthManager for logout button
} else if (document.getElementById('loginForm')) {
    new AuthManager();
}
