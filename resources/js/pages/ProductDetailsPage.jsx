import React, { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
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
    Breadcrumbs,
    Link,
} from '@mui/material';
import {
    ShoppingCart,
    ArrowBack,
    Add,
    Remove,
} from '@mui/icons-material';
import { useSnackbar } from 'notistack';
import apiService from '../services/apiService';
import { useCart } from '../contexts/CartContext';

const ProductDetailsPage = () => {
    const { slug } = useParams();
    const navigate = useNavigate();
    const { addToCart } = useCart();
    const { enqueueSnackbar } = useSnackbar();

    const [product, setProduct] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');
    const [quantity, setQuantity] = useState(1);
    const [reviews, setReviews] = useState([]);

    useEffect(() => {
        loadProduct();
    }, [slug]);

    const loadProduct = async () => {
        try {
            setLoading(true);
            setError('');

            // Try to fetch by slug first, then by ID
            let response;
            try {
                response = await apiService.getProductBySlug(slug);
            } catch (slugError) {
                // If slug fails, try ID
                response = await apiService.getProduct(slug);
            }

            setProduct(response.data);

            // Load reviews
            try {
                const reviewsResponse = await apiService.getProductReviews(response.data.id);
                setReviews(reviewsResponse.data || []);
            } catch (reviewError) {
                console.error('Failed to load reviews:', reviewError);
            }
        } catch (err) {
            console.error('Failed to load product:', err);
            setError('Failed to load product details. Please try again.');
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
                variant: 'success'
            });
        }
    };

    const handleBackToProducts = () => {
        navigate('/products');
    };

    if (loading) {
        return (
            <Box display="flex" justifyContent="center" alignItems="center" minHeight="60vh">
                <CircularProgress />
            </Box>
        );
    }

    if (error) {
        return (
            <Alert severity="error" sx={{ mt: 2 }}>
                {error}
                <Button onClick={handleBackToProducts} sx={{ ml: 2 }}>
                    Back to Products
                </Button>
            </Alert>
        );
    }

    if (!product) {
        return (
            <Alert severity="warning" sx={{ mt: 2 }}>
                Product not found.
                <Button onClick={handleBackToProducts} sx={{ ml: 2 }}>
                    Back to Products
                </Button>
            </Alert>
        );
    }

    return (
        <Box>
            {/* Breadcrumbs */}
            <Breadcrumbs sx={{ mb: 3 }}>
                <Link
                    color="inherit"
                    onClick={handleBackToProducts}
                    sx={{ cursor: 'pointer' }}
                >
                    Products
                </Link>
                <Typography color="text.primary">{product.name}</Typography>
            </Breadcrumbs>

            {/* Back Button */}
            <Button
                startIcon={<ArrowBack />}
                onClick={handleBackToProducts}
                sx={{ mb: 3 }}
            >
                Back to Products
            </Button>

            <Grid container spacing={4}>
                {/* Product Image */}
                <Grid item xs={12} md={6}>
                    <Card>
                        <CardMedia
                            component="img"
                            image={product.image || '/api/placeholder/600/400'}
                            alt={product.name}
                            sx={{
                                height: { xs: 300, md: 500 },
                                objectFit: 'cover'
                            }}
                        />
                    </Card>
                </Grid>

                {/* Product Details */}
                <Grid item xs={12} md={6}>
                    <Box>
                        <Typography variant="h4" gutterBottom>
                            {product.name}
                        </Typography>

                        <Typography variant="h5" color="primary" gutterBottom>
                            ${product.price}
                        </Typography>

                        <Box sx={{ mb: 3 }}>
                            <Typography variant="body2" color="text.secondary">
                                Category: {product.category?.name || 'N/A'}
                            </Typography>
                            <Typography variant="body2" color="text.secondary">
                                Stock: {product.stock || 0} items available
                            </Typography>
                            <Typography variant="body2" color="text.secondary">
                                SKU: {product.sku || 'N/A'}
                            </Typography>
                        </Box>

                        <Divider sx={{ mb: 3 }} />

                        <Typography variant="body1" paragraph>
                            {product.description || 'No description available.'}
                        </Typography>

                        <Divider sx={{ mb: 3 }} />

                        {/* Quantity Selection */}
                        <Box sx={{ mb: 3 }}>
                            <Typography variant="subtitle1" gutterBottom>
                                Quantity
                            </Typography>
                            <Box sx={{ display: 'flex', alignItems: 'center', gap: 2 }}>
                                <IconButton
                                    onClick={() => handleQuantityChange(-1)}
                                    disabled={quantity <= 1}
                                >
                                    <Remove />
                                </IconButton>
                                <TextField
                                    type="number"
                                    value={quantity}
                                    onChange={(e) => {
                                        const val = parseInt(e.target.value) || 1;
                                        if (val >= 1 && val <= (product.stock || 0)) {
                                            setQuantity(val);
                                        }
                                    }}
                                    inputProps={{
                                        min: 1,
                                        max: product.stock || 0,
                                        style: { textAlign: 'center', width: '60px' }
                                    }}
                                />
                                <IconButton
                                    onClick={() => handleQuantityChange(1)}
                                    disabled={quantity >= (product.stock || 0)}
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
                            disabled={!product.stock || quantity > product.stock}
                            sx={{
                                width: { xs: '100%', sm: 'auto' },
                                backgroundColor: 'black',
                                '&:hover': { backgroundColor: '#333' },
                                py: 1.5,
                                px: 4,
                            }}
                        >
                            Add to Cart
                        </Button>

                        {(!product.stock || product.stock === 0) && (
                            <Alert severity="warning" sx={{ mt: 2 }}>
                                This product is currently out of stock.
                            </Alert>
                        )}
                    </Box>
                </Grid>
            </Grid>

            {/* Product Reviews Section */}
            {reviews.length > 0 && (
                <Box sx={{ mt: 6 }}>
                    <Typography variant="h5" gutterBottom>
                        Customer Reviews
                    </Typography>
                    <Grid container spacing={2}>
                        {reviews.slice(0, 3).map((review) => (
                            <Grid item xs={12} md={4} key={review.id}>
                                <Paper sx={{ p: 2 }}>
                                    <Typography variant="subtitle2" gutterBottom>
                                        {review.user?.name || 'Anonymous'}
                                    </Typography>
                                    <Typography variant="body2" color="text.secondary">
                                        Rating: {review.rating}/5
                                    </Typography>
                                    <Typography variant="body2" sx={{ mt: 1 }}>
                                        {review.comment}
                                    </Typography>
                                </Paper>
                            </Grid>
                        ))}
                    </Grid>
                </Box>
            )}
        </Box>
    );
};

export default ProductDetailsPage;
