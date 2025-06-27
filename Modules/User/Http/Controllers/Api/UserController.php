<?php

namespace Modules\User\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\User\Http\Requests\Api\User\UpdateUserRequest;
use Modules\User\Http\Requests\Api\User\ChangePasswordRequest;
use Modules\User\Http\Requests\Api\User\UpdateGeneralPreferencesRequest;
use Modules\User\Http\Requests\Api\User\UpdateUserProfileImageRequest;
use Modules\User\Repositories\UserRepository;
use Modules\User\Transformers\Api\User\UserCollection;
use Modules\User\Transformers\Api\User\UserResource;

class UserController extends Controller
{
    use ApiResponseTrait;

    protected $userRepository;

    protected $_config;
    protected $guard;

    public function __construct(UserRepository $userRepository)
    {
        $this->guard = 'user-api';
        Auth::setDefaultDriver($this->guard);
        $this->_config = request('_config');
        $this->userRepository = $userRepository;
        $this->middleware('auth:' . $this->guard)->except(['index', 'getOneByUserId', 'showBySlug']);
    }

    public function index()
    {
        try {
            $data = $this->userRepository->getAllActive()->paginate();
            return $this->successResponse(new UserCollection($data));
        } catch (Exception $e) {

            return $this->errorResponse(
                [$e->getMessage()],
                __('app.something-went-wrong'),
                500
            );
        }
    }
    public function get()
    {
        try {
            $user = auth($this->guard)->user();
            return $this->successResponse(
                new UserResource($user),
                "",
                200
            );
        } catch (Exception $e) {
            return $this->errorResponse(
                [],
                __('app.something-went-wrong'),
                500
            );
        }
    }

    public function getOneByUserId(int $id)
    {
        try {
            $user = $this->userRepository->getOneByUserId($id);
            $data = new UserResource($user);
            return $this->successResponse($data);
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
            $data = $this->userRepository->findActiveBySlug($slug);
            if (!$data) {
                return $this->errorResponse(
                    [],
                    __('app.data-not-found'),
                    404
                );
            }
            return $this->successResponse(new UserResource($data));
        } catch (Exception $e) {
            return $this->errorResponse(
                [],
                __('app.something-went-wrong'),
                500
            );
        }
    }

    public function update(UpdateUserRequest $request)
    {
        try {
            $id = auth($this->guard)->id();
            $userData = $request->only('name', 'address', 'email', 'phone' /*'country_id', 'city_id'*/);
            $userProfileData = $request->only('gender', 'birth_date');
            $updated = $this->userRepository->updateOne($userData, $userProfileData, $id);
            if ($updated) {
                return $this->successResponse(
                    new UserResource($updated),
                    "Data updated successfully",
                    200
                );
            }{
                return $this->messageResponse(
                    __("app.users.updated-failed"),
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
    public function updateUserProfileImage(UpdateUserProfileImageRequest $request)
    {
        try {
            $id = auth($this->guard)->id();
            $updated = $this->userRepository->updateUserProfileImage($id);
            if ($updated) {
                return $this->successResponse(
                    new UserResource($updated),
                    "Data updated successfully",
                    200
                );
            }{
                return $this->messageResponse(
                    __("app.users.updated-failed"),
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

    public function deleteUserProfileImage()
    {
        try {
            $id = auth($this->guard)->id();
            $updated = $this->userRepository->deleteUserProfileImage($id);
            if ($updated) {
                return $this->successResponse(
                    new UserResource($updated),
                    __('app.users.updated-successfully'),
                    200
                );
            }{
                return $this->messageResponse(
                    __("app.users.updated-failed"),
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
    public function changeAccountActivity()
    {
        try {
            $id = auth($this->guard)->id();
            $changed = $this->userRepository->changeAccountActivity($id);
            if ($changed) {
                return $this->successResponse(
                    new UserResource($changed),
                    __('app.users.updated-successfully'),
                    200
                );
            }{
                return $this->messageResponse(
                    __("app.users.updated-failed"),
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

    public function updateGeneralPreferences(UpdateGeneralPreferencesRequest $request)
    {
        try {
            $id = auth($this->guard)->id();
            $data = $request->validated();
            $updated = $this->userRepository->updateGeneralPreferences($data, $id);
            if ($updated) {
                return $this->successResponse(
                    new UserResource($updated),
                    __('app.users.updated-successfully'),
                    200
                );
            }{
                return $this->messageResponse(
                    __("app.users.updated-failed"),
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
    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            $user = auth($this->guard)->user();
            if (!Hash::check($request->current_password, $user->password)) {
                return $this->errorResponse(
                    [],
                    __("app.users.current-password-incorrect"),
                    422
                );
            }
            $data = $request->validated();
            $updated = $this->userRepository->changePassword($data['new_password'], $user->id);
            if ($updated) {
                return $this->messageResponse(
                    __("app.users.updated-successfully"),
                    true,
                    200
                );
            }{
                return $this->messageResponse(
                    __("app.users.updated-failed"),
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
}
