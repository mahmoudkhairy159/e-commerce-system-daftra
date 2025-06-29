import { useState, useCallback } from 'react';
import { useSnackbar } from 'notistack';

/**
 * Custom hook for handling API calls with loading, error states, and retry functionality
 * @param {Object} options - Configuration options
 * @param {boolean} options.showErrorNotification - Whether to show error notifications
 * @param {boolean} options.showSuccessNotification - Whether to show success notifications
 * @param {number} options.maxRetries - Maximum number of retries for failed requests
 */
export const useApi = (options = {}) => {
    const {
        showErrorNotification = true,
        showSuccessNotification = false,
        maxRetries = 2
    } = options;

    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);
    const { enqueueSnackbar } = useSnackbar();

    const execute = useCallback(async (apiCall, {
        retryCount = 0,
        successMessage = null,
        errorMessage = null,
        onSuccess = null,
        onError = null
    } = {}) => {
        try {
            setLoading(true);
            setError(null);

            const response = await apiCall();

            if (successMessage && showSuccessNotification) {
                enqueueSnackbar(successMessage, { variant: 'success' });
            }

            if (onSuccess) {
                onSuccess(response);
            }

            return { success: true, data: response };
        } catch (err) {
            console.error('API call failed:', err);

            // Retry logic
            if (retryCount < maxRetries) {
                console.log(`Retrying API call... Attempt ${retryCount + 1}/${maxRetries}`);
                return execute(apiCall, {
                    retryCount: retryCount + 1,
                    successMessage,
                    errorMessage,
                    onSuccess,
                    onError
                });
            }

            const finalError = errorMessage || err.response?.data?.message || err.message || 'An error occurred';
            setError(finalError);

            if (showErrorNotification) {
                enqueueSnackbar(finalError, { variant: 'error' });
            }

            if (onError) {
                onError(err);
            }

            return { success: false, error: finalError };
        } finally {
            setLoading(false);
        }
    }, [enqueueSnackbar, showErrorNotification, showSuccessNotification, maxRetries]);

    const reset = useCallback(() => {
        setLoading(false);
        setError(null);
    }, []);

    return {
        execute,
        loading,
        error,
        reset
    };
};

export default useApi;
