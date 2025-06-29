import React, { createContext, useContext, useState, useEffect } from 'react';
import { useSnackbar } from 'notistack';
import apiService from '../services/apiService';
import { useAuth } from './AuthContext';

const CartContext = createContext();

export const useCart = () => {
    const context = useContext(CartContext);
    if (!context) {
        throw new Error('useCart must be used within a CartProvider');
    }
    return context;
};

export const CartProvider = ({ children }) => {
    const [cart, setCart] = useState([]);
    const [loading, setLoading] = useState(false);
    const { isAuthenticated } = useAuth();
    const { enqueueSnackbar } = useSnackbar();

    useEffect(() => {
        if (isAuthenticated) {
            fetchCart();
        }
    }, [isAuthenticated]);

    const fetchCart = async () => {
        if (!isAuthenticated) return;

        try {
            setLoading(true);
            const response = await apiService.get('/cart/cart-products');

            // Ensure we always set an array
            const cartData = response.data;
            if (Array.isArray(cartData)) {
                setCart(cartData);
            } else if (cartData && Array.isArray(cartData.data)) {
                // Handle nested data structure
                setCart(cartData.data);
            } else {
                setCart([]);
            }
        } catch (error) {
            console.error('Failed to fetch cart:', error);
            setCart([]); // Ensure cart is always an array even on error
            enqueueSnackbar('Failed to fetch cart', { variant: 'error' });
        } finally {
            setLoading(false);
        }
    };

    const addToCart = async (productId, quantity = 1) => {
        try {
            setLoading(true);
            const response = await apiService.post('/cart/cart-products', {
                product_id: productId,
                quantity,
            });

            await fetchCart(); // Refresh cart after adding
            enqueueSnackbar('Product added to cart', { variant: 'success' });
            return { success: true };
        } catch (error) {
            console.error('Failed to add to cart:', error);
            enqueueSnackbar(
                error.response?.data?.message || 'Failed to add to cart',
                { variant: 'error' }
            );
            return { success: false };
        } finally {
            setLoading(false);
        }
    };

    const updateCartItem = async (cartItemId, quantity) => {
        try {
            setLoading(true);
            await apiService.put(`/cart/cart-products/${cartItemId}`, {
                quantity,
            });

            await fetchCart(); // Refresh cart after updating
            enqueueSnackbar('Cart updated', { variant: 'success' });
            return { success: true };
        } catch (error) {
            console.error('Failed to update cart:', error);
            enqueueSnackbar(
                error.response?.data?.message || 'Failed to update cart',
                { variant: 'error' }
            );
            return { success: false };
        } finally {
            setLoading(false);
        }
    };

    const removeFromCart = async (cartItemId) => {
        try {
            setLoading(true);
            await apiService.delete(`/cart/cart-products/${cartItemId}`);

            await fetchCart(); // Refresh cart after removing
            enqueueSnackbar('Product removed from cart', { variant: 'success' });
            return { success: true };
        } catch (error) {
            console.error('Failed to remove from cart:', error);
            enqueueSnackbar(
                error.response?.data?.message || 'Failed to remove from cart',
                { variant: 'error' }
            );
            return { success: false };
        } finally {
            setLoading(false);
        }
    };

    const clearCart = async () => {
        try {
            setLoading(true);
            await apiService.delete('/cart/clear');

            setCart([]);
            enqueueSnackbar('Cart cleared', { variant: 'success' });
            return { success: true };
        } catch (error) {
            console.error('Failed to clear cart:', error);
            enqueueSnackbar(
                error.response?.data?.message || 'Failed to clear cart',
                { variant: 'error' }
            );
            return { success: false };
        } finally {
            setLoading(false);
        }
    };

    const getCartTotal = () => {
        if (!Array.isArray(cart)) {
            return 0;
        }
        return cart.reduce((total, item) => {
            return total + (item.product?.price || 0) * item.quantity;
        }, 0);
    };

    const getCartItemsCount = () => {
        if (!Array.isArray(cart)) {
            return 0;
        }
        return cart.reduce((count, item) => count + item.quantity, 0);
    };

    const value = {
        cart,
        loading,
        fetchCart,
        addToCart,
        updateCartItem,
        removeFromCart,
        clearCart,
        getCartTotal,
        getCartItemsCount,
    };

    return (
        <CartContext.Provider value={value}>
            {children}
        </CartContext.Provider>
    );
};
