<?php

namespace Modules\Category\Repositories;

use App\Traits\SoftDeletableTrait;
use App\Traits\UploadFileTrait;
use App\Traits\CacheTrait;
use Illuminate\Support\Facades\DB;
use Modules\Category\Models\Category;
use Modules\Category\Models\CategoryTranslation;
use Prettus\Repository\Eloquent\BaseRepository;

class CategoryRepository extends BaseRepository
{
    use UploadFileTrait;
    use SoftDeletableTrait;
    use CacheTrait;

    public $retrievedData = [
        'id',
        'parent_id',
        'name',
        'slug',
        'code',
        'image',
        'position',
        'is_featured',
        'status',
        'created_at',
        'updated_at',
    ];

    public function model()
    {
        return Category::class;
    }

    /***************************************** Retrieving For Admins **************************************/
    public function getAll()
    {
        return $this->model
            ->filter(request()->all())
            ->orderBy('created_at', 'desc');
    }

    /*****************************************End Retrieving For Admins **************************************/

    /***************************************** Retrieving For Users **************************************/
    public function getAllActive()
    {
        return $this->model
            ->filter(request()->all())
            ->active()
            ->orderBy('position', 'asc');
    }

    public function getFeaturedCategories()
    {
        return $this->model
            ->take(10)
            ->active()
            ->orderBy('position', 'asc');
    }

    /*****************************************End Retrieving For Users **************************************/

    /***************************************** Cached Methods **************************************/

    /**
     * Get cached all categories with filter support
     */
    public function getCachedCategories()
    {
        // If filters are present, query database directly with filters
        if (!$this->shouldUseCache()) {
            return $this->getAllFiltered();
        }

        return app('cache.categories')->getAll();
    }

    /**
     * Get cached active categories with filter support
     */
    public function getCachedActiveCategories(string $locale = null)
    {
        // If filters are present, query database directly with filters
        if (!$this->shouldUseCache()) {
            return $this->getAllActiveFiltered();
        }

        return app('cache.categories')->getAllActive($locale);
    }

    /**
     * Get cached featured categories with filter support
     */
    public function getCachedFeaturedCategories(string $locale = null)
    {
        // If filters are present, query database directly with filters
        if (!$this->shouldUseCache()) {
            return $this->getFeaturedCategoriesFiltered();
        }

        return app('cache.categories')->getFeatured($locale);
    }

    /**
     * Get cached main categories with filter support
     */
    public function getCachedMainCategories(string $locale = null)
    {
        // If filters are present, query database directly with filters
        if (!$this->shouldUseCache()) {
            return $this->getMainCategoriesFiltered();
        }

        return app('cache.categories')->getMainCategories($locale);
    }

    /**
     * Get cached category tree structure with filter support
     */
    public function getCachedTreeStructure(string $locale = null)
    {
        // If filters are present, query database directly with filters
        if (!$this->shouldUseCache()) {
            return $this->getTreeStructureFiltered();
        }

        return app('cache.categories')->getTree($locale);
    }

    /**
     * Get cached categories by parent ID with filter support
     */
    public function getCachedByParentId(int $parentId, string $locale = null)
    {
        // If filters are present, query database directly with filters
        if (!$this->shouldUseCache()) {
            return $this->getByParentIdFiltered($parentId);
        }

        return app('cache.categories')->getByParent($parentId, $locale);
    }

    /*****************************************Filtered Query Methods ********************************************/

    /**
     * Get all categories with filters applied
     */
    private function getAllFiltered()
    {
        return $this->model
            ->filter(request()->all())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get active categories with filters applied
     */
    private function getAllActiveFiltered()
    {
        return $this->model
            ->active()
            ->filter(request()->all())
            ->orderBy('position', 'asc')
            ->get();
    }

    /**
     * Get featured categories with filters applied
     */
    private function getFeaturedCategoriesFiltered()
    {
        return $this->model
            ->active()
            ->where('is_featured', 1)
            ->filter(request()->all())
            ->orderBy('position', 'asc')
            ->take(10)
            ->get();
    }

    /**
     * Get main categories with filters applied
     */
    private function getMainCategoriesFiltered()
    {
        return $this->model
            ->active()
            ->whereNull('parent_id')
            ->filter(request()->all())
            ->orderBy('position', 'asc')
            ->get();
    }

    /**
     * Get tree structure with filters applied
     */
    private function getTreeStructureFiltered()
    {
        return $this->model
            ->active()
            ->with([
                'children' => function ($query) {
                    $query->active()->orderBy('position', 'asc');
                }
            ])
            ->whereNull('parent_id')
            ->filter(request()->all())
            ->orderBy('position', 'asc')
            ->get();
    }

    /**
     * Get categories by parent ID with filters applied
     */
    private function getByParentIdFiltered(int $parentId)
    {
        return $this->model
            ->active()
            ->where('parent_id', $parentId)
            ->filter(request()->all())
            ->orderBy('position', 'asc')
            ->get();
    }

    /*****************************************End Cached Methods **************************************/

    public function getOneById($id)
    {
        return $this->model
            ->where('id', $id)
            ->first();
    }

    public function getActiveOneById($id)
    {
        return $this->model
            ->where('id', $id)
            ->active()
            ->first();
    }

    public function findBySlug(string $slug)
    {
        $id = CategoryTranslation::where('slug', $slug)->value('category_id');
        return $this->model->where('id', $id)->first();
    }

    public function findActiveBySlug(string $slug)
    {
        $id = CategoryTranslation::where('slug', $slug)->value('category_id');
        return $this->model
            ->where('id', $id)
            ->active()
            ->first();
    }

    public function createOne(array $data)
    {
        try {
            DB::beginTransaction();

            if (request()->hasFile('image')) {
                $data['image'] = $this->uploadFile(request()->file('image'), 'categories');
            }
            $created = $this->model->create($data);

            // Invalidate category caches
            $this->invalidateCategoryCache();
            if (isset($data['parent_id']) && $data['parent_id']) {
                app('cache.categories')->invalidate($data['parent_id']);
            }

            DB::commit();
            return $created;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Create a new category with cache invalidation
     */
    public function create(array $attributes)
    {
        try {
            DB::beginTransaction();

            if (request()->hasFile('image')) {
                $attributes['image'] = $this->uploadFile(request()->file('image'), 'categories');
            }

            $category = parent::create($attributes);

            // Invalidate category caches
            $this->invalidateCategoryCache();
            if (isset($attributes['parent_id']) && $attributes['parent_id']) {
                app('cache.categories')->invalidate($attributes['parent_id']);
            }

            DB::commit();
            return $category;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function updateOne(array $data, int $id)
    {
        try {
            DB::beginTransaction();

            $category = $this->model->findOrFail($id);
            $oldParentId = $category->parent_id;

            if (request()->hasFile('image')) {
                if ($category->image) {
                    $this->deleteFile($category->image);
                }
                $data['image'] = $this->uploadFile(request()->file('image'), 'categories');
            }
            $updated = $category->update($data);

            // Invalidate category caches
            $this->invalidateCategoryCache();

            // Invalidate old parent cache
            if ($oldParentId) {
                app('cache.categories')->invalidate($oldParentId);
            }

            // Invalidate new parent cache if different
            if (isset($data['parent_id']) && $data['parent_id'] && $data['parent_id'] !== $oldParentId) {
                app('cache.categories')->invalidate($data['parent_id']);
            }

            DB::commit();
            return $category->refresh();
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Update a category with cache invalidation
     */
    public function update(array $attributes, $id)
    {
        try {
            DB::beginTransaction();

            $category = $this->find($id);
            $oldParentId = $category->parent_id;

            if (request()->hasFile('image')) {
                if ($category->image) {
                    $this->deleteFile($category->image);
                }
                $attributes['image'] = $this->uploadFile(request()->file('image'), 'categories');
            }

            $updated = parent::update($attributes, $id);

            // Invalidate category caches
            $this->invalidateCategoryCache();

            // Invalidate old parent cache
            if ($oldParentId) {
                app('cache.categories')->invalidate($oldParentId);
            }

            // Invalidate new parent cache if different
            if (isset($attributes['parent_id']) && $attributes['parent_id'] && $attributes['parent_id'] !== $oldParentId) {
                app('cache.categories')->invalidate($attributes['parent_id']);
            }

            DB::commit();
            return $updated;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function deleteOne(int $id)
    {
        try {
            DB::beginTransaction();
            $category = $this->model->findOrFail($id);
            $parentId = $category->parent_id;

            $deleted = $category->delete();

            // Invalidate category caches
            $this->invalidateCategoryCache();
            if ($parentId) {
                app('cache.categories')->invalidate($parentId);
            }

            DB::commit();
            return $deleted;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Delete a category with cache invalidation
     */
    public function delete($id)
    {
        try {
            DB::beginTransaction();

            $category = $this->find($id);
            $parentId = $category->parent_id;

            $deleted = parent::delete($id);

            // Invalidate category caches
            $this->invalidateCategoryCache();
            if ($parentId) {
                app('cache.categories')->invalidate($parentId);
            }

            DB::commit();
            return $deleted;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function restore(int $id)
    {
        try {
            DB::beginTransaction();
            $model = $this->model->withTrashed()->findOrFail($id);
            $restored = $model->restore();

            // Invalidate category caches
            $this->invalidateCategoryCache();
            if ($model->parent_id) {
                app('cache.categories')->invalidate($model->parent_id);
            }

            DB::commit();
            return $model;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }

    /*********** Additional Functions **************/

    public function getByParentId(int $parentId)
    {
        return $this->model->where('parent_id', $parentId);
    }

    public function getActiveByParentId(int $parentId)
    {
        return $this->model
            ->where('status', Category::STATUS_ACTIVE)
            ->where('parent_id', $parentId);
    }

    public function getMainCategories()
    {
        return $this->model
            ->with('children')
            ->whereNull('parent_id');
    }

    public function getActiveMainCategories()
    {
        return $this->model
            ->with([
                'children' => function ($query) {
                    $query->active();
                }
            ])
            ->active()
            ->whereNull('parent_id');
    }

    public function getTreeStructure()
    {
        $categories = Category::with('children')->whereNull('parent_id')->get();
        return $categories;
    }

    public function getActiveTreeStructure()
    {
        $categories = Category::with([
            'children' => function ($query) {
                $query->active();
            }
        ])
            ->active()
            ->whereNull('parent_id')
            ->get();

        return $categories;
    }

    public function bulkUpdateStatus(array $ids, int $status)
    {
        try {
            DB::beginTransaction();

            $updated = $this->model->whereIn('id', $ids)->update(['status' => $status]);

            // Invalidate all category caches since multiple categories changed
            app('cache.categories')->invalidateAll();

            DB::commit();
            return $updated;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }

    private function uploadAndDeleteImage($file = null, $existingImage = null)
    {
        if ($file) {
            if ($existingImage) {
                $this->deleteFile($existingImage);
            }
            return $this->uploadFile($file, 'categories');
        }
        return $existingImage;
    }

    public function changeStatus(int $id)
    {
        try {
            DB::beginTransaction();
            $category = $this->model->findOrFail($id);
            $newStatus = $category->status == Category::STATUS_ACTIVE ? Category::STATUS_INACTIVE : Category::STATUS_ACTIVE;
            $category->update(['status' => $newStatus]);

            // Invalidate category caches
            $this->invalidateCategoryCache();
            if ($category->parent_id) {
                app('cache.categories')->invalidate($category->parent_id);
            }

            DB::commit();
            return $category;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }

    public function updatePosition(array $data, int $id)
    {
        try {
            DB::beginTransaction();
            $category = $this->model->findOrFail($id);
            $category->update($data);

            // Invalidate category caches since position affects ordering
            $this->invalidateCategoryCache();
            if ($category->parent_id) {
                app('cache.categories')->invalidate($category->parent_id);
            }

            DB::commit();
            return $category;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }
}
