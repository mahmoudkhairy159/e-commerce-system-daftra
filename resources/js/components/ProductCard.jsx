import React from "react";
import { useNavigate } from "react-router-dom";
import {
    Grid,
    Typography,
    Box,
    IconButton,
    Chip,
    CircularProgress,
    useMediaQuery,
    useTheme,
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
    const theme = useTheme();
    const isMobile = useMediaQuery(theme.breakpoints.down("sm"));

    const handleQuantityChange = (productId, change) => {
        onQuantityChange(productId, change);
    };

    return (
        <Grid item xs={12} sm={6} lg={4} key={product.id}>
            <Box
                sx={{
                    position: "relative",
                    mb: 2,
                    maxWidth: { xs: "100%", sm: "280px" },
                    mx: "auto"
                }}
            >
                <Box
                    component="img"
                    src={product.image_url || "/default.jpg"}
                    alt={product.name}
                    sx={{
                        width: "100%",
                        height: { xs: 250, sm: 300 },
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
                            width: { xs: 28, sm: 24 },
                            height: { xs: 28, sm: 24 },
                            display: "flex",
                            alignItems: "center",
                            justifyContent: "center",
                            fontSize: { xs: "14px", sm: "12px" },
                            fontWeight: "bold",
                            boxShadow: "0 2px 8px rgba(0,0,0,0.15)",
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
                            px: { xs: 1.5, sm: 1 },
                            py: { xs: 0.7, sm: 0.5 },
                            fontSize: { xs: "13px", sm: "12px" },
                            fontWeight: "bold",
                            boxShadow: "0 2px 8px rgba(0,0,0,0.15)",
                        }}
                    >
                        SALE
                    </Box>
                )}
            </Box>

            {/* Product Info */}
            <Typography
                variant="h6"
                sx={{
                    fontWeight: 500,
                    fontSize: { xs: "15px", sm: "16px" },
                    mb: 0.5,
                    lineHeight: 1.3,
                    cursor: "pointer",
                    "&:hover": { color: "#2196f3" }
                }}
                onClick={() => navigate(`/products/${product.slug}`)}
            >
                {isMobile
                    ? (product.name.length > 25 ? product.name.substring(0, 25) + "..." : product.name)
                    : (product.name.length > 30 ? product.name.substring(0, 30) + "..." : product.name)
                }
            </Typography>

            <Chip
                label={product.code}
                size="small"
                sx={{
                    fontSize: { xs: "11px", sm: "12px" },
                    height: { xs: 22, sm: 20 },
                    backgroundColor: "#f0f0f0",
                    color: "#666",
                    mb: 1,
                }}
            />

            {/* Price Display */}
            <Box
                sx={{
                    display: "flex",
                    alignItems: "center",
                    gap: 1,
                    mb: 0.5,
                    flexWrap: "wrap"
                }}
            >
                {product.offer_price && product.offer_price < product.price ? (
                    <>
                        <Typography
                            variant="h6"
                            sx={{
                                fontWeight: 600,
                                fontSize: { xs: "17px", sm: "16px" },
                                color: "#ff4444",
                            }}
                        >
                            ${product.offer_price}
                        </Typography>
                        <Typography
                            variant="body2"
                            sx={{
                                fontSize: { xs: "13px", sm: "14px" },
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
                        sx={{
                            fontWeight: 600,
                            fontSize: { xs: "17px", sm: "16px" }
                        }}
                    >
                        ${product.price}
                    </Typography>
                )}
            </Box>

            {/* Stock Status */}
            <Typography
                variant="body2"
                sx={{
                    color: product.stock > 0 ? "#666" : "#ff4444",
                    fontSize: { xs: "11px", sm: "12px" },
                    mb: 2,
                    fontWeight: product.stock > 0 ? 400 : 500
                }}
            >
                Stock: {product.stock > 0 ? product.stock : "Out of Stock"}
            </Typography>

            {/* Quantity Controls */}
            <Box
                sx={{
                    display: "flex",
                    alignItems: "center",
                    justifyContent: "center",
                    gap: 0,
                }}
            >
                <IconButton
                    size={isMobile ? "medium" : "small"}
                    sx={{
                        border: "1px solid #ddd",
                        borderRadius: { xs: 1, sm: 0 },
                        borderTopRightRadius: 0,
                        borderBottomRightRadius: 0,
                        width: { xs: 44, sm: "auto" },
                        height: { xs: 44, sm: "auto" },
                        "&:hover": {
                            backgroundColor: "#f5f5f5",
                            borderColor: "#999"
                        }
                    }}
                    onClick={() => handleQuantityChange(product.id, -1)}
                    disabled={
                        product.stock === 0 ||
                        loading ||
                        (productQuantities[product.id] || 0) === 0
                    }
                >
                    {loading ? (
                        <CircularProgress size={isMobile ? 20 : 16} />
                    ) : (
                        <Remove fontSize={isMobile ? "medium" : "small"} />
                    )}
                </IconButton>
                <Typography
                    sx={{
                        px: { xs: 2.5, sm: 2 },
                        py: { xs: 1.5, sm: 1 },
                        border: "1px solid #ddd",
                        borderLeft: 0,
                        borderRight: 0,
                        minWidth: { xs: 50, sm: 40 },
                        textAlign: "center",
                        fontSize: { xs: "16px", sm: "14px" },
                        fontWeight: 500,
                        opacity: loading ? 0.6 : 1,
                        backgroundColor: "#fafafa",
                        display: "flex",
                        alignItems: "center",
                        justifyContent: "center",
                        height: { xs: 44, sm: "auto" },
                    }}
                >
                    {productQuantities[product.id] || 0}
                </Typography>
                <IconButton
                    size={isMobile ? "medium" : "small"}
                    sx={{
                        border: "1px solid #ddd",
                        borderRadius: { xs: 1, sm: 0 },
                        borderTopLeftRadius: 0,
                        borderBottomLeftRadius: 0,
                        width: { xs: 44, sm: "auto" },
                        height: { xs: 44, sm: "auto" },
                        "&:hover": {
                            backgroundColor: "#f5f5f5",
                            borderColor: "#999"
                        },
                        "&:disabled": {
                            backgroundColor: product.stock === 0 ? "#ffebee" : "inherit"
                        }
                    }}
                    onClick={() => handleQuantityChange(product.id, 1)}
                    disabled={product.stock === 0 || loading}
                >
                    {loading ? (
                        <CircularProgress size={isMobile ? 20 : 16} />
                    ) : (
                        <Add fontSize={isMobile ? "medium" : "small"} />
                    )}
                </IconButton>
            </Box>
        </Grid>
    );
};

export default ProductCard;
