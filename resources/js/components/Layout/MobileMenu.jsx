import React from 'react';
import {
    Drawer,
    List,
    ListItem,
    ListItemIcon,
    ListItemText,
    Divider,
    Typography,
    Box
} from '@mui/material';
import {
    Store,
    Login,
    Person,
    Logout,
} from '@mui/icons-material';

/**
 * Mobile menu component
 * @param {Object} props - Component props
 * @param {boolean} props.open - Whether menu is open
 * @param {Function} props.onClose - Close handler
 * @param {boolean} props.isAuthenticated - Authentication status
 * @param {Object} props.user - User object
 * @param {Function} props.onProductsClick - Products click handler
 * @param {Function} props.onLoginClick - Login click handler
 * @param {Function} props.onLogout - Logout handler
 */
const MobileMenu = ({
    open = false,
    onClose = null,
    isAuthenticated = false,
    user = null,
    onProductsClick = null,
    onLoginClick = null,
    onLogout = null
}) => {
    const handleItemClick = (handler) => {
        if (handler) {
            handler();
        }
        if (onClose) {
            onClose();
        }
    };

    return (
        <Drawer
            anchor="left"
            open={open}
            onClose={onClose}
            sx={{
                '& .MuiDrawer-paper': {
                    width: 280,
                    backgroundColor: 'background.paper'
                }
            }}
        >
            <Box sx={{ p: 2, borderBottom: 1, borderColor: 'divider' }}>
                <Typography variant="h6" sx={{ fontWeight: 'bold' }}>
                    Menu
                </Typography>
            </Box>

            <List>
                {/* Products */}
                <ListItem
                    button
                    onClick={() => handleItemClick(onProductsClick)}
                >
                    <ListItemIcon>
                        <Store />
                    </ListItemIcon>
                    <ListItemText primary="Shop" />
                </ListItem>

                <Divider />

                {/* Authentication */}
                {isAuthenticated ? (
                    <>
                        <ListItem>
                            <ListItemIcon>
                                <Person />
                            </ListItemIcon>
                            <ListItemText
                                primary={user?.name || 'User'}
                                secondary={user?.email}
                            />
                        </ListItem>

                        <ListItem
                            button
                            onClick={() => handleItemClick(onLogout)}
                        >
                            <ListItemIcon>
                                <Logout />
                            </ListItemIcon>
                            <ListItemText primary="Logout" />
                        </ListItem>
                    </>
                ) : (
                    <ListItem
                        button
                        onClick={() => handleItemClick(onLoginClick)}
                    >
                        <ListItemIcon>
                            <Login />
                        </ListItemIcon>
                        <ListItemText primary="Login" />
                    </ListItem>
                )}
            </List>
        </Drawer>
    );
};

export default MobileMenu;
