import axios from 'axios';

class ApiService {
    constructor() {
        this.api = axios.create({
            baseURL: '/api/user/v1',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
        });

        // Request interceptor
        this.api.interceptors.request.use(
            (config) => {
                const token = localStorage.getItem('token');
                if (token) {
                    config.headers.Authorization = `Bearer ${token}`;
                }
                return config;
            },
            (error) => {
                return Promise.reject(error);
            }
        );

        // Response interceptor
        this.api.interceptors.response.use(
            (response) => {
                return response;
            },
            (error) => {
                if (error.response?.status === 401) {
                    // Token expired or invalid
                    localStorage.removeItem('token');
                    localStorage.removeItem('user');
                    localStorage.removeItem('token_type');
                    window.location.href = '/login';
                }
                return Promise.reject(error);
            }
        );
    }

    setAuthToken(token) {
        if (token) {
            this.api.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        } else {
            delete this.api.defaults.headers.common['Authorization'];
        }
    }

    // Generic HTTP methods
    async get(url, config = {}) {
        return this.api.get(url, config);
    }

    async post(url, data = {}, config = {}) {
        return this.api.post(url, data, config);
    }

    async put(url, data = {}, config = {}) {
        return this.api.put(url, data, config);
    }

    async patch(url, data = {}, config = {}) {
        return this.api.patch(url, data, config);
    }

    async delete(url, config = {}) {
        return this.api.delete(url, config);
    }

    // Auth API methods
    async login(email, password) {
        return this.post('/auth/login', { email, password });
    }

    async register(userData) {
        return this.post('/auth/register', userData);
    }

    async logout() {
        return this.post('/auth/logout');
    }

    async refreshToken() {
        return this.post('/auth/refresh-token');
    }

    async getUserInfo() {
        return this.get('/users/get');
    }

                    // Product API methods
    async getProducts(params = {}) {
        return this.get('/products', {
            params,
            paramsSerializer: function(params) {
                const searchParams = new URLSearchParams();

                Object.keys(params).forEach(key => {
                    const value = params[key];
                    if (Array.isArray(value)) {
                        // Handle arrays with [] notation for Laravel
                        value.forEach(item => {
                            searchParams.append(`${key}[]`, item);
                        });
                    } else if (value !== null && value !== undefined) {
                        searchParams.append(key, value);
                    }
                });

                const queryString = searchParams.toString();
                return queryString;
            }
        });
    }

    async getProduct(id) {
        return this.get(`/products/${id}`);
    }

    async getProductBySlug(slug) {
        return this.get(`/products/slugs/${slug}`);
    }

    async getFeaturedProducts() {
        return this.get('/products/featured');
    }

    async getNewArrivals() {
        return this.get('/products/new-arrivals');
    }

    async getBestSellers() {
        return this.get('/products/best-sellers');
    }

    async getProductsByCategory(categoryId) {
        return this.get(`/products/category/${categoryId}`);
    }

    async getProductReviews(productId) {
        return this.get(`/product-reviews/product/${productId}`);
    }

    async createProductReview(data) {
        return this.post('/product-reviews', data);
    }

    // Cart API methods
    async getCart() {
        return this.get('/cart/cart-products');
    }

    async addToCart(productId, quantity) {
        return this.post('/cart/cart-products', {
            product_id: productId,
            quantity,
        });
    }

    async updateCartItem(cartItemId, quantity) {
        return this.put(`/cart/cart-products/${cartItemId}`, { quantity });
    }

    async removeFromCart(cartItemId) {
        return this.delete(`/cart/cart-products/${cartItemId}`);
    }

    async clearCart() {
        return this.delete('/cart/clear');
    }

    // Order API methods
    async getOrders() {
        return this.get('/orders');
    }

    async getOrder(id) {
        return this.get(`/orders/${id}`);
    }

    async createOrder(orderData) {
        return this.post('/orders', orderData);
    }

    // Category API methods
    async getCategories() {
        return this.get('/categories');
    }
     async getMainCategories() {
        return this.get('/categories/parents');
    }


    // Area API methods
    async getCountries() {
        return this.get('/countries');
    }

    async getStatesByCountry(countryId) {
        return this.get(`/states/country/${countryId}`);
    }

    async getCitiesByState(stateId) {
        return this.get(`/cities/state/${stateId}`);
    }
}

const apiService = new ApiService();
export default apiService;
