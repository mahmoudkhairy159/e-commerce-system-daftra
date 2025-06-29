import { useState, useEffect, useCallback } from 'react';

/**
 * Custom hook for debouncing values
 * @param {any} value - The value to debounce
 * @param {number} delay - Delay in milliseconds
 * @returns {Object} - Object containing debouncedValue and isDebouncing state
 */
export const useDebounce = (value, delay = 500) => {
    const [debouncedValue, setDebouncedValue] = useState(value);
    const [isDebouncing, setIsDebouncing] = useState(false);

    useEffect(() => {
        setIsDebouncing(true);

        const handler = setTimeout(() => {
            setDebouncedValue(value);
            setIsDebouncing(false);
        }, delay);

        return () => {
            clearTimeout(handler);
            setIsDebouncing(false);
        };
    }, [value, delay]);

    const reset = useCallback(() => {
        setDebouncedValue('');
        setIsDebouncing(false);
    }, []);

    return {
        debouncedValue,
        isDebouncing,
        reset
    };
};

export default useDebounce;
