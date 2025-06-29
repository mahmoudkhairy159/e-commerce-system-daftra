import React, { useState } from 'react';
import { Outlet, useNavigate } from 'react-router-dom';
import {
    AppBar,
    Toolbar,
    Typography,
    IconButton,
    Badge,
    Menu,
    MenuItem,
    Box,
    Container,
    Button,
} from '@mui/material';
import {
    ShoppingCart,
    AccountCircle,
    Close,
} from '@mui/icons-material';
import { useAuth } from '../../contexts/AuthContext';
import { useCart } from '../../contexts/CartContext';

const Layout = ({ children, useContainer = true }) => {
    const navigate = useNavigate();
    const { user, logout, isAuthenticated } = useAuth();
    const { getCartItemsCount } = useCart();
    const [anchorEl, setAnchorEl] = useState(null);
    const [showPromoBanner, setShowPromoBanner] = useState(true);

    const handleMenu = (event) => {
        setAnchorEl(event.currentTarget);
    };

    const handleClose = () => {
        setAnchorEl(null);
    };

    const handleLogout = async () => {
        await logout();
        handleClose();
        navigate('/login');
    };

    const handleCartClick = () => {
        navigate('/cart');
    };

    const handleProductsClick = () => {
        navigate('/products');
    };

    return (
        <Box sx={{ flexGrow: 1 }}>
            {/* Promotional Banner */}
            {showPromoBanner && (
                <Box
                    sx={{
                        backgroundColor: '#000',
                        color: '#fff',
                        py: 1,
                        px: 2,
                        textAlign: 'center',
                        position: 'relative',
                    }}
                >
                    <Typography variant="body2">
                        Sign up and get 20% off to your first order!{' '}
                        <Box component="span" sx={{ textDecoration: 'underline', cursor: 'pointer' }}>
                            Sign Up Now
                        </Box>
                    </Typography>
                    <IconButton
                        onClick={() => setShowPromoBanner(false)}
                        sx={{
                            position: 'absolute',
                            right: 8,
                            top: '50%',
                            transform: 'translateY(-50%)',
                            color: '#fff',
                            padding: '4px',
                        }}
                    >
                        <Close fontSize="small" />
                    </IconButton>
                </Box>
            )}

            {/* Header */}
            <AppBar position="static" sx={{ backgroundColor: '#fff', boxShadow: 'none', borderBottom: '1px solid #e0e0e0' }}>
                <Toolbar sx={{ justifyContent: 'space-between', px: 3 }}>
                    {/* Left Side - Logo and Navigation */}
                    <Box sx={{ display: 'flex', alignItems: 'center', gap: 4 }}>
                        {/* Logo */}
                        <Box
                            sx={{
                                border: '2px solid #000',
                                borderRadius: '4px',
                                padding: '4px 8px',
                                display: 'flex',
                                alignItems: 'center',
                                cursor: 'pointer',
                            }}
                            onClick={handleProductsClick}
                        >
                            <ShoppingCart sx={{ fontSize: 20, color: '#000', mr: 0.5 }} />
                            <Typography variant="h6" sx={{ color: '#000', fontWeight: 'bold' }}>
                                rzam
                            </Typography>
                        </Box>

                        {/* Navigation */}
                        <Box sx={{ display: 'flex', alignItems: 'center', gap: 3 }}>
                            <Typography
                                variant="body1"
                                onClick={handleProductsClick}
                                sx={{
                                    color: '#000',
                                    cursor: 'pointer',
                                    fontWeight: 'bold',
                                    fontSize: '16px',
                                    '&:hover': {
                                        color: '#333',
                                    }
                                }}
                            >
                                Products
                            </Typography>
                            <Button
                                variant="contained"
                                sx={{
                                    backgroundColor: '#000',
                                    color: '#fff',
                                    textTransform: 'none',
                                    px: 2,
                                    py: 1,
                                    borderRadius: '6px',
                                    fontSize: '14px',
                                    fontWeight: 500,
                                    '&:hover': {
                                        backgroundColor: '#333',
                                    },
                                }}
                            >
                                Sell Your Product
                            </Button>
                        </Box>
                    </Box>

                    {/* Right Side */}
                    <Box sx={{ display: 'flex', alignItems: 'center', gap: 2 }}>
                        <IconButton onClick={handleCartClick} sx={{ color: '#000', p: 0 }}>
                            <Badge badgeContent={getCartItemsCount()} color="error">
                                <ShoppingCart sx={{ fontSize: 24 }} />
                            </Badge>
                        </IconButton>

                        {isAuthenticated ? (
                            <>
                                <IconButton
                                    onClick={handleMenu}
                                    sx={{
                                        color: '#000',
                                        '&:hover': {
                                            color: '#333',
                                        }
                                    }}
                                >
                                    <AccountCircle sx={{ fontSize: 24 }} />
                                </IconButton>
                                <Menu
                                    id="menu-appbar"
                                    anchorEl={anchorEl}
                                    anchorOrigin={{
                                        vertical: 'bottom',
                                        horizontal: 'right',
                                    }}
                                    keepMounted
                                    transformOrigin={{
                                        vertical: 'top',
                                        horizontal: 'right',
                                    }}
                                    open={Boolean(anchorEl)}
                                    onClose={handleClose}
                                >
                                    <MenuItem onClick={handleClose}>
                                        <Typography variant="subtitle1">
                                            {user?.name || user?.email}
                                        </Typography>
                                    </MenuItem>
                                    <MenuItem onClick={handleLogout}>Logout</MenuItem>
                                </Menu>
                            </>
                        ) : (
                            <Button
                                variant="contained"
                                sx={{
                                    backgroundColor: '#000',
                                    color: '#fff',
                                    textTransform: 'none',
                                    px: 2,
                                    py: 1,
                                    borderRadius: '6px',
                                    fontSize: '14px',
                                    fontWeight: 500,
                                    '&:hover': {
                                        backgroundColor: '#333',
                                    },
                                }}
                                onClick={() => navigate('/login')}
                            >
                                Login
                            </Button>
                        )}
                    </Box>
                </Toolbar>
            </AppBar>

            {useContainer ? (
                <Container maxWidth="lg" sx={{ mt: 4, mb: 4 }}>
                    {children || <Outlet />}
                </Container>
            ) : (
                children || <Outlet />
            )}
        </Box>
    );
};

export default Layout;
