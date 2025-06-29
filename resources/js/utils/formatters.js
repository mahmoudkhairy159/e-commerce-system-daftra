/**
 * Format price with currency symbol
 * @param {number} price - Price to format
 * @param {string} currency - Currency symbol (default: '$')
 * @param {number} decimalPlaces - Number of decimal places (default: 2)
 * @returns {string} - Formatted price string
 */
export const formatPrice = (price, currency = '$', decimalPlaces = 2) => {
    if (price == null || isNaN(price)) {
        return `${currency}0.00`;
    }

    const numericPrice = parseFloat(price);
    return `${currency}${numericPrice.toFixed(decimalPlaces)}`;
};

/**
 * Format number with proper thousands separators
 * @param {number} number - Number to format
 * @returns {string} - Formatted number string
 */
export const formatNumber = (number) => {
    if (number == null || isNaN(number)) {
        return '0';
    }

    return parseFloat(number).toLocaleString();
};

/**
 * Format date in a readable format
 * @param {string|Date} date - Date to format
 * @param {Object} options - Intl.DateTimeFormat options
 * @returns {string} - Formatted date string
 */
export const formatDate = (date, options = {}) => {
    if (!date) return '';

    const defaultOptions = {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        ...options
    };

    try {
        const dateObj = new Date(date);
        return dateObj.toLocaleDateString('en-US', defaultOptions);
    } catch (error) {
        console.error('Date formatting error:', error);
        return '';
    }
};

/**
 * Format date and time
 * @param {string|Date} date - Date to format
 * @returns {string} - Formatted date and time string
 */
export const formatDateTime = (date) => {
    return formatDate(date, {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

/**
 * Truncate text to specified length
 * @param {string} text - Text to truncate
 * @param {number} maxLength - Maximum length
 * @param {string} suffix - Suffix to add when truncated (default: '...')
 * @returns {string} - Truncated text
 */
export const truncateText = (text, maxLength, suffix = '...') => {
    if (!text || text.length <= maxLength) {
        return text || '';
    }

    return text.substring(0, maxLength).trim() + suffix;
};

/**
 * Format file size
 * @param {number} bytes - File size in bytes
 * @returns {string} - Formatted file size
 */
export const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes';

    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));

    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

/**
 * Capitalize first letter of each word
 * @param {string} text - Text to capitalize
 * @returns {string} - Capitalized text
 */
export const capitalizeWords = (text) => {
    if (!text) return '';

    return text.replace(/\b\w/g, (char) => char.toUpperCase());
};

/**
 * Format percentage
 * @param {number} value - Value to format as percentage
 * @param {number} decimalPlaces - Number of decimal places (default: 1)
 * @returns {string} - Formatted percentage
 */
export const formatPercentage = (value, decimalPlaces = 1) => {
    if (value == null || isNaN(value)) {
        return '0%';
    }

    return `${parseFloat(value).toFixed(decimalPlaces)}%`;
};
