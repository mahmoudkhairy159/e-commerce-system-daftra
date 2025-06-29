import React from "react";
import {
    FormControl,
    InputLabel,
    Select,
    MenuItem,
    useMediaQuery,
    useTheme,
} from "@mui/material";
import { SORT_OPTIONS } from "../../utils/constants";

/**
 * Reusable product sort component
 * @param {Object} props - Component props
 * @param {string} props.value - Current sort value
 * @param {Function} props.onChange - Sort change handler
 * @param {Array} props.options - Sort options (optional, uses SORT_OPTIONS by default)
 * @param {string} props.label - Select label
 * @param {Object} props.sx - Custom styles
 */
const ProductSort = ({
    value = "latest",
    onChange = null,
    options = SORT_OPTIONS,
    label = "Sort by",
    sx = {},
}) => {
    const theme = useTheme();
    const isMobile = useMediaQuery(theme.breakpoints.down("sm"));

    const handleChange = (event) => {
        if (onChange) {
            onChange(event.target.value);
        }
    };

    return (
        <FormControl
            size={isMobile ? "small" : "medium"}
            sx={{
                minWidth: { xs: 120, sm: 140 },
                ...sx,
            }}
        >
            <InputLabel id="product-sort-label">{label}</InputLabel>
            <Select
                labelId="product-sort-label"
                value={value}
                label={label}
                onChange={handleChange}
                sx={{
                    borderRadius: 2,
                    backgroundColor: "background.paper",
                }}
            >
                {options.map((option) => (
                    <MenuItem key={option.value} value={option.value}>
                        {option.label}
                    </MenuItem>
                ))}
            </Select>
        </FormControl>
    );
};

export default ProductSort;
