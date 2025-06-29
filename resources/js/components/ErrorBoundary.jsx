import React from 'react';
import { Box, Typography, Button, Paper } from '@mui/material';
import { Error as ErrorIcon } from '@mui/icons-material';

class ErrorBoundary extends React.Component {
    constructor(props) {
        super(props);
        this.state = { hasError: false, error: null, errorInfo: null };
    }

    static getDerivedStateFromError(error) {
        // Update state so the next render will show the fallback UI.
        return { hasError: true };
    }

    componentDidCatch(error, errorInfo) {
        // Log the error to console
        console.error('ErrorBoundary caught an error:', error, errorInfo);

        this.setState({
            error,
            errorInfo
        });
    }

    render() {
        if (this.state.hasError) {
            return (
                <Box
                    sx={{
                        minHeight: '100vh',
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'center',
                        p: 3,
                        backgroundColor: '#f5f5f5',
                    }}
                >
                    <Paper
                        sx={{
                            p: 4,
                            maxWidth: 600,
                            textAlign: 'center',
                            border: '1px solid #e0e0e0',
                        }}
                    >
                        <ErrorIcon sx={{ fontSize: 48, color: 'error.main', mb: 2 }} />

                        <Typography variant="h4" gutterBottom>
                            Oops! Something went wrong
                        </Typography>

                        <Typography variant="body1" color="text.secondary" sx={{ mb: 3 }}>
                            We're sorry, but something unexpected happened. Please try refreshing the page.
                        </Typography>

                        <Button
                            variant="contained"
                            onClick={() => window.location.reload()}
                            sx={{
                                backgroundColor: '#000',
                                color: '#fff',
                                '&:hover': {
                                    backgroundColor: '#333',
                                },
                            }}
                        >
                            Refresh Page
                        </Button>

                        {process.env.NODE_ENV === 'development' && this.state.error && (
                            <Box sx={{ mt: 3, textAlign: 'left' }}>
                                <Typography variant="h6" gutterBottom>
                                    Error Details (Development Only):
                                </Typography>
                                <Paper sx={{ p: 2, backgroundColor: '#f9f9f9', overflow: 'auto' }}>
                                    <Typography variant="body2" component="pre" sx={{ fontSize: '12px' }}>
                                        {this.state.error.toString()}
                                        {this.state.errorInfo.componentStack}
                                    </Typography>
                                </Paper>
                            </Box>
                        )}
                    </Paper>
                </Box>
            );
        }

        return this.props.children;
    }
}

export default ErrorBoundary;
