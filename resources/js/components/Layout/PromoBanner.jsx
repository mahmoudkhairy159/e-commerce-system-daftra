import React from "react";
import { Box, Typography, IconButton } from "@mui/material";
import { Close } from "@mui/icons-material";

/**
 * Promotional banner component
 * @param {Object} props - Component props
 * @param {boolean} props.visible - Whether banner is visible
 * @param {Function} props.onClose - Close handler
 * @param {string} props.message - Banner message
 * @param {string} props.actionText - Action text
 * @param {Function} props.onAction - Action handler
 */
const PromoBanner = ({
    visible = true,
    onClose = null,
    message = "Sign up and get 20% off to your first order!",
    actionText = "Sign Up Now",
    onAction = null,
}) => {
    if (!visible) return null;

    return (
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
                {message}{" "}
                {actionText && (
                    <Box
                        component="span"
                        onClick={onAction}
                        sx={{
                            textDecoration: "underline",
                            cursor: "pointer",
                            display: { xs: "block", sm: "inline" },
                            mt: { xs: 0.5, sm: 0 },
                        }}
                    >
                        {actionText}
                    </Box>
                )}
            </Typography>

            {onClose && (
                <IconButton
                    onClick={onClose}
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
            )}
        </Box>
    );
};

export default PromoBanner;
