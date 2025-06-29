import React, { useState } from "react";
import { useNavigate } from "react-router-dom";
import {
    Typography,
    Button,
    Box,
    IconButton,
    Chip,
    Divider,
    CircularProgress,
} from "@mui/material";
import { Add, Remove } from "@mui/icons-material";
import { useSnackbar } from "notistack";
import apiService from "../services/apiService";
import { useCart } from "../contexts/CartContext";

const OrderSummarySidebar = ({
    cartItems = [], // Array of cart items
    subtotal = 385,
    shipping = 15.0,
    tax = 12.5,
    total = 412.5,
    onQuantityChange,
    onCheckout,
    loading = false,
}) => {
    const navigate = useNavigate();
    const [checkoutLoading, setCheckoutLoading] = useState(false);
    const { fetchCart } = useCart();
    const { enqueueSnackbar } = useSnackbar();

    const handleCheckout = async () => {
        if (cartItems.length === 0) return;

        try {
            setCheckoutLoading(true);

            // Static request body as specified
            const orderData = {
                user_address_id: 1,
                shipping_method_id: 1,
                description: "Please deliver during business hours",
            };

            const response = await apiService.createOrder(orderData);


            // Access the actual API response data (axios wraps the response in response.data)
            const apiData = response.data;

            if (apiData.success) {
                // Show success notification with order number
                enqueueSnackbar(
                    `Order ${apiData.data.order.order_number} created successfully! Total amount: $${apiData.data.order.total_amount}`,
                    { variant: "success" }
                );



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
            setCheckoutLoading(false);
        }
    };



    const displayItems = cartItems.length > 0 ? cartItems : [];

    // Set values to 0 when cart is empty
    const displaySubtotal = displayItems.length === 0 ? 0 : subtotal;
    const displayShipping = 0; // Always set shipping to 0
    const displayTax = displayItems.length === 0 ? 0 : tax;
    const displayTotal = displayItems.length === 0 ? 0 : displaySubtotal + displayShipping + displayTax;

    const handleQuantityChange = (itemId, change) => {
        if (onQuantityChange) {
            onQuantityChange(itemId, change);
        }
    };

    return (
        <Box
            sx={{
                width: 320,
                bgcolor: "white",
                borderLeft: "1px solid #e0e0e0",
                p: 3,
                display: { xs: "none", lg: "block" },
            }}
        >
            <Box sx={{ display: "flex", alignItems: "center", mb: 3 }}>
                <Typography
                    variant="h6"
                    sx={{ fontWeight: 600, fontSize: "18px" }}
                >
                    Order Summary
                </Typography>
                {loading && (
                    <CircularProgress size={16} sx={{ ml: 2, color: "#666" }} />
                )}
            </Box>

            {/* Cart Items */}
            {displayItems.length === 0 ? (
                <Box
                    sx={{
                        display: "flex",
                        flexDirection: "column",
                        alignItems: "center",
                        justifyContent: "center",
                        py: 4,
                        mb: 3,
                    }}
                >
                    <Typography
                        variant="body1"
                        sx={{ color: "#666", textAlign: "center", mb: 2 }}
                    >
                        Your cart is empty
                    </Typography>
                    <Typography
                        variant="body2"
                        sx={{ color: "#999", textAlign: "center" }}
                    >
                        Add products to see them here
                    </Typography>
                </Box>
            ) : (
                displayItems.map((item, index) => (
                    <Box
                        key={item.id}
                        sx={{
                            display: "flex",
                            alignItems: "center",
                            mb: index === displayItems.length - 1 ? 3 : 2,
                        }}
                    >
                        <Box
                            component="img"
                            src={item.image}
                            sx={{
                                width: 50,
                                height: 50,
                                borderRadius: 1,
                                mr: 2,
                            }}
                        />
                        <Box sx={{ flexGrow: 1 }}>
                            <Typography
                                variant="body2"
                                sx={{ fontWeight: 500, fontSize: "14px" }}
                            >
                                {item.name.length > 20
                                    ? item.name.substring(0, 20) + "..."
                                    : item.name}
                            </Typography>
                            <Chip
                                label={item.chipLabel}
                                size="small"
                                sx={{
                                    backgroundColor: "#ff4444",
                                    color: "white",
                                    fontSize: "10px",
                                    height: 16,
                                    mb: 1,
                                }}
                            />
                            <Box
                                sx={{
                                    display: "flex",
                                    alignItems: "center",
                                    gap: 1,
                                }}
                            >
                                <IconButton
                                    size="small"
                                    sx={{
                                        border: "1px solid #ddd",
                                        width: 20,
                                        height: 20,
                                    }}
                                    onClick={() =>
                                        handleQuantityChange(item.id, -1)
                                    }
                                    disabled={loading}
                                >
                                    {loading ? (
                                        <CircularProgress size={10} />
                                    ) : (
                                        <Remove fontSize="small" />
                                    )}
                                </IconButton>
                                <Typography
                                    variant="body2"
                                    sx={{
                                        fontSize: "12px",
                                        opacity: loading ? 0.6 : 1,
                                    }}
                                >
                                    {item.quantity}
                                </Typography>
                                <IconButton
                                    size="small"
                                    sx={{
                                        border: "1px solid #ddd",
                                        width: 20,
                                        height: 20,
                                    }}
                                    onClick={() =>
                                        handleQuantityChange(item.id, 1)
                                    }
                                    disabled={loading}
                                >
                                    {loading ? (
                                        <CircularProgress size={10} />
                                    ) : (
                                        <Add fontSize="small" />
                                    )}
                                </IconButton>
                            </Box>
                        </Box>
                        <Typography
                            variant="body2"
                            sx={{ fontWeight: 600, fontSize: "14px" }}
                        >
                            ${item.price}
                        </Typography>
                    </Box>
                ))
            )}

            {/* Order Summary Calculations */}
            <Box sx={{ mt: 3 }}>
                <Box
                    sx={{
                        display: "flex",
                        justifyContent: "space-between",
                        mb: 1,
                    }}
                >
                    <Typography
                        variant="body2"
                        sx={{ fontSize: "14px", color: "#666" }}
                    >
                        Subtotal
                    </Typography>
                    <Typography
                        variant="body2"
                        sx={{ fontSize: "14px", fontWeight: 500 }}
                    >
                        ${displaySubtotal}
                    </Typography>
                </Box>
                <Box
                    sx={{
                        display: "flex",
                        justifyContent: "space-between",
                        mb: 1,
                    }}
                >
                    <Typography
                        variant="body2"
                        sx={{ fontSize: "14px", color: "#666" }}
                    >
                        Shipping
                    </Typography>
                    <Typography
                        variant="body2"
                        sx={{ fontSize: "14px", fontWeight: 500 }}
                    >
                        ${displayShipping.toFixed(2)}
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
                        variant="body2"
                        sx={{ fontSize: "14px", color: "#666" }}
                    >
                        Tax
                    </Typography>
                    <Typography
                        variant="body2"
                        sx={{ fontSize: "14px", fontWeight: 500 }}
                    >
                        ${displayTax.toFixed(2)}
                    </Typography>
                </Box>
                <Divider sx={{ mb: 2 }} />
                <Box
                    sx={{
                        display: "flex",
                        justifyContent: "space-between",
                        mb: 3,
                    }}
                >
                    <Typography variant="subtitle1" sx={{ fontWeight: 600 }}>
                        Total
                    </Typography>
                    <Typography variant="subtitle1" sx={{ fontWeight: 600 }}>
                        ${displayTotal.toFixed(2)}
                    </Typography>
                </Box>
                <Button
                    fullWidth
                    variant="contained"
                    sx={{
                        backgroundColor:
                            cartItems.length === 0 || loading || checkoutLoading
                                ? "#ccc"
                                : "#000",
                        color: "white",
                        py: 1.5,
                        fontSize: "14px",
                        textTransform: "none",
                        "&:hover": {
                            backgroundColor:
                                cartItems.length === 0 ||
                                loading ||
                                checkoutLoading
                                    ? "#ccc"
                                    : "#333",
                        },
                    }}
                    onClick={handleCheckout}
                    disabled={
                        cartItems.length === 0 || loading || checkoutLoading
                    }
                >
                    {checkoutLoading ? (
                        <>
                            <CircularProgress
                                size={16}
                                sx={{ mr: 1, color: "white" }}
                            />
                            Creating Order...
                        </>
                    ) : loading ? (
                        <>
                            <CircularProgress
                                size={16}
                                sx={{ mr: 1, color: "white" }}
                            />
                            Updating Cart...
                        </>
                    ) : cartItems.length === 0 ? (
                        "Cart Empty"
                    ) : (
                        "Proceed to Checkout"
                    )}
                </Button>
            </Box>
        </Box>
    );
};

export default OrderSummarySidebar;
