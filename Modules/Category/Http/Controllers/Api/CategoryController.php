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
    /**Introduction
    Issues
    Changelog
    FAQ

     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data = $this->categoryRepository->getAllActive()->get();
            return $this->successResponse(CategoryResource::collection($data));
        } catch (Exception $e) {
            dd($e->getMessage());

            return $this->errorResponse(
                [],
                __('app.something-went-wrong'),
                500
            );
        }
    }

    public function getFeaturedCategories()
    {
        try {
            $data = $this->categoryRepository->getFeaturedCategories()->get();

            return $this->successResponse(CategoryResource::collection($data));
        } catch (Exception $e) {
            return $this->errorResponse(
                [$e->getMessage()],
                __('app.something-went-wrong'),
                500
            );
        }
    }

    /**
     * Get child categories by parent ID.
     */
    public function getByParentId($parentId)
    {
        try {
            // Fetch child categories where parent_id matches the provided parentId
            $data = $this->categoryRepository->getByParentId($parentId)->get();

            // Return success response with the fetched categories
            return $this->successResponse(CategoryResource::collection($data));
        } catch (Exception $e) {
            return $this->errorResponse([], __('app.something-went-wrong'), 500);
        }
    }
    /**
     * Get the hierarchical structure of categories.
     */
    public function getMainCategories()
    {
        try {
            // Retrieve all categories, and organize them in a tree structure
            $data = $this->categoryRepository->getMainCategories()->get();
            return $this->successResponse(CategoryResource::collection($data));
        } catch (Exception $e) {
            return $this->errorResponse([], __('app.something-went-wrong'), 500);
        }
    }
    public function getTreeStructure()
    {
        try {
            // Retrieve all categories, and organize them in a tree structure
            $data = $this->categoryRepository->getTreeStructure();
            return $this->successResponse(CategoryResource::collection($data));
        } catch (Exception $e) {
            return $this->errorResponse([], __('app.something-went-wrong'), 500);
        }
    }



    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        try {
            $data = $this->categoryRepository->getActiveOneById($id);
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
            $data = $this->categoryRepository->findBySlug($slug);
            if (!$data) {
                return $this->errorResponse([], __('app.data-not-found'), 404);
            }
            return $this->successResponse(new CategoryResource($data));
        } catch (Exception $e) {
            return $this->errorResponse([], __('app.something-went-wrong'), 500);
        }
    }





}
