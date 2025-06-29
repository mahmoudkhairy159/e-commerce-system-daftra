import React from "react";
import {
    Card,
    CardContent,
    CardMedia,
    Typography,
    IconButton,
    TextField,
    Box,
    Chip,
    CircularProgress,
} from "@mui/material";
import { Delete, Add, Remove } from "@mui/icons-material";
import { formatPrice } from "../../utils/formatters";

/**
 * Reusable cart item component
 * @param {Object} props - Component props
 * @param {Object} props.item - Cart item object
 * @param {Function} props.onQuantityChange - Quantity change handler
 * @param {Function} props.onRemove - Remove item handler
 * @param {boolean} props.updating - Whether item is being updated
 * @param {boolean} props.disabled - Whether item actions are disabled
 */
const CartItem = ({
    item,
    onQuantityChange = null,
    onRemove = null,
    updating = false,
    disabled = false,
}) => {
    const handleQuantityChange = (newQuantity) => {
        if (onQuantityChange && newQuantity >= 1) {
            onQuantityChange(item.id, newQuantity);
        }
    };

    const handleRemove = () => {
        if (onRemove) {
            onRemove(item.id, item.name || item.product?.name);
        }
    };

    const productName = item.name || item.product?.name || "Unknown Product";
    const productImage =
        item.image || item.product?.image_url || "/default.jpg";
    const productPrice = item.price || item.product?.price || 0;
    const productCode = item.chipLabel || item.product?.code;
    const quantity = parseInt(item.quantity || 0);
    const totalPrice = productPrice * quantity;

    return (
        <Card
            sx={{
                display: "flex",
                mb: 2,
                position: "relative",
                opacity: updating || disabled ? 0.7 : 1,
                transition: "opacity 0.2s",
            }}
        >
            {/* Product Image */}
            <CardMedia
                component="img"
                sx={{
                    width: { xs: 80, sm: 120 },
                    height: { xs: 80, sm: 120 },
                    objectFit: "cover",
                    borderRadius: 1,
                }}
                image={productImage}
                alt={productName}
            />

            {/* Product Details */}
            <Box sx={{ display: "flex", flexDirection: "column", flex: 1 }}>
                <CardContent sx={{ flex: "1 0 auto", pb: 1 }}>
                    {/* Product Name */}
                    <Typography
                        variant="h6"
                        sx={{
                            fontSize: { xs: "0.9rem", sm: "1.1rem" },
                            fontWeight: 500,
                            mb: 1,
                            lineHeight: 1.2,
                        }}
                    >
                        {productName}
                    </Typography>

                    {/* Product Code Chip */}
                    {productCode && (
                        <Chip
                            label={productCode}
                            size="small"
                            variant="outlined"
                            sx={{ mb: 1, fontSize: "0.75rem" }}
                        />
                    )}

                    {/* Price */}
                    <Typography
                        variant="body1"
                        sx={{
                            fontWeight: 600,
                            color: "primary.main",
                            mb: 1,
                        }}
                    >
                        {formatPrice(productPrice)} each
                    </Typography>

                    {/* Quantity Controls */}
                    <Box
                        sx={{
                            display: "flex",
                            alignItems: "center",
                            gap: 1,
                            mb: 1,
                        }}
                    >
                        <IconButton
                            size="small"
                            onClick={() => handleQuantityChange(quantity - 1)}
                            disabled={updating || disabled || quantity <= 1}
                        >
                            <Remove />
                        </IconButton>

                        <TextField
                            type="number"
                            value={quantity}
                            onChange={(e) => {
                                const newQuantity = parseInt(e.target.value);
                                if (!isNaN(newQuantity)) {
                                    handleQuantityChange(newQuantity);
                                }
                            }}
                            size="small"
                            disabled={updating || disabled}
                            inputProps={{
                                min: 1,
                                style: { textAlign: "center", width: "60px" },
                            }}
                            sx={{
                                "& .MuiOutlinedInput-root": {
                                    borderRadius: 1,
                                },
                            }}
                        />

                        <IconButton
                            size="small"
                            onClick={() => handleQuantityChange(quantity + 1)}
                            disabled={updating || disabled}
                        >
                            <Add />
                        </IconButton>
                    </Box>

                    {/* Total Price */}
                    <Typography
                        variant="body1"
                        sx={{
                            fontWeight: 700,
                            color: "text.primary",
                        }}
                    >
                        Total: {formatPrice(totalPrice)}
                    </Typography>
                </CardContent>
            </Box>

            {/* Remove Button */}
            <Box
                sx={{
                    position: "absolute",
                    top: 8,
                    right: 8,
                }}
            >
                <IconButton
                    size="small"
                    onClick={handleRemove}
                    disabled={updating || disabled}
                    sx={{
                        backgroundColor: "background.paper",
                        "&:hover": {
                            backgroundColor: "error.light",
                            color: "error.contrastText",
                        },
                    }}
                >
                    {updating ? (
                        <CircularProgress size={16} />
                    ) : (
                        <Delete fontSize="small" />
                    )}
                </IconButton>
            </Box>
        </Card>
    );
};

export default CartItem;
