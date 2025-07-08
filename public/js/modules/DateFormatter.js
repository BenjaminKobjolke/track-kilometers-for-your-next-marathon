export class DateFormatter {
    static isValidGermanDate(dateStr) {
        if (!dateStr) return false;
        const pattern = /^(\d{2})\.(\d{2})\.(\d{4})$/;
        if (!pattern.test(dateStr)) return false;

        const [_, day, month, year] = dateStr.match(pattern);
        const date = new Date(year, month - 1, day);
        return date.getDate() == day && date.getMonth() == month - 1 && date.getFullYear() == year;
    }

    static germanToIsoDate(germanDate) {
        if (!germanDate || !this.isValidGermanDate(germanDate)) return '';
        const [day, month, year] = germanDate.split('.');
        return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
    }

    static getCurrentGermanDate() {
        const now = new Date();
        const day = now.getDate().toString().padStart(2, '0');
        const month = (now.getMonth() + 1).toString().padStart(2, '0');
        const year = now.getFullYear();
        return `${day}.${month}.${year}`;
    }

    static validateDateInput(input) {
        if (input.value && !this.isValidGermanDate(input.value)) {
            alert('Please enter the date in DD.MM.YYYY format');
            input.value = '';
            return false;
        }
        return true;
    }

    static isoToGermanDate(isoDate) {
        if (!isoDate) return '';
        
        // Remove any timezone info
        const cleanDate = isoDate.split('T')[0];
        const [year, month, day] = cleanDate.split('-');
        if (!year || !month || !day) return '';
        
        return `${day}.${month}.${year}`;
    }

    static formatDateForDisplay(date) {
        if (!date) return '';
        if (typeof date === 'string') {
            // Remove any timezone info
            const cleanDate = date.split('T')[0];
            const [year, month, day] = cleanDate.split('-');
            if (!year || !month || !day) return '';
            return `${day}.${month}.${year}`;
        }
        
        const day = date.getDate().toString().padStart(2, '0');
        const month = (date.getMonth() + 1).toString().padStart(2, '0');
        const year = date.getFullYear();
        return `${day}.${month}.${year}`;
    }
}
