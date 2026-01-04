/**
 * Utility functions for formatting data
 */

/**
 * Format a number as Brazilian currency (BRL)
 * @param value - The numeric value to format
 * @param locale - The locale to use (default: 'pt-BR')
 * @returns Formatted currency string (e.g., "R$ 1.234,56")
 */
export const formatCurrency = (value: number, locale = 'pt-BR'): string => {
    return new Intl.NumberFormat(locale, {
        style: 'currency',
        currency: 'BRL',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(value);
};

/**
 * Format a date string or Date object as Brazilian date format
 * @param date - The date to format (string or Date object)
 * @param locale - The locale to use (default: 'pt-BR')
 * @returns Formatted date string (e.g., "31/12/2024")
 */
export const formatDate = (
    date: string | Date,
    locale = 'pt-BR',
): string => {
    if (typeof date === 'string') {
        // Ensure proper date parsing for YYYY-MM-DD format
        // Add timezone offset to avoid date shifting
        const dateObj = new Date(date + 'T00:00:00');
        return dateObj.toLocaleDateString(locale);
    }
    return date.toLocaleDateString(locale);
};

/**
 * Format a date with time
 * @param date - The date to format
 * @param locale - The locale to use (default: 'pt-BR')
 * @returns Formatted datetime string (e.g., "31/12/2024 14:30")
 */
export const formatDateTime = (
    date: string | Date,
    locale = 'pt-BR',
): string => {
    const dateObj = typeof date === 'string' ? new Date(date) : date;
    return dateObj.toLocaleString(locale, {
        dateStyle: 'short',
        timeStyle: 'short',
    });
};

/**
 * Format a number with thousand separators
 * @param value - The numeric value to format
 * @param locale - The locale to use (default: 'pt-BR')
 * @returns Formatted number string (e.g., "1.234,56")
 */
export const formatNumber = (value: number, locale = 'pt-BR'): string => {
    return new Intl.NumberFormat(locale, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(value);
};

/**
 * Format a percentage value
 * @param value - The percentage value (e.g., 0.75 for 75%)
 * @param decimals - Number of decimal places (default: 0)
 * @returns Formatted percentage string (e.g., "75%")
 */
export const formatPercentage = (
    value: number,
    decimals = 0,
): string => {
    return `${value.toFixed(decimals)}%`;
};
