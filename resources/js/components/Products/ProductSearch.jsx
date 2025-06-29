import React, { useState, useEffect } from "react";
import {
    TextField,
    InputAdornment,
    IconButton,
    Box,
    Chip,
    useMediaQuery,
    useTheme,
} from "@mui/material";
import { Search, Clear } from "@mui/icons-material";
import { useDebounce } from "../../hooks/useDebounce";

/**
 * Reusable product search component
 * @param {Object} props - Component props
 * @param {string} props.value - Search input value
 * @param {Function} props.onChange - Input change handler
 * @param {Function} props.onSearch - Search submit handler
 * @param {string} props.placeholder - Input placeholder
 * @param {boolean} props.showSearchChip - Whether to show applied search as chip
 * @param {Function} props.onClearSearch - Clear search handler
 * @param {Object} props.sx - Custom styles
 */
const ProductSearch = ({
    value = "",
    onChange = null,
    onSearch = null,
    placeholder = "Search products...",
    showSearchChip = true,
    onClearSearch = null,
    sx = {},
}) => {
    const theme = useTheme();
    const isMobile = useMediaQuery(theme.breakpoints.down("sm"));
    const [inputValue, setInputValue] = useState(value);
    const { debouncedValue, isDebouncing } = useDebounce(inputValue, 300);

    // Update input when external value changes
    useEffect(() => {
        setInputValue(value);
    }, [value]);

    // Trigger search when debounced value changes
    useEffect(() => {
        if (onChange && debouncedValue !== value) {
            onChange(debouncedValue);
        }
    }, [debouncedValue, onChange, value]);

    const handleInputChange = (event) => {
        setInputValue(event.target.value);
    };

    const handleKeyPress = (event) => {
        if (event.key === "Enter") {
            event.preventDefault();
            if (onSearch) {
                onSearch(inputValue);
            }
        }
    };

    const handleSearchClick = () => {
        if (onSearch) {
            onSearch(inputValue);
        }
    };

    const handleClear = () => {
        setInputValue("");
        if (onClearSearch) {
            onClearSearch();
        }
        if (onChange) {
            onChange("");
        }
    };

    return (
        <Box sx={{ width: "100%", ...sx }}>
            <TextField
                fullWidth
                variant="outlined"
                placeholder={placeholder}
                value={inputValue}
                onChange={handleInputChange}
                onKeyPress={handleKeyPress}
                size={isMobile ? "small" : "medium"}
                InputProps={{
                    startAdornment: (
                        <InputAdornment position="start">
                            <IconButton
                                onClick={handleSearchClick}
                                edge="start"
                                size="small"
                                disabled={isDebouncing}
                            >
                                <Search />
                            </IconButton>
                        </InputAdornment>
                    ),
                    endAdornment: inputValue && (
                        <InputAdornment position="end">
                            <IconButton
                                onClick={handleClear}
                                edge="end"
                                size="small"
                            >
                                <Clear />
                            </IconButton>
                        </InputAdornment>
                    ),
                }}
                sx={{
                    "& .MuiOutlinedInput-root": {
                        borderRadius: 2,
                        backgroundColor: "background.paper",
                    },
                }}
            />

            {/* Applied search chip */}
            {showSearchChip && value && value !== inputValue && (
                <Box sx={{ mt: 2 }}>
                    <Chip
                        label={`Search: "${value}"`}
                        onDelete={handleClear}
                        size="small"
                        variant="outlined"
                        sx={{
                            borderRadius: 1,
                            "& .MuiChip-deleteIcon": {
                                fontSize: 16,
                            },
                        }}
                    />
                </Box>
            )}
        </Box>
    );
};

export default ProductSearch;
