import React from 'react';
import { createRoot } from 'react-dom/client';
import { BrowserRouter } from 'react-router-dom';
import { ThemeProvider, createTheme } from '@mui/material/styles';
import CssBaseline from '@mui/material/CssBaseline';
import { SnackbarProvider } from 'notistack';
import { AuthProvider } from './contexts/AuthContext';
import { CartProvider } from './contexts/CartContext';
import AppRoutes from './AppRoutes';
import ErrorBoundary from './components/ErrorBoundary';

// Create Material-UI theme
const theme = createTheme({
    palette: {
        mode: 'light',
        primary: {
            main: '#1976d2',
        },
        secondary: {
            main: '#dc004e',
        },
    },
});

function App() {
    return (
        <ErrorBoundary>
            <ThemeProvider theme={theme}>
                <CssBaseline />
                <BrowserRouter>
                    <AuthProvider>
                        <CartProvider>
                            <SnackbarProvider
                                maxSnack={3}
                                anchorOrigin={{
                                    vertical: 'top',
                                    horizontal: 'right',
                                }}
                            >
                                <AppRoutes />
                            </SnackbarProvider>
                        </CartProvider>
                    </AuthProvider>
                </BrowserRouter>
            </ThemeProvider>
        </ErrorBoundary>
    );
}

// Render the app
const container = document.getElementById('app');

// Check if we're in development mode and handle HMR
if (import.meta.hot) {
    // Clear existing root in development
    if (container._reactRoot) {
        container._reactRoot.unmount();
        delete container._reactRoot;
    }
}

// Create or reuse root
if (!container._reactRoot) {
    const root = createRoot(container);
    container._reactRoot = root;
}

// Render the app
container._reactRoot.render(<App />);


