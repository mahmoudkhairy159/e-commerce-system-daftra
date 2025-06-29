import React from "react";
import { useNavigate } from "react-router-dom";
import {
    Grid,
    Typography,
    Box,
    IconButton,
    Chip,
    CircularProgress,
} from "@mui/material";
import { Add, Remove } from "@mui/icons-material";

const ProductCard = ({
    product,
    quantity,
    currentPage,
    perPage,
    productQuantities,
    onQuantityChange,
    loading = false,
}) => {
    const navigate = useNavigate();

    const handleQuantityChange = (productId, change) => {
        onQuantityChange(productId, change);
    };


    return (
        <Grid item xs={12} sm={6} md={4} key={product.id}>
            <Box sx={{ position: "relative", mb: 2 }}>
                <Box
                    component="img"
                    src={
                        product.image_url ||
                        "https://images.unsplash.com/photo-1520607162513-77705c0f0d4a?w=400&h=400&fit=crop"
                    }
                    alt={product.name}
                    sx={{
                        width: "100%",
                        height: 300,
                        objectFit: "cover",
                        borderRadius: 2,
                        cursor: "pointer",
                        "&:hover": { opacity: 0.9 },
                    }}
                    onClick={() => navigate(`/products/${product.slug}`)}
                />

                {/* Cart Quantity Badge */}
                {(productQuantities[product.id] || 0) > 0 && (
                    <Box
                        sx={{
                            position: "absolute",
                            bottom: 8,
                            right: 8,
                            bgcolor: "#2196f3",
                            color: "white",
                            borderRadius: "50%",
                            width: 24,
                            height: 24,
                            display: "flex",
                            alignItems: "center",
                            justifyContent: "center",
                            fontSize: "12px",
                            fontWeight: "bold",
                        }}
                    >
                        {productQuantities[product.id]}
                    </Box>
                )}

                {/* Sale Badge */}
                {product.offer_price && product.offer_price < product.price && (
                    <Box
                        sx={{
                            position: "absolute",
                            top: 8,
                            left: 8,
                            bgcolor: "#ff4444",
                            color: "white",
                            borderRadius: 1,
                            px: 1,
                            py: 0.5,
                            fontSize: "12px",
                            fontWeight: "bold",
                        }}
                    >
                        SALE
                    </Box>
                )}
            </Box>

            {/* Product Info */}
            <Typography
                variant="h6"
                sx={{ fontWeight: 500, fontSize: "16px", mb: 0.5 }}
            >
                {product.name.length > 30
                    ? product.name.substring(0, 30) + "..."
                    : product.name}
            </Typography>

            <Chip
                label={product.code}
                size="small"
                sx={{
                    fontSize: "12px",
                    height: 20,
                    backgroundColor: "#f0f0f0",
                    color: "#666",
                    mb: 1,
                }}
            />

            {/* Price Display */}
            <Box
                sx={{ display: "flex", alignItems: "center", gap: 1, mb: 0.5 }}
            >
                {product.offer_price && product.offer_price < product.price ? (
                    <>
                        <Typography
                            variant="h6"
                            sx={{
                                fontWeight: 600,
                                fontSize: "16px",
                                color: "#ff4444",
                            }}
                        >
                            ${product.offer_price}
                        </Typography>
                        <Typography
                            variant="body2"
                            sx={{
                                fontSize: "14px",
                                color: "#999",
                                textDecoration: "line-through",
                            }}
                        >
                            ${product.price}
                        </Typography>
                    </>
                ) : (
                    <Typography
                        variant="h6"
                        sx={{ fontWeight: 600, fontSize: "16px" }}
                    >
                        ${product.price}
                    </Typography>
                )}
            </Box>

            {/* Stock Status */}
            <Typography
                variant="body2"
                sx={{ color: "#666", fontSize: "12px", mb: 2 }}
            >
                Stock: {product.stock > 0 ? product.stock : "Out of Stock"}
            </Typography>

            {/* Quantity Controls */}
            <Box
                sx={{
                    display: "flex",
                    alignItems: "center",
                    justifyContent: "center",
                }}
            >
                <IconButton
                    size="small"
                    sx={{ border: "1px solid #ddd", borderRadius: 0 }}
                    onClick={() => handleQuantityChange(product.id, -1)}
                    disabled={
                        product.stock === 0 ||
                        loading ||
                        (productQuantities[product.id] || 0) === 0
                    }
                >
                    {loading ? (
                        <CircularProgress size={16} />
                    ) : (
                        <Remove fontSize="small" />
                    )}
                </IconButton>
                <Typography
                    sx={{
                        px: 2,
                        py: 1,
                        border: "1px solid #ddd",
                        borderLeft: 0,
                        borderRight: 0,
                        minWidth: 40,
                        textAlign: "center",
                        fontSize: "14px",
                        opacity: loading ? 0.6 : 1,
                    }}
                >
                    {productQuantities[product.id] || 0}
                </Typography>
                <IconButton
                    size="small"
                    sx={{ border: "1px solid #ddd", borderRadius: 0 }}
                    onClick={() => handleQuantityChange(product.id, 1)}
                    disabled={product.stock === 0 || loading}
                >
                    {loading ? (
                        <CircularProgress size={16} />
                    ) : (
                        <Add fontSize="small" />
                    )}
                </IconButton>
            </Box>
        </Grid>
    );
};

export default ProductCard;
