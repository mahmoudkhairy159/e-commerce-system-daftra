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
            DB::commit();
            return $created;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }


    public function updateOne(array $data, int $id)
    {
        try {
            DB::beginTransaction();

            $category = $this->model->findOrFail($id);

            if (request()->hasFile('image')) {
                if ($category->image) {
                    $this->deleteFile($category->image);
                }
                $data['image'] = $this->uploadFile(request()->file('image'), 'categories');
            }
            $updated = $category->update($data);

            // Invalidate category caches
            $this->invalidateCategoryCache();
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

            $deleted = $category->delete();

            // Invalidate category caches
            $this->invalidateCategoryCache();


            DB::commit();
            return $deleted;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
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

            DB::commit();
            return $model;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }

    /*********** Additional Functions **************/

   

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