<?php

namespace Modules\Category\Repositories;

use App\Traits\SoftDeletableTrait;
use App\Traits\UploadFileTrait;
use Illuminate\Support\Facades\DB;
use Modules\Category\Models\Category;
use Modules\Category\Models\CategoryTranslation;
use Prettus\Repository\Eloquent\BaseRepository;

class CategoryRepository extends BaseRepository
{
    use UploadFileTrait;
    use SoftDeletableTrait;
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
            ->orderBy('position', 'asc')
            ->select(['id', 'name', 'image']);
    }



    /*****************************************End Retrieving For Users **************************************/

    public function getOneById($id)
    {
        return $this->model
            ->where(column: 'id', operator: $id)
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
          
            DB::commit();
            return $category->refresh();
        } catch (\Throwable $th) {

            DB::rollBack();
            return false;
        }
    }
    public function deleteOne(int $id)
    {
        try {
            DB::beginTransaction();
            $category = $this->model->findOrFail($id);
            $deleted = $category->delete();
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
            ->with('children') // Assumes a `children` relationship is defined in the model
            ->whereNull('parent_id');
    }
    public function getActiveMainCategories()
    {
        return $this->model
            ->with('children') // Assumes a `children` relationship is defined in the model
            ->active()
            ->whereNull('parent_id');
    }
    public function getTreeStructure()
    {
        // Get all categories with their children relationships eager-loaded
        $categories = Category::with('children')->whereNull('parent_id')->get();

        return $categories;
    }
    public function getActiveTreeStructure()
    {
        // Get all active categories with their active children relationships eager-loaded
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



    /*********** Status and Position Management **************/

    public function changeStatus(int $id)
    {
        try {
            DB::beginTransaction();

            $category = $this->model->findOrFail($id);
            $category->status = $category->status == Category::STATUS_ACTIVE ? Category::STATUS_INACTIVE : Category::STATUS_ACTIVE;
            $updated = $category->save();

            DB::commit();
            return $updated;
        } catch (\Throwable $th) {
            DB::rollBack();

            return false;
        }
    }

    public function updatePosition(array $data, int $id)
    {
        try {
            DB::beginTransaction();

            $model = $this->model->findOrFail($id);
            $updated = $model->update($data);

            DB::commit();
            return $updated;
        } catch (\Throwable $th) {
            DB::rollBack();

            return false;
        }
    }



}