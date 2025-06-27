<?php

namespace Modules\User\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Support\Facades\Auth;
use Modules\User\Http\Requests\Admin\UserAddress\StoreUserAddressRequest;
use Modules\User\Http\Requests\Admin\UserAddress\UpdateUserAddressRequest;
use Modules\User\Models\User;
use Modules\User\Repositories\UserAddressRepository;
use Modules\User\Transformers\Admin\UserAddress\UserAddressCollection;
use Modules\User\Transformers\Admin\UserAddress\UserAddressResource;

class UserAddressController extends Controller
{
    use ApiResponseTrait;
    protected $userAddressRepository;
    protected $_config;
    protected $guard;
    public function __construct(UserAddressRepository $userAddressRepository)
    {
        $this->guard = 'admin-api';
        request()->merge(['token' => 'true']);
        Auth::setDefaultDriver($this->guard);
        $this->_config = request('_config');
        $this->userAddressRepository = $userAddressRepository;
        // permissions
        $this->middleware('auth:' . $this->guard);
        $this->middleware(['permission:userAddresses.show'])->only(['index', 'show', 'showBySlug']);
        $this->middleware(['permission:userAddresses.create'])->only(['store']);
        $this->middleware(['permission:userAddresses.update'])->only(['update']);
        $this->middleware(['permission:userAddresses.destroy'])->only(['destroy']);
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
            $data = $this->userAddressRepository->getAll()->paginate();
            return $this->successResponse(new UserAddressCollection($data));
        } catch (Exception $e) {
            return $this->errorResponse(
                [$e->getMessage()],
                __('app.something-went-wrong'),
                500
            );
        }
    }
    public function getByUserId($userId)
    {
        try {
            $data = $this->userAddressRepository->getByUserId($userId)->get();
            return $this->successResponse(UserAddressResource::collection($data));
        } catch (Exception $e) {
            return $this->errorResponse(
                [],
                __('app.something-went-wrong'),
                500
            );
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserAddressRequest $request)
    {
        try {
            $data = $request->validated();
            $data['created_by'] = auth()->guard($this->guard)->id();
            $created = $this->userAddressRepository->createOne($data);

            if ($created) {
                return $this->messageResponse(
                    __("user::app.userAddresses.created-successfully"),
                    true,
                    201
                );
            } {
                return $this->messageResponse(
                    __("user::app.userAddresses.created-failed"),
                    false,
                    400
                );
            }
        } catch (Exception $e) {
            //    return  $this->messageResponse( $e->getMessage());
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
            $data = $this->userAddressRepository->with('user')->findOrFail($id);
            return $this->successResponse(new UserAddressResource($data));
        } catch (Exception $e) {
            return $this->errorResponse(
                [],
                __('app.something-went-wrong'),
                500
            );
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserAddressRequest $request, $id)
    {
        try {

            $data = $request->validated();
            $data['updated_by'] = auth()->guard($this->guard)->id();
            $updated = $this->userAddressRepository->updateOne($data, $id);
            if ($updated) {
                return $this->messageResponse(
                    __("user::app.userAddresses.updated-successfully"),
                    true,
                    200
                );
            } {
                return $this->messageResponse(
                    __("user::app.userAddresses.updated-failed"),
                    false,
                    400
                );
            }
        } catch (Exception $e) {
            // return  $this->messageResponse( $e->getMessage());

            return $this->errorResponse(
                [],
                __('app.something-went-wrong'),
                500
            );
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $deleted = $this->userAddressRepository->deleteOne($id);
            if ($deleted) {
                return $this->messageResponse(
                    __("user::app.userAddresses.deleted-successfully"),
                    true,
                    200
                );
            } {
                return $this->messageResponse(
                    __("user::app.userAddresses.deleted-failed"),
                    false,
                    400
                );
            }
        } catch (Exception $e) {
            return $this->errorResponse(
                [],
                __('app.something-went-wrong'),
                500
            );
        }
    }
    public function setDefaultAddress($id, User $user)
    {
        try {
            $updated = $this->userAddressRepository->setDefaultAddress($id, $user);
            if ($updated) {
                return $this->messageResponse(
                    __("user::app.userAddresses.set-default-address-successfully"),
                    true,
                    200
                );
            } {
                return $this->messageResponse(
                    __("user::app.userAddresses.set-default-address-failed"),
                    false,
                    400
                );
            }
        } catch (Exception $e) {
            // return  $this->messageResponse( $e->getMessage());

            return $this->errorResponse(
                [],
                __('app.something-went-wrong'),
                500
            );
        }

    }
}