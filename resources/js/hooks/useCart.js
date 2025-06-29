import { useState, useCallback } from 'react';
import { useSnackbar } from 'notistack';
import apiService from '../services/apiService';
import { MESSAGES } from '../utils/constants';
import { calculateSubtotal, calculateItemsCount, calculateTotal } from '../utils/calculations';

/**
 * Custom hook for cart operations
 * @param {Object} options - Configuration options
 * @returns {Object} - Cart state and operations
 */
export const useCart = (options = {}) => {
    const { showNotifications = true } = options;
    const [cartData, setCartData] = useState(null);
    const [loading, setLoading] = useState(false);
    const [updatingItems, setUpdatingItems] = useState(new Set());
    const { enqueueSnackbar } = useSnackbar();

    const showNotification = useCallback((message, variant = 'success') => {
        if (showNotifications) {
            enqueueSnackbar(message, { variant });
        }
    }, [enqueueSnackbar, showNotifications]);

    const fetchCart = useCallback(async () => {
        try {
            setLoading(true);
            const response = await apiService.getCart();

            if (response.data && response.data.success) {
                setCartData(response.data.data);
                return { success: true, data: response.data.data };
            } else {
                setCartData(null);
                return { success: false, error: 'Failed to fetch cart' };
            }
        } catch (error) {
            console.error('Failed to fetch cart:', error);
            setCartData(null);
            showNotification(MESSAGES.ERROR.FETCH_CART_FAILED, 'error');
            return { success: false, error: error.message };
        } finally {
            setLoading(false);
        }
    }, [showNotification]);

    const addToCart = useCallback(async (productId, quantity = 1) => {
        try {
            setLoading(true);
            await apiService.addToCart(productId, quantity);

            // Refresh cart data
            const result = await fetchCart();

            if (result.success) {
                showNotification(MESSAGES.SUCCESS.PRODUCT_ADDED_TO_CART);
                return { success: true };
            }

            return result;
        } catch (error) {
            console.error('Failed to add to cart:', error);
            showNotification(MESSAGES.ERROR.ADD_TO_CART_FAILED, 'error');
            return { success: false, error: error.message };
        } finally {
            setLoading(false);
        }
    }, [fetchCart, showNotification]);

    const updateCartItem = useCallback(async (cartItemId, quantity) => {
        if (quantity < 1) return { success: false, error: 'Invalid quantity' };

        setUpdatingItems(prev => new Set(prev).add(cartItemId));

        try {
            await apiService.updateCartItem(cartItemId, quantity);

            // Refresh cart data
            const result = await fetchCart();

            if (result.success) {
                showNotification(MESSAGES.SUCCESS.CART_UPDATED);
                return { success: true };
            }

            return result;
        } catch (error) {
            console.error('Failed to update cart item:', error);
            showNotification(MESSAGES.ERROR.UPDATE_CART_FAILED, 'error');
            return { success: false, error: error.message };
        } finally {
            setUpdatingItems(prev => {
                const newSet = new Set(prev);
                newSet.delete(cartItemId);
                return newSet;
            });
        }
    }, [fetchCart, showNotification]);

    const removeFromCart = useCallback(async (cartItemId, productName = '') => {
        setUpdatingItems(prev => new Set(prev).add(cartItemId));

        try {
            await apiService.removeFromCart(cartItemId);

            // Refresh cart data
            const result = await fetchCart();

            if (result.success) {
                const message = productName
                    ? `${productName} removed from cart`
                    : MESSAGES.SUCCESS.PRODUCT_REMOVED_FROM_CART;
                showNotification(message);
                return { success: true };
            }

            return result;
        } catch (error) {
            console.error('Failed to remove from cart:', error);
            showNotification(MESSAGES.ERROR.REMOVE_FROM_CART_FAILED, 'error');
            return { success: false, error: error.message };
        } finally {
            setUpdatingItems(prev => {
                const newSet = new Set(prev);
                newSet.delete(cartItemId);
                return newSet;
            });
        }
    }, [fetchCart, showNotification]);

    const clearCart = useCallback(async () => {
        try {
            setLoading(true);
            await apiService.clearCart();

            // Refresh cart data
            const result = await fetchCart();

            if (result.success) {
                showNotification(MESSAGES.SUCCESS.CART_CLEARED);
                return { success: true };
            }

            return result;
        } catch (error) {
            console.error('Failed to clear cart:', error);
            showNotification(MESSAGES.ERROR.CLEAR_CART_FAILED, 'error');
            return { success: false, error: error.message };
        }
    }, [fetchCart, showNotification]);

    const createOrder = useCallback(async (orderData) => {
        try {
            setLoading(true);
            const response = await apiService.createOrder(orderData);

            // Access the actual API response data
            const apiData = response.data;

            if (apiData.success) {
                // Refresh cart after successful order creation
                await fetchCart();

                showNotification(
                    `Order ${apiData.data.order.order_number} created successfully! Total: $${apiData.data.order.total_amount}`
                );

                return {
                    success: true,
                    data: apiData.data,
                    orderId: apiData.data.order.id
                };
            }

            return { success: false, error: 'Failed to create order' };
        } catch (error) {
            console.error('Failed to create order:', error);
            showNotification(MESSAGES.ERROR.CREATE_ORDER_FAILED, 'error');
            return { success: false, error: error.message };
        } finally {
            setLoading(false);
        }
    }, [fetchCart, showNotification]);

    // Computed values
    const cartItems = cartData?.cartProducts || [];
    const subtotal = cartData ? parseFloat(cartData.sum_subtotal || 0) : 0;
    const tax = cartData ? parseFloat(cartData.sum_tax || 0) : 0;
    const itemCount = cartData ? parseInt(cartData.sum_quantity || 0) : 0;
    const isEmpty = cartItems.length === 0;

    const isUpdating = useCallback((itemId) => {
        return updatingItems.has(itemId);
    }, [updatingItems]);

    return {
        // State
        cartData,
        cartItems,
        loading,
        isEmpty,

        // Computed values
        subtotal,
        tax,
        itemCount,

        // Operations
        fetchCart,
        addToCart,
        updateCartItem,
        removeFromCart,
        clearCart,
        createOrder,

        // Utilities
        isUpdating
    };
};

export default useCart;
