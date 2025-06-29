import React from "react";
import { Grid, Box } from "@mui/material";
import ProductCard from "../ProductCard";
import LoadingSpinner from "../UI/LoadingSpinner";
import ErrorMessage from "../UI/ErrorMessage";
import EmptyState from "../UI/EmptyState";

/**
 * Reusable product grid component
 * @param {Object} props - Component props
 * @param {Array} props.products - Array of products to display
 * @param {boolean} props.loading - Loading state
 * @param {string} props.error - Error message
 * @param {Function} props.onAddToCart - Add to cart handler
 * @param {Function} props.onQuantityChange - Quantity change handler
 * @param {Object} props.productQuantities - Product quantities state
 * @param {Function} props.onRetry - Retry function for errors
 * @param {Object} props.gridProps - Additional props for the Grid container
 */
const ProductGrid = ({
    products = [],
    loading = false,
    error = null,
    onAddToCart = null,
    onQuantityChange = null,
    productQuantities = {},
    onRetry = null,
    gridProps = {},
}) => {
    // Loading state
    if (loading) {
        return (
            <Box sx={{ py: 8 }}>
                <LoadingSpinner message="Loading products..." />
            </Box>
        );
    }

    // Error state
    if (error) {
        return (
            <ErrorMessage message={error} onRetry={onRetry} sx={{ my: 4 }} />
        );
    }

    // Empty state
    if (!products || products.length === 0) {
        return <EmptyState type="search" sx={{ py: 8 }} />;
    }

    return (
        <Grid container spacing={{ xs: 2, sm: 3 }} {...gridProps}>
            {products.map((product) => (
                <Grid item xs={12} sm={6} md={4} lg={4} key={product.id}>
                    <ProductCard
                        product={product}
                        quantity={productQuantities[product.id] || 1}
                        onQuantityChange={
                            onQuantityChange
                                ? (change) =>
                                      onQuantityChange(product.id, change)
                                : null
                        }
                        onAddToCart={
                            onAddToCart
                                ? () =>
                                      onAddToCart(
                                          product.id,
                                          productQuantities[product.id] || 1
                                      )
                                : null
                        }
                    />
                </Grid>
            ))}
        </Grid>
    );
};

export default ProductGrid;
