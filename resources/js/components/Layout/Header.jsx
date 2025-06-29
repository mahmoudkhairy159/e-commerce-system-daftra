import React from "react";
import {
    AppBar,
    Toolbar,
    Typography,
    IconButton,
    Badge,
    Box,
    Button,
    useMediaQuery,
    useTheme,
} from "@mui/material";
import { ShoppingCart, Menu as MenuIcon } from "@mui/icons-material";

/**
 * Header component
 * @param {Object} props - Component props
 * @param {Function} props.onCartClick - Cart click handler
 * @param {Function} props.onProductsClick - Products click handler
 * @param {Function} props.onMobileMenuToggle - Mobile menu toggle handler
 * @param {number} props.cartItemsCount - Number of items in cart
 * @param {boolean} props.isAuthenticated - Authentication status
 */
const Header = ({
    onCartClick = null,
    onProductsClick = null,
    onMobileMenuToggle = null,
    cartItemsCount = 0,
    isAuthenticated = false,
}) => {
    const theme = useTheme();
    const isMobile = useMediaQuery(theme.breakpoints.down("md"));

    return (
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
                            onClick={onMobileMenuToggle}
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
                        onClick={onProductsClick}
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
                        <Box sx={{ display: "flex", gap: 3 }}>
                            <Button
                                onClick={onProductsClick}
                                sx={{
                                    color: "#000",
                                    textTransform: "none",
                                    fontSize: "16px",
                                    fontWeight: 500,
                                    "&:hover": {
                                        backgroundColor: "transparent",
                                        textDecoration: "underline",
                                    },
                                }}
                            >
                                Shop
                            </Button>
                        </Box>
                    )}
                </Box>

                {/* Right Side - Actions */}
                <Box sx={{ display: "flex", alignItems: "center", gap: 1 }}>
                    {/* Cart Button */}
                    <IconButton
                        onClick={onCartClick}
                        sx={{
                            color: "#000",
                            p: { xs: 1, sm: 1.5 },
                        }}
                    >
                        <Badge
                            badgeContent={cartItemsCount}
                            color="primary"
                            sx={{
                                "& .MuiBadge-badge": {
                                    fontSize: "0.7rem",
                                    minWidth: "18px",
                                    height: "18px",
                                },
                            }}
                        >
                            <ShoppingCart
                                sx={{
                                    fontSize: { xs: 20, sm: 24 },
                                }}
                            />
                        </Badge>
                    </IconButton>
                </Box>
            </Toolbar>
        </AppBar>
    );
};

export default Header;
