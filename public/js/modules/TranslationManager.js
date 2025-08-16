export class TranslationManager {
    constructor() {
        this.translations = window.appConfig.translations || {};
        this.language = window.appConfig.language || 'en';
    }

    translate(key, params = {}) {
        if (!this.translations[key]) {
            console.warn(`Translation key not found: ${key}`);
            return key;
        }

        let translation = this.translations[key];

        // Replace parameters
        Object.entries(params).forEach(([param, value]) => {
            translation = translation.replace(`{${param}}`, value);
        });

        return translation;
    }

    getCurrentLanguage() {
        return this.language;
    }
}

// Create a singleton instance
const translationManager = new TranslationManager();
export default translationManager;
