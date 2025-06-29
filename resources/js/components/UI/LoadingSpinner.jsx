import React from "react";
import { Box, CircularProgress, Typography } from "@mui/material";

/**
 * Reusable loading spinner component
 * @param {Object} props - Component props
 * @param {string} props.message - Loading message to display
 * @param {string} props.size - Size of the spinner ('small', 'medium', 'large')
 * @param {boolean} props.overlay - Whether to show as overlay
 * @param {Object} props.sx - Custom styles
 */
const LoadingSpinner = ({
    message = "Loading...",
    size = "medium",
    overlay = false,
    sx = {},
}) => {
    const spinnerSize = {
        small: 24,
        medium: 40,
        large: 56,
    };

    const containerStyles = {
        display: "flex",
        flexDirection: "column",
        alignItems: "center",
        justifyContent: "center",
        gap: 2,
        ...(overlay && {
            position: "absolute",
            top: 0,
            left: 0,
            right: 0,
            bottom: 0,
            backgroundColor: "rgba(255, 255, 255, 0.8)",
            zIndex: 1000,
        }),
        ...sx,
    };

    return (
        <Box sx={containerStyles}>
            <CircularProgress
                size={spinnerSize[size]}
                thickness={4}
                sx={{ color: "primary.main" }}
            />
            {message && (
                <Typography
                    variant="body2"
                    color="text.secondary"
                    sx={{ textAlign: "center" }}
                >
                    {message}
                </Typography>
            )}
        </Box>
    );
};

export default LoadingSpinner;
