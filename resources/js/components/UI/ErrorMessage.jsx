import React from "react";
import { Alert, AlertTitle, Box, Button } from "@mui/material";
import { Refresh } from "@mui/icons-material";

/**
 * Reusable error message component
 * @param {Object} props - Component props
 * @param {string} props.message - Error message to display
 * @param {string} props.title - Error title
 * @param {Function} props.onRetry - Retry function
 * @param {string} props.severity - Alert severity ('error', 'warning', 'info', 'success')
 * @param {Object} props.sx - Custom styles
 */
const ErrorMessage = ({
    message = "An error occurred",
    title = null,
    onRetry = null,
    severity = "error",
    sx = {},
}) => {
    return (
        <Box sx={{ width: "100%", ...sx }}>
            <Alert
                severity={severity}
                sx={{
                    borderRadius: 2,
                    "& .MuiAlert-message": {
                        width: "100%",
                    },
                }}
            >
                {title && <AlertTitle>{title}</AlertTitle>}
                <Box
                    sx={{
                        display: "flex",
                        justifyContent: "space-between",
                        alignItems: "center",
                        gap: 2,
                    }}
                >
                    <Box sx={{ flex: 1 }}>{message}</Box>
                    {onRetry && (
                        <Button
                            variant="outlined"
                            size="small"
                            startIcon={<Refresh />}
                            onClick={onRetry}
                            sx={{
                                minWidth: "auto",
                                borderColor: "currentColor",
                                color: "inherit",
                            }}
                        >
                            Retry
                        </Button>
                    )}
                </Box>
            </Alert>
        </Box>
    );
};

export default ErrorMessage;
