<?php
namespace Modules\Product\Filters;

use EloquentFilter\ModelFilter;
use Carbon\Carbon;

class ProductFilter extends ModelFilter
{
    /**
     * Search across product fields
     */
    public function search($search)
    {
        return $this->where(function ($q) use ($search) {
            return $q->whereTranslationLike('name', "%$search%")
                ->orWhereTranslationLike('short_description', "%$search%")
                ->orWhereTranslationLike('long_description', "%$search%")
                ->orWhere('code', 'LIKE', "%$search%");
        });
    }

    /**
     * Filter by exact product code
     */
    public function code($code)
    {
        return $this->where('code', $code);
    }






    public function categoryId($categoryId)
    {
        return $this->whereHas('categories', function ($q) use ($categoryId) {
            $q->where('categories.id', $categoryId);
        });
    }




    /**
     * Filter by product status
     */
    public function status($status)
    {
        return $this->where('status', $status);
    }

    /**
     * Filter by product type
     */
    public function type($type)
    {
        return $this->where('type', $type);
    }

    /**
     * Filter by exact creation date
     */
    public function createdAt($createdAt)
    {
        return $this->whereDate('created_at', Carbon::parse($createdAt));
    }

    /**
     * Filter by category name
     */
    public function categoryName($categoryName)
    {
        return $this->whereHas('categories', function ($q) use ($categoryName) {
            $q->whereTranslationLike('name', "%$categoryName%");
        });
    }

  

    /**
     * Order by latest creation date
     */
    public function latest()
    {
        return $this->orderBy('created_at', 'DESC');
    }

    /**
     * Order by position
     */
    public function position()
    {
        return $this->orderBy('position', 'asc');
    }

    /**
     * Filter active offers
     */
    public function offers($offers)
    {
        if ($offers) {
            return $this->where('offer_price', '>', 0)
                ->whereDate('offer_start_date', '<=', now())
                ->whereDate('offer_end_date', '>=', now());
        }
        return $this;
    }

    /**
     * Filter by minimum price
     */
    public function fromPrice($fromPrice)
    {
        return $this->where('price', '>=', $fromPrice);
    }

    /**
     * Filter by maximum price
     */
    public function toPrice($toPrice)
    {
        return $this->where('price', '<=', $toPrice);
    }

    /**
     * Filter by a single attribute value
     */
    public function attribute($attributeCode, $value)
    {
        return $this->whereHas('productAttributeValues', function ($query) use ($attributeCode, $value) {
            $query->whereHas('attribute', function ($attrQuery) use ($attributeCode) {
                $attrQuery->where('code', $attributeCode);
            })
            ->where(function ($valueQuery) use ($value) {
                $valueQuery->whereHas('attributeValue', function ($valQuery) use ($value) {
                    $valQuery->whereTranslation('value', $value);
                })
                ->orWhereTranslation('custom_value', $value);
            });
        });
    }

    /**
     * Filter by multiple attribute values
     */
    public function attributes(array $attributes)
    {
        return $this->where(function ($query) use ($attributes) {
            foreach ($attributes as $attributeCode => $value) {
                $query->whereHas('productAttributeValues', function ($attrQuery) use ($attributeCode, $value) {
                    $attrQuery->whereHas('attribute', function ($codeQuery) use ($attributeCode) {
                        $codeQuery->where('code', $attributeCode);
                    })
                    ->where(function ($valueQuery) use ($value) {
                        $valueQuery->whereHas('attributeValue', function ($valQuery) use ($value) {
                            $valQuery->whereTranslation('value', $value);
                        })
                        ->orWhereTranslation('custom_value', $value);
                    });
                });
            }
        });
    }

    /**
     * Filter by attribute range (for numeric attributes)
     */
    public function attributeRange($attributeCode, $min = null, $max = null)
    {
        return $this->whereHas('productAttributeValues', function ($query) use ($attributeCode, $min, $max) {
            $query->whereHas('attribute', function ($attrQuery) use ($attributeCode) {
                $attrQuery->where('code', $attributeCode)
                         ->where('type', 'number');
            });

            // Filter by minimum value
            if ($min !== null) {
                $query->where(function ($subQuery) use ($min) {
                    $subQuery->whereHas('attributeValue', function ($valQuery) use ($min) {
                        $valQuery->whereTranslation('value', '>=', $min);
                    })
                    ->orWhereTranslation('custom_value', '>=', $min);
                });
            }

            // Filter by maximum value
            if ($max !== null) {
                $query->where(function ($subQuery) use ($max) {
                    $subQuery->whereHas('attributeValue', function ($valQuery) use ($max) {
                        $valQuery->whereTranslation('value', '<=', $max);
                    })
                    ->orWhereTranslation('custom_value', '<=', $max);
                });
            }
        });
    }

    /**
     * Filter by attributes with multiple values
     */
    public function attributeIn($attributeCode, array $values)
    {
        return $this->whereHas('productAttributeValues', function ($query) use ($attributeCode, $values) {
            $query->whereHas('attribute', function ($attrQuery) use ($attributeCode) {
                $attrQuery->where('code', $attributeCode);
            })
            ->where(function ($valueQuery) use ($values) {
                $valueQuery->whereHas('attributeValue', function ($valQuery) use ($values) {
                    $valQuery->whereTranslationIn('value', $values);
                })
                ->orWhereTranslationIn('custom_value', $values);
            });
        });
    }

    /**
     * Filter by boolean attribute
     */
    public function attributeBoolean($attributeCode, $value)
    {
        return $this->whereHas('productAttributeValues', function ($query) use ($attributeCode, $value) {
            $query->whereHas('attribute', function ($attrQuery) use ($attributeCode) {
                $attrQuery->where('code', $attributeCode)
                         ->where('type', 'boolean');
            })
            ->where(function ($valueQuery) use ($value) {
                $valueQuery->whereHas('attributeValue', function ($valQuery) use ($value) {
                    $valQuery->whereTranslation('value', $value ? 1 : 0);
                })
                ->orWhereTranslation('custom_value', $value ? 1 : 0);
            });
        });
    }

    /**
     * Date range filter
     */
    public function dateFrom($date)
    {
        return $this->whereDate('created_at', '>=', Carbon::parse($date));
    }

    /**
     * Date range filter
     */
    public function dateTo($date)
    {
        return $this->whereDate('created_at', '<=', Carbon::parse($date));
    }
}