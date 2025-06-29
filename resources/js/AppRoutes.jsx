import React from "react";
import { Routes, Route, Navigate, Outlet } from "react-router-dom";
import { Box } from "@mui/material";
import LoginPage from "./pages/LoginPage";
import ProductsPage from "./pages/ProductsPage";
import ProductDetailsPage from "./pages/ProductDetailsPage";
import CartPage from "./pages/CartPage";
import OrderDetailsPage from "./pages/OrderDetailsPage";
import ProtectedRoute from "./components/ProtectedRoute";
import Layout from "./components/Layout/Layout";

function AppRoutes() {
    return (
        <Box sx={{ minHeight: "100vh" }}>
            <Routes>
                {/* Public Routes */}
                <Route path="/login" element={<LoginPage />} />

                {/* Public Products Routes */}
                <Route
                    path="/products"
                    element={
                        <Layout>
                            <ProductsPage />
                        </Layout>
                    }
                />
                <Route
                    path="/products/:slug"
                    element={
                        <Layout>
                            <ProductDetailsPage />
                        </Layout>
                    }
                />

                {/* Protected Routes with Layout */}
                <Route
                    path="/"
                    element={
                        <ProtectedRoute>
                            <Layout>
                                <Outlet />
                            </Layout>
                        </ProtectedRoute>
                    }
                >
                    <Route
                        index
                        element={<Navigate to="/products" replace />}
                    />
                    <Route path="cart" element={<CartPage />} />
                    <Route path="orders/:id" element={<OrderDetailsPage />} />
                </Route>

                {/* Catch all route */}
                <Route path="*" element={<Navigate to="/products" replace />} />
            </Routes>
        </Box>
    );
}

export default AppRoutes;
