<?php

namespace Modules\Product\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\Product\Models\ProductReview ;
use Prettus\Repository\Eloquent\BaseRepository;

class ProductReviewRepository extends BaseRepository
{
    public function model()
    {
        return ProductReview::class;
    }
    public function getByUserId($userId)
    {
        return  $this->model
            ->with([
                'user' => function ($query) {
                    $query->select('id', 'name', 'image');
                },
            ])->where('user_id', $userId)
            ->filter(request()->all())
            ->orderBy('created_at', 'desc');
    }
    public function getByProductId($productId)
    {
        return $this->model
            ->with([
                'user' => function ($query) {
                    $query->select('id', 'name', 'image');
                },
            ])
            ->where('product_id', $productId)
            ->filter(request()->all())
            ->orderBy('created_at', 'desc');
    }
    public function getReviewStatistics($productId)
    {
        // Get count of reviews by rating
        $ratingCounts = $this->model
            ->where('product_id', $productId)
            ->select('rating', DB::raw('count(*) as count'))
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();

        // Ensure all ratings (1-5) have a value
        $starCounts = [];
        for ($i = 1; $i <= 5; $i++) {
            $starCounts[$i] = $ratingCounts[$i] ?? 0;
        }

        // Get total count of reviews
        $totalCount = array_sum($starCounts);

        // Calculate average rating
        $averageRating = $totalCount > 0
            ? round(array_sum(array_map(function($rating, $count) {
                return $rating * $count;
              }, array_keys($starCounts), $starCounts)) / $totalCount, 1)
            : 0;

        return [
            'average_rating' => $averageRating,
            'total_reviews' => $totalCount,
            'star_counts' => $starCounts
        ];
    }

    public function getOneById($id)
    {
        return  $this->model
            ->with([
                'user' => function ($query) {
                    $query->select('id', 'name', 'image');
                },
            ])
            ->where('id', $id)
            ->filter(request()->all())
            ->first();
    }



    public function updateOne(array $data, int $id)
    {
        try {
            DB::beginTransaction();
            $model = $this->model->where('user_id', $data['user_id'])->findOrFail($id);
            $updated = $model->update($data);
            DB::commit();
            return $updated;
        } catch (\Throwable $th) {

            DB::rollBack();
            return false;
        }
    }
    public function deleteOneByUser( int $id)
    {
        try {
            DB::beginTransaction();
            $model = $this->model->where('user_id', auth('user-api')->id())->findOrFail($id);
            $deleted = $model->delete();
            DB::commit();
            return $deleted;
        } catch (\Throwable $th) {

            DB::rollBack();
            return false;
        }
    }
    public function deleteOne( int $id)
    {
        try {
            DB::beginTransaction();
            $model = $this->model->findOrFail($id);
            $deleted = $model->delete();
            DB::commit();
            return $deleted;
        } catch (\Throwable $th) {

            DB::rollBack();
            return false;
        }
    }


}
