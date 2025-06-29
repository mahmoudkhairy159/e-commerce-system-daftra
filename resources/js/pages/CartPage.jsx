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
} from "@mui/material";
import {
    Delete,
    Add,
    Remove,
    ShoppingCartOutlined,
    ArrowBack,
} from "@mui/icons-material";
import { useSnackbar } from "notistack";
import apiService from "../services/apiService";

const CartPage = () => {
    const navigate = useNavigate();
    const { enqueueSnackbar } = useSnackbar();

    // Local state for cart data
    const [cartData, setCartData] = useState(null);
    const [loading, setLoading] = useState(true);
    const [processingOrder, setProcessingOrder] = useState(false);
    const [updatingItems, setUpdatingItems] = useState(new Set());

    // Fetch cart data on component mount
    useEffect(() => {
        fetchCartData();
    }, []);

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

    // Calculate totals from API response
    const subtotal = cartData ? parseFloat(cartData.sum_subtotal || 0) : 0;
    const shipping = 0.0; // Free shipping
    const tax = cartData ? parseFloat(cartData.sum_tax || 0) : 0;
    const total = subtotal + shipping + tax;
    const cartItems = cartData?.cartProducts || [];
    const itemCount = cartData ? parseInt(cartData.sum_quantity || 0) : 0;

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
        <Box sx={{ maxWidth: 1200, mx: "auto", p: 3 }}>
            {/* Header */}
            <Box sx={{ display: "flex", alignItems: "center", mb: 4 }}>
                <Button
                    startIcon={<ArrowBack />}
                    onClick={handleContinueShopping}
                    sx={{ mr: 2 }}
                >
                    Continue Shopping
                </Button>
                <Typography variant="h4" sx={{ flexGrow: 1, fontWeight: 600 }}>
                    Your cart
                </Typography>
                {cartItems.length > 0 && (
                    <Button
                        variant="outlined"
                        color="error"
                        onClick={handleClearCart}
                        disabled={loading}
                    >
                        Clear Cart
                    </Button>
                )}
            </Box>

            {cartItems.length === 0 ? (
                <Paper sx={{ textAlign: "center", py: 8 }}>
                    <ShoppingCartOutlined
                        sx={{ fontSize: 80, color: "text.secondary", mb: 2 }}
                    />
                    <Typography variant="h5" gutterBottom>
                        Your cart is empty
                    </Typography>
                    <Typography
                        variant="body1"
                        color="text.secondary"
                        gutterBottom
                    >
                        Add some products to your cart to get started
                    </Typography>
                    <Button
                        variant="contained"
                        onClick={handleContinueShopping}
                        sx={{
                            mt: 2,
                            backgroundColor: "black",
                            "&:hover": { backgroundColor: "#333" },
                        }}
                    >
                        Shop Now
                    </Button>
                </Paper>
            ) : (
                <Grid container spacing={4}>
                    {/* Cart Items */}
                    <Grid item xs={12} md={8}>
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
                                            p: 2,
                                            boxShadow:
                                                "0 2px 8px rgba(0,0,0,0.1)",
                                            borderRadius: 2,
                                            opacity: isUpdating ? 0.7 : 1,
                                        }}
                                    >
                                        <CardMedia
                                            component="img"
                                            sx={{
                                                width: 120,
                                                height: 120,
                                                borderRadius: 2,
                                                objectFit: "cover",
                                            }}
                                            image={
                                                cartItem.product?.image_url ||
                                                "/api/placeholder/120/120"
                                            }
                                            alt={cartItem.name}
                                        />
                                        <CardContent sx={{ flex: 1, p: 2 }}>
                                            <Box
                                                sx={{
                                                    display: "flex",
                                                    justifyContent:
                                                        "space-between",
                                                    alignItems: "flex-start",
                                                    mb: 2,
                                                }}
                                            >
                                                <Box>
                                                    <Typography
                                                        variant="h6"
                                                        gutterBottom
                                                        sx={{ fontWeight: 600 }}
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
                                                            fontSize: "10px",
                                                            height: 20,
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
                                                        }}
                                                    >
                                                        <Typography
                                                            variant="h6"
                                                            sx={{
                                                                fontWeight: 600,
                                                                color: "#000",
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
                                                    sx={{ color: "#ff4444" }}
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
                                                    gap: 1,
                                                    mt: 2,
                                                }}
                                            >
                                                <IconButton
                                                    size="small"
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
                                                        width: 32,
                                                        height: 32,
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
                                                        minWidth: 40,
                                                        textAlign: "center",
                                                        py: 1,
                                                        px: 2,
                                                        border: "1px solid #ddd",
                                                        borderRadius: 1,
                                                        fontSize: "14px",
                                                        fontWeight: 500,
                                                    }}
                                                >
                                                    {cartItem.quantity}
                                                </Box>
                                                <IconButton
                                                    size="small"
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
                                                        width: 32,
                                                        height: 32,
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
                    <Grid item xs={12} md={4}>
                        <Paper
                            sx={{
                                p: 3,
                                position: "sticky",
                                top: 100,
                                boxShadow: "0 4px 12px rgba(0,0,0,0.15)",
                                borderRadius: 3,
                            }}
                        >
                            <Box
                                sx={{
                                    display: "flex",
                                    justifyContent: "space-between",
                                    alignItems: "center",
                                    mb: 3,
                                }}
                            >
                                <Typography
                                    variant="h6"
                                    sx={{ fontWeight: 600 }}
                                >
                                    Order Summary ( #
                                    {Math.floor(Math.random() * 1000) + 100} )
                                </Typography>
                                <Typography
                                    variant="body2"
                                    color="text.secondary"
                                >
                                    {new Date().toLocaleDateString("en-US", {
                                        day: "numeric",
                                        month: "long",
                                        year: "numeric",
                                    })}
                                </Typography>
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
                                    >
                                        Subtotal
                                    </Typography>
                                    <Typography
                                        variant="body1"
                                        sx={{ fontWeight: 500 }}
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
                                    >
                                        Shipping
                                    </Typography>
                                    <Typography
                                        variant="body1"
                                        sx={{ fontWeight: 500 }}
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
                                    >
                                        Tax
                                    </Typography>
                                    <Typography
                                        variant="body1"
                                        sx={{ fontWeight: 500 }}
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
                                        sx={{ fontWeight: 600 }}
                                    >
                                        Total
                                    </Typography>
                                    <Typography
                                        variant="h6"
                                        sx={{
                                            fontWeight: 600,
                                            fontSize: "1.25rem",
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
                                    py: 2,
                                    fontSize: "16px",
                                    fontWeight: 600,
                                    textTransform: "none",
                                    borderRadius: 2,
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
                    </Grid>
                </Grid>
            )}
        </Box>
    );
};

export default CartPage;
