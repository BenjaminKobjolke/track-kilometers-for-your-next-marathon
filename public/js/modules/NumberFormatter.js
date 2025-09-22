export class NumberFormatter {
    // Format numbers to match the current app locale
    static format(value, decimals = 1) {
        const num = parseFloat(value);
        if (isNaN(num)) return '0.0';

        // Get current language from app config
        const language = window.appConfig?.language || 'en';
        const locale = language === 'de' ? 'de-DE' : 'en-US';

        return num.toLocaleString(locale, {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        });
    }
}

// Export a default instance for convenience
const numberFormatter = {
    format: NumberFormatter.format
};

export default numberFormatter;