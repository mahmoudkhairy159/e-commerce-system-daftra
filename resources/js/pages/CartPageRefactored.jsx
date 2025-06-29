import React, { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import {
    Grid,
    Typography,
    Button,
    Box,
    useTheme,
    useMediaQuery,
    Fab,
    Container,
} from "@mui/material";
import { ArrowBack, Receipt } from "@mui/icons-material";

// Import new reusable components and hooks
import { useApi } from "../hooks";
import { CartItem, CartSummary } from "../components/Cart";
import { LoadingSpinner, ErrorMessage, EmptyState } from "../components/UI";
import apiService from "../services/apiService";
import { MESSAGES } from "../utils";

const CartPageRefactored = () => {
    const navigate = useNavigate();
    const theme = useTheme();
    const isMobile = useMediaQuery(theme.breakpoints.down("md"));

    // Use custom hooks
    const { execute: executeApi, loading: apiLoading } = useApi();

    // States
    const [cartData, setCartData] = useState(null);
    const [updatingItems, setUpdatingItems] = useState(new Set());
    const [mobileOrderSummaryOpen, setMobileOrderSummaryOpen] = useState(false);

    // Computed values
    const subtotal = cartData ? parseFloat(cartData.sum_subtotal || 0) : 0;
    const shipping = 0.0; // Free shipping
    const tax = cartData ? parseFloat(cartData.sum_tax || 0) : 0;
    const total = subtotal + shipping + tax;
    const cartItems = cartData?.cartProducts || [];
    const itemCount = cartData ? parseInt(cartData.sum_quantity || 0) : 0;

    // Fetch cart data
    const fetchCartData = async () => {
        const result = await executeApi(() => apiService.getCart(), {
            errorMessage: MESSAGES.ERROR.FETCH_CART_FAILED,
            onSuccess: (response) => {
                if (response.data?.success) {
                    setCartData(response.data.data);
                } else {
                    setCartData(null);
                }
            },
        });
        return result;
    };

    // Handle quantity change
    const handleQuantityChange = async (cartItemId, newQuantity) => {
        if (newQuantity < 1) return;

        setUpdatingItems((prev) => new Set(prev).add(cartItemId));

        const result = await executeApi(
            () => apiService.updateCartItem(cartItemId, newQuantity),
            {
                successMessage: MESSAGES.SUCCESS.CART_UPDATED,
                errorMessage: MESSAGES.ERROR.UPDATE_CART_FAILED,
                onSuccess: () => {
                    fetchCartData(); // Refresh cart data
                },
            }
        );

        setUpdatingItems((prev) => {
            const newSet = new Set(prev);
            newSet.delete(cartItemId);
            return newSet;
        });

        return result;
    };

    // Handle remove item
    const handleRemoveItem = async (cartItemId, productName) => {
        setUpdatingItems((prev) => new Set(prev).add(cartItemId));

        const result = await executeApi(
            () => apiService.removeFromCart(cartItemId),
            {
                successMessage: `${productName} removed from cart`,
                errorMessage: MESSAGES.ERROR.REMOVE_FROM_CART_FAILED,
                onSuccess: () => {
                    fetchCartData(); // Refresh cart data
                },
            }
        );

        setUpdatingItems((prev) => {
            const newSet = new Set(prev);
            newSet.delete(cartItemId);
            return newSet;
        });

        return result;
    };

    // Handle clear cart
    const handleClearCart = async () => {
        const result = await executeApi(() => apiService.clearCart(), {
            successMessage: MESSAGES.SUCCESS.CART_CLEARED,
            errorMessage: MESSAGES.ERROR.CLEAR_CART_FAILED,
            onSuccess: () => {
                fetchCartData(); // Refresh cart data
            },
        });
        return result;
    };

    // Handle checkout
    const handleCheckout = async () => {
        if (
            !cartData ||
            !cartData.cartProducts ||
            cartData.cartProducts.length === 0
        ) {
            return { success: false, error: MESSAGES.WARNING.EMPTY_CART };
        }

        const orderData = {
            user_address_id: 1,
            shipping_method_id: 1,
            description: "Please deliver during business hours",
        };

        const result = await executeApi(
            () => apiService.createOrder(orderData),
            {
                errorMessage: MESSAGES.ERROR.CREATE_ORDER_FAILED,
                onSuccess: (response) => {
                    const apiData = response.data;
                    if (apiData.success) {
                        fetchCartData(); // Refresh cart after successful order
                        navigate(`/orders/${apiData.data.order.id}`);
                    }
                },
            }
        );

        return result;
    };

    const handleContinueShopping = () => {
        navigate("/products");
    };

    // Effects
    useEffect(() => {
        fetchCartData();
    }, []);

    useEffect(() => {
        if (isMobile && cartItems.length > 0) {
            setMobileOrderSummaryOpen(true);
        }
    }, [isMobile, cartItems.length]);

    // Loading state
    if (apiLoading && !cartData) {
        return (
            <Container maxWidth="lg" sx={{ py: 4 }}>
                <LoadingSpinner message="Loading your cart..." />
            </Container>
        );
    }

    // Empty cart state
    if (!cartItems || cartItems.length === 0) {
        return (
            <Container maxWidth="lg" sx={{ py: 4 }}>
                <Box sx={{ mb: 3 }}>
                    <Button
                        startIcon={<ArrowBack />}
                        onClick={handleContinueShopping}
                        sx={{ mb: 2 }}
                    >
                        Back to Shopping
                    </Button>
                    <Typography variant="h4" sx={{ fontWeight: "bold", mb: 3 }}>
                        Shopping Cart
                    </Typography>
                </Box>

                <EmptyState type="cart" onAction={handleContinueShopping} />
            </Container>
        );
    }

    return (
        <Container maxWidth="lg" sx={{ py: 4 }}>
            {/* Header */}
            <Box sx={{ mb: 4 }}>
                <Button
                    startIcon={<ArrowBack />}
                    onClick={handleContinueShopping}
                    sx={{ mb: 2 }}
                >
                    Continue Shopping
                </Button>

                <Box
                    sx={{
                        display: "flex",
                        justifyContent: "space-between",
                        alignItems: "center",
                        mb: 3,
                    }}
                >
                    <Typography
                        variant="h4"
                        sx={{
                            fontWeight: "bold",
                            fontSize: { xs: "1.5rem", sm: "2rem" },
                        }}
                    >
                        Shopping Cart ({itemCount} items)
                    </Typography>

                    <Button
                        variant="outlined"
                        color="error"
                        onClick={handleClearCart}
                        disabled={apiLoading}
                        size="small"
                    >
                        Clear Cart
                    </Button>
                </Box>
            </Box>

            {/* Cart Content */}
            <Grid container spacing={4}>
                {/* Cart Items */}
                <Grid item xs={12} md={8}>
                    <Box sx={{ position: "relative" }}>
                        {apiLoading && (
                            <LoadingSpinner
                                overlay
                                message="Updating cart..."
                            />
                        )}

                        {cartItems.map((item) => (
                            <CartItem
                                key={item.id}
                                item={item}
                                onQuantityChange={handleQuantityChange}
                                onRemove={handleRemoveItem}
                                updating={updatingItems.has(item.id)}
                                disabled={apiLoading}
                            />
                        ))}
                    </Box>
                </Grid>

                {/* Order Summary - Desktop */}
                {!isMobile && (
                    <Grid item xs={12} md={4}>
                        <Box sx={{ position: "sticky", top: 20 }}>
                            <CartSummary
                                subtotal={subtotal}
                                tax={tax}
                                shipping={shipping}
                                itemCount={itemCount}
                                onCheckout={handleCheckout}
                                onContinueShopping={handleContinueShopping}
                                loading={apiLoading}
                            />
                        </Box>
                    </Grid>
                )}
            </Grid>

            {/* Mobile Order Summary FAB */}
            {isMobile && (
                <Fab
                    variant="extended"
                    color="primary"
                    sx={{
                        position: "fixed",
                        bottom: 16,
                        left: "50%",
                        transform: "translateX(-50%)",
                        zIndex: 1000,
                        minWidth: 200,
                    }}
                    onClick={handleCheckout}
                    disabled={apiLoading || itemCount === 0}
                >
                    <Receipt sx={{ mr: 1 }} />
                    Checkout • ${total.toFixed(2)}
                </Fab>
            )}
        </Container>
    );
};

export default CartPageRefactored;
