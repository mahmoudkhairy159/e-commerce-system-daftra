import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/App.jsx'],
            refresh: true,
        }),
        react({
            jsxRuntime: 'automatic',
            // Add these options to help with detection
            include: "**/*.{jsx,tsx}",
            exclude: /node_modules/,
        }),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
    server: {
        hmr: {
            host: 'localhost',
        },
        host: 'localhost',
        port: 5173,
    },
    optimizeDeps: {
        include: ['react', 'react-dom'],
    },
    // Add esbuild options for JSX
    esbuild: {
        jsx: 'automatic',
    },
});