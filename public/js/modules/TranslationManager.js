export class TranslationManager {
    constructor() {
        this.translations = window.appConfig.translations || {};
        this.language = window.appConfig.language || 'en';
        this.sessionContext = window.appConfig.session || { unit: 'Kilometers', unit_short: 'km' };
    }

    translate(key, params = {}) {
        if (!this.translations[key]) {
            console.warn(`Translation key not found: ${key}`);
            return key;
        }

        let translation = this.translations[key];

        // Auto-inject session unit context with fallbacks
        const defaultParams = {
            unit: this.sessionContext?.unit || 'Entry',
            unit_short: this.sessionContext?.unit_short || 'entries'
        };

        const allParams = {
            ...defaultParams,
            ...params
        };

        // Replace parameters
        Object.entries(allParams).forEach(([param, value]) => {
            translation = translation.replace(`{${param}}`, value);
        });

        return translation;
    }

    updateSessionContext(sessionData) {
        if (sessionData && sessionData.unit && sessionData.unit_short) {
            this.sessionContext = {
                unit: sessionData.unit,
                unit_short: sessionData.unit_short
            };
        }
    }

    getCurrentLanguage() {
        return this.language;
    }
}

// Create a singleton instance
const translationManager = new TranslationManager();
export default translationManager;
