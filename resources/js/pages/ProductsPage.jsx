import React, { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import {
    Grid,
    Typography,
    Button,
    Box,
    TextField,
    IconButton,
    useMediaQuery,
    useTheme,
    CircularProgress,
    Alert,
    Chip,
    Breadcrumbs,
    Link,
    InputAdornment,
    Pagination,
    Select,
    MenuItem,
    FormControl,
    InputLabel,
    Drawer,
    Badge,
    Fab,
} from "@mui/material";
import { FilterList, Search, ShoppingCart, Close } from "@mui/icons-material";

import { useSnackbar } from "notistack";
import apiService from "../services/apiService";
import { useCart } from "../contexts/CartContext";
import { useAuth } from "../contexts/AuthContext";
import ProductsFilters from "../components/ProductsFilters";
import ProductCard from "../components/ProductCard";
import OrderSummarySidebar from "../components/OrderSummarySidebar";

const ProductsPage = () => {
    const navigate = useNavigate();
    const theme = useTheme();
    const isMobile = useMediaQuery(theme.breakpoints.down("lg"));
    const isTablet = useMediaQuery(theme.breakpoints.down("md"));
    const isSmallMobile = useMediaQuery(theme.breakpoints.down("sm"));
    const { addToCart, cart, getCartTotal } = useCart();
    const { user, isAuthenticated } = useAuth();
    const { enqueueSnackbar } = useSnackbar();

    // States
    const [products, setProducts] = useState([]);
    const [categories, setCategories] = useState([]);
    const [loading, setLoading] = useState(false);
    const [categoriesLoading, setCategoriesLoading] = useState(false);
    const [error, setError] = useState("");
    const [searchTerm, setSearchTerm] = useState(""); // API search term (triggers API calls)
    const [searchInput, setSearchInput] = useState(""); // UI input value (no API calls)
    const [filterDrawerOpen, setFilterDrawerOpen] = useState(false);
    const [cartDrawerOpen, setCartDrawerOpen] = useState(false);
    const [productQuantities, setProductQuantities] = useState({});
    const [currentPage, setCurrentPage] = useState(1);
    const [pagination, setPagination] = useState({
        total: 0,
        per_page: 6,
        current_page: 1,
        last_page: 1,
        from: 1,
        to: 6,
    });
    const [sortBy, setSortBy] = useState("latest");
    const [isDebouncing, setIsDebouncing] = useState(false);
    const [searchApplied, setSearchApplied] = useState(false);
    const [appliedFilters, setAppliedFilters] = useState({
        priceRange: [0, 5000],
        categories: { all: true },
    });
    const [filtersResetTrigger, setFiltersResetTrigger] = useState(0);
    // Cart-related states
    const [cartData, setCartData] = useState({
        items: [],
        subtotal: 0,
        shipping: 15.0,
        tax: 0,
        total: 0,
    });
    const [cartLoading, setCartLoading] = useState(false);

    // Fetch cart data from API
    const fetchCartData = async () => {
        try {
            setCartLoading(true);
            const response = await apiService.getCart();

            if (response.data && response.data.success) {
                const cartResponse = response.data.data;

                // Transform cart products to match OrderSummarySidebar format
                const transformedItems = cartResponse.cartProducts.map(
                    (cartProduct) => ({
                        id: cartProduct.id,
                        product_id: cartProduct.product_id,
                        name: cartProduct.name,
                        image: cartProduct.product.image_url || "/default.jpg",
                        quantity: parseInt(cartProduct.quantity),
                        price: parseFloat(cartProduct.price),
                        chipLabel: cartProduct.product.code,
                    })
                );

                // Calculate totals
                const subtotal = parseFloat(cartResponse.price_amount);
                const tax = parseFloat(cartResponse.sum_tax);
                const shipping = 15.0; // Default shipping - can be dynamic based on your logic
                const total = subtotal + shipping + tax;

                setCartData({
                    items: transformedItems,
                    subtotal: subtotal,
                    shipping: shipping,
                    tax: tax,
                    total: total,
                });
            }
        } catch (error) {
            console.error("Failed to fetch cart data:", error);
            // Don't show error to user, just keep default empty cart
        } finally {
            setCartLoading(false);
        }
    };

    // Handle cart quantity changes from OrderSummarySidebar
    const handleCartQuantityChange = async (itemId, change) => {
        try {
            setCartLoading(true);

            // Find the cart item to get current quantity
            const cartItem = cartData.items.find((item) => item.id === itemId);
            if (!cartItem) return;

            const newQuantity = cartItem.quantity + change;

            if (newQuantity <= 0) {
                // Remove item from cart if quantity becomes 0 or less
                await apiService.removeFromCart(itemId);
                enqueueSnackbar(`${cartItem.name} removed from cart`, {
                    variant: "success",
                });
            } else {
                // Update cart item quantity
                await apiService.updateCartItem(itemId, newQuantity);
                enqueueSnackbar(`Cart updated`, {
                    variant: "success",
                });
            }

            // Refresh cart data after successful API call
            await fetchCartData();
        } catch (error) {
            console.error("Failed to update cart:", error);
            enqueueSnackbar("Failed to update cart. Please try again.", {
                variant: "error",
            });
        } finally {
            setCartLoading(false);
        }
    };

    // Build API parameters for product filtering
    const buildApiParams = (
        overrideFilters = null,
        overrideSearch = null,
        overrideSort = null,
        overridePage = null
    ) => {
        // Use provided filters or fall back to state
        const filtersToUse = overrideFilters || appliedFilters;
        const searchToUse =
            overrideSearch !== null ? overrideSearch : searchTerm;
        const sortToUse = overrideSort !== null ? overrideSort : sortBy;
        const pageToUse = overridePage !== null ? overridePage : currentPage;

        const params = {
            page: pageToUse,
            per_page: pagination.per_page,
        };

        // Add search term
        if (searchToUse && searchToUse.trim()) {
            params.search = searchToUse.trim();
        }

        // Add price range filters
        if (filtersToUse.priceRange[0] > 0) {
            params.fromPrice = filtersToUse.priceRange[0];
        }
        if (filtersToUse.priceRange[1] < 5000) {
            params.toPrice = filtersToUse.priceRange[1];
        }

        // Add category filters
        if (!filtersToUse.categories.all) {
            const selectedCategoryIds = Object.keys(filtersToUse.categories)
                .filter((key) => key !== "all" && filtersToUse.categories[key])
                .map((key) => parseInt(key));

            if (selectedCategoryIds.length > 0) {
                params.categoryIds = selectedCategoryIds; // Send as array
            }
        }

        // Add sorting
        if (sortToUse === "latest") {
            params.latest = true;
        } else if (sortToUse === "position") {
            params.position = true;
        }

        // Only show active products
        params.status = 1;

        return params;
    };

    // Fetch cart data when component mounts
    useEffect(() => {
        fetchCartData();
    }, []);

    // Update product quantities whenever cart data or products change
    useEffect(() => {
        if (cartData.items.length >= 0 && products.length > 0) {
            const updatedQuantities = {};
            products.forEach((product) => {
                const cartItem = cartData.items.find(
                    (item) => item.product_id === product.id
                );
                updatedQuantities[product.id] = cartItem
                    ? cartItem.quantity
                    : 0;
            });
            setProductQuantities(updatedQuantities);
        }
    }, [cartData.items, products]);

    // Fetch categories from API with rate limiting handling
    useEffect(() => {
        const fetchCategories = async (retryCount = 0) => {
            try {
                setCategoriesLoading(true);
                const response = await apiService.getCategories();
                if (response.data && response.data.success) {
                    const fetchedCategories = response.data.data || [];
                    setCategories(fetchedCategories);
                } else {
                    setError("Failed to fetch categories");
                }
            } catch (error) {
                console.error("Error fetching categories:", error);

                // Handle rate limiting with retry
                if (error.response?.status === 429 && retryCount < 2) {
                    const retryDelay = (retryCount + 1) * 2000; // 2s, 4s delay
                    enqueueSnackbar(
                        `Rate limit reached. Retrying categories in ${
                            retryDelay / 1000
                        } seconds...`,
                        { variant: "info" }
                    );

                    setTimeout(() => {
                        fetchCategories(retryCount + 1);
                    }, retryDelay);
                    return;
                } else if (error.response?.status === 429) {
                    setError("Too many requests. Please refresh the page.");
                    enqueueSnackbar(
                        "Too many requests. Please refresh the page.",
                        { variant: "warning" }
                    );
                } else {
                    setError("Error loading categories");
                    enqueueSnackbar("Failed to load categories", {
                        variant: "error",
                    });
                }
            } finally {
                setCategoriesLoading(false);
            }
        };

        fetchCategories();
    }, [enqueueSnackbar]);

    // Fetch products from API with request cancellation and retry logic
    const fetchProducts = async (retryCount = 0) => {
        return await fetchProductsWithFilters(
            null,
            null,
            null,
            null,
            retryCount
        );
    };

    // Fetch products with specific filters (avoiding state timing issues)
    const fetchProductsWithFilters = async (
        overrideFilters = null,
        overrideSearch = null,
        overrideSort = null,
        overridePage = null,
        retryCount = 0
    ) => {
        try {
            setLoading(true);
            setError("");

            // Cancel any pending request
            if (fetchProducts.controller) {
                fetchProducts.controller.abort();
            }

            // Create new AbortController for this request
            fetchProducts.controller = new AbortController();

            const params = buildApiParams(
                overrideFilters,
                overrideSearch,
                overrideSort,
                overridePage
            );
            const response = await apiService.getProducts(params);

            if (response.data && response.data.success) {
                const productsData = response.data.data.data || [];
                const paginationData = response.data.data.pagination || {};

                setProducts(productsData);
                setPagination(paginationData);
            } else {
                setError("Failed to fetch products");
                enqueueSnackbar("Failed to load products", {
                    variant: "error",
                });
            }
        } catch (error) {
            // Don't show error if request was cancelled
            if (error.name === "AbortError" || error.code === "ERR_CANCELED") {
                return;
            }

            console.error("Error fetching products:", error);

            // Handle rate limiting with retry
            if (error.response?.status === 429 && retryCount < 2) {
                const retryDelay = (retryCount + 1) * 2000; // 2s, 4s delay
                enqueueSnackbar(
                    `Rate limit reached. Retrying in ${
                        retryDelay / 1000
                    } seconds...`,
                    { variant: "info" }
                );

                setTimeout(() => {
                    fetchProductsWithFilters(
                        overrideFilters,
                        overrideSearch,
                        overrideSort,
                        overridePage,
                        retryCount + 1
                    );
                }, retryDelay);
                return;
            } else if (error.response?.status === 429) {
                setError(
                    "Too many requests. Please wait a moment and try again."
                );
                enqueueSnackbar("Too many requests. Please try again later.", {
                    variant: "warning",
                });
            } else {
                setError("Error loading products");
                enqueueSnackbar("Failed to load products", {
                    variant: "error",
                });
            }
        } finally {
            setLoading(false);
        }
    };

    // Effect to handle pagination and sorting changes only
    useEffect(() => {
        // Don't fetch if categories haven't loaded yet
        if (categories.length === 0) {
            return;
        }

        // Show debouncing indicator for navigation changes
        setIsDebouncing(true);

        // Debounce navigation API calls to prevent rate limiting
        const timeoutId = setTimeout(() => {
            setIsDebouncing(false);
            fetchProducts();
        }, 300); // 300ms delay

        return () => {
            clearTimeout(timeoutId);
            setIsDebouncing(false);
            // Cancel any pending request when dependencies change
            if (fetchProducts.controller) {
                fetchProducts.controller.abort();
            }
        };
    }, [categories.length, currentPage, sortBy]);

    // Separate effect for search term changes (triggered only on blur/Enter/Search button)
    useEffect(() => {
        // Don't fetch if categories haven't loaded yet
        if (categories.length === 0) {
            return;
        }

        // Fetch products when search term changes
        fetchProducts();
    }, [searchTerm]);

    // Handle filter changes from ProductsFilters component
    const handleFiltersChange = (filters) => {
        setAppliedFilters(filters);
        setCurrentPage(1);

        // Trigger product fetch with new filters immediately using the provided filters and reset to page 1
        fetchProductsWithFilters(filters, null, null, 1);
    };

    // Handle product card quantity changes (adds/updates cart via API)
    const handleProductCardQuantityChange = async (productId, change) => {
        try {
            setCartLoading(true);

            const currentQuantity = productQuantities[productId] || 0;
            const newQuantity = currentQuantity + change;

            if (newQuantity <= 0) {
                // Find the cart item for this product and remove it
                const cartItem = cartData.items.find(
                    (item) => item.product_id === productId
                );
                if (cartItem) {
                    await apiService.removeFromCart(cartItem.id);
                    enqueueSnackbar(`Product removed from cart`, {
                        variant: "success",
                    });
                }
            } else if (currentQuantity === 0) {
                // Add new product to cart
                await apiService.addToCart(productId, newQuantity);
                enqueueSnackbar(`Product added to cart`, {
                    variant: "success",
                });
            } else {
                // Update existing cart item
                const cartItem = cartData.items.find(
                    (item) => item.product_id === productId
                );
                if (cartItem) {
                    await apiService.updateCartItem(cartItem.id, newQuantity);
                    enqueueSnackbar(`Cart updated`, {
                        variant: "success",
                    });
                }
            }

            // Refresh cart data and update product quantities
            await fetchCartData();
        } catch (error) {
            console.error("Failed to update cart:", error);
            enqueueSnackbar("Failed to update cart. Please try again.", {
                variant: "error",
            });
        } finally {
            setCartLoading(false);
        }
    };

    const handleQuantityChange = (productId, change) => {
        setProductQuantities((prev) => ({
            ...prev,
            [productId]: Math.max(0, (prev[productId] || 0) + change),
        }));
    };

    const handleSearchInputChange = (searchValue) => {
        setSearchInput(searchValue);
    };

    const handleSearchSubmit = () => {
        setSearchTerm(searchInput.trim());
        setCurrentPage(1); // Reset to first page when searching

        // Show success animation
        setSearchApplied(true);
        setTimeout(() => setSearchApplied(false), 1000);
    };

    const handleSearchKeyPress = (e) => {
        if (e.key === "Enter") {
            handleSearchSubmit();
        }
    };

    const handleSearchBlur = () => {
        handleSearchSubmit();
    };

    const handleSortChange = (sortValue) => {
        setSortBy(sortValue);
        setCurrentPage(1); // Reset to first page when sorting
    };

    const handleClearAllFilters = () => {
        // Clear search and sort as well
        setSearchTerm("");
        setSearchInput("");
        setSortBy("latest");
        setCurrentPage(1);
        setSearchApplied(false);

        // Create cleared filters with all categories set to false
        const clearedFilters = {
            priceRange: [0, 5000],
            categories: { all: true },
        };

        // Add all category IDs as false
        categories.forEach((category) => {
            clearedFilters.categories[category.id] = false;
        });

        // Reset filters to initial state
        setAppliedFilters(clearedFilters);

        // Trigger filters reset in ProductsFilters component
        setFiltersResetTrigger((prev) => prev + 1);

        // Trigger product fetch with cleared filters immediately using the cleared values
        setTimeout(() => {
            fetchProductsWithFilters(clearedFilters, "", "latest", 1);
        }, 50);
    };

    const handlePageChange = (event, page) => {
        setCurrentPage(page);
        window.scrollTo({ top: 0, behavior: "smooth" });
    };

    if (categoriesLoading && categories.length === 0) {
        return (
            <Box
                display="flex"
                justifyContent="center"
                alignItems="center"
                minHeight="60vh"
            >
                <CircularProgress />
                <Typography sx={{ ml: 2 }}>Loading categories...</Typography>
            </Box>
        );
    }

    if (error) {
        return (
            <Alert severity="error" sx={{ mt: 2 }}>
                {error}
            </Alert>
        );
    }

    return (
        <Box>
            {/* Mobile Cart FAB */}
            {isMobile && cartData.items.length > 0 && (
                <Fab
                    color="primary"
                    sx={{
                        position: "fixed",
                        bottom: 80,
                        right: 16,
                        zIndex: 1000,
                        backgroundColor: "#000",
                        "&:hover": {
                            backgroundColor: "#333",
                        },
                    }}
                    onClick={() => setCartDrawerOpen(true)}
                >
                    <Badge badgeContent={cartData.items.length} color="error">
                        <ShoppingCart />
                    </Badge>
                </Fab>
            )}

            {/* Mobile Cart Drawer */}
            <Drawer
                anchor="right"
                open={cartDrawerOpen}
                onClose={() => setCartDrawerOpen(false)}
                sx={{
                    display: { xs: "block", lg: "none" },
                    "& .MuiDrawer-paper": {
                        width: "100%",
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
                    <Typography variant="h6">Shopping Cart</Typography>
                    <IconButton onClick={() => setCartDrawerOpen(false)}>
                        <Close />
                    </IconButton>
                </Box>
                <OrderSummarySidebar
                    cartItems={cartData.items}
                    subtotal={cartData.subtotal}
                    shipping={cartData.shipping}
                    tax={cartData.tax}
                    total={cartData.total}
                    onQuantityChange={handleCartQuantityChange}
                    loading={cartLoading}
                    isMobile={true}
                />
            </Drawer>

            {/* Main Layout */}
            <Box
                sx={{
                    display: "flex",
                    minHeight: "90vh",
                    flexDirection: { xs: "column", lg: "row" },
                }}
            >
                {/* Filters Component */}
                <ProductsFilters
                    categories={categories}
                    categoriesLoading={categoriesLoading}
                    onFiltersChange={handleFiltersChange}
                    isMobile={isMobile}
                    filterDrawerOpen={filterDrawerOpen}
                    setFilterDrawerOpen={setFilterDrawerOpen}
                    resetTrigger={filtersResetTrigger}
                    initialFilters={appliedFilters}
                />

                {/* Main Content */}
                <Box
                    sx={{
                        flex: 1,
                        bgcolor: "white",
                        p: { xs: 2, sm: 3 },
                        order: { xs: 2, lg: 1 },
                    }}
                >
                    {/* Breadcrumbs */}
                    <Breadcrumbs
                        sx={{ mb: 2, display: { xs: "none", sm: "flex" } }}
                    >
                        <Link
                            underline="hover"
                            color="inherit"
                            onClick={() => navigate("/")}
                            sx={{
                                fontSize: "14px",
                                color: "#666",
                                cursor: "pointer",
                            }}
                        >
                            Home
                        </Link>
                        <Typography sx={{ fontSize: "14px", color: "#000" }}>
                            Casual
                        </Typography>
                    </Breadcrumbs>

                    {/* Mobile Header */}
                    <Box
                        sx={{
                            display: { xs: "flex", sm: "none" },
                            justifyContent: "space-between",
                            alignItems: "center",
                            mb: 2,
                        }}
                    >
                        <Typography
                            variant="h5"
                            sx={{ fontWeight: 600, fontSize: "24px" }}
                        >
                            Products
                        </Typography>
                        <IconButton
                            onClick={() => setFilterDrawerOpen(true)}
                            sx={{
                                border: "1px solid #e0e0e0",
                                borderRadius: 2,
                            }}
                        >
                            <FilterList />
                        </IconButton>
                    </Box>

                    {/* Search and Sort Section */}
                    <Box
                        sx={{
                            display: "flex",
                            flexDirection: { xs: "column", sm: "row" },
                            alignItems: { xs: "stretch", sm: "center" },
                            gap: 2,
                            mb: 3,
                            justifyContent: "space-between",
                        }}
                    >
                        {/* Search Section */}
                        <Box
                            sx={{
                                display: "flex",
                                alignItems: "center",
                                gap: 1,
                                flex: { xs: "none", sm: 1 },
                                maxWidth: { xs: "100%", sm: 500 },
                            }}
                        >
                            {/* Desktop Filter Toggle */}
                            <IconButton
                                onClick={() => setFilterDrawerOpen(true)}
                                sx={{
                                    display: {
                                        xs: "none",
                                        sm: "block",
                                        lg: "none",
                                    },
                                    mr: 1,
                                }}
                            >
                                <FilterList />
                            </IconButton>

                            {/* Search Input */}
                            <Box
                                sx={{
                                    display: "flex",
                                    alignItems: "flex-start",
                                    gap: 0,
                                    position: "relative",
                                    width: "100%",
                                }}
                            >
                                <TextField
                                    placeholder="Search products..."
                                    helperText={
                                        searchInput !== searchTerm &&
                                        searchInput.trim() !== ""
                                            ? "Press Enter or tap Search to apply"
                                            : ""
                                    }
                                    value={searchInput}
                                    onChange={(e) =>
                                        handleSearchInputChange(e.target.value)
                                    }
                                    onKeyPress={handleSearchKeyPress}
                                    onBlur={handleSearchBlur}
                                    size="small"
                                    fullWidth
                                    InputProps={{
                                        startAdornment: (
                                            <InputAdornment position="start">
                                                <Search
                                                    sx={{
                                                        color:
                                                            searchInput !==
                                                                searchTerm &&
                                                            searchInput.trim() !==
                                                                ""
                                                                ? "#2196f3"
                                                                : "#999",
                                                        transition:
                                                            "color 0.2s ease",
                                                    }}
                                                />
                                            </InputAdornment>
                                        ),
                                        endAdornment:
                                            loading && searchTerm ? (
                                                <InputAdornment position="end">
                                                    <CircularProgress
                                                        size={16}
                                                        sx={{
                                                            color: "#2196f3",
                                                        }}
                                                    />
                                                </InputAdornment>
                                            ) : null,
                                        sx: {
                                            borderTopRightRadius: 0,
                                            borderBottomRightRadius: 0,
                                            "&.Mui-focused .MuiOutlinedInput-notchedOutline":
                                                {
                                                    borderRightColor:
                                                        "transparent",
                                                },
                                        },
                                    }}
                                    sx={{
                                        flex: 1,
                                        "& .MuiOutlinedInput-root": {
                                            borderTopRightRadius: 0,
                                            borderBottomRightRadius: 0,
                                            backgroundColor: "white",
                                            transition:
                                                "border-color 0.2s ease",
                                            transform: "none !important",
                                            "& .MuiOutlinedInput-notchedOutline":
                                                {
                                                    borderColor:
                                                        searchInput !==
                                                            searchTerm &&
                                                        searchInput.trim() !==
                                                            ""
                                                            ? "#2196f3"
                                                            : "#e0e0e0",
                                                    borderRightWidth: 0,
                                                    transition:
                                                        "border-color 0.2s ease",
                                                },
                                            "& .MuiInputBase-input": {
                                                transform: "none !important",
                                                transition: "none !important",
                                            },
                                            "& .MuiInputLabel-root": {
                                                transform: "none !important",
                                                transition: "none !important",
                                                position: "static !important",
                                            },
                                        },
                                        "& .MuiFormHelperText-root": {
                                            color:
                                                searchInput !== searchTerm &&
                                                searchInput.trim() !== ""
                                                    ? "#2196f3"
                                                    : "#666",
                                            fontSize: "12px",
                                            marginTop: "4px",
                                            marginLeft: "8px",
                                            fontWeight:
                                                searchInput !== searchTerm &&
                                                searchInput.trim() !== ""
                                                    ? 500
                                                    : 400,
                                        },
                                    }}
                                />
                                <Button
                                    variant="contained"
                                    size="small"
                                    onClick={handleSearchSubmit}
                                    disabled={loading}
                                    startIcon={
                                        loading ? null : (
                                            <Search
                                                sx={{
                                                    fontSize: "18px !important",
                                                    display: {
                                                        xs: "none",
                                                        sm: "block",
                                                    },
                                                }}
                                            />
                                        )
                                    }
                                    sx={{
                                        minWidth: { xs: 80, sm: "auto" },
                                        height: "40px",
                                        px: {
                                            xs: 2,
                                            sm:
                                                searchInput !== searchTerm &&
                                                searchInput.trim() !== ""
                                                    ? 3
                                                    : 2.5,
                                        },
                                        borderTopLeftRadius: 0,
                                        borderBottomLeftRadius: 0,
                                        backgroundColor: searchApplied
                                            ? "#4caf50"
                                            : searchInput !== searchTerm &&
                                              searchInput.trim() !== ""
                                            ? "#2196f3"
                                            : "#666",
                                        borderColor: searchApplied
                                            ? "#4caf50"
                                            : searchInput !== searchTerm &&
                                              searchInput.trim() !== ""
                                            ? "#2196f3"
                                            : "#666",
                                        color: "white",
                                        fontWeight: 600,
                                        fontSize: { xs: "13px", sm: "14px" },
                                        textTransform: "none",
                                        boxShadow: searchApplied
                                            ? "0 2px 8px rgba(76, 175, 80, 0.3)"
                                            : searchInput !== searchTerm &&
                                              searchInput.trim() !== ""
                                            ? "0 2px 8px rgba(33, 150, 243, 0.3)"
                                            : "0 2px 4px rgba(0,0,0,0.1)",
                                        transition: "all 0.2s ease",
                                        position: "relative",
                                        overflow: "hidden",
                                        "&::before": {
                                            content: '""',
                                            position: "absolute",
                                            top: 0,
                                            left: "-100%",
                                            width: "100%",
                                            height: "100%",
                                            background:
                                                "linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent)",
                                            transition: "left 0.5s",
                                        },
                                        "&:hover": {
                                            backgroundColor: searchApplied
                                                ? "#45a049"
                                                : searchInput !== searchTerm &&
                                                  searchInput.trim() !== ""
                                                ? "#1976d2"
                                                : "#555",
                                            boxShadow: searchApplied
                                                ? "0 4px 12px rgba(76, 175, 80, 0.4)"
                                                : searchInput !== searchTerm &&
                                                  searchInput.trim() !== ""
                                                ? "0 4px 12px rgba(33, 150, 243, 0.4)"
                                                : "0 4px 8px rgba(0,0,0,0.2)",
                                            "&::before": {
                                                left: "100%",
                                            },
                                        },
                                        "&.Mui-disabled": {
                                            backgroundColor: "#ccc",
                                            color: "#999",
                                            boxShadow: "none",
                                        },
                                    }}
                                >
                                    {loading ? (
                                        <CircularProgress
                                            size={16}
                                            sx={{ color: "white" }}
                                        />
                                    ) : searchApplied ? (
                                        "Applied!"
                                    ) : searchInput !== searchTerm &&
                                      searchInput.trim() !== "" ? (
                                        "Search"
                                    ) : (
                                        "Go"
                                    )}
                                </Button>
                            </Box>
                        </Box>

                        {/* Sort dropdown */}
                        <FormControl
                            size="small"
                            sx={{ minWidth: { xs: "100%", sm: 150 } }}
                        >
                            <InputLabel>Sort by</InputLabel>
                            <Select
                                value={sortBy}
                                label="Sort by"
                                onChange={(e) =>
                                    handleSortChange(e.target.value)
                                }
                            >
                                <MenuItem value="latest">Latest</MenuItem>
                                <MenuItem value="position">Position</MenuItem>
                            </Select>
                        </FormControl>
                    </Box>

                    {/* Desktop Page Title and Results Count */}
                    <Box sx={{ display: { xs: "none", sm: "block" } }}>
                        <Typography
                            variant="h4"
                            sx={{
                                fontWeight: 600,
                                mb: 1,
                                fontSize: { sm: "28px", md: "32px" },
                            }}
                        >
                            Products
                        </Typography>
                        <Typography
                            variant="body2"
                            sx={{ mb: 4, color: "#666", fontSize: "14px" }}
                        >
                            Showing {pagination.from}-{pagination.to} of{" "}
                            {pagination.total} Products
                        </Typography>
                    </Box>

                    {/* Mobile Results Count */}
                    <Typography
                        variant="body2"
                        sx={{
                            display: { xs: "block", sm: "none" },
                            mb: 3,
                            color: "#666",
                            fontSize: "13px",
                            textAlign: "center",
                        }}
                    >
                        {pagination.from}-{pagination.to} of {pagination.total}{" "}
                        Products
                    </Typography>

                    {/* Loading overlay */}
                    {(loading || isDebouncing) && (
                        <Box
                            sx={{
                                display: "flex",
                                justifyContent: "center",
                                alignItems: "center",
                                py: 4,
                            }}
                        >
                            <CircularProgress size={isDebouncing ? 20 : 40} />
                            <Typography
                                sx={{
                                    ml: 2,
                                    fontSize: { xs: "14px", sm: "16px" },
                                }}
                            >
                                {isDebouncing
                                    ? "Updating results..."
                                    : "Loading products..."}
                            </Typography>
                        </Box>
                    )}

                    {/* Products Grid */}
                    {products.length === 0 && !loading && !isDebouncing ? (
                        <Box
                            sx={{
                                display: "flex",
                                flexDirection: "column",
                                alignItems: "center",
                                justifyContent: "center",
                                py: { xs: 6, sm: 8 },
                                px: 2,
                                textAlign: "center",
                            }}
                        >
                            <Typography
                                variant="h5"
                                sx={{
                                    mb: 2,
                                    color: "#666",
                                    fontSize: { xs: "20px", sm: "24px" },
                                }}
                            >
                                No products found
                            </Typography>
                            <Typography
                                variant="body1"
                                sx={{
                                    mb: 3,
                                    color: "#999",
                                    textAlign: "center",
                                    fontSize: { xs: "14px", sm: "16px" },
                                }}
                            >
                                Try adjusting your search criteria or filters to
                                find what you're looking for.
                            </Typography>
                            <Button
                                variant="outlined"
                                onClick={handleClearAllFilters}
                                sx={{
                                    borderRadius: 2,
                                    textTransform: "none",
                                    px: 3,
                                }}
                            >
                                Clear Filters
                            </Button>
                        </Box>
                    ) : (
                        <Grid
                            container
                            spacing={{ xs: 2, sm: 3 }}
                            sx={{ mb: 4 }}
                        >
                            {products.map((product, index) => (
                                <ProductCard
                                    key={product.id}
                                    product={product}
                                    quantity={index}
                                    currentPage={currentPage}
                                    perPage={pagination.per_page}
                                    productQuantities={productQuantities}
                                    onQuantityChange={
                                        handleProductCardQuantityChange
                                    }
                                    loading={cartLoading}
                                />
                            ))}
                        </Grid>
                    )}

                    {/* Pagination */}
                    {pagination.last_page > 1 && (
                        <Box
                            sx={{
                                display: "flex",
                                justifyContent: "center",
                                mt: 4,
                                px: 2,
                            }}
                        >
                            <Pagination
                                count={pagination.last_page}
                                page={pagination.current_page}
                                onChange={handlePageChange}
                                disabled={loading || isDebouncing}
                                size={isSmallMobile ? "small" : "medium"}
                                siblingCount={isSmallMobile ? 0 : 1}
                                sx={{
                                    "& .MuiPaginationItem-root": {
                                        color:
                                            loading || isDebouncing
                                                ? "#ccc"
                                                : "#666",
                                        fontSize: { xs: "14px", sm: "16px" },
                                        minWidth: { xs: "32px", sm: "40px" },
                                        height: { xs: "32px", sm: "40px" },
                                        "&.Mui-selected": {
                                            backgroundColor:
                                                loading || isDebouncing
                                                    ? "#ccc"
                                                    : "#000",
                                            color: "white",
                                        },
                                    },
                                }}
                            />
                        </Box>
                    )}
                </Box>

                {/* Desktop Order Summary Sidebar */}
                <Box sx={{ display: { xs: "none", lg: "block" }, order: 3 }}>
                    <OrderSummarySidebar
                        cartItems={cartData.items}
                        subtotal={cartData.subtotal}
                        shipping={cartData.shipping}
                        tax={cartData.tax}
                        total={cartData.total}
                        onQuantityChange={handleCartQuantityChange}
                        loading={cartLoading}
                    />
                </Box>
            </Box>
        </Box>
    );
};

export default ProductsPage;
