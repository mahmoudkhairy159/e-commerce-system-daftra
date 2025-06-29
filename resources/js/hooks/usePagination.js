import { useState, useCallback, useMemo } from 'react';

/**
 * Custom hook for managing pagination state
 * @param {Object} initialState - Initial pagination state
 * @returns {Object} - Pagination state and handlers
 */
export const usePagination = (initialState = {}) => {
    const [pagination, setPagination] = useState({
        total: 0,
        per_page: 6,
        current_page: 1,
        last_page: 1,
        from: 1,
        to: 6,
        ...initialState
    });

    const updatePagination = useCallback((newPagination) => {
        setPagination(prev => ({
            ...prev,
            ...newPagination
        }));
    }, []);

    const setPage = useCallback((page) => {
        setPagination(prev => ({
            ...prev,
            current_page: page
        }));
    }, []);

    const setPerPage = useCallback((perPage) => {
        setPagination(prev => ({
            ...prev,
            per_page: perPage,
            current_page: 1 // Reset to first page when changing per_page
        }));
    }, []);

    const reset = useCallback(() => {
        setPagination({
            total: 0,
            per_page: 6,
            current_page: 1,
            last_page: 1,
            from: 1,
            to: 6,
            ...initialState
        });
    }, [initialState]);

    // Computed values
    const paginationInfo = useMemo(() => ({
        hasNextPage: pagination.current_page < pagination.last_page,
        hasPrevPage: pagination.current_page > 1,
        isFirstPage: pagination.current_page === 1,
        isLastPage: pagination.current_page === pagination.last_page,
        totalPages: pagination.last_page,
        currentPage: pagination.current_page,
        itemsPerPage: pagination.per_page,
        totalItems: pagination.total,
        startItem: pagination.from,
        endItem: pagination.to
    }), [pagination]);

    return {
        pagination,
        updatePagination,
        setPage,
        setPerPage,
        reset,
        ...paginationInfo
    };
};

export default usePagination;
