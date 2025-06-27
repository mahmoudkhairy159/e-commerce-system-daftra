<?php

namespace Modules\User\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Support\Facades\Auth;
use Modules\User\Http\Requests\Api\UserAddress\StoreUserAddressRequest;
use Modules\User\Http\Requests\Api\UserAddress\UpdateUserAddressRequest;
use Modules\User\Repositories\UserAddressRepository;
use Modules\User\Transformers\Api\UserAddress\UserAddressCollection;
use Modules\User\Transformers\Api\UserAddress\UserAddressResource;

class UserAddressController extends Controller
{
    use ApiResponseTrait;
    protected $userAddressRepository;
    protected $_config;
    protected $guard;
    public function __construct(UserAddressRepository $userAddressRepository)
    {
        $this->guard = 'user-api';
        request()->merge(['token' => 'true']);
        Auth::setDefaultDriver($this->guard);
        $this->_config = request('_config');
        $this->userAddressRepository = $userAddressRepository;
        // permissions
        $this->middleware('auth:' . $this->guard);
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
            $userId=auth()->guard('user-api')->id();
            $data = $this->userAddressRepository->getByUserId( $userId)->get();
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
            $data['user_id'] = auth()->guard($this->guard)->id();
            $created = $this->userAddressRepository->createOne($data);

            if ($created) {
                return $this->messageResponse(
                    __("user::app.userAddresses.created-successfully"),
                    true,
                    201
                );
            }{
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
            $data = $this->userAddressRepository->where('user_id', auth()->guard('user-api')->id())->findOrFail($id);
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
            $updated = $this->userAddressRepository->updateOneByUser($data, $id);
            if ($updated) {
                return $this->messageResponse(
                    __("user::app.userAddresses.updated-successfully"),
                    true,
                    200
                );
            }{
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
            }{
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
    public function setDefaultAddress($id)
    {
        try {
            $user = auth()->guard('user-api')->user();
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