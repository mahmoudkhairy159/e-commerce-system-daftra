<?php

namespace Modules\Category\Observers;

use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Modules\Category\Models\Category;

class CategoryObserver  implements ShouldHandleEventsAfterCommit
{


    /**
     * Handle the Category "created" category.
     */
    public function created(Category $category): void {}

    /**
     * Handle the Category "updated" category.
     */
    public function updated(Category $category): void
    {
        // Custom logic when a Category is updated, if needed
    }

    /**
     * Handle the Category "deleted" category (soft delete).
     */
    public function deleted(Category $category): void
    {

    }

    /**
     * Handle the Category "restored" category.
     */
    public function restored(Category $category): void
    {

    }

    /**
     * Handle the Category "force deleted" category.
     */
    public function forceDeleted(Category $category): void
    {
        // Force delete related models
        if ($category->image) {
            $category->deleteFile($category->image);
        }
    }



}
