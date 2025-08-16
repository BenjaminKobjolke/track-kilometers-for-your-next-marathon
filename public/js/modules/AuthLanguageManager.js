export class AuthLanguageManager {
    constructor() {
        this.storageKey = 'auth_language_preference';
        this.init();
    }

    init() {
        // Check if we need to redirect based on localStorage preference
        this.checkAndApplyStoredLanguage();
        
        // Set up event listeners for language switches
        this.bindLanguageSwitchers();
        
        // Store current language in localStorage
        this.storeCurrentLanguage();
    }

    checkAndApplyStoredLanguage() {
        const urlParams = new URLSearchParams(window.location.search);
        const currentLangParam = urlParams.get('lang');
        const storedLanguage = localStorage.getItem(this.storageKey);
        
        // If no language parameter in URL but we have a stored preference, redirect
        if (!currentLangParam && storedLanguage && storedLanguage !== 'en') {
            urlParams.set('lang', storedLanguage);
            window.location.search = urlParams.toString();
            return;
        }
        
        // If there's a language parameter, make sure it's stored
        if (currentLangParam) {
            localStorage.setItem(this.storageKey, currentLangParam);
        }
    }

    storeCurrentLanguage() {
        const urlParams = new URLSearchParams(window.location.search);
        const currentLang = urlParams.get('lang') || 'en';
        localStorage.setItem(this.storageKey, currentLang);
    }

    bindLanguageSwitchers() {
        // Handle language switcher links
        document.addEventListener('click', (e) => {
            if (e.target.matches('a[href*="lang="]') || e.target.closest('a[href*="lang="]')) {
                const link = e.target.matches('a') ? e.target : e.target.closest('a');
                const url = new URL(link.href, window.location.origin);
                const newLang = url.searchParams.get('lang');
                
                if (newLang) {
                    // Store the new language preference
                    localStorage.setItem(this.storageKey, newLang);
                    
                    // Update current URL to include language parameter
                    const currentUrl = new URL(window.location);
                    currentUrl.searchParams.set('lang', newLang);
                    
                    // Preserve any existing parameters (like token for password reset)
                    for (const [key, value] of url.searchParams) {
                        if (key !== 'lang') {
                            currentUrl.searchParams.set(key, value);
                        }
                    }
                    
                    window.location.href = currentUrl.toString();
                    e.preventDefault();
                }
            }
        });
    }

    // Method to get current language
    getCurrentLanguage() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('lang') || localStorage.getItem(this.storageKey) || 'en';
    }

    // Method to switch language programmatically
    switchLanguage(langCode) {
        localStorage.setItem(this.storageKey, langCode);
        const url = new URL(window.location);
        url.searchParams.set('lang', langCode);
        window.location.href = url.toString();
    }
}