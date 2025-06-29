import React, { useState, useEffect } from "react";
import { useParams, useNavigate } from "react-router-dom";
import {
    Grid,
    Card,
    CardContent,
    Typography,
    Button,
    Box,
    Paper,
    Divider,
    Chip,
    CircularProgress,
    Alert,
    Table,
    TableBody,
    TableCell,
    TableContainer,
    TableHead,
    TableRow,
    Stepper,
    Step,
    StepLabel,
    useMediaQuery,
    useTheme,
    Accordion,
    AccordionSummary,
    AccordionDetails,
} from "@mui/material";
import {
    ArrowBack,
    CheckCircle,
    LocalShipping,
    Schedule,
    Cancel,
    Autorenew,
    AttachMoney,
    KeyboardReturn,
    ExpandMore,
} from "@mui/icons-material";
import apiService from "../services/apiService";

// Status mapping based on OrderStatusEnum.php
const ORDER_STATUS = {
    0: "pending",
    1: "processing",
    2: "shipped",
    3: "delivered",
    4: "cancelled",
    5: "refunded",
    6: "returned",
};

const ORDER_STATUS_LABELS = {
    0: "Pending",
    1: "Processing",
    2: "Shipped",
    3: "Delivered",
    4: "Cancelled",
    5: "Refunded",
    6: "Returned",
};

const OrderDetailsPage = () => {
    const { id } = useParams();
    const navigate = useNavigate();
    const theme = useTheme();
    const isMobile = useMediaQuery(theme.breakpoints.down("md"));
    const isSmallMobile = useMediaQuery(theme.breakpoints.down("sm"));

    const [order, setOrder] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState("");

    useEffect(() => {
        loadOrder();
    }, [id]);

    const loadOrder = async () => {
        try {
            setLoading(true);
            setError("");
            const response = await apiService.getOrder(id);

            if (response.data && response.data.data) {
                setOrder(response.data.data);
            } else if (response.data) {
                setOrder(response.data);
            } else {
                throw new Error("No order data received");
            }
        } catch (err) {
            console.error("Failed to load order:", err);
            console.error("Error details:", err.response?.data || err.message);
            setError(
                `Failed to load order details: ${
                    err.response?.data?.message || err.message
                }`
            );
        } finally {
            setLoading(false);
        }
    };

    const getStatusColor = (statusCode) => {
        switch (statusCode) {
            case 0: // pending
                return "warning";
            case 1: // processing
                return "info";
            case 2: // shipped
                return "primary";
            case 3: // delivered
                return "success";
            case 4: // cancelled
                return "error";
            case 5: // refunded
                return "secondary";
            case 6: // returned
                return "warning";
            default:
                return "default";
        }
    };

    const getStatusIcon = (statusCode) => {
        switch (statusCode) {
            case 0: // pending
                return <Schedule />;
            case 1: // processing
                return <Autorenew />;
            case 2: // shipped
                return <LocalShipping />;
            case 3: // delivered
                return <CheckCircle />;
            case 4: // cancelled
                return <Cancel />;
            case 5: // refunded
                return <AttachMoney />;
            case 6: // returned
                return <KeyboardReturn />;
            default:
                return <Schedule />;
        }
    };

    const getOrderSteps = () => {
        const steps = ["Order Placed", "Processing", "Shipped", "Delivered"];
        const currentStatus = order?.status;

        let activeStep = 0;
        switch (currentStatus) {
            case 0: // pending
                activeStep = 0;
                break;
            case 1: // processing
                activeStep = 1;
                break;
            case 2: // shipped
                activeStep = 2;
                break;
            case 3: // delivered
                activeStep = 3;
                break;
            case 4: // cancelled
            case 5: // refunded
            case 6: // returned
                activeStep = -1;
                break;
            default:
                activeStep = 0;
        }

        return { steps, activeStep };
    };

    const handleBackToProducts = () => {
        navigate("/products");
    };

    // Mobile-friendly order item component
    const MobileOrderItem = ({ item }) => (
        <Card sx={{ mb: 2, p: 2 }}>
            <Box sx={{ mb: 2 }}>
                <Typography
                    variant="subtitle1"
                    sx={{
                        fontWeight: 600,
                        fontSize: { xs: "16px", sm: "18px" },
                        mb: 1,
                    }}
                >
                    {item.product?.name || "Product"}
                </Typography>
                <Typography
                    variant="body2"
                    color="text.secondary"
                    sx={{ fontSize: { xs: "12px", sm: "14px" } }}
                >
                    Original Price: $
                    {parseFloat(item.original_price).toFixed(2)}
                </Typography>
            </Box>

            <Grid container spacing={2}>
                <Grid item xs={6}>
                    <Typography
                        variant="body2"
                        color="text.secondary"
                        sx={{ fontSize: { xs: "12px", sm: "14px" } }}
                    >
                        Quantity
                    </Typography>
                    <Typography
                        variant="body1"
                        sx={{
                            fontWeight: 500,
                            fontSize: { xs: "14px", sm: "16px" },
                        }}
                    >
                        {item.quantity}
                    </Typography>
                </Grid>
                <Grid item xs={6}>
                    <Typography
                        variant="body2"
                        color="text.secondary"
                        sx={{ fontSize: { xs: "12px", sm: "14px" } }}
                    >
                        Unit Price
                    </Typography>
                    <Typography
                        variant="body1"
                        sx={{
                            fontWeight: 500,
                            fontSize: { xs: "14px", sm: "16px" },
                        }}
                    >
                        ${parseFloat(item.price || 0).toFixed(2)}
                    </Typography>
                </Grid>
                <Grid item xs={6}>
                    <Typography
                        variant="body2"
                        color="text.secondary"
                        sx={{ fontSize: { xs: "12px", sm: "14px" } }}
                    >
                        Discount
                    </Typography>
                    <Typography
                        variant="body1"
                        sx={{
                            fontWeight: 500,
                            fontSize: { xs: "14px", sm: "16px" },
                        }}
                    >
                        ${parseFloat(item.discount_amount || 0).toFixed(2)}
                    </Typography>
                </Grid>
                <Grid item xs={6}>
                    <Typography
                        variant="body2"
                        color="text.secondary"
                        sx={{ fontSize: { xs: "12px", sm: "14px" } }}
                    >
                        Subtotal
                    </Typography>
                    <Typography
                        variant="body1"
                        sx={{
                            fontWeight: 600,
                            fontSize: { xs: "14px", sm: "16px" },
                            color: "primary.main",
                        }}
                    >
                        ${parseFloat(item.subtotal || 0).toFixed(2)}
                    </Typography>
                </Grid>
            </Grid>
        </Card>
    );

    if (loading) {
        return (
            <Box
                display="flex"
                justifyContent="center"
                alignItems="center"
                minHeight="60vh"
            >
                <CircularProgress />
            </Box>
        );
    }

    if (error) {
        return (
            <Alert severity="error" sx={{ mt: 2, mx: { xs: 2, sm: 0 } }}>
                {error}
                <Button onClick={handleBackToProducts} sx={{ ml: 2 }}>
                    Back to Products
                </Button>
            </Alert>
        );
    }

    if (!order) {
        return (
            <Alert severity="warning" sx={{ mt: 2, mx: { xs: 2, sm: 0 } }}>
                Order not found.
                <Button onClick={handleBackToProducts} sx={{ ml: 2 }}>
                    Back to Products
                </Button>
            </Alert>
        );
    }

    const { steps, activeStep } = getOrderSteps();
    const statusLabel = ORDER_STATUS_LABELS[order?.status] || "Unknown";

    // Provide default values to prevent undefined errors
    const safeOrder = {
        id: 0,
        order_number: "N/A",
        status: 0,
        total_amount: 0,
        shipping_amount: 0,
        tax_amount: 0,
        discount_amount: 0,
        created_at: new Date().toISOString(),
        user: { name: "N/A" },
        order_products: [],
        ...order,
    };

    return (
        <Box sx={{ px: { xs: 2, sm: 3, md: 0 } }}>
            {/* Header */}
            <Box
                sx={{
                    display: "flex",
                    alignItems: { xs: "flex-start", sm: "center" },
                    flexDirection: { xs: "column", sm: "row" },
                    mb: { xs: 2, sm: 3 },
                    gap: { xs: 2, sm: 0 },
                }}
            >
                <Button
                    startIcon={<ArrowBack />}
                    onClick={handleBackToProducts}
                    sx={{
                        mr: { xs: 0, sm: 2 },
                        fontSize: { xs: "14px", sm: "16px" },
                        minWidth: { xs: "auto", sm: "auto" },
                    }}
                    size={isSmallMobile ? "small" : "medium"}
                >
                    Back to Products
                </Button>
                <Typography
                    variant="h4"
                    sx={{
                        flexGrow: 1,
                        fontSize: { xs: "24px", sm: "28px", md: "32px" },
                        fontWeight: 600,
                    }}
                >
                    Order Details
                </Typography>
                <Chip
                    icon={getStatusIcon(safeOrder.status)}
                    label={statusLabel}
                    color={getStatusColor(safeOrder.status)}
                    variant="outlined"
                    sx={{
                        fontSize: { xs: "12px", sm: "14px" },
                        height: { xs: 28, sm: 32 },
                    }}
                />
            </Box>

            <Grid container spacing={{ xs: 2, sm: 3 }}>
                {/* Order Information */}
                <Grid item xs={12} lg={8}>
                    {/* Order Status Stepper */}
                    {![4, 5, 6].includes(safeOrder.status) && (
                        <Paper
                            sx={{ p: { xs: 2, sm: 3 }, mb: { xs: 2, sm: 3 } }}
                        >
                            <Typography
                                variant="h6"
                                gutterBottom
                                sx={{ fontSize: { xs: "18px", sm: "20px" } }}
                            >
                                Order Progress
                            </Typography>
                            <Stepper
                                activeStep={activeStep}
                                sx={{
                                    mt: 2,
                                    "& .MuiStepLabel-label": {
                                        fontSize: { xs: "12px", sm: "14px" },
                                    },
                                }}
                                orientation={
                                    isSmallMobile ? "vertical" : "horizontal"
                                }
                            >
                                {steps.map((label) => (
                                    <Step key={label}>
                                        <StepLabel>{label}</StepLabel>
                                    </Step>
                                ))}
                            </Stepper>
                        </Paper>
                    )}

                    {/* Order Items */}
                    <Paper sx={{ p: { xs: 2, sm: 3 } }}>
                        <Typography
                            variant="h6"
                            gutterBottom
                            sx={{ fontSize: { xs: "18px", sm: "20px" } }}
                        >
                            Order Items
                        </Typography>

                        {/* Mobile View - Cards */}
                        {isMobile ? (
                            <Box sx={{ mt: 2 }}>
                                {safeOrder.order_products?.map((item) => (
                                    <MobileOrderItem
                                        key={item.id}
                                        item={item}
                                    />
                                ))}
                            </Box>
                        ) : (
                            /* Desktop View - Table */
                            <TableContainer>
                                <Table>
                                    <TableHead>
                                        <TableRow>
                                            <TableCell>Product</TableCell>
                                            <TableCell align="center">
                                                Quantity
                                            </TableCell>
                                            <TableCell align="right">
                                                Unit Price
                                            </TableCell>
                                            <TableCell align="right">
                                                Discount
                                            </TableCell>
                                            <TableCell align="right">
                                                Subtotal
                                            </TableCell>
                                        </TableRow>
                                    </TableHead>
                                    <TableBody>
                                        {safeOrder.order_products?.map(
                                            (item) => (
                                                <TableRow key={item.id}>
                                                    <TableCell>
                                                        <Box
                                                            sx={{
                                                                display: "flex",
                                                                alignItems:
                                                                    "center",
                                                            }}
                                                        >
                                                            <Box>
                                                                <Typography variant="subtitle2">
                                                                    {item
                                                                        .product
                                                                        ?.name ||
                                                                        "Product"}
                                                                </Typography>
                                                                <Typography
                                                                    variant="body2"
                                                                    color="text.secondary"
                                                                >
                                                                    Original
                                                                    Price: $
                                                                    {parseFloat(
                                                                        item.original_price
                                                                    ).toFixed(
                                                                        2
                                                                    )}
                                                                </Typography>
                                                            </Box>
                                                        </Box>
                                                    </TableCell>
                                                    <TableCell align="center">
                                                        {item.quantity}
                                                    </TableCell>
                                                    <TableCell align="right">
                                                        $
                                                        {parseFloat(
                                                            item.price || 0
                                                        ).toFixed(2)}
                                                    </TableCell>
                                                    <TableCell align="right">
                                                        $
                                                        {parseFloat(
                                                            item.discount_amount ||
                                                                0
                                                        ).toFixed(2)}
                                                    </TableCell>
                                                    <TableCell align="right">
                                                        $
                                                        {parseFloat(
                                                            item.subtotal || 0
                                                        ).toFixed(2)}
                                                    </TableCell>
                                                </TableRow>
                                            )
                                        )}
                                    </TableBody>
                                </Table>
                            </TableContainer>
                        )}
                    </Paper>
                </Grid>

                {/* Order Summary - Mobile: Full width, Desktop: Sidebar */}
                <Grid item xs={12} lg={4}>
                    <Paper sx={{ p: { xs: 2, sm: 3 }, mb: { xs: 2, sm: 3 } }}>
                        <Typography
                            variant="h6"
                            gutterBottom
                            sx={{ fontSize: { xs: "18px", sm: "20px" } }}
                        >
                            Order Summary
                        </Typography>
                        <Divider sx={{ mb: 2 }} />

                        <Box sx={{ mb: 2 }}>
                            <Typography
                                variant="body2"
                                color="text.secondary"
                                gutterBottom
                                sx={{ fontSize: { xs: "12px", sm: "14px" } }}
                            >
                                Order Number: {safeOrder.order_number}
                            </Typography>
                            <Typography
                                variant="body2"
                                color="text.secondary"
                                gutterBottom
                                sx={{ fontSize: { xs: "12px", sm: "14px" } }}
                            >
                                Order Date:{" "}
                                {new Date(
                                    safeOrder.created_at
                                ).toLocaleDateString()}
                            </Typography>
                            <Typography
                                variant="body2"
                                color="text.secondary"
                                gutterBottom
                                sx={{ fontSize: { xs: "12px", sm: "14px" } }}
                            >
                                Customer: {safeOrder.user?.name}
                            </Typography>
                            <Typography
                                variant="body2"
                                color="text.secondary"
                                gutterBottom
                                sx={{ fontSize: { xs: "12px", sm: "14px" } }}
                            >
                                Status: {statusLabel}
                            </Typography>
                        </Box>

                        <Divider sx={{ mb: 2 }} />

                        <Box sx={{ mb: 2 }}>
                            <Box
                                sx={{
                                    display: "flex",
                                    justifyContent: "space-between",
                                    mb: 1,
                                }}
                            >
                                <Typography
                                    variant="body1"
                                    sx={{
                                        fontSize: { xs: "14px", sm: "16px" },
                                    }}
                                >
                                    Subtotal
                                </Typography>
                                <Typography
                                    variant="body1"
                                    sx={{
                                        fontSize: { xs: "14px", sm: "16px" },
                                    }}
                                >
                                    $
                                    {(
                                        parseFloat(
                                            safeOrder.total_amount || 0
                                        ) -
                                        parseFloat(
                                            safeOrder.shipping_amount || 0
                                        ) -
                                        parseFloat(safeOrder.tax_amount || 0) +
                                        parseFloat(
                                            safeOrder.discount_amount || 0
                                        )
                                    ).toFixed(2)}
                                </Typography>
                            </Box>

                            {parseFloat(safeOrder.discount_amount || 0) > 0 && (
                                <Box
                                    sx={{
                                        display: "flex",
                                        justifyContent: "space-between",
                                        mb: 1,
                                    }}
                                >
                                    <Typography
                                        variant="body1"
                                        color="success.main"
                                        sx={{
                                            fontSize: {
                                                xs: "14px",
                                                sm: "16px",
                                            },
                                        }}
                                    >
                                        Discount
                                    </Typography>
                                    <Typography
                                        variant="body1"
                                        color="success.main"
                                        sx={{
                                            fontSize: {
                                                xs: "14px",
                                                sm: "16px",
                                            },
                                        }}
                                    >
                                        -$
                                        {parseFloat(
                                            safeOrder.discount_amount || 0
                                        ).toFixed(2)}
                                    </Typography>
                                </Box>
                            )}

                            <Box
                                sx={{
                                    display: "flex",
                                    justifyContent: "space-between",
                                    mb: 1,
                                }}
                            >
                                <Typography
                                    variant="body1"
                                    sx={{
                                        fontSize: { xs: "14px", sm: "16px" },
                                    }}
                                >
                                    Shipping
                                </Typography>
                                <Typography
                                    variant="body1"
                                    sx={{
                                        fontSize: { xs: "14px", sm: "16px" },
                                    }}
                                >
                                    $
                                    {parseFloat(
                                        safeOrder.shipping_amount || 0
                                    ).toFixed(2)}
                                </Typography>
                            </Box>

                            <Box
                                sx={{
                                    display: "flex",
                                    justifyContent: "space-between",
                                    mb: 1,
                                }}
                            >
                                <Typography
                                    variant="body1"
                                    sx={{
                                        fontSize: { xs: "14px", sm: "16px" },
                                    }}
                                >
                                    Tax
                                </Typography>
                                <Typography
                                    variant="body1"
                                    sx={{
                                        fontSize: { xs: "14px", sm: "16px" },
                                    }}
                                >
                                    $
                                    {parseFloat(
                                        safeOrder.tax_amount || 0
                                    ).toFixed(2)}
                                </Typography>
                            </Box>

                            <Divider sx={{ my: 2 }} />

                            <Box
                                sx={{
                                    display: "flex",
                                    justifyContent: "space-between",
                                }}
                            >
                                <Typography
                                    variant="h6"
                                    sx={{
                                        fontSize: { xs: "18px", sm: "20px" },
                                    }}
                                >
                                    Total
                                </Typography>
                                <Typography
                                    variant="h6"
                                    color="primary"
                                    sx={{
                                        fontSize: { xs: "18px", sm: "20px" },
                                    }}
                                >
                                    $
                                    {parseFloat(
                                        safeOrder.total_amount || 0
                                    ).toFixed(2)}
                                </Typography>
                            </Box>
                        </Box>
                    </Paper>

                    {/* Shipping Method */}
                    {safeOrder.shipping_method && (
                        <Paper
                            sx={{ p: { xs: 2, sm: 3 }, mb: { xs: 2, sm: 3 } }}
                        >
                            <Typography
                                variant="h6"
                                gutterBottom
                                sx={{ fontSize: { xs: "16px", sm: "18px" } }}
                            >
                                Shipping Method
                            </Typography>
                            <Typography
                                variant="body1"
                                gutterBottom
                                sx={{ fontSize: { xs: "14px", sm: "16px" } }}
                            >
                                {safeOrder.shipping_method.title}
                            </Typography>
                            <Typography
                                variant="body2"
                                color="text.secondary"
                                sx={{ fontSize: { xs: "12px", sm: "14px" } }}
                            >
                                {safeOrder.shipping_method.description}
                            </Typography>
                            <Typography
                                variant="body2"
                                color="text.secondary"
                                sx={{
                                    mt: 1,
                                    fontSize: { xs: "12px", sm: "14px" },
                                }}
                            >
                                Type: {safeOrder.shipping_method.type}
                            </Typography>
                        </Paper>
                    )}

                    {/* Shipping Address */}
                    {safeOrder.order_address && (
                        <Paper sx={{ p: { xs: 2, sm: 3 } }}>
                            <Typography
                                variant="h6"
                                gutterBottom
                                sx={{ fontSize: { xs: "16px", sm: "18px" } }}
                            >
                                {safeOrder.order_address.title ||
                                    "Shipping Address"}
                            </Typography>
                            <Typography
                                variant="body2"
                                gutterBottom
                                sx={{ fontSize: { xs: "12px", sm: "14px" } }}
                            >
                                {safeOrder.order_address.address}
                            </Typography>
                            <Typography
                                variant="body2"
                                gutterBottom
                                sx={{ fontSize: { xs: "12px", sm: "14px" } }}
                            >
                                Zip: {safeOrder.order_address.zip_code}
                            </Typography>
                            <Typography
                                variant="body2"
                                gutterBottom
                                sx={{ fontSize: { xs: "12px", sm: "14px" } }}
                            >
                                Phone: {safeOrder.order_address.phone_code}{" "}
                                {safeOrder.order_address.phone}
                            </Typography>
                        </Paper>
                    )}
                </Grid>
            </Grid>
        </Box>
    );
};

export default OrderDetailsPage;
