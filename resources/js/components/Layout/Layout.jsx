import React, { useState } from "react";
import { Outlet, useNavigate } from "react-router-dom";
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
    useMediaQuery,
    useTheme,
    Drawer,
    List,
    ListItem,
    ListItemIcon,
    ListItemText,
    Divider,
} from "@mui/material";
import {
    ShoppingCart,
    AccountCircle,
    Close,
    Menu as MenuIcon,
    Store,
    Login,
    Person,
    Logout,
} from "@mui/icons-material";
import { useAuth } from "../../contexts/AuthContext";
import { useCart } from "../../contexts/CartContext";

const Layout = ({ children, useContainer = true }) => {
    const navigate = useNavigate();
    const theme = useTheme();
    const isMobile = useMediaQuery(theme.breakpoints.down("md"));
    const isSmallMobile = useMediaQuery(theme.breakpoints.down("sm"));
    const { user, logout, isAuthenticated } = useAuth();
    const { getCartItemsCount } = useCart();
    const [anchorEl, setAnchorEl] = useState(null);
    const [showPromoBanner, setShowPromoBanner] = useState(true);
    const [mobileMenuOpen, setMobileMenuOpen] = useState(false);

    const handleMenu = (event) => {
        setAnchorEl(event.currentTarget);
    };

    const handleClose = () => {
        setAnchorEl(null);
    };

    const handleLogout = async () => {
        await logout();
        handleClose();
        setMobileMenuOpen(false);
        navigate("/login");
    };

    const handleCartClick = () => {
        navigate("/cart");
        setMobileMenuOpen(false);
    };

    const handleProductsClick = () => {
        navigate("/products");
        setMobileMenuOpen(false);
    };

    const handleLoginClick = () => {
        navigate("/login");
        setMobileMenuOpen(false);
    };

    const handleMobileMenuToggle = () => {
        setMobileMenuOpen(!mobileMenuOpen);
    };

    return (
        <Box sx={{ flexGrow: 1 }}>
            {/* Promotional Banner */}
            {showPromoBanner && (
                <Box
                    sx={{
                        backgroundColor: "#000",
                        color: "#fff",
                        py: { xs: 0.7, sm: 1 },
                        px: { xs: 1, sm: 2 },
                        textAlign: "center",
                        position: "relative",
                    }}
                >
                    <Typography
                        variant="body2"
                        sx={{
                            fontSize: { xs: "12px", sm: "14px" },
                            pr: { xs: 4, sm: 2 },
                        }}
                    >
                        Sign up and get 20% off to your first order!{" "}
                        <Box
                            component="span"
                            sx={{
                                textDecoration: "underline",
                                cursor: "pointer",
                                display: { xs: "block", sm: "inline" },
                                mt: { xs: 0.5, sm: 0 },
                            }}
                        >
                            Sign Up Now
                        </Box>
                    </Typography>
                    <IconButton
                        onClick={() => setShowPromoBanner(false)}
                        sx={{
                            position: "absolute",
                            right: { xs: 4, sm: 8 },
                            top: "50%",
                            transform: "translateY(-50%)",
                            color: "#fff",
                            padding: "4px",
                            minWidth: "auto",
                            width: { xs: 28, sm: 32 },
                            height: { xs: 28, sm: 32 },
                        }}
                    >
                        <Close fontSize="small" />
                    </IconButton>
                </Box>
            )}

            {/* Header */}
            <AppBar
                position="static"
                sx={{
                    backgroundColor: "#fff",
                    boxShadow: "none",
                    borderBottom: "1px solid #e0e0e0",
                }}
            >
                <Toolbar
                    sx={{
                        justifyContent: "space-between",
                        px: { xs: 2, sm: 3 },
                        minHeight: { xs: 56, sm: 64 },
                    }}
                >
                    {/* Left Side - Logo and Navigation */}
                    <Box
                        sx={{
                            display: "flex",
                            alignItems: "center",
                            gap: { xs: 2, md: 4 },
                        }}
                    >
                        {/* Mobile Menu Button */}
                        {isMobile && (
                            <IconButton
                                onClick={handleMobileMenuToggle}
                                sx={{
                                    color: "#000",
                                    p: 1,
                                    mr: 1,
                                }}
                            >
                                <MenuIcon />
                            </IconButton>
                        )}

                        {/* Logo */}
                        <Box
                            sx={{
                                border: "2px solid #000",
                                borderRadius: "4px",
                                padding: { xs: "3px 6px", sm: "4px 8px" },
                                display: "flex",
                                alignItems: "center",
                                cursor: "pointer",
                            }}
                            onClick={handleProductsClick}
                        >
                            <ShoppingCart
                                sx={{
                                    fontSize: { xs: 18, sm: 20 },
                                    color: "#000",
                                    mr: 0.5,
                                }}
                            />
                            <Typography
                                variant="h6"
                                sx={{
                                    color: "#000",
                                    fontWeight: "bold",
                                    fontSize: { xs: "18px", sm: "20px" },
                                }}
                            >
                                rzam
                            </Typography>
                        </Box>

                        {/* Desktop Navigation */}
                        {!isMobile && (
                            <Box
                                sx={{
                                    display: "flex",
                                    alignItems: "center",
                                    gap: 3,
                                }}
                            >
                                <Typography
                                    variant="body1"
                                    onClick={handleProductsClick}
                                    sx={{
                                        color: "#000",
                                        cursor: "pointer",
                                        fontWeight: "bold",
                                        fontSize: "16px",
                                        "&:hover": {
                                            color: "#333",
                                        },
                                    }}
                                >
                                    Products
                                </Typography>
                                <Button
                                    variant="contained"
                                    sx={{
                                        backgroundColor: "#000",
                                        color: "#fff",
                                        textTransform: "none",
                                        px: 2,
                                        py: 1,
                                        borderRadius: "6px",
                                        fontSize: "14px",
                                        fontWeight: 500,
                                        "&:hover": {
                                            backgroundColor: "#333",
                                        },
                                    }}
                                >
                                    Sell Your Product
                                </Button>
                            </Box>
                        )}
                    </Box>

                    {/* Right Side */}
                    <Box
                        sx={{
                            display: "flex",
                            alignItems: "center",
                            gap: { xs: 1, sm: 2 },
                        }}
                    >
                        {/* Cart Icon */}
                        <IconButton
                            onClick={handleCartClick}
                            sx={{
                                color: "#000",
                                p: 1,
                                minWidth: { xs: 44, sm: "auto" },
                                minHeight: { xs: 44, sm: "auto" },
                            }}
                        >
                            <Badge
                                badgeContent={getCartItemsCount()}
                                color="error"
                            >
                                <ShoppingCart
                                    sx={{ fontSize: { xs: 22, sm: 24 } }}
                                />
                            </Badge>
                        </IconButton>

                        {/* Desktop User Menu */}
                        {!isMobile && isAuthenticated ? (
                            <>
                                <IconButton
                                    onClick={handleMenu}
                                    sx={{
                                        color: "#000",
                                        "&:hover": {
                                            color: "#333",
                                        },
                                    }}
                                >
                                    <AccountCircle sx={{ fontSize: 24 }} />
                                </IconButton>
                                <Menu
                                    id="menu-appbar"
                                    anchorEl={anchorEl}
                                    anchorOrigin={{
                                        vertical: "bottom",
                                        horizontal: "right",
                                    }}
                                    keepMounted
                                    transformOrigin={{
                                        vertical: "top",
                                        horizontal: "right",
                                    }}
                                    open={Boolean(anchorEl)}
                                    onClose={handleClose}
                                >
                                    <MenuItem onClick={handleClose}>
                                        <Typography variant="subtitle1">
                                            {user?.name || user?.email}
                                        </Typography>
                                    </MenuItem>
                                    <MenuItem onClick={handleLogout}>
                                        Logout
                                    </MenuItem>
                                </Menu>
                            </>
                        ) : !isMobile && !isAuthenticated ? (
                            <Button
                                variant="contained"
                                sx={{
                                    backgroundColor: "#000",
                                    color: "#fff",
                                    textTransform: "none",
                                    px: 2,
                                    py: 1,
                                    borderRadius: "6px",
                                    fontSize: "14px",
                                    fontWeight: 500,
                                    "&:hover": {
                                        backgroundColor: "#333",
                                    },
                                }}
                                onClick={() => navigate("/login")}
                            >
                                Login
                            </Button>
                        ) : null}
                    </Box>
                </Toolbar>
            </AppBar>

            {/* Mobile Navigation Drawer */}
            <Drawer
                anchor="left"
                open={mobileMenuOpen}
                onClose={() => setMobileMenuOpen(false)}
                sx={{
                    display: { xs: "block", md: "none" },
                    "& .MuiDrawer-paper": {
                        width: 280,
                        pt: 2,
                    },
                }}
            >
                <Box sx={{ overflow: "auto" }}>
                    {/* Mobile Menu Header */}
                    <Box
                        sx={{
                            display: "flex",
                            justifyContent: "space-between",
                            alignItems: "center",
                            px: 2,
                            pb: 2,
                        }}
                    >
                        <Typography variant="h6" sx={{ fontWeight: 600 }}>
                            Menu
                        </Typography>
                        <IconButton onClick={() => setMobileMenuOpen(false)}>
                            <Close />
                        </IconButton>
                    </Box>

                    <Divider />

                    <List>
                        {/* Products */}
                        <ListItem
                            button
                            onClick={handleProductsClick}
                            sx={{ py: 2 }}
                        >
                            <ListItemIcon>
                                <ShoppingCart />
                            </ListItemIcon>
                            <ListItemText
                                primary="Products"
                                primaryTypographyProps={{
                                    fontWeight: 500,
                                    fontSize: "16px",
                                }}
                            />
                        </ListItem>

                        {/* Sell Your Product */}
                        <ListItem button sx={{ py: 2 }}>
                            <ListItemIcon>
                                <Store />
                            </ListItemIcon>
                            <ListItemText
                                primary="Sell Your Product"
                                primaryTypographyProps={{
                                    fontWeight: 500,
                                    fontSize: "16px",
                                }}
                            />
                        </ListItem>

                        <Divider sx={{ my: 1 }} />

                        {/* User Section */}
                        {isAuthenticated ? (
                            <>
                                <ListItem sx={{ py: 2 }}>
                                    <ListItemIcon>
                                        <Person />
                                    </ListItemIcon>
                                    <ListItemText
                                        primary={user?.name || user?.email}
                                        primaryTypographyProps={{
                                            fontWeight: 500,
                                            fontSize: "16px",
                                        }}
                                    />
                                </ListItem>
                                <ListItem
                                    button
                                    onClick={handleLogout}
                                    sx={{ py: 2 }}
                                >
                                    <ListItemIcon>
                                        <Logout />
                                    </ListItemIcon>
                                    <ListItemText
                                        primary="Logout"
                                        primaryTypographyProps={{
                                            fontWeight: 500,
                                            fontSize: "16px",
                                        }}
                                    />
                                </ListItem>
                            </>
                        ) : (
                            <ListItem
                                button
                                onClick={handleLoginClick}
                                sx={{ py: 2 }}
                            >
                                <ListItemIcon>
                                    <Login />
                                </ListItemIcon>
                                <ListItemText
                                    primary="Login"
                                    primaryTypographyProps={{
                                        fontWeight: 500,
                                        fontSize: "16px",
                                    }}
                                />
                            </ListItem>
                        )}
                    </List>
                </Box>
            </Drawer>

            {/* Main Content */}
            {useContainer ? (
                <Container
                    maxWidth="lg"
                    sx={{
                        mt: { xs: 2, sm: 4 },
                        mb: { xs: 2, sm: 4 },
                        px: { xs: 2, sm: 3 },
                    }}
                >
                    {children || <Outlet />}
                </Container>
            ) : (
                children || <Outlet />
            )}
        </Box>
    );
};

export default Layout;
