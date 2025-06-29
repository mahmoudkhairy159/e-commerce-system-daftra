import React, { useState, useEffect } from "react";
import { useParams, useNavigate } from "react-router-dom";
import {
    Grid,
    Card,
    CardMedia,
    Typography,
    Button,
    Box,
    Paper,
    Divider,
    CircularProgress,
    Alert,
    TextField,
    IconButton,
    Link,
    Chip,
    Container,
    Rating,
    Badge,
} from "@mui/material";
import {
    ShoppingCart,
    ArrowBack,
    Add,
    Remove,
    LocalOffer,
    Inventory,
    Category,
    Info,
    KeyboardArrowRight,
} from "@mui/icons-material";
import { useSnackbar } from "notistack";
import apiService from "../services/apiService";
import { useCart } from "../contexts/CartContext";

const ProductDetailsPage = () => {
    const { slug } = useParams();
    const navigate = useNavigate();
    const { addToCart } = useCart();
    const { enqueueSnackbar } = useSnackbar();

    const [product, setProduct] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState("");
    const [quantity, setQuantity] = useState(1);
    const [selectedImage, setSelectedImage] = useState(0);

    useEffect(() => {
        loadProduct();
    }, [slug]);

    const loadProduct = async () => {
        try {
            setLoading(true);
            setError("");

            const response = await apiService.getProductBySlug(slug);
            setProduct(response.data.data); // Note: API returns data.data structure
        } catch (err) {
            console.error("Failed to load product:", err);
            setError("Failed to load product details. Please try again.");
        } finally {
            setLoading(false);
        }
    };

    const handleQuantityChange = (change) => {
        const newQuantity = quantity + change;
        if (newQuantity >= 1 && newQuantity <= (product?.stock || 0)) {
            setQuantity(newQuantity);
        }
    };

    const handleAddToCart = async () => {
        if (!product) return;

        const success = await addToCart(product.id, quantity);
        if (success) {
            enqueueSnackbar(`${quantity} x ${product.name} added to cart`, {
                variant: "success",
            });
        }
    };

    const handleBackToProducts = () => {
        navigate("/products");
    };

    const handleRelatedProductClick = (productSlug) => {
        navigate(`/products/${productSlug}`);
    };

    const calculateDiscountPercentage = () => {
        if (product?.offer_price && product?.price) {
            return Math.round(
                ((product.price - product.offer_price) / product.price) * 100
            );
        }
        return 0;
    };

    const getCurrentPrice = () => {
        return product?.offer_price || product?.price || 0;
    };

    const hasDiscount = () => {
        return product?.offer_price && product?.offer_price < product?.price;
    };

    const isOfferValid = () => {
        if (!product?.offer_start_date || !product?.offer_end_date)
            return false;
        const now = new Date();
        const startDate = new Date(product.offer_start_date);
        const endDate = new Date(product.offer_end_date);
        return now >= startDate && now <= endDate;
    };

    if (loading) {
        return (
            <Container maxWidth="lg" sx={{ py: 4 }}>
                <Box
                    display="flex"
                    justifyContent="center"
                    alignItems="center"
                    minHeight="60vh"
                >
                    <CircularProgress size={60} />
                </Box>
            </Container>
        );
    }

    if (error) {
        return (
            <Container maxWidth="lg" sx={{ py: 4 }}>
                <Alert severity="error" sx={{ mt: 2 }}>
                    {error}
                    <Button onClick={handleBackToProducts} sx={{ ml: 2 }}>
                        Back to Products
                    </Button>
                </Alert>
            </Container>
        );
    }

    if (!product) {
        return (
            <Container maxWidth="lg" sx={{ py: 4 }}>
                <Alert severity="warning" sx={{ mt: 2 }}>
                    Product not found.
                    <Button onClick={handleBackToProducts} sx={{ ml: 2 }}>
                        Back to Products
                    </Button>
                </Alert>
            </Container>
        );
    }

    return (
        <Container maxWidth="lg" sx={{ py: 4 }}>
            {/* Back Button */}
            <Button
                startIcon={<ArrowBack />}
                onClick={handleBackToProducts}
                sx={{
                    mb: 4,
                    color: "text.secondary",
                    "&:hover": { color: "primary.main" },
                }}
            >
                Back to Products
            </Button>

            <Grid container spacing={6}>
                {/* Product Images */}
                <Grid item xs={12} md={6}>
                    <Box sx={{ position: "relative" }}>
                        {/* Discount Badge Above Image */}
                        {hasDiscount() && isOfferValid() && (
                            <Box
                                sx={{
                                    position: "absolute",
                                    top: -12,
                                    right: 16,
                                    zIndex: 10,
                                    bgcolor: "error.main",
                                    color: "white",
                                    px: 2,
                                    py: 1,
                                    borderRadius: "20px",
                                    fontWeight: "bold",
                                    fontSize: "1rem",
                                    boxShadow: "0 4px 12px rgba(0,0,0,0.15)",
                                    border: "3px solid white",
                                    display: "flex",
                                    alignItems: "center",
                                    justifyContent: "center",
                                    minWidth: "60px",
                                    height: "60px",
                                    background:
                                        "linear-gradient(135deg, #ff4444 0%, #cc0000 100%)",
                                    transform: "rotate(-10deg)",
                                    "&::before": {
                                        content: '""',
                                        position: "absolute",
                                        top: "50%",
                                        left: "50%",
                                        transform: "translate(-50%, -50%)",
                                        width: "100%",
                                        height: "100%",
                                        borderRadius: "50%",
                                        background:
                                            "radial-gradient(circle at 30% 30%, rgba(255,255,255,0.3) 0%, transparent 50%)",
                                        pointerEvents: "none",
                                    },
                                }}
                            >
                                <Box
                                    sx={{
                                        display: "flex",
                                        flexDirection: "column",
                                        alignItems: "center",
                                        lineHeight: 1,
                                        zIndex: 1,
                                    }}
                                >
                                    <Typography
                                        sx={{
                                            fontSize: "0.75rem",
                                            fontWeight: "600",
                                            textTransform: "uppercase",
                                            letterSpacing: "0.5px",
                                        }}
                                    >
                                        Save
                                    </Typography>
                                    <Typography
                                        sx={{
                                            fontSize: "1.1rem",
                                            fontWeight: "bold",
                                            lineHeight: 1,
                                        }}
                                    >
                                        {calculateDiscountPercentage()}%
                                    </Typography>
                                </Box>
                            </Box>
                        )}

                        <Card
                            elevation={0}
                            sx={{
                                borderRadius: 2,
                                position: "relative",
                                overflow: "visible",
                            }}
                        >
                            <CardMedia
                                component="img"
                                image={product.image_url || "/default.jpg"}
                                alt={product.name}
                                sx={{
                                    height: { xs: 400, md: 600 },
                                    objectFit: "cover",
                                    borderRadius: 2,
                                }}
                            />

                            {/* Corner Sale Tag */}
                            {hasDiscount() && isOfferValid() && (
                                <Box
                                    sx={{
                                        position: "absolute",
                                        top: 16,
                                        left: 16,
                                        bgcolor: "success.main",
                                        color: "white",
                                        px: 2,
                                        py: 0.5,
                                        borderRadius: "4px",
                                        fontWeight: "bold",
                                        fontSize: "0.875rem",
                                        boxShadow: "0 2px 8px rgba(0,0,0,0.2)",
                                        textTransform: "uppercase",
                                        letterSpacing: "0.5px",
                                    }}
                                >
                                    Sale
                                </Box>
                            )}
                        </Card>
                    </Box>
                </Grid>

                {/* Product Details */}
                <Grid item xs={12} md={6}>
                    <Box>
                        {/* Product Category */}
                        {product.categories?.length > 0 && (
                            <Chip
                                icon={<Category />}
                                label={product.categories[0]}
                                variant="outlined"
                                size="small"
                                sx={{ mb: 2 }}
                            />
                        )}

                        {/* Product Name */}
                        <Typography
                            variant="h4"
                            gutterBottom
                            fontWeight="bold"
                            sx={{ color: "text.primary" }}
                        >
                            {product.name}
                        </Typography>

                        {/* Short Description */}
                        {product.short_description && (
                            <Typography
                                variant="body1"
                                color="text.secondary"
                                sx={{ mb: 3 }}
                            >
                                {product.short_description}
                            </Typography>
                        )}

                        {/* Price Section */}
                        <Box sx={{ mb: 4 }}>
                            <Box
                                sx={{
                                    display: "flex",
                                    alignItems: "center",
                                    gap: 2,
                                    mb: 1,
                                }}
                            >
                                <Typography
                                    variant="h4"
                                    color="primary"
                                    fontWeight="bold"
                                >
                                    ${getCurrentPrice()}
                                </Typography>
                                {hasDiscount() && (
                                    <Typography
                                        variant="h5"
                                        color="text.secondary"
                                        sx={{ textDecoration: "line-through" }}
                                    >
                                        ${product.price}
                                    </Typography>
                                )}
                                {hasDiscount() && isOfferValid() && (
                                    <Chip
                                        icon={<LocalOffer />}
                                        label={`Save $${
                                            product.price - product.offer_price
                                        }`}
                                        color="success"
                                        size="small"
                                    />
                                )}
                            </Box>
                            <Typography variant="body2" color="text.secondary">
                                {product.currency} • Code: {product.code}
                            </Typography>
                        </Box>

                        {/* Stock Information */}
                        <Box sx={{ mb: 3 }}>
                            <Box
                                sx={{
                                    display: "flex",
                                    alignItems: "center",
                                    gap: 1,
                                    mb: 1,
                                }}
                            >
                                <Inventory fontSize="small" color="action" />
                                <Typography
                                    variant="body2"
                                    color="text.secondary"
                                >
                                    Stock:
                                </Typography>
                                <Chip
                                    label={`${
                                        product.stock || 0
                                    } items available`}
                                    size="small"
                                    color={
                                        product.stock > 10
                                            ? "success"
                                            : product.stock > 0
                                            ? "warning"
                                            : "error"
                                    }
                                />
                            </Box>
                        </Box>

                        <Divider sx={{ mb: 3 }} />

                        {/* Quantity Selection */}
                        <Box sx={{ mb: 4 }}>
                            <Typography
                                variant="subtitle1"
                                gutterBottom
                                fontWeight="medium"
                            >
                                Quantity
                            </Typography>
                            <Box
                                sx={{
                                    display: "flex",
                                    alignItems: "center",
                                    gap: 2,
                                }}
                            >
                                <IconButton
                                    onClick={() => handleQuantityChange(-1)}
                                    disabled={quantity <= 1}
                                    sx={{
                                        border: "1px solid",
                                        borderColor: "divider",
                                        borderRadius: 1,
                                    }}
                                >
                                    <Remove />
                                </IconButton>
                                <TextField
                                    type="number"
                                    value={quantity}
                                    onChange={(e) => {
                                        const val =
                                            parseInt(e.target.value) || 1;
                                        if (
                                            val >= 1 &&
                                            val <= (product.stock || 0)
                                        ) {
                                            setQuantity(val);
                                        }
                                    }}
                                    inputProps={{
                                        min: 1,
                                        max: product.stock || 0,
                                        style: {
                                            textAlign: "center",
                                            width: "60px",
                                        },
                                    }}
                                    size="small"
                                    sx={{
                                        "& .MuiOutlinedInput-root": {
                                            borderRadius: 1,
                                        },
                                    }}
                                />
                                <IconButton
                                    onClick={() => handleQuantityChange(1)}
                                    disabled={quantity >= (product.stock || 0)}
                                    sx={{
                                        border: "1px solid",
                                        borderColor: "divider",
                                        borderRadius: 1,
                                    }}
                                >
                                    <Add />
                                </IconButton>
                            </Box>
                        </Box>

                        {/* Add to Cart Button */}
                        <Button
                            variant="contained"
                            size="large"
                            startIcon={<ShoppingCart />}
                            onClick={handleAddToCart}
                            disabled={
                                !product.stock || quantity > product.stock
                            }
                            sx={{
                                width: "100%",
                                backgroundColor: "black",
                                "&:hover": { backgroundColor: "#333" },
                                py: 1.5,
                                borderRadius: 2,
                                textTransform: "none",
                                fontSize: "1.1rem",
                                fontWeight: "bold",
                            }}
                        >
                            Add to Cart
                        </Button>

                        {(!product.stock || product.stock === 0) && (
                            <Alert
                                severity="warning"
                                sx={{ mt: 2, borderRadius: 2 }}
                            >
                                This product is currently out of stock.
                            </Alert>
                        )}

                        {/* Return Policy */}
                        {product.return_policy && (
                            <Box
                                sx={{
                                    mt: 3,
                                    p: 2,
                                    bgcolor: "grey.50",
                                    borderRadius: 2,
                                }}
                            >
                                <Box
                                    sx={{
                                        display: "flex",
                                        alignItems: "center",
                                        gap: 1,
                                        mb: 1,
                                    }}
                                >
                                    <Info fontSize="small" color="action" />
                                    <Typography
                                        variant="subtitle2"
                                        fontWeight="medium"
                                    >
                                        Return Policy
                                    </Typography>
                                </Box>
                                <Typography
                                    variant="body2"
                                    color="text.secondary"
                                >
                                    {product.return_policy}
                                </Typography>
                            </Box>
                        )}
                    </Box>
                </Grid>
            </Grid>

            {/* Product Description */}
            {product.long_description && (
                <Box sx={{ mt: 6 }}>
                    <Typography variant="h5" gutterBottom fontWeight="bold">
                        Product Description
                    </Typography>
                    <Paper sx={{ p: 3, borderRadius: 2 }}>
                        <Typography variant="body1" sx={{ lineHeight: 1.7 }}>
                            {product.long_description}
                        </Typography>
                    </Paper>
                </Box>
            )}

            {/* Related Products */}
            {product.related_products?.length > 0 && (
                <Box sx={{ mt: 6 }}>
                    <Typography variant="h5" gutterBottom fontWeight="bold">
                        Related Products
                    </Typography>
                    <Grid container spacing={3}>
                        {product.related_products.map((relatedProduct) => (
                            <Grid
                                item
                                xs={12}
                                sm={6}
                                md={4}
                                key={relatedProduct.id}
                            >
                                <Card
                                    sx={{
                                        cursor: "pointer",
                                        transition: "all 0.3s ease-in-out",
                                        "&:hover": {
                                            transform: "translateY(-8px)",
                                            boxShadow:
                                                "0 20px 40px rgba(0,0,0,0.1)",
                                        },
                                        borderRadius: 3,
                                        overflow: "hidden",
                                        height: "100%",
                                        display: "flex",
                                        flexDirection: "column",
                                        border: "1px solid rgba(0,0,0,0.08)",
                                        background:
                                            "linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%)",
                                    }}
                                    onClick={() =>
                                        handleRelatedProductClick(
                                            relatedProduct.slug
                                        )
                                    }
                                >
                                    <Box
                                        sx={{
                                            position: "relative",
                                            overflow: "hidden",
                                        }}
                                    >
                                        <CardMedia
                                            component="img"
                                            height="250"
                                            image={
                                                relatedProduct.image_url ||
                                                "/default.jpg"
                                            }
                                            alt={relatedProduct.name}
                                            sx={{
                                                objectFit: "cover",
                                                transition:
                                                    "transform 0.3s ease-in-out",
                                                "&:hover": {
                                                    transform: "scale(1.05)",
                                                },
                                            }}
                                        />

                                        {/* Discount Badge for Related Products */}
                                        {relatedProduct.offer_price &&
                                            relatedProduct.offer_price <
                                                relatedProduct.price && (
                                                <Box
                                                    sx={{
                                                        position: "absolute",
                                                        top: 12,
                                                        right: 12,
                                                        bgcolor: "error.main",
                                                        color: "white",
                                                        px: 1.5,
                                                        py: 0.5,
                                                        borderRadius: "12px",
                                                        fontSize: "0.75rem",
                                                        fontWeight: "bold",
                                                        boxShadow:
                                                            "0 2px 8px rgba(0,0,0,0.2)",
                                                        zIndex: 2,
                                                    }}
                                                >
                                                    -
                                                    {Math.round(
                                                        ((relatedProduct.price -
                                                            relatedProduct.offer_price) /
                                                            relatedProduct.price) *
                                                            100
                                                    )}
                                                    %
                                                </Box>
                                            )}

                                        {/* Gradient Overlay */}
                                        <Box
                                            sx={{
                                                position: "absolute",
                                                bottom: 0,
                                                left: 0,
                                                right: 0,
                                                height: "60px",
                                                background:
                                                    "linear-gradient(transparent, rgba(0,0,0,0.1))",
                                                pointerEvents: "none",
                                            }}
                                        />
                                    </Box>

                                    <Box
                                        sx={{
                                            p: 3,
                                            flexGrow: 1,
                                            display: "flex",
                                            flexDirection: "column",
                                            justifyContent: "space-between",
                                        }}
                                    >
                                        <Box>
                                            <Typography
                                                variant="h6"
                                                gutterBottom
                                                sx={{
                                                    fontWeight: "600",
                                                    fontSize: "1.1rem",
                                                    lineHeight: 1.3,
                                                    height: "2.6em",
                                                    overflow: "hidden",
                                                    display: "-webkit-box",
                                                    WebkitLineClamp: 2,
                                                    WebkitBoxOrient: "vertical",
                                                    mb: 2,
                                                }}
                                            >
                                                {relatedProduct.name}
                                            </Typography>
                                        </Box>

                                        <Box>
                                            <Box
                                                sx={{
                                                    display: "flex",
                                                    alignItems: "center",
                                                    gap: 1,
                                                    mb: 1,
                                                }}
                                            >
                                                <Typography
                                                    variant="h6"
                                                    color="primary"
                                                    fontWeight="bold"
                                                    sx={{ fontSize: "1.25rem" }}
                                                >
                                                    $
                                                    {relatedProduct.offer_price ||
                                                        relatedProduct.price}
                                                </Typography>
                                                {relatedProduct.offer_price &&
                                                    relatedProduct.offer_price <
                                                        relatedProduct.price && (
                                                        <Typography
                                                            variant="body2"
                                                            color="text.secondary"
                                                            sx={{
                                                                textDecoration:
                                                                    "line-through",
                                                                fontSize:
                                                                    "1rem",
                                                            }}
                                                        >
                                                            $
                                                            {
                                                                relatedProduct.price
                                                            }
                                                        </Typography>
                                                    )}
                                            </Box>

                                            <Box
                                                sx={{
                                                    display: "flex",
                                                    alignItems: "center",
                                                    gap: 1,
                                                }}
                                            >
                                                <Inventory
                                                    fontSize="small"
                                                    sx={{
                                                        color: "text.secondary",
                                                        fontSize: "1rem",
                                                    }}
                                                />
                                                <Typography
                                                    variant="body2"
                                                    color="text.secondary"
                                                    sx={{
                                                        fontSize: "0.875rem",
                                                    }}
                                                >
                                                    {relatedProduct.stock} in
                                                    stock
                                                </Typography>
                                            </Box>
                                        </Box>
                                    </Box>
                                </Card>
                            </Grid>
                        ))}
                    </Grid>
                </Box>
            )}

            {/* Accessories */}
            {product.accessories?.length > 0 && (
                <Box sx={{ mt: 6 }}>
                    <Typography variant="h5" gutterBottom fontWeight="bold">
                        Recommended Accessories
                    </Typography>
                    <Grid container spacing={3}>
                        {product.accessories.map((accessory) => (
                            <Grid item xs={12} sm={6} md={4} key={accessory.id}>
                                <Card
                                    sx={{
                                        borderRadius: 3,
                                        overflow: "hidden",
                                        height: "100%",
                                        display: "flex",
                                        flexDirection: "column",
                                        border: "1px solid rgba(0,0,0,0.08)",
                                        background:
                                            "linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%)",
                                        transition: "all 0.3s ease-in-out",
                                        "&:hover": {
                                            transform: "translateY(-4px)",
                                            boxShadow:
                                                "0 15px 30px rgba(0,0,0,0.1)",
                                        },
                                    }}
                                >
                                    <Box
                                        sx={{
                                            position: "relative",
                                            overflow: "hidden",
                                        }}
                                    >
                                        <CardMedia
                                            component="img"
                                            height="200"
                                            image={
                                                accessory.image_url ||
                                                "/default.jpg"
                                            }
                                            alt={accessory.name}
                                            sx={{
                                                objectFit: "cover",
                                                transition:
                                                    "transform 0.3s ease-in-out",
                                                "&:hover": {
                                                    transform: "scale(1.03)",
                                                },
                                            }}
                                        />

                                        {/* Gradient Overlay */}
                                        <Box
                                            sx={{
                                                position: "absolute",
                                                bottom: 0,
                                                left: 0,
                                                right: 0,
                                                height: "40px",
                                                background:
                                                    "linear-gradient(transparent, rgba(0,0,0,0.08))",
                                                pointerEvents: "none",
                                            }}
                                        />
                                    </Box>

                                    <Box
                                        sx={{
                                            p: 3,
                                            flexGrow: 1,
                                            display: "flex",
                                            flexDirection: "column",
                                            justifyContent: "space-between",
                                        }}
                                    >
                                        <Typography
                                            variant="h6"
                                            gutterBottom
                                            sx={{
                                                fontWeight: "600",
                                                fontSize: "1.1rem",
                                                lineHeight: 1.3,
                                                mb: 2,
                                                height: "2.6em",
                                                overflow: "hidden",
                                                display: "-webkit-box",
                                                WebkitLineClamp: 2,
                                                WebkitBoxOrient: "vertical",
                                            }}
                                        >
                                            {accessory.name}
                                        </Typography>
                                        <Typography
                                            variant="h6"
                                            color="primary"
                                            fontWeight="bold"
                                            sx={{ fontSize: "1.25rem" }}
                                        >
                                            ${accessory.price}
                                        </Typography>
                                    </Box>
                                </Card>
                            </Grid>
                        ))}
                    </Grid>
                </Box>
            )}
        </Container>
    );
};

export default ProductDetailsPage;
