import React, { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import {
    Grid,
    Card,
    CardContent,
    CardMedia,
    Typography,
    Button,
    Box,
    Paper,
    Divider,
    IconButton,
    TextField,
    Alert,
    CircularProgress,
    Chip,
    useTheme,
    useMediaQuery,
    Fab,
} from "@mui/material";
import {
    Delete,
    Add,
    Remove,
    ShoppingCartOutlined,
    ArrowBack,
    Close,
    Receipt,
} from "@mui/icons-material";
import { useSnackbar } from "notistack";
import apiService from "../services/apiService";

const CartPage = () => {
    const navigate = useNavigate();
    const { enqueueSnackbar } = useSnackbar();
    const theme = useTheme();
    const isMobile = useMediaQuery(theme.breakpoints.down("md"));
    const isSmallMobile = useMediaQuery(theme.breakpoints.down("sm"));

    // Local state for cart data
    const [cartData, setCartData] = useState(null);
    const [loading, setLoading] = useState(true);
    const [processingOrder, setProcessingOrder] = useState(false);
    const [updatingItems, setUpdatingItems] = useState(new Set());
    const [mobileOrderSummaryOpen, setMobileOrderSummaryOpen] = useState(false);

    // Calculate totals from API response
    const subtotal = cartData ? parseFloat(cartData.sum_subtotal || 0) : 0;
    const shipping = 0.0; // Free shipping
    const tax = cartData ? parseFloat(cartData.sum_tax || 0) : 0;
    const total = subtotal + shipping + tax;
    const cartItems = cartData?.cartProducts || [];
    const itemCount = cartData ? parseInt(cartData.sum_quantity || 0) : 0;

    // Fetch cart data on component mount
    useEffect(() => {
        fetchCartData();
        // Clear any previous session storage on page entry to always start fresh
        sessionStorage.removeItem("orderSummaryClosed");
    }, []);

    // Auto-open order summary on mobile when there are items (always open by default)
    useEffect(() => {
        if (isMobile && cartItems.length > 0) {
            // Always open by default when entering the page
            setMobileOrderSummaryOpen(true);
        }
    }, [isMobile, cartItems.length]);

    const fetchCartData = async () => {
        try {
            setLoading(true);
            const response = await apiService.getCart();

            if (response.data && response.data.success) {
                setCartData(response.data.data);
            } else {
                setCartData(null);
            }
        } catch (error) {
            setCartData(null);
            enqueueSnackbar("Failed to load cart", { variant: "error" });
        } finally {
            setLoading(false);
        }
    };

    const handleQuantityChange = async (cartItemId, newQuantity) => {
        if (newQuantity < 1) return;

        setUpdatingItems((prev) => new Set(prev).add(cartItemId));

        try {
            // Call API to update cart item quantity
            await apiService.updateCartItem(cartItemId, newQuantity);

            // Refresh cart data after successful update
            await fetchCartData();

            enqueueSnackbar("Cart updated successfully", {
                variant: "success",
            });
        } catch (error) {
            console.error("Failed to update cart item:", error);
            enqueueSnackbar("Failed to update cart item", { variant: "error" });
        } finally {
            setUpdatingItems((prev) => {
                const newSet = new Set(prev);
                newSet.delete(cartItemId);
                return newSet;
            });
        }
    };

    const handleRemoveItem = async (cartItemId, productName) => {
        setUpdatingItems((prev) => new Set(prev).add(cartItemId));

        try {
            // Call API to remove cart item
            await apiService.removeFromCart(cartItemId);

            // Refresh cart data after successful removal
            await fetchCartData();

            enqueueSnackbar(`${productName} removed from cart`, {
                variant: "success",
            });
        } catch (error) {
            console.error("Failed to remove cart item:", error);
            enqueueSnackbar("Failed to remove item from cart", {
                variant: "error",
            });
        } finally {
            setUpdatingItems((prev) => {
                const newSet = new Set(prev);
                newSet.delete(cartItemId);
                return newSet;
            });
        }
    };

    const handleClearCart = async () => {
        try {
            setLoading(true);
            // Call API to clear entire cart
            await apiService.clearCart();

            // Refresh cart data
            await fetchCartData();

            enqueueSnackbar("Cart cleared successfully", {
                variant: "success",
            });
        } catch (error) {
            console.error("Failed to clear cart:", error);
            enqueueSnackbar("Failed to clear cart", { variant: "error" });
        }
    };

    const handleContinueShopping = () => {
        navigate("/products");
    };

    const handleCheckout = async () => {
        if (
            !cartData ||
            !cartData.cartProducts ||
            cartData.cartProducts.length === 0
        ) {
            enqueueSnackbar("Your cart is empty", { variant: "warning" });
            return;
        }

        setProcessingOrder(true);
        try {
            // Static request body as in OrderSummarySidebar
            const orderData = {
                user_address_id: 1,
                shipping_method_id: 1,
                description: "Please deliver during business hours",
            };

            const response = await apiService.createOrder(orderData);

            // Access the actual API response data
            const apiData = response.data;

            if (apiData.success) {
                // Show success notification with order number
                enqueueSnackbar(
                    `Order ${apiData.data.order.order_number} created successfully! Total amount: $${apiData.data.order.total_amount}`,
                    { variant: "success" }
                );

                // Refresh cart after successful order creation
                await fetchCartData();

                // Navigate to order details page with the order ID
                navigate(`/orders/${apiData.data.order.id}`);
            } else {
                console.error("Order creation was not successful:", apiData);
                enqueueSnackbar(apiData.message || "Order creation failed", {
                    variant: "error",
                });
            }
        } catch (error) {
            console.error("Order creation failed:", error);
            console.error("Error response:", error.response?.data);

            // Show error notification to user
            let errorMessage = "Failed to create order. Please try again.";

            if (error.response?.data) {
                const data = error.response.data;

                // Handle validation errors (422)
                if (data.errors) {
                    // Get first validation error
                    const firstErrorKey = Object.keys(data.errors)[0];
                    errorMessage = data.errors[firstErrorKey][0];
                } else if (data.message) {
                    errorMessage = data.message;
                }
            }

            enqueueSnackbar(errorMessage, { variant: "error" });
        } finally {
            setProcessingOrder(false);
        }
    };

    if (loading) {
        return (
            <Box
                display="flex"
                justifyContent="center"
                alignItems="center"
                minHeight="60vh"
            >
                <CircularProgress />
            </Box>
        );
    }

    return (
        <Box
            sx={{
                maxWidth: 1200,
                mx: "auto",
                p: { xs: 2, sm: 3 },
                pb: {
                    xs: mobileOrderSummaryOpen ? 10 : 3, // Only add bottom padding when summary is open
                    md: 3,
                },
            }}
        >
            {/* Header */}
            <Box
                sx={{
                    display: "flex",
                    alignItems: "center",
                    mb: { xs: 2, md: 4 },
                    flexDirection: { xs: "column", sm: "row" },
                    gap: { xs: 1, sm: 0 },
                }}
            >
                <Button
                    startIcon={<ArrowBack />}
                    onClick={handleContinueShopping}
                    sx={{
                        mr: { xs: 0, sm: 2 },
                        mb: { xs: 1, sm: 0 },
                        minWidth: { xs: "100%", sm: "auto" },
                    }}
                    size={isMobile ? "large" : "medium"}
                >
                    Continue Shopping
                </Button>
                <Typography
                    variant={isSmallMobile ? "h5" : "h4"}
                    sx={{
                        flexGrow: 1,
                        fontWeight: 600,
                        textAlign: { xs: "center", sm: "left" },
                    }}
                >
                    Your cart
                </Typography>
                {cartItems.length > 0 && (
                    <Button
                        variant="outlined"
                        color="error"
                        onClick={handleClearCart}
                        disabled={loading}
                        size={isMobile ? "large" : "medium"}
                        sx={{
                            minWidth: { xs: "100%", sm: "auto" },
                            mt: { xs: 1, sm: 0 },
                        }}
                    >
                        Clear Cart
                    </Button>
                )}
            </Box>

            {cartItems.length === 0 ? (
                <Paper
                    sx={{
                        textAlign: "center",
                        py: { xs: 6, md: 8 },
                        px: { xs: 2, md: 4 },
                    }}
                >
                    <ShoppingCartOutlined
                        sx={{
                            fontSize: { xs: 60, md: 80 },
                            color: "text.secondary",
                            mb: 2,
                        }}
                    />
                    <Typography variant={isMobile ? "h6" : "h5"} gutterBottom>
                        Your cart is empty
                    </Typography>
                    <Typography
                        variant="body1"
                        color="text.secondary"
                        gutterBottom
                        sx={{ px: { xs: 2, md: 0 } }}
                    >
                        Add some products to your cart to get started
                    </Typography>
                    <Button
                        variant="contained"
                        onClick={handleContinueShopping}
                        size="large"
                        sx={{
                            mt: 2,
                            backgroundColor: "black",
                            "&:hover": { backgroundColor: "#333" },
                            minWidth: { xs: "200px", md: "auto" },
                            py: { xs: 1.5, md: 1 },
                        }}
                    >
                        Shop Now
                    </Button>
                </Paper>
            ) : (
                <Grid container spacing={{ xs: 2, md: 4 }}>
                    {/* Cart Items */}
                    <Grid item xs={12} lg={8}>
                        <Box sx={{ mb: 3 }}>
                            {cartItems.map((cartItem, index) => {
                                const isUpdating = updatingItems.has(
                                    cartItem.id
                                );

                                return (
                                    <Card
                                        key={cartItem.id}
                                        sx={{
                                            mb: 2,
                                            display: "flex",
                                            flexDirection: {
                                                xs: "column",
                                                sm: "row",
                                            },
                                            p: { xs: 2, sm: 2 },
                                            boxShadow:
                                                "0 2px 8px rgba(0,0,0,0.1)",
                                            borderRadius: 2,
                                            opacity: isUpdating ? 0.7 : 1,
                                        }}
                                    >
                                        <CardMedia
                                            component="img"
                                            sx={{
                                                width: {
                                                    xs: "100%",
                                                    sm: 120,
                                                    md: 140,
                                                },
                                                height: {
                                                    xs: 200,
                                                    sm: 120,
                                                    md: 140,
                                                },
                                                borderRadius: 2,
                                                objectFit: "cover",
                                                mb: { xs: 2, sm: 0 },
                                                mr: { xs: 0, sm: 2 },
                                            }}
                                            image={
                                                cartItem.product?.image_url ||
                                                "/default.jpg"
                                            }
                                            alt={cartItem.name}
                                        />
                                        <CardContent
                                            sx={{
                                                flex: 1,
                                                p: { xs: 0, sm: 2 },
                                                "&:last-child": {
                                                    pb: { xs: 0, sm: 2 },
                                                },
                                            }}
                                        >
                                            <Box
                                                sx={{
                                                    display: "flex",
                                                    justifyContent:
                                                        "space-between",
                                                    alignItems: "flex-start",
                                                    mb: 2,
                                                    flexDirection: {
                                                        xs: "column",
                                                        sm: "row",
                                                    },
                                                    gap: { xs: 1, sm: 0 },
                                                }}
                                            >
                                                <Box
                                                    sx={{
                                                        flex: 1,
                                                        width: {
                                                            xs: "100%",
                                                            sm: "auto",
                                                        },
                                                    }}
                                                >
                                                    <Typography
                                                        variant={
                                                            isMobile
                                                                ? "h6"
                                                                : "h6"
                                                        }
                                                        gutterBottom
                                                        sx={{
                                                            fontWeight: 600,
                                                            fontSize: {
                                                                xs: "1.1rem",
                                                                sm: "1.25rem",
                                                            },
                                                        }}
                                                    >
                                                        {cartItem.name}
                                                    </Typography>
                                                    <Chip
                                                        label="T-shirts"
                                                        size="small"
                                                        sx={{
                                                            backgroundColor:
                                                                "#ff4444",
                                                            color: "white",
                                                            fontSize: {
                                                                xs: "9px",
                                                                sm: "10px",
                                                            },
                                                            height: {
                                                                xs: 18,
                                                                sm: 20,
                                                            },
                                                            mb: 1,
                                                        }}
                                                    />
                                                    <Box
                                                        sx={{
                                                            display: "flex",
                                                            gap: 1,
                                                            alignItems:
                                                                "center",
                                                            mb: 1,
                                                            flexWrap: "wrap",
                                                        }}
                                                    >
                                                        <Typography
                                                            variant="h6"
                                                            sx={{
                                                                fontWeight: 600,
                                                                color: "#000",
                                                                fontSize: {
                                                                    xs: "1rem",
                                                                    sm: "1.25rem",
                                                                },
                                                            }}
                                                        >
                                                            $
                                                            {parseFloat(
                                                                cartItem.price
                                                            ).toFixed(2)}
                                                        </Typography>
                                                        {parseFloat(
                                                            cartItem.original_price
                                                        ) >
                                                            parseFloat(
                                                                cartItem.price
                                                            ) && (
                                                            <Typography
                                                                variant="body2"
                                                                sx={{
                                                                    textDecoration:
                                                                        "line-through",
                                                                    color: "text.secondary",
                                                                }}
                                                            >
                                                                $
                                                                {parseFloat(
                                                                    cartItem.original_price
                                                                ).toFixed(2)}
                                                            </Typography>
                                                        )}
                                                    </Box>
                                                    <Typography
                                                        variant="body2"
                                                        color="text.secondary"
                                                    >
                                                        Stock:{" "}
                                                        {cartItem.product
                                                            ?.stock || 25}
                                                    </Typography>
                                                </Box>
                                                <IconButton
                                                    color="error"
                                                    onClick={() =>
                                                        handleRemoveItem(
                                                            cartItem.id,
                                                            cartItem.name
                                                        )
                                                    }
                                                    disabled={isUpdating}
                                                    sx={{
                                                        color: "#ff4444",
                                                        alignSelf: {
                                                            xs: "flex-end",
                                                            sm: "flex-start",
                                                        },
                                                        mt: { xs: -1, sm: 0 },
                                                    }}
                                                    size={
                                                        isMobile
                                                            ? "large"
                                                            : "medium"
                                                    }
                                                >
                                                    {isUpdating ? (
                                                        <CircularProgress
                                                            size={20}
                                                        />
                                                    ) : (
                                                        <Delete />
                                                    )}
                                                </IconButton>
                                            </Box>

                                            <Box
                                                sx={{
                                                    display: "flex",
                                                    alignItems: "center",
                                                    justifyContent: {
                                                        xs: "center",
                                                        sm: "flex-start",
                                                    },
                                                    gap: { xs: 2, sm: 1 },
                                                    mt: 2,
                                                }}
                                            >
                                                <IconButton
                                                    size={
                                                        isMobile
                                                            ? "large"
                                                            : "small"
                                                    }
                                                    onClick={() =>
                                                        handleQuantityChange(
                                                            cartItem.id,
                                                            parseInt(
                                                                cartItem.quantity
                                                            ) - 1
                                                        )
                                                    }
                                                    disabled={
                                                        parseInt(
                                                            cartItem.quantity
                                                        ) <= 1 || isUpdating
                                                    }
                                                    sx={{
                                                        border: "1px solid #ddd",
                                                        width: {
                                                            xs: 44,
                                                            sm: 32,
                                                        },
                                                        height: {
                                                            xs: 44,
                                                            sm: 32,
                                                        },
                                                    }}
                                                >
                                                    {isUpdating ? (
                                                        <CircularProgress
                                                            size={14}
                                                        />
                                                    ) : (
                                                        <Remove fontSize="small" />
                                                    )}
                                                </IconButton>
                                                <Box
                                                    sx={{
                                                        minWidth: {
                                                            xs: 60,
                                                            sm: 40,
                                                        },
                                                        textAlign: "center",
                                                        py: { xs: 1.5, sm: 1 },
                                                        px: 2,
                                                        border: "1px solid #ddd",
                                                        borderRadius: 1,
                                                        fontSize: {
                                                            xs: "16px",
                                                            sm: "14px",
                                                        },
                                                        fontWeight: 500,
                                                    }}
                                                >
                                                    {cartItem.quantity}
                                                </Box>
                                                <IconButton
                                                    size={
                                                        isMobile
                                                            ? "large"
                                                            : "small"
                                                    }
                                                    onClick={() =>
                                                        handleQuantityChange(
                                                            cartItem.id,
                                                            parseInt(
                                                                cartItem.quantity
                                                            ) + 1
                                                        )
                                                    }
                                                    disabled={isUpdating}
                                                    sx={{
                                                        border: "1px solid #ddd",
                                                        width: {
                                                            xs: 44,
                                                            sm: 32,
                                                        },
                                                        height: {
                                                            xs: 44,
                                                            sm: 32,
                                                        },
                                                    }}
                                                >
                                                    {isUpdating ? (
                                                        <CircularProgress
                                                            size={14}
                                                        />
                                                    ) : (
                                                        <Add fontSize="small" />
                                                    )}
                                                </IconButton>
                                            </Box>
                                        </CardContent>
                                    </Card>
                                );
                            })}
                        </Box>
                    </Grid>

                    {/* Order Summary */}
                    <Grid item xs={12} lg={4}>
                        {/* Show order summary conditionally on mobile, always on desktop */}
                        {(!isMobile || mobileOrderSummaryOpen) && (
                            <Paper
                                sx={{
                                    p: { xs: 2, sm: 3 },
                                    position: { xs: "fixed", lg: "sticky" },
                                    bottom: { xs: 0, lg: "auto" },
                                    left: { xs: 0, lg: "auto" },
                                    right: { xs: 0, lg: "auto" },
                                    top: { xs: "auto", lg: 100 },
                                    boxShadow: {
                                        xs: "0 -4px 12px rgba(0,0,0,0.15)",
                                        lg: "0 4px 12px rgba(0,0,0,0.15)",
                                    },
                                    borderRadius: {
                                        xs: "12px 12px 0 0",
                                        lg: 3,
                                    },
                                    zIndex: { xs: 1000, lg: "auto" },
                                    maxHeight: { xs: "50vh", lg: "none" },
                                    overflowY: { xs: "auto", lg: "visible" },
                                    transform: {
                                        xs: mobileOrderSummaryOpen
                                            ? "translateY(0)"
                                            : "translateY(100%)",
                                        lg: "translateY(0)",
                                    },
                                    transition: {
                                        xs: "transform 0.3s ease-in-out",
                                        lg: "none",
                                    },
                                }}
                            >
                                <Box
                                    sx={{
                                        display: "flex",
                                        justifyContent: "space-between",
                                        alignItems: "flex-start",
                                        mb: 3,
                                        position: "relative",
                                    }}
                                >
                                    {/* Close button for mobile - positioned absolutely */}
                                    {isMobile && (
                                        <IconButton
                                            onClick={() => {
                                                setMobileOrderSummaryOpen(
                                                    false
                                                );
                                                // Remember that user closed the summary
                                                sessionStorage.setItem(
                                                    "orderSummaryClosed",
                                                    "true"
                                                );
                                            }}
                                            size="large"
                                            sx={{
                                                position: "absolute",
                                                top: -8,
                                                right: -8,
                                                backgroundColor:
                                                    "rgba(255,255,255,0.9)",
                                                color: "text.primary",
                                                zIndex: 10,
                                                boxShadow:
                                                    "0 2px 8px rgba(0,0,0,0.1)",
                                                "&:hover": {
                                                    backgroundColor:
                                                        "rgba(255,255,255,1)",
                                                    color: "error.main",
                                                },
                                                width: 40,
                                                height: 40,
                                            }}
                                        >
                                            <Close fontSize="medium" />
                                        </IconButton>
                                    )}

                                    <Box sx={{ flex: 1, pr: isMobile ? 4 : 0 }}>
                                        <Typography
                                            variant={isMobile ? "h6" : "h6"}
                                            sx={{
                                                fontWeight: 600,
                                                fontSize: {
                                                    xs: "1.1rem",
                                                    sm: "1.25rem",
                                                },
                                                mb: { xs: 1, sm: 0 },
                                            }}
                                        >
                                            Order Summary ( #
                                            {Math.floor(Math.random() * 1000) +
                                                100}{" "}
                                            )
                                        </Typography>
                                        <Typography
                                            variant="body2"
                                            color="text.secondary"
                                            sx={{
                                                fontSize: {
                                                    xs: "0.8rem",
                                                    sm: "0.875rem",
                                                },
                                            }}
                                        >
                                            {new Date().toLocaleDateString(
                                                "en-US",
                                                {
                                                    day: "numeric",
                                                    month: "long",
                                                    year: "numeric",
                                                }
                                            )}
                                        </Typography>
                                    </Box>
                                </Box>

                                <Box sx={{ mb: 3 }}>
                                    <Box
                                        sx={{
                                            display: "flex",
                                            justifyContent: "space-between",
                                            mb: 2,
                                        }}
                                    >
                                        <Typography
                                            variant="body1"
                                            color="text.secondary"
                                            sx={{
                                                fontSize: {
                                                    xs: "0.9rem",
                                                    sm: "1rem",
                                                },
                                            }}
                                        >
                                            Subtotal
                                        </Typography>
                                        <Typography
                                            variant="body1"
                                            sx={{
                                                fontWeight: 500,
                                                fontSize: {
                                                    xs: "0.9rem",
                                                    sm: "1rem",
                                                },
                                            }}
                                        >
                                            ${subtotal.toFixed(2)}
                                        </Typography>
                                    </Box>

                                    <Box
                                        sx={{
                                            display: "flex",
                                            justifyContent: "space-between",
                                            mb: 2,
                                        }}
                                    >
                                        <Typography
                                            variant="body1"
                                            color="text.secondary"
                                            sx={{
                                                fontSize: {
                                                    xs: "0.9rem",
                                                    sm: "1rem",
                                                },
                                            }}
                                        >
                                            Shipping
                                        </Typography>
                                        <Typography
                                            variant="body1"
                                            sx={{
                                                fontWeight: 500,
                                                fontSize: {
                                                    xs: "0.9rem",
                                                    sm: "1rem",
                                                },
                                            }}
                                        >
                                            ${shipping.toFixed(2)}
                                        </Typography>
                                    </Box>

                                    <Box
                                        sx={{
                                            display: "flex",
                                            justifyContent: "space-between",
                                            mb: 3,
                                        }}
                                    >
                                        <Typography
                                            variant="body1"
                                            color="text.secondary"
                                            sx={{
                                                fontSize: {
                                                    xs: "0.9rem",
                                                    sm: "1rem",
                                                },
                                            }}
                                        >
                                            Tax
                                        </Typography>
                                        <Typography
                                            variant="body1"
                                            sx={{
                                                fontWeight: 500,
                                                fontSize: {
                                                    xs: "0.9rem",
                                                    sm: "1rem",
                                                },
                                            }}
                                        >
                                            ${tax.toFixed(2)}
                                        </Typography>
                                    </Box>

                                    <Divider sx={{ my: 2 }} />

                                    <Box
                                        sx={{
                                            display: "flex",
                                            justifyContent: "space-between",
                                            mb: 3,
                                        }}
                                    >
                                        <Typography
                                            variant="h6"
                                            sx={{
                                                fontWeight: 600,
                                                fontSize: {
                                                    xs: "1.1rem",
                                                    sm: "1.25rem",
                                                },
                                            }}
                                        >
                                            Total
                                        </Typography>
                                        <Typography
                                            variant="h6"
                                            sx={{
                                                fontWeight: 600,
                                                fontSize: {
                                                    xs: "1.2rem",
                                                    sm: "1.25rem",
                                                },
                                            }}
                                        >
                                            ${total.toFixed(2)}
                                        </Typography>
                                    </Box>
                                </Box>

                                <Button
                                    fullWidth
                                    variant="contained"
                                    size="large"
                                    onClick={handleCheckout}
                                    disabled={
                                        loading ||
                                        processingOrder ||
                                        cartItems.length === 0 ||
                                        updatingItems.size > 0
                                    }
                                    sx={{
                                        backgroundColor: "#000",
                                        color: "white",
                                        py: { xs: 2.5, sm: 2 },
                                        fontSize: { xs: "18px", sm: "16px" },
                                        fontWeight: 600,
                                        textTransform: "none",
                                        borderRadius: 2,
                                        minHeight: { xs: 56, sm: 48 },
                                        "&:hover": {
                                            backgroundColor: "#333",
                                        },
                                        "&:disabled": {
                                            backgroundColor: "#ccc",
                                            color: "#666",
                                        },
                                    }}
                                >
                                    {processingOrder ? (
                                        <>
                                            <CircularProgress
                                                size={20}
                                                sx={{ mr: 1, color: "white" }}
                                            />
                                            Creating Order...
                                        </>
                                    ) : updatingItems.size > 0 ? (
                                        <>
                                            <CircularProgress
                                                size={20}
                                                sx={{ mr: 1, color: "white" }}
                                            />
                                            Updating Cart...
                                        </>
                                    ) : (
                                        "Place the order"
                                    )}
                                </Button>
                            </Paper>
                        )}
                    </Grid>
                </Grid>
            )}

            {/* Mobile backdrop overlay */}
            {isMobile && mobileOrderSummaryOpen && (
                <Box
                    onClick={() => {
                        setMobileOrderSummaryOpen(false);
                        // Remember that user closed the summary
                        sessionStorage.setItem("orderSummaryClosed", "true");
                    }}
                    sx={{
                        position: "fixed",
                        top: 0,
                        left: 0,
                        right: 0,
                        bottom: 0,
                        backgroundColor: "rgba(0,0,0,0.3)",
                        zIndex: 999,
                        transition: "opacity 0.3s ease-in-out",
                    }}
                />
            )}

            {/* Floating Order Summary Button for Mobile */}
            {isMobile && !mobileOrderSummaryOpen && cartItems.length > 0 && (
                <Box
                    sx={{
                        position: "fixed",
                        bottom: 24,
                        right: 24,
                        zIndex: 1001,
                    }}
                >
                    <Fab
                        onClick={() => {
                            setMobileOrderSummaryOpen(true);
                            // Clear the closed flag when user explicitly opens
                            sessionStorage.removeItem("orderSummaryClosed");
                        }}
                        sx={{
                            backgroundColor: "#000",
                            color: "white",
                            "&:hover": {
                                backgroundColor: "#333",
                            },
                            boxShadow: "0 4px 20px rgba(0,0,0,0.3)",
                            position: "relative",
                        }}
                        aria-label="Open order summary"
                    >
                        <Receipt />
                    </Fab>
                    {/* Total amount badge */}
                    <Box
                        sx={{
                            position: "absolute",
                            top: -8,
                            right: -8,
                            backgroundColor: "#ff4444",
                            color: "white",
                            borderRadius: "12px",
                            padding: "4px 8px",
                            fontSize: "12px",
                            fontWeight: 600,
                            minWidth: "24px",
                            textAlign: "center",
                            boxShadow: "0 2px 8px rgba(0,0,0,0.2)",
                        }}
                    >
                        ${total.toFixed(0)}
                    </Box>
                </Box>
            )}
        </Box>
    );
};

export default CartPage;
