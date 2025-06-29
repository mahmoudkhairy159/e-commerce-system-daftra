import { createContext, useContext, useState, useEffect } from 'react';
import apiService from '../services/apiService';

const AuthContext = createContext();

export const useAuth = () => {
    const context = useContext(AuthContext);
    if (!context) {
        throw new Error('useAuth must be used within an AuthProvider');
    }
    return context;
};

export const AuthProvider = ({ children }) => {
    const [user, setUser] = useState(null);
    const [loading, setLoading] = useState(true);
    const [isAuthenticated, setIsAuthenticated] = useState(false);

    useEffect(() => {
        checkAuthStatus();
    }, []);

    const checkAuthStatus = async () => {
        try {
            const token = localStorage.getItem('token');
            const storedUser = localStorage.getItem('user');

            if (token && storedUser) {
                apiService.setAuthToken(token);
                const userData = JSON.parse(storedUser);
                setUser(userData);
                setIsAuthenticated(true);

                // Optional: Verify token is still valid by making a test request
                try {
                    await apiService.get('/users/get');
                } catch (verifyError) {
                    // Token is invalid, clear auth data
                    throw verifyError;
                }
            }
        } catch (error) {
            console.error('Auth check failed:', error);
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            localStorage.removeItem('token_type');
            apiService.setAuthToken(null);
            setUser(null);
            setIsAuthenticated(false);
        } finally {
            setLoading(false);
        }
    };

    const login = async (email, password) => {
        try {
            const response = await apiService.post('/auth/login', {
                email,
                password,
            });

                        // Handle the response structure from your API
            const { data } = response.data;
            const { token, token_type, user } = data;

            // Save just the token (apiService will add Bearer prefix)
            localStorage.setItem('token', token);
            localStorage.setItem('user', JSON.stringify(user));
            localStorage.setItem('token_type', token_type);

            // Set auth token for future requests
            apiService.setAuthToken(token);
            setUser(user);
            setIsAuthenticated(true);

            return { success: true };
        } catch (error) {
            console.error('Login failed:', error);

            // Handle different error response structures
            let errorMessage = 'Login failed';
            if (error.response?.data?.message) {
                errorMessage = error.response.data.message;
            } else if (error.response?.data?.error) {
                errorMessage = error.response.data.error;
            } else if (error.message) {
                errorMessage = error.message;
            }

            return {
                success: false,
                error: errorMessage
            };
        }
    };

    const logout = async () => {
        try {
            await apiService.post('/auth/logout');
        } catch (error) {
            console.error('Logout error:', error);
        } finally {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            localStorage.removeItem('token_type');
            apiService.setAuthToken(null);
            setUser(null);
            setIsAuthenticated(false);
        }
    };

    const register = async (userData) => {
        try {
            const response = await apiService.post('/auth/register', userData);
            return { success: true, data: response.data };
        } catch (error) {
            console.error('Registration failed:', error);
            return {
                success: false,
                error: error.response?.data?.message || 'Registration failed'
            };
        }
    };

    // Helper function to get user profile information
    const getUserProfile = () => {
        return user?.profile || null;
    };

    // Helper function to get user addresses
    const getUserAddresses = () => {
        return user?.user_addresses || [];
    };

    // Helper function to get default address
    const getDefaultAddress = () => {
        return user?.default_address || null;
    };

    const value = {
        user,
        loading,
        isAuthenticated,
        login,
        logout,
        register,
        checkAuthStatus,
        getUserProfile,
        getUserAddresses,
        getDefaultAddress,
    };

    return (
        <AuthContext.Provider value={value}>
            {children}
        </AuthContext.Provider>
    );
};

export default AuthContext;
