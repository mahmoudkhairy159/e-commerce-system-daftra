// API Configuration
export const API_CONFIG = {
    DEFAULT_PER_PAGE: 6,
    MAX_RETRIES: 2,
    DEBOUNCE_DELAY: 300,
    DEFAULT_SHIPPING_COST: 15.0
};

// Price Ranges
export const PRICE_RANGES = {
    MIN: 0,
    MAX: 5000,
    DEFAULT: [0, 5000]
};

// Sorting Options
export const SORT_OPTIONS = [
    { value: 'latest', label: 'Latest' },
    { value: 'oldest', label: 'Oldest' },
    { value: 'price_low_to_high', label: 'Price: Low to High' },
    { value: 'price_high_to_low', label: 'Price: High to Low' },
    { value: 'name_a_to_z', label: 'Name: A to Z' },
    { value: 'name_z_to_a', label: 'Name: Z to A' }
];

// Notification Messages
export const MESSAGES = {
    SUCCESS: {
        PRODUCT_ADDED_TO_CART: 'Product added to cart successfully',
        CART_UPDATED: 'Cart updated successfully',
        PRODUCT_REMOVED_FROM_CART: 'Product removed from cart',
        CART_CLEARED: 'Cart cleared successfully',
        ORDER_CREATED: 'Order created successfully'
    },
    ERROR: {
        FETCH_PRODUCTS_FAILED: 'Failed to load products. Please try again.',
        FETCH_CATEGORIES_FAILED: 'Failed to load categories. Please try again.',
        FETCH_CART_FAILED: 'Failed to load cart',
        ADD_TO_CART_FAILED: 'Failed to add product to cart',
        UPDATE_CART_FAILED: 'Failed to update cart',
        REMOVE_FROM_CART_FAILED: 'Failed to remove item from cart',
        CLEAR_CART_FAILED: 'Failed to clear cart',
        CREATE_ORDER_FAILED: 'Failed to create order',
        NETWORK_ERROR: 'Network error. Please check your connection.',
        GENERIC_ERROR: 'Something went wrong. Please try again.'
    },
    WARNING: {
        EMPTY_CART: 'Your cart is empty',
        INVALID_QUANTITY: 'Please enter a valid quantity'
    }
};

// UI Constants
export const UI_CONFIG = {
    BREAKPOINTS: {
        SM: 600,
        MD: 900,
        LG: 1200,
        XL: 1536
    },
    DRAWER_WIDTH: 300,
    HEADER_HEIGHT: 64,
    MOBILE_HEADER_HEIGHT: 56
};

// Default Images
export const DEFAULT_IMAGES = {
    PRODUCT: '/default.jpg',
    USER_AVATAR: '/default-avatar.jpg'
};

// Theme Colors
export const THEME_COLORS = {
    PRIMARY: '#1976d2',
    SECONDARY: '#dc004e',
    SUCCESS: '#4caf50',
    ERROR: '#f44336',
    WARNING: '#ff9800',
    INFO: '#2196f3'
};

// Order Status
export const ORDER_STATUS = {
    PENDING: 'pending',
    PROCESSING: 'processing',
    SHIPPED: 'shipped',
    DELIVERED: 'delivered',
    CANCELLED: 'cancelled'
};

// Payment Status
export const PAYMENT_STATUS = {
    PENDING: 'pending',
    PAID: 'paid',
    FAILED: 'failed',
    REFUNDED: 'refunded'
};
