import React from "react";
import {
    Paper,
    Typography,
    Divider,
    Button,
    Box,
    CircularProgress,
} from "@mui/material";
import { ShoppingCart, Receipt } from "@mui/icons-material";
import { formatPrice } from "../../utils/formatters";
import { API_CONFIG } from "../../utils/constants";

/**
 * Reusable cart summary component
 * @param {Object} props - Component props
 * @param {number} props.subtotal - Cart subtotal
 * @param {number} props.tax - Tax amount
 * @param {number} props.shipping - Shipping cost
 * @param {number} props.itemCount - Number of items in cart
 * @param {Function} props.onCheckout - Checkout handler
 * @param {Function} props.onContinueShopping - Continue shopping handler
 * @param {boolean} props.loading - Loading state
 * @param {boolean} props.disabled - Whether checkout is disabled
 * @param {string} props.checkoutText - Checkout button text
 * @param {string} props.continueShoppingText - Continue shopping button text
 */
const CartSummary = ({
    subtotal = 0,
    tax = 0,
    shipping = 0,
    itemCount = 0,
    onCheckout = null,
    onContinueShopping = null,
    loading = false,
    disabled = false,
    checkoutText = "Proceed to Checkout",
    continueShoppingText = "Continue Shopping",
}) => {
    const total = subtotal + tax + shipping;
    const isEmpty = itemCount === 0;

    return (
        <Paper
            elevation={2}
            sx={{
                p: 3,
                borderRadius: 2,
                backgroundColor: "background.paper",
            }}
        >
            <Typography
                variant="h6"
                sx={{
                    mb: 3,
                    fontWeight: 600,
                    display: "flex",
                    alignItems: "center",
                    gap: 1,
                }}
            >
                <Receipt />
                Order Summary
            </Typography>

            {/* Items Count */}
            <Box
                sx={{
                    display: "flex",
                    justifyContent: "space-between",
                    mb: 2,
                }}
            >
                <Typography variant="body1">Items ({itemCount})</Typography>
                <Typography variant="body1" fontWeight={500}>
                    {formatPrice(subtotal)}
                </Typography>
            </Box>

            {/* Shipping */}
            <Box
                sx={{
                    display: "flex",
                    justifyContent: "space-between",
                    mb: 2,
                }}
            >
                <Typography variant="body1">Shipping</Typography>
                <Typography
                    variant="body1"
                    fontWeight={500}
                    color={shipping === 0 ? "success.main" : "text.primary"}
                >
                    {shipping === 0 ? "Free" : formatPrice(shipping)}
                </Typography>
            </Box>

            {/* Tax */}
            {tax > 0 && (
                <Box
                    sx={{
                        display: "flex",
                        justifyContent: "space-between",
                        mb: 2,
                    }}
                >
                    <Typography variant="body1">Tax</Typography>
                    <Typography variant="body1" fontWeight={500}>
                        {formatPrice(tax)}
                    </Typography>
                </Box>
            )}

            <Divider sx={{ my: 2 }} />

            {/* Total */}
            <Box
                sx={{
                    display: "flex",
                    justifyContent: "space-between",
                    mb: 3,
                }}
            >
                <Typography variant="h6" sx={{ fontWeight: 600 }}>
                    Total
                </Typography>
                <Typography
                    variant="h6"
                    sx={{
                        fontWeight: 700,
                        color: "primary.main",
                    }}
                >
                    {formatPrice(total)}
                </Typography>
            </Box>

            {/* Action Buttons */}
            <Box sx={{ display: "flex", flexDirection: "column", gap: 2 }}>
                {onCheckout && (
                    <Button
                        variant="contained"
                        size="large"
                        fullWidth
                        onClick={onCheckout}
                        disabled={loading || disabled || isEmpty}
                        startIcon={
                            loading ? (
                                <CircularProgress size={20} color="inherit" />
                            ) : (
                                <ShoppingCart />
                            )
                        }
                        sx={{
                            py: 1.5,
                            borderRadius: 2,
                            textTransform: "none",
                            fontSize: "1rem",
                            fontWeight: 600,
                        }}
                    >
                        {loading ? "Processing..." : checkoutText}
                    </Button>
                )}

                {onContinueShopping && (
                    <Button
                        variant="outlined"
                        size="large"
                        fullWidth
                        onClick={onContinueShopping}
                        disabled={loading}
                        sx={{
                            py: 1.5,
                            borderRadius: 2,
                            textTransform: "none",
                            fontSize: "0.95rem",
                        }}
                    >
                        {continueShoppingText}
                    </Button>
                )}
            </Box>

            {/* Free Shipping Info */}
            {shipping > 0 && subtotal < 100 && (
                <Typography
                    variant="caption"
                    sx={{
                        display: "block",
                        mt: 2,
                        p: 1,
                        backgroundColor: "info.light",
                        color: "info.contrastText",
                        borderRadius: 1,
                        textAlign: "center",
                    }}
                >
                    Add {formatPrice(100 - subtotal)} more for free shipping!
                </Typography>
            )}
        </Paper>
    );
};

export default CartSummary;
