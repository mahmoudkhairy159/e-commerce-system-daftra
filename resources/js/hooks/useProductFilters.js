import { useState, useCallback, useMemo } from 'react';
import { PRICE_RANGES, SORT_OPTIONS } from '../utils/constants';

/**
 * Custom hook for managing product filters
 * @param {Object} initialFilters - Initial filter state
 * @returns {Object} - Filter state and handlers
 */
export const useProductFilters = (initialFilters = {}) => {
    const [filters, setFilters] = useState({
        priceRange: PRICE_RANGES.DEFAULT,
        categories: { all: true },
        sortBy: 'latest',
        searchTerm: '',
        ...initialFilters
    });

    const updateFilters = useCallback((newFilters) => {
        setFilters(prev => ({
            ...prev,
            ...newFilters
        }));
    }, []);

    const setPriceRange = useCallback((priceRange) => {
        setFilters(prev => ({
            ...prev,
            priceRange
        }));
    }, []);

    const setCategories = useCallback((categories) => {
        setFilters(prev => ({
            ...prev,
            categories
        }));
    }, []);

    const setSortBy = useCallback((sortBy) => {
        setFilters(prev => ({
            ...prev,
            sortBy
        }));
    }, []);

    const setSearchTerm = useCallback((searchTerm) => {
        setFilters(prev => ({
            ...prev,
            searchTerm
        }));
    }, []);

    const clearAllFilters = useCallback(() => {
        setFilters({
            priceRange: PRICE_RANGES.DEFAULT,
            categories: { all: true },
            sortBy: 'latest',
            searchTerm: ''
        });
    }, []);

    const clearPriceFilter = useCallback(() => {
        setPriceRange(PRICE_RANGES.DEFAULT);
    }, [setPriceRange]);

    const clearCategoryFilter = useCallback(() => {
        setCategories({ all: true });
    }, [setCategories]);

    const clearSearchFilter = useCallback(() => {
        setSearchTerm('');
    }, [setSearchTerm]);

    // Build API parameters from current filters
    const buildApiParams = useCallback((additionalParams = {}) => {
        const params = {
            ...additionalParams
        };

        // Add search term
        if (filters.searchTerm.trim()) {
            params.search = filters.searchTerm.trim();
        }

        // Add price range filters
        if (filters.priceRange[0] > PRICE_RANGES.MIN) {
            params.fromPrice = filters.priceRange[0];
        }
        if (filters.priceRange[1] < PRICE_RANGES.MAX) {
            params.toPrice = filters.priceRange[1];
        }

        // Add category filters
        if (!filters.categories.all) {
            const selectedCategoryIds = Object.keys(filters.categories)
                .filter(key => key !== 'all' && filters.categories[key])
                .map(key => parseInt(key));

            if (selectedCategoryIds.length > 0) {
                params.categoryIds = selectedCategoryIds;
            }
        }

        // Add sorting
        if (filters.sortBy === 'latest') {
            params.sortBy = 'latest';
        } else if (filters.sortBy === 'oldest') {
            params.sortBy = 'oldest';
        } else if (filters.sortBy === 'price_low_to_high') {
            params.sortBy = 'price';
            params.sortOrder = 'asc';
        } else if (filters.sortBy === 'price_high_to_low') {
            params.sortBy = 'price';
            params.sortOrder = 'desc';
        } else if (filters.sortBy === 'name_a_to_z') {
            params.sortBy = 'name';
            params.sortOrder = 'asc';
        } else if (filters.sortBy === 'name_z_to_a') {
            params.sortBy = 'name';
            params.sortOrder = 'desc';
        }

        return params;
    }, [filters]);

    // Check if filters have been applied (different from defaults)
    const hasActiveFilters = useMemo(() => {
        return (
            filters.searchTerm.trim() !== '' ||
            filters.priceRange[0] !== PRICE_RANGES.DEFAULT[0] ||
            filters.priceRange[1] !== PRICE_RANGES.DEFAULT[1] ||
            !filters.categories.all ||
            filters.sortBy !== 'latest'
        );
    }, [filters]);

    // Get active filter count
    const activeFilterCount = useMemo(() => {
        let count = 0;

        if (filters.searchTerm.trim()) count++;
        if (filters.priceRange[0] !== PRICE_RANGES.DEFAULT[0] ||
            filters.priceRange[1] !== PRICE_RANGES.DEFAULT[1]) count++;
        if (!filters.categories.all) count++;
        if (filters.sortBy !== 'latest') count++;

        return count;
    }, [filters]);

    // Get selected categories count
    const selectedCategoriesCount = useMemo(() => {
        if (filters.categories.all) return 0;

        return Object.keys(filters.categories)
            .filter(key => key !== 'all' && filters.categories[key])
            .length;
    }, [filters.categories]);

    return {
        filters,
        updateFilters,
        setPriceRange,
        setCategories,
        setSortBy,
        setSearchTerm,
        clearAllFilters,
        clearPriceFilter,
        clearCategoryFilter,
        clearSearchFilter,
        buildApiParams,
        hasActiveFilters,
        activeFilterCount,
        selectedCategoriesCount
    };
};

export default useProductFilters;
