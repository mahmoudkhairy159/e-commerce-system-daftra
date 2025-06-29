import React, { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import {
    Grid,
    Typography,
    Button,
    Box,
    IconButton,
    useMediaQuery,
    useTheme,
    Drawer,
    Badge,
    Fab,
    Pagination,
    Breadcrumbs,
    Link,
    Chip,
} from "@mui/material";
import { FilterList, Close } from "@mui/icons-material";

import apiService from "../services/apiService";
import { useCart } from "../contexts/CartContext";
import { useAuth } from "../contexts/AuthContext";

// Import new reusable components and hooks
import {
    useApi,
    useDebounce,
    usePagination,
    useProductFilters,
} from "../hooks";
import {
    ProductGrid,
    ProductSearch,
    ProductSort,
} from "../components/Products";
import { LoadingSpinner, ErrorMessage, EmptyState } from "../components/UI";
import ProductsFilters from "../components/ProductsFilters";
import OrderSummarySidebar from "../components/OrderSummarySidebar";
import { API_CONFIG, MESSAGES } from "../utils";

const ProductsPageRefactored = () => {
    const navigate = useNavigate();
    const theme = useTheme();
    const isMobile = useMediaQuery(theme.breakpoints.down("lg"));
    const isTablet = useMediaQuery(theme.breakpoints.down("md"));
    const { addToCart, cart, getCartTotal } = useCart();
    const { user, isAuthenticated } = useAuth();

    // Use custom hooks for cleaner state management
    const { execute: executeApi, loading: apiLoading } = useApi();
    const { pagination, updatePagination, setPage, currentPage, totalPages } =
        usePagination({
            per_page: API_CONFIG.DEFAULT_PER_PAGE,
        });

    const {
        filters,
        updateFilters,
        buildApiParams,
        hasActiveFilters,
        activeFilterCount,
        clearAllFilters,
    } = useProductFilters();

    // Debounced search
    const { debouncedValue: debouncedSearchTerm } = useDebounce(
        filters.searchTerm,
        API_CONFIG.DEBOUNCE_DELAY
    );

    // States
    const [products, setProducts] = useState([]);
    const [categories, setCategories] = useState([]);
    const [filterDrawerOpen, setFilterDrawerOpen] = useState(false);
    const [cartDrawerOpen, setCartDrawerOpen] = useState(false);
    const [productQuantities, setProductQuantities] = useState({});
    const [cartData, setCartData] = useState({
        items: [],
        subtotal: 0,
        shipping: 15.0,
        tax: 0,
        total: 0,
    });

    // Fetch categories
    const fetchCategories = async () => {
        const result = await executeApi(() => apiService.getCategories(), {
            errorMessage: MESSAGES.ERROR.FETCH_CATEGORIES_FAILED,
            onSuccess: (response) => {
                if (response.data?.success) {
                    setCategories(response.data.data || []);
                }
            },
        });
        return result;
    };

    // Fetch products with filters
    const fetchProducts = async () => {
        const apiParams = buildApiParams({
            page: currentPage,
            per_page: pagination.per_page,
        });

        const result = await executeApi(
            () => apiService.getProducts(apiParams),
            {
                errorMessage: MESSAGES.ERROR.FETCH_PRODUCTS_FAILED,
                onSuccess: (response) => {
                    if (response.data?.success) {
                        const responseData = response.data.data;
                        setProducts(responseData.data || []);
                        updatePagination({
                            total: responseData.total,
                            current_page: responseData.current_page,
                            last_page: responseData.last_page,
                            from: responseData.from,
                            to: responseData.to,
                        });
                    }
                },
            }
        );
        return result;
    };

    // Fetch cart data
    const fetchCartData = async () => {
        const result = await executeApi(() => apiService.getCart(), {
            showErrorNotification: false,
            onSuccess: (response) => {
                if (response.data?.success) {
                    const cartResponse = response.data.data;

                    // Transform cart products
                    const transformedItems = cartResponse.cartProducts.map(
                        (cartProduct) => ({
                            id: cartProduct.id,
                            product_id: cartProduct.product_id,
                            name: cartProduct.name,
                            image:
                                cartProduct.product.image_url || "/default.jpg",
                            quantity: parseInt(cartProduct.quantity),
                            price: parseFloat(cartProduct.price),
                            chipLabel: cartProduct.product.code,
                        })
                    );

                    const subtotal = parseFloat(cartResponse.price_amount);
                    const tax = parseFloat(cartResponse.sum_tax);
                    const shipping = 15.0;
                    const total = subtotal + shipping + tax;

                    setCartData({
                        items: transformedItems,
                        subtotal,
                        shipping,
                        tax,
                        total,
                    });
                }
            },
        });
        return result;
    };

    // Effects
    useEffect(() => {
        fetchCategories();
        fetchCartData();
    }, []);

    useEffect(() => {
        fetchProducts();
    }, [
        currentPage,
        debouncedSearchTerm,
        filters.categories,
        filters.priceRange,
        filters.sortBy,
    ]);

    // Handlers
    const handleSearchChange = (searchValue) => {
        updateFilters({ searchTerm: searchValue });
        setPage(1); // Reset to first page when searching
    };

    const handleFiltersChange = (newFilters) => {
        updateFilters(newFilters);
        setPage(1); // Reset to first page when filtering
    };

    const handleSortChange = (sortValue) => {
        updateFilters({ sortBy: sortValue });
        setPage(1); // Reset to first page when sorting
    };

    const handlePageChange = (event, page) => {
        setPage(page);
    };

    const handleProductQuantityChange = (productId, change) => {
        setProductQuantities((prev) => ({
            ...prev,
            [productId]: Math.max(1, (prev[productId] || 1) + change),
        }));
    };

    const handleAddToCart = async (productId, quantity = 1) => {
        const result = await addToCart(productId, quantity);
        if (result.success) {
            await fetchCartData(); // Refresh cart data
        }
    };

    const handleCartQuantityChange = async (itemId, change) => {
        // This would be handled by OrderSummarySidebar
        // Similar to the original implementation
        await fetchCartData();
    };

    return (
        <Box sx={{ minHeight: "100vh", backgroundColor: "#f5f5f5" }}>
            {/* Main Content */}
            <Box sx={{ display: "flex", minHeight: "100vh" }}>
                {/* Filters Sidebar - Desktop */}
                {!isMobile && (
                    <Box sx={{ width: 300, flexShrink: 0 }}>
                        <ProductsFilters
                            categories={categories}
                            loading={apiLoading}
                            onFiltersChange={handleFiltersChange}
                            appliedFilters={filters}
                        />
                    </Box>
                )}

                {/* Products Section */}
                <Box sx={{ flex: 1, p: { xs: 2, sm: 3 } }}>
                    {/* Header Section */}
                    <Box sx={{ mb: 4 }}>
                        <Breadcrumbs sx={{ mb: 2 }}>
                            <Link color="inherit" href="/" underline="hover">
                                Home
                            </Link>
                            <Typography color="text.primary">
                                Products
                            </Typography>
                        </Breadcrumbs>

                        <Typography
                            variant="h4"
                            sx={{
                                fontWeight: "bold",
                                mb: 3,
                                fontSize: { xs: "1.5rem", sm: "2rem" },
                            }}
                        >
                            Products
                        </Typography>

                        {/* Search and Sort */}
                        <Box
                            sx={{
                                display: "flex",
                                gap: 2,
                                mb: 3,
                                flexDirection: { xs: "column", sm: "row" },
                                alignItems: { xs: "stretch", sm: "center" },
                            }}
                        >
                            <Box sx={{ flex: 1 }}>
                                <ProductSearch
                                    value={filters.searchTerm}
                                    onChange={handleSearchChange}
                                    placeholder="Search products..."
                                />
                            </Box>

                            <ProductSort
                                value={filters.sortBy}
                                onChange={handleSortChange}
                            />

                            {/* Mobile Filter Button */}
                            {isMobile && (
                                <Button
                                    variant="outlined"
                                    startIcon={<FilterList />}
                                    onClick={() => setFilterDrawerOpen(true)}
                                    sx={{ minWidth: "auto" }}
                                >
                                    <Badge
                                        badgeContent={activeFilterCount}
                                        color="primary"
                                    >
                                        Filters
                                    </Badge>
                                </Button>
                            )}
                        </Box>

                        {/* Active Filters */}
                        {hasActiveFilters && (
                            <Box
                                sx={{
                                    display: "flex",
                                    gap: 1,
                                    mb: 2,
                                    flexWrap: "wrap",
                                }}
                            >
                                <Button
                                    variant="text"
                                    onClick={clearAllFilters}
                                    size="small"
                                    sx={{ textDecoration: "underline" }}
                                >
                                    Clear all filters
                                </Button>
                            </Box>
                        )}
                    </Box>

                    {/* Products Grid */}
                    <ProductGrid
                        products={products}
                        loading={apiLoading}
                        productQuantities={productQuantities}
                        onQuantityChange={handleProductQuantityChange}
                        onAddToCart={handleAddToCart}
                        onRetry={fetchProducts}
                    />

                    {/* Pagination */}
                    {totalPages > 1 && (
                        <Box
                            sx={{
                                display: "flex",
                                justifyContent: "center",
                                mt: 4,
                                mb: 2,
                            }}
                        >
                            <Pagination
                                count={totalPages}
                                page={currentPage}
                                onChange={handlePageChange}
                                color="primary"
                                size={isMobile ? "small" : "medium"}
                                showFirstButton
                                showLastButton
                            />
                        </Box>
                    )}
                </Box>

                {/* Cart Sidebar - Desktop */}
                {!isMobile && isAuthenticated && (
                    <Box sx={{ width: 350, flexShrink: 0 }}>
                        <OrderSummarySidebar
                            cartData={cartData}
                            onQuantityChange={handleCartQuantityChange}
                            loading={apiLoading}
                        />
                    </Box>
                )}
            </Box>

            {/* Mobile Filter Drawer */}
            <Drawer
                anchor="left"
                open={filterDrawerOpen}
                onClose={() => setFilterDrawerOpen(false)}
                sx={{
                    "& .MuiDrawer-paper": {
                        width: "85vw",
                        maxWidth: 400,
                    },
                }}
            >
                <ProductsFilters
                    categories={categories}
                    loading={apiLoading}
                    onFiltersChange={handleFiltersChange}
                    appliedFilters={filters}
                    mobile
                    onClose={() => setFilterDrawerOpen(false)}
                />
            </Drawer>

            {/* Mobile Cart FAB */}
            {isMobile && isAuthenticated && (
                <Fab
                    color="primary"
                    sx={{
                        position: "fixed",
                        bottom: 16,
                        right: 16,
                        zIndex: 1000,
                    }}
                    onClick={() => setCartDrawerOpen(true)}
                >
                    <Badge badgeContent={cartData.items.length} color="error">
                        <FilterList />
                    </Badge>
                </Fab>
            )}

            {/* Mobile Cart Drawer */}
            <Drawer
                anchor="right"
                open={cartDrawerOpen}
                onClose={() => setCartDrawerOpen(false)}
                sx={{
                    "& .MuiDrawer-paper": {
                        width: "90vw",
                        maxWidth: 400,
                    },
                }}
            >
                <Box
                    sx={{
                        p: 2,
                        display: "flex",
                        justifyContent: "space-between",
                        alignItems: "center",
                    }}
                >
                    <Typography variant="h6">Cart</Typography>
                    <IconButton onClick={() => setCartDrawerOpen(false)}>
                        <Close />
                    </IconButton>
                </Box>
                <OrderSummarySidebar
                    cartData={cartData}
                    onQuantityChange={handleCartQuantityChange}
                    loading={apiLoading}
                />
            </Drawer>
        </Box>
    );
};

export default ProductsPageRefactored;
