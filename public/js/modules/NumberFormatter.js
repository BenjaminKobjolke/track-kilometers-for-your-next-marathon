export class NumberFormatter {
    // Format numbers to match PHP number_format() behavior
    static format(value, decimals = 1) {
        const num = parseFloat(value);
        if (isNaN(num)) return '0,0';

        // Use German locale formatting to match PHP number_format behavior
        return num.toLocaleString('de-DE', {
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