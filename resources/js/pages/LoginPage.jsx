import React, { useState, useEffect } from 'react';
import { useNavigate, useLocation } from 'react-router-dom';
import {
    Container,
    Paper,
    TextField,
    Button,
    Typography,
    Box,
    Alert,
    CircularProgress,
    InputAdornment,
    IconButton,
} from '@mui/material';
import {
    Visibility,
    VisibilityOff,
} from '@mui/icons-material';
import { useAuth } from '../contexts/AuthContext';
import Layout from '../components/Layout/Layout';

const LoginPage = () => {
    const navigate = useNavigate();
    const location = useLocation();
    const { login, isAuthenticated } = useAuth();

    const [formData, setFormData] = useState({
        email: '',
        password: '',
    });
    const [showPassword, setShowPassword] = useState(false);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');

    const from = location.state?.from?.pathname || '/products';

    useEffect(() => {
        if (isAuthenticated) {
            navigate(from, { replace: true });
        }
    }, [isAuthenticated, navigate, from]);

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData(prev => ({
            ...prev,
            [name]: value,
        }));
        // Clear error when user starts typing
        if (error) setError('');
    };

    const handleSubmit = async (e) => {
        e.preventDefault();

        if (!formData.email || !formData.password) {
            setError('Please fill in all fields');
            return;
        }

        setLoading(true);
        setError('');

        try {
            const result = await login(formData.email, formData.password);

            if (result.success) {
                navigate(from, { replace: true });
            } else {
                setError(result.error);
            }
        } catch (err) {
            setError('An unexpected error occurred');
        } finally {
            setLoading(false);
        }
    };

    const togglePasswordVisibility = () => {
        setShowPassword(!showPassword);
    };

    return (
        <Layout useContainer={false}>
            <Box sx={{ minHeight: '100vh', backgroundColor: '#f5f5f5' }}>
                <Container component="main" maxWidth="sm">
                <Box
                    sx={{
                        minHeight: 'calc(100vh - 120px)',
                        display: 'flex',
                        flexDirection: 'column',
                        justifyContent: 'center',
                        alignItems: 'center',
                        py: 4,
                    }}
                >
                    <Paper
                        elevation={0}
                        sx={{
                            padding: 4,
                            display: 'flex',
                            flexDirection: 'column',
                            alignItems: 'center',
                            width: '100%',
                            maxWidth: 400,
                            backgroundColor: '#fff',
                            borderRadius: 2,
                            border: '1px solid #e0e0e0',
                        }}
                    >
                        <Typography
                            component="h1"
                            variant="h4"
                            sx={{
                                mb: 1,
                                fontWeight: 600,
                                color: '#000',
                                textAlign: 'center'
                            }}
                        >
                            Welcome back
                        </Typography>

                        <Typography
                            variant="body1"
                            sx={{
                                mb: 3,
                                color: '#666',
                                textAlign: 'center'
                            }}
                        >
                            Please enter your details to sign in
                        </Typography>

                        {error && (
                            <Alert severity="error" sx={{ width: '100%', mb: 2 }}>
                                {error}
                            </Alert>
                        )}

                        <Box component="form" onSubmit={handleSubmit} sx={{ width: '100%' }}>
                            <Typography variant="body2" sx={{ mb: 1, color: '#333', fontWeight: 500 }}>
                                Email
                            </Typography>
                            <TextField
                                required
                                fullWidth
                                id="email"
                                name="email"
                                autoComplete="email"
                                autoFocus
                                value={formData.email}
                                onChange={handleChange}
                                disabled={loading}
                                placeholder="Enter your email"
                                sx={{
                                    mb: 2,
                                    '& .MuiOutlinedInput-root': {
                                        borderRadius: 1,
                                        '& fieldset': {
                                            borderColor: '#e0e0e0',
                                        },
                                        '&:hover fieldset': {
                                            borderColor: '#c0c0c0',
                                        },
                                        '&.Mui-focused fieldset': {
                                            borderColor: '#000',
                                        },
                                    },
                                }}
                            />

                            <Typography variant="body2" sx={{ mb: 1, color: '#333', fontWeight: 500 }}>
                                Password
                            </Typography>
                            <TextField
                                required
                                fullWidth
                                name="password"
                                type={showPassword ? 'text' : 'password'}
                                id="password"
                                autoComplete="current-password"
                                value={formData.password}
                                onChange={handleChange}
                                disabled={loading}
                                placeholder="Enter your password"
                                sx={{
                                    mb: 3,
                                    '& .MuiOutlinedInput-root': {
                                        borderRadius: 1,
                                        '& fieldset': {
                                            borderColor: '#e0e0e0',
                                        },
                                        '&:hover fieldset': {
                                            borderColor: '#c0c0c0',
                                        },
                                        '&.Mui-focused fieldset': {
                                            borderColor: '#000',
                                        },
                                    },
                                }}
                                InputProps={{
                                    endAdornment: (
                                        <InputAdornment position="end">
                                            <IconButton
                                                aria-label="toggle password visibility"
                                                onClick={togglePasswordVisibility}
                                                edge="end"
                                            >
                                                {showPassword ? <VisibilityOff /> : <Visibility />}
                                            </IconButton>
                                        </InputAdornment>
                                    ),
                                }}
                            />

                            <Button
                                type="submit"
                                fullWidth
                                variant="contained"
                                disabled={loading}
                                sx={{
                                    py: 1.5,
                                    backgroundColor: '#000',
                                    color: '#fff',
                                    borderRadius: 1,
                                    textTransform: 'none',
                                    fontSize: '16px',
                                    fontWeight: 500,
                                    '&:hover': {
                                        backgroundColor: '#333',
                                    },
                                    '&:disabled': {
                                        backgroundColor: '#ccc',
                                    },
                                }}
                            >
                                {loading ? (
                                    <CircularProgress size={24} color="inherit" />
                                ) : (
                                    'Login'
                                )}
                            </Button>
                        </Box>
                    </Paper>
                </Box>
            </Container>
            </Box>
        </Layout>
    );
};

export default LoginPage;
