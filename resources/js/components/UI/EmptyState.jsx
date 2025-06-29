import React from "react";
import { Box, Typography, Button } from "@mui/material";
import { ShoppingCartOutlined, SearchOff, Inbox } from "@mui/icons-material";

/**
 * Reusable empty state component
 * @param {Object} props - Component props
 * @param {string} props.type - Type of empty state ('cart', 'search', 'general')
 * @param {string} props.title - Title text
 * @param {string} props.description - Description text
 * @param {string} props.actionText - Action button text
 * @param {Function} props.onAction - Action button handler
 * @param {React.ReactNode} props.icon - Custom icon
 * @param {Object} props.sx - Custom styles
 */
const EmptyState = ({
    type = "general",
    title = null,
    description = null,
    actionText = null,
    onAction = null,
    icon = null,
    sx = {},
}) => {
    const getDefaultContent = () => {
        switch (type) {
            case "cart":
                return {
                    icon: (
                        <ShoppingCartOutlined
                            sx={{ fontSize: 64, color: "text.secondary" }}
                        />
                    ),
                    title: "Your cart is empty",
                    description:
                        "Add some products to your cart to get started",
                    actionText: "Browse Products",
                };
            case "search":
                return {
                    icon: (
                        <SearchOff
                            sx={{ fontSize: 64, color: "text.secondary" }}
                        />
                    ),
                    title: "No results found",
                    description: "Try adjusting your search terms or filters",
                    actionText: "Clear Filters",
                };
            default:
                return {
                    icon: (
                        <Inbox sx={{ fontSize: 64, color: "text.secondary" }} />
                    ),
                    title: "Nothing to show",
                    description: "There's nothing here yet",
                    actionText: null,
                };
        }
    };

    const defaultContent = getDefaultContent();
    const displayIcon = icon || defaultContent.icon;
    const displayTitle = title || defaultContent.title;
    const displayDescription = description || defaultContent.description;
    const displayActionText = actionText || defaultContent.actionText;

    return (
        <Box
            sx={{
                display: "flex",
                flexDirection: "column",
                alignItems: "center",
                justifyContent: "center",
                textAlign: "center",
                py: 8,
                px: 4,
                ...sx,
            }}
        >
            {displayIcon}

            <Typography
                variant="h6"
                sx={{
                    mt: 2,
                    mb: 1,
                    fontWeight: 500,
                    color: "text.primary",
                }}
            >
                {displayTitle}
            </Typography>

            {displayDescription && (
                <Typography
                    variant="body2"
                    sx={{
                        mb: 3,
                        color: "text.secondary",
                        maxWidth: 400,
                    }}
                >
                    {displayDescription}
                </Typography>
            )}

            {displayActionText && onAction && (
                <Button
                    variant="contained"
                    onClick={onAction}
                    sx={{
                        mt: 1,
                        minWidth: 140,
                    }}
                >
                    {displayActionText}
                </Button>
            )}
        </Box>
    );
};

export default EmptyState;
