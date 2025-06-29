import { API_CONFIG } from './constants';

/**
 * Calculate cart subtotal
 * @param {Array} cartItems - Array of cart items
 * @returns {number} - Subtotal amount
 */
export const calculateSubtotal = (cartItems = []) => {
    if (!Array.isArray(cartItems)) {
        return 0;
    }

    return cartItems.reduce((total, item) => {
        const price = parseFloat(item.price || item.product?.price || 0);
        const quantity = parseInt(item.quantity || 0);
        return total + (price * quantity);
    }, 0);
};

/**
 * Calculate tax amount
 * @param {number} subtotal - Subtotal amount
 * @param {number} taxRate - Tax rate as decimal (e.g., 0.08 for 8%)
 * @returns {number} - Tax amount
 */
export const calculateTax = (subtotal, taxRate = 0) => {
    if (!subtotal || !taxRate) {
        return 0;
    }

    return subtotal * taxRate;
};

/**
 * Calculate shipping cost
 * @param {number} subtotal - Subtotal amount
 * @param {number} freeShippingThreshold - Free shipping threshold
 * @param {number} shippingCost - Default shipping cost
 * @returns {number} - Shipping cost
 */
export const calculateShipping = (
    subtotal,
    freeShippingThreshold = 100,
    shippingCost = API_CONFIG.DEFAULT_SHIPPING_COST
) => {
    if (subtotal >= freeShippingThreshold) {
        return 0;
    }

    return shippingCost;
};

/**
 * Calculate cart total
 * @param {number} subtotal - Subtotal amount
 * @param {number} tax - Tax amount
 * @param {number} shipping - Shipping cost
 * @returns {number} - Total amount
 */
export const calculateTotal = (subtotal, tax = 0, shipping = 0) => {
    return subtotal + tax + shipping;
};

/**
 * Calculate discount amount
 * @param {number} subtotal - Subtotal amount
 * @param {number} discountPercent - Discount percentage (0-100)
 * @returns {number} - Discount amount
 */
export const calculateDiscountAmount = (subtotal, discountPercent = 0) => {
    if (!subtotal || !discountPercent) {
        return 0;
    }

    return subtotal * (discountPercent / 100);
};

/**
 * Calculate final price after discount
 * @param {number} originalPrice - Original price
 * @param {number} discountPercent - Discount percentage (0-100)
 * @returns {number} - Final price after discount
 */
export const calculateDiscountedPrice = (originalPrice, discountPercent = 0) => {
    if (!originalPrice) {
        return 0;
    }

    const discountAmount = calculateDiscountAmount(originalPrice, discountPercent);
    return originalPrice - discountAmount;
};

/**
 * Calculate cart items count
 * @param {Array} cartItems - Array of cart items
 * @returns {number} - Total items count
 */
export const calculateItemsCount = (cartItems = []) => {
    if (!Array.isArray(cartItems)) {
        return 0;
    }

    return cartItems.reduce((count, item) => {
        return count + parseInt(item.quantity || 0);
    }, 0);
};

/**
 * Calculate price per unit
 * @param {number} totalPrice - Total price
 * @param {number} quantity - Quantity
 * @returns {number} - Price per unit
 */
export const calculatePricePerUnit = (totalPrice, quantity) => {
    if (!totalPrice || !quantity || quantity === 0) {
        return 0;
    }

    return totalPrice / quantity;
};

/**
 * Calculate savings amount
 * @param {number} originalPrice - Original price
 * @param {number} salePrice - Sale price
 * @returns {number} - Savings amount
 */
export const calculateSavings = (originalPrice, salePrice) => {
    if (!originalPrice || !salePrice || salePrice >= originalPrice) {
        return 0;
    }

    return originalPrice - salePrice;
};

/**
 * Calculate savings percentage
 * @param {number} originalPrice - Original price
 * @param {number} salePrice - Sale price
 * @returns {number} - Savings percentage
 */
export const calculateSavingsPercentage = (originalPrice, salePrice) => {
    const savings = calculateSavings(originalPrice, salePrice);

    if (!savings || !originalPrice) {
        return 0;
    }

    return (savings / originalPrice) * 100;
};

/**
 * Round to specified decimal places
 * @param {number} value - Value to round
 * @param {number} decimalPlaces - Number of decimal places (default: 2)
 * @returns {number} - Rounded value
 */
export const roundToDecimals = (value, decimalPlaces = 2) => {
    if (!value || isNaN(value)) {
        return 0;
    }

    return Math.round(value * Math.pow(10, decimalPlaces)) / Math.pow(10, decimalPlaces);
};
