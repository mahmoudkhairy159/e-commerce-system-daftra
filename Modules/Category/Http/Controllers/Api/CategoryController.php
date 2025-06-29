<?php

namespace Modules\Category\Http\Controllers\Api;

use Exception;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Category\Repositories\CategoryRepository;
use Modules\Category\Transformers\Api\Category\CategoryResource;

class CategoryController extends Controller
{
    use ApiResponseTrait;

    protected $categoryRepository;
    protected $_config;
    protected $guard;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->guard = 'user-api';
        request()->merge(['token' => 'true']);
        Auth::setDefaultDriver($this->guard);
        $this->_config = request('_config');
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $currentLocale = core()->getCurrentLocale();
            $data = $this->categoryRepository->getCachedActiveCategories($currentLocale);

            if (!$data || $data->isEmpty()) {
                return $this->messageResponse(
                    __("app.data_not_found"),
                    false,
                    404
                );
            }

            return $this->successResponse(CategoryResource::collection($data));
        } catch (Exception $e) {
            return $this->errorResponse(
                [],
                __('app.something-went-wrong'),
                500
            );
        }
    }








    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        try {
            $data = $this->categoryRepository->getActiveOneById($id);

            if (!$data) {
                return $this->messageResponse(
                    __("app.data_not_found"),
                    false,
                    404
                );
            }

            return $this->successResponse(new CategoryResource($data));
        } catch (Exception $e) {
            return $this->errorResponse(
                [],
                __('app.something-went-wrong'),
                500
            );
        }
    }

    public function showBySlug(string $slug)
    {
        try {
            $data = $this->categoryRepository->findActiveBySlug($slug);

            if (!$data) {
                return $this->errorResponse([], __('app.data-not-found'), 404);
            }

            return $this->successResponse(new CategoryResource($data));
        } catch (Exception $e) {
            return $this->errorResponse([], __('app.something-went-wrong'), 500);
        }
    }
}