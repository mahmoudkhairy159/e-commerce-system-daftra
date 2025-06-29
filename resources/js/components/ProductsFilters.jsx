import React, { useState, useEffect } from "react";
import {
    Typography,
    Button,
    Box,
    FormControlLabel,
    Checkbox,
    Drawer,
    IconButton,
    CircularProgress,
    Slider,
    Chip,
} from "@mui/material";
import { Close, ExpandMore, FilterList } from "@mui/icons-material";

const ProductsFilters = ({
    categories,
    categoriesLoading,
    onFiltersChange,
    isMobile,
    filterDrawerOpen,
    setFilterDrawerOpen,
    resetTrigger,
    initialFilters,
}) => {
    // Filter states
    const [priceRange, setPriceRange] = useState([0, 5000]);
    const [selectedCategories, setSelectedCategories] = useState({});
    const [hasUnsavedFilters, setHasUnsavedFilters] = useState(false);
    const [appliedPriceRange, setAppliedPriceRange] = useState([0, 5000]);
    const [appliedCategories, setAppliedCategories] = useState({});
    const [desktopSidebarOpen, setDesktopSidebarOpen] = useState(true);
    const [isClearing, setIsClearing] = useState(false);

    // Initialize categories when they load
    useEffect(() => {
        if (categories.length > 0) {
            // Only initialize if selectedCategories is completely empty or doesn't have all the category IDs
            const hasAllCategoryIds = categories.every((category) =>
                selectedCategories.hasOwnProperty(category.id)
            );

            if (
                Object.keys(selectedCategories).length === 0 ||
                !hasAllCategoryIds
            ) {
                const initialCategorySelection = { all: true };
                categories.forEach((category) => {
                    initialCategorySelection[category.id] = false;
                });
                setSelectedCategories(initialCategorySelection);
                setAppliedCategories(initialCategorySelection);
            }
        }
    }, [categories]);

    // Handle reset trigger from parent component
    useEffect(() => {
        if (resetTrigger && categories.length > 0) {
            const clearedPriceRange = [0, 5000];
            const clearedCategories = { all: true };

            categories.forEach((category) => {
                clearedCategories[category.id] = false;
            });

            setPriceRange(clearedPriceRange);
            setAppliedPriceRange(clearedPriceRange);
            setSelectedCategories(clearedCategories);
            setAppliedCategories(clearedCategories);
            setHasUnsavedFilters(false);
            setIsClearing(false); // Reset clearing state
        }
    }, [resetTrigger, categories]);

    // Sync with initial filters from parent
    useEffect(() => {
        if (initialFilters && categories.length > 0) {
            setPriceRange(initialFilters.priceRange || [0, 5000]);
            setAppliedPriceRange(initialFilters.priceRange || [0, 5000]);
            setSelectedCategories(initialFilters.categories || { all: true });
            setAppliedCategories(initialFilters.categories || { all: true });
        }
    }, [initialFilters, categories]);

    // Effect to track unsaved filter changes
    useEffect(() => {
        const priceChanged =
            priceRange[0] !== appliedPriceRange[0] ||
            priceRange[1] !== appliedPriceRange[1];
        const categoriesChanged =
            JSON.stringify(selectedCategories) !==
            JSON.stringify(appliedCategories);
        setHasUnsavedFilters(priceChanged || categoriesChanged);
    }, [priceRange, selectedCategories, appliedPriceRange, appliedCategories]);

    const handleCategoryChange = (categoryKey) => {
        if (categoryKey === "all") {
            // Set all to true and all other categories to false
            const newCategories = { all: true };
            categories.forEach((category) => {
                newCategories[category.id] = false;
            });
            setSelectedCategories(newCategories);
        } else {
            // Toggle the specific category and set all to false
            const newCategories = {
                ...selectedCategories,
                all: false,
                [categoryKey]: !selectedCategories[categoryKey],
            };

            // If no categories are selected, set all to true
            const anyCategorySelected = categories.some(
                (category) => newCategories[category.id]
            );
            if (!anyCategorySelected) {
                newCategories.all = true;
            }

            setSelectedCategories(newCategories);
        }
    };

    const handlePriceRangeChange = (newRange) => {
        setPriceRange(newRange);
    };

    const handleApplyFilter = () => {
        // Apply the current UI filter state
        setAppliedPriceRange([...priceRange]);
        setAppliedCategories({ ...selectedCategories });
        setHasUnsavedFilters(false);

        if (isMobile) setFilterDrawerOpen(false);

        // Send applied filters to parent
        const filters = {
            priceRange: [...priceRange],
            categories: { ...selectedCategories },
        };
        onFiltersChange(filters);
    };

    const handleClearFilters = () => {
        setIsClearing(true);

        const clearedPriceRange = [0, 5000];
        const clearedCategories = { all: true };

        // Ensure all category IDs are set to false, even if categories array is empty
        categories.forEach((category) => {
            clearedCategories[category.id] = false;
        });

        // Also clear any existing category selections that might not be in the current categories array
        Object.keys(selectedCategories).forEach((key) => {
            if (key !== "all") {
                clearedCategories[key] = false;
            }
        });

        // Clear both UI and applied states
        setPriceRange(clearedPriceRange);
        setAppliedPriceRange(clearedPriceRange);
        setSelectedCategories(clearedCategories);
        setAppliedCategories(clearedCategories);
        setHasUnsavedFilters(false);

        // Close mobile drawer if open
        if (isMobile) setFilterDrawerOpen(false);

        // Send cleared filters to parent
        onFiltersChange({
            priceRange: clearedPriceRange,
            categories: clearedCategories,
        });

        // Reset clearing state after a short delay
        setTimeout(() => {
            setIsClearing(false);
        }, 1000);
    };

    const toggleDesktopSidebar = () => {
        setDesktopSidebarOpen(!desktopSidebarOpen);
    };

    const FilterContent = () => (
        <>
            {/* Price Filter */}
            <Box sx={{ mb: 4 }}>
                <Box
                    sx={{
                        display: "flex",
                        justifyContent: "space-between",
                        alignItems: "center",
                        mb: 2,
                    }}
                >
                    <Typography variant="subtitle1" sx={{ fontWeight: 500 }}>
                        Price
                    </Typography>
                    <ExpandMore sx={{ color: "#999" }} />
                </Box>
                <Slider
                    value={priceRange}
                    onChange={(e, newValue) => handlePriceRangeChange(newValue)}
                    valueLabelDisplay="off"
                    min={0}
                    max={5000}
                    sx={{
                        color: "#000",
                        "& .MuiSlider-thumb": {
                            backgroundColor: "#000",
                            width: 16,
                            height: 16,
                        },
                        "& .MuiSlider-track": { backgroundColor: "#000" },
                        "& .MuiSlider-rail": { backgroundColor: "#e0e0e0" },
                    }}
                />
                <Box
                    sx={{
                        display: "flex",
                        justifyContent: "space-between",
                        mt: 1,
                    }}
                >
                    <Typography
                        variant="body2"
                        sx={{ fontSize: "14px", color: "#666" }}
                    >
                        ${priceRange[0]}
                    </Typography>
                    <Typography
                        variant="body2"
                        sx={{ fontSize: "14px", color: "#666" }}
                    >
                        ${priceRange[1]}
                    </Typography>
                </Box>
            </Box>

            {/* Category Filter */}
            <Box sx={{ mb: 4 }}>
                <Box
                    sx={{
                        display: "flex",
                        justifyContent: "space-between",
                        alignItems: "center",
                        mb: 2,
                    }}
                >
                    <Typography variant="subtitle1" sx={{ fontWeight: 500 }}>
                        Category
                    </Typography>
                    <ExpandMore sx={{ color: "#999" }} />
                </Box>

                {categoriesLoading ? (
                    <Box
                        sx={{
                            display: "flex",
                            justifyContent: "center",
                            py: 2,
                        }}
                    >
                        <CircularProgress size={24} />
                    </Box>
                ) : (
                    <>
                        {/* All Categories Option */}
                        <FormControlLabel
                            control={
                                <Checkbox
                                    checked={selectedCategories.all || false}
                                    onChange={() => handleCategoryChange("all")}
                                    sx={{
                                        color: selectedCategories.all
                                            ? "#2196f3"
                                            : "#ccc",
                                        "&.Mui-checked": { color: "#2196f3" },
                                    }}
                                />
                            }
                            label={
                                <Typography
                                    sx={{ fontSize: "14px", color: "#333" }}
                                >
                                    All
                                </Typography>
                            }
                            sx={{
                                margin: 0,
                                display: "flex",
                                alignItems: "center",
                                mb: 1,
                                width: "100%",
                            }}
                        />

                        {/* Dynamic Categories */}
                        {categories.map((category) => (
                            <FormControlLabel
                                key={category.id}
                                control={
                                    <Checkbox
                                        checked={
                                            selectedCategories[category.id] ||
                                            false
                                        }
                                        onChange={() =>
                                            handleCategoryChange(category.id)
                                        }
                                        sx={{
                                            color: selectedCategories[
                                                category.id
                                            ]
                                                ? "#2196f3"
                                                : "#ccc",
                                            "&.Mui-checked": {
                                                color: "#2196f3",
                                            },
                                        }}
                                    />
                                }
                                label={
                                    <Typography
                                        sx={{ fontSize: "14px", color: "#333" }}
                                    >
                                        {category.name}
                                    </Typography>
                                }
                                sx={{
                                    margin: 0,
                                    display: "flex",
                                    alignItems: "center",
                                    mb: 1,
                                    width: "100%",
                                }}
                            />
                        ))}
                    </>
                )}
            </Box>

            {/* Filter Actions */}
            <Button
                fullWidth
                variant="contained"
                onClick={handleApplyFilter}
                disabled={!hasUnsavedFilters}
                sx={{
                    backgroundColor: hasUnsavedFilters ? "#000000" : "#e0e0e0",
                    color: hasUnsavedFilters ? "white" : "#999",
                    mb: 2,
                    py: 1.5,
                    fontSize: "14px",
                    textTransform: "none",
                    fontWeight: hasUnsavedFilters ? 600 : 400,
                    boxShadow: hasUnsavedFilters
                        ? "0 2px 8px rgba(0, 0, 0, 0.3)"
                        : "none",
                    transition: "all 0.2s ease",
                    "&:hover": {
                        backgroundColor: hasUnsavedFilters
                            ? "#333333"
                            : "#e0e0e0",
                    },
                    "&.Mui-disabled": {
                        backgroundColor: "#e0e0e0",
                        color: "#999",
                        cursor: "not-allowed",
                    },
                }}
            >
                {hasUnsavedFilters ? "Apply Filter" : "No Changes"}
            </Button>
            <Button
                fullWidth
                variant="text"
                onClick={handleClearFilters}
                disabled={isClearing}
                startIcon={
                    isClearing ? (
                        <CircularProgress size={16} sx={{ color: "#4caf50" }} />
                    ) : null
                }
                sx={{
                    color: isClearing ? "#4caf50" : "#666",
                    fontSize: "14px",
                    textTransform: "none",
                    py: 1,
                    transition: "all 0.2s ease",
                    fontWeight: isClearing ? 500 : 400,
                    "&:hover": {
                        backgroundColor: isClearing
                            ? "rgba(76, 175, 80, 0.1)"
                            : "rgba(244, 67, 54, 0.1)",
                        color: isClearing ? "#4caf50" : "#f44336",
                        transform: isClearing ? "none" : "translateY(-1px)",
                    },
                    "&:active": {
                        transform: "translateY(0px)",
                    },
                    "&.Mui-disabled": {
                        color: "#4caf50",
                        backgroundColor: "transparent",
                    },
                }}
            >
                {isClearing ? "Clearing..." : "Clear all filters"}
            </Button>
        </>
    );

    return (
        <>
            {/* Desktop Filter Sidebar */}
            {desktopSidebarOpen ? (
                <Box
                    sx={{
                        width: 300,
                        bgcolor: "white",
                        borderRight: "1px solid #e0e0e0",
                        p: 3,
                        display: { xs: "none", lg: "block" },
                    }}
                >
                    <Box
                        sx={{
                            display: "flex",
                            justifyContent: "space-between",
                            alignItems: "center",
                            mb: 3,
                        }}
                    >
                        <Box
                            sx={{
                                display: "flex",
                                alignItems: "center",
                                gap: 1,
                            }}
                        >
                            <Typography
                                variant="h6"
                                sx={{ fontWeight: 600, fontSize: "18px" }}
                            >
                                Filters
                            </Typography>
                            {hasUnsavedFilters && (
                                <Chip
                                    label="Changes"
                                    size="small"
                                    sx={{
                                        backgroundColor: "#2196f3",
                                        color: "white",
                                        fontSize: "10px",
                                        height: 20,
                                        fontWeight: 500,
                                    }}
                                />
                            )}
                        </Box>
                        <IconButton onClick={toggleDesktopSidebar} size="small">
                            <Close sx={{ color: "#999" }} />
                        </IconButton>
                    </Box>

                    <FilterContent />
                </Box>
            ) : (
                <Box
                    sx={{
                        width: 60,
                        bgcolor: "white",
                        borderRight: "1px solid #e0e0e0",
                        p: 2,
                        display: { xs: "none", lg: "flex" },
                        flexDirection: "column",
                        alignItems: "center",
                    }}
                >
                    <IconButton
                        onClick={toggleDesktopSidebar}
                        sx={{
                            mb: 2,
                            backgroundColor: hasUnsavedFilters
                                ? "#f0f8ff"
                                : "transparent",
                            border: hasUnsavedFilters
                                ? "2px solid #2196f3"
                                : "none",
                            "&:hover": {
                                backgroundColor: "#f5f5f5",
                            },
                        }}
                    >
                        <FilterList
                            sx={{
                                color: hasUnsavedFilters ? "#2196f3" : "#666",
                            }}
                        />
                    </IconButton>
                    {hasUnsavedFilters && (
                        <Box
                            sx={{
                                width: 8,
                                height: 8,
                                backgroundColor: "#2196f3",
                                borderRadius: "50%",
                                mt: -1,
                            }}
                        />
                    )}
                </Box>
            )}

            {/* Mobile Filter Drawer */}
            <Drawer
                anchor="left"
                open={filterDrawerOpen}
                onClose={() => setFilterDrawerOpen(false)}
                sx={{ display: { lg: "none" } }}
            >
                <Box sx={{ width: 300, p: 3 }}>
                    <Box
                        sx={{
                            display: "flex",
                            justifyContent: "space-between",
                            alignItems: "center",
                            mb: 3,
                        }}
                    >
                        <Box
                            sx={{
                                display: "flex",
                                alignItems: "center",
                                gap: 1,
                            }}
                        >
                            <Typography
                                variant="h6"
                                sx={{ fontWeight: 600, fontSize: "18px" }}
                            >
                                Filters
                            </Typography>
                            {hasUnsavedFilters && (
                                <Chip
                                    label="Changes"
                                    size="small"
                                    sx={{
                                        backgroundColor: "#2196f3",
                                        color: "white",
                                        fontSize: "10px",
                                        height: 20,
                                        fontWeight: 500,
                                    }}
                                />
                            )}
                        </Box>
                        <IconButton onClick={() => setFilterDrawerOpen(false)}>
                            <Close sx={{ color: "#999" }} />
                        </IconButton>
                    </Box>

                    <FilterContent />
                </Box>
            </Drawer>
        </>
    );
};

export default ProductsFilters;
