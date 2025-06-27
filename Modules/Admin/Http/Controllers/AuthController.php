<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\Admin\Http\Requests\Auth\AdminLoginRequest;
use Modules\Admin\Http\Requests\Auth\AuthUpdateAdminRequest;
use Modules\Admin\Http\Requests\Auth\AuthUpdatePasswordRequest;
use Modules\Admin\Models\Admin;
use Modules\Admin\Repositories\AdminRepository;
use Modules\Admin\Transformers\Admin\AdminResource;

class AuthController extends Controller
{
    use ApiResponseTrait;
    /**
     * Contains current guard
     *
     * @var string
     */
    protected $guard;
    protected $adminRepository;
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;


    public function __construct(AdminRepository $adminRepository)
    {
        $this->guard = 'admin-api';
        $this->_config = request('_config');
        Auth::setDefaultDriver($this->guard);
        $this->adminRepository = $adminRepository;

        $this->middleware('auth:' . $this->guard)->except('create');
    }


    /**
     * @param AdminLoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(AdminLoginRequest $request)
    {
        try {
            $admin = Admin::where('email', $request->email)->first();

            // Check if admin exists and password is correct
            if (!$admin || !Hash::check($request->password, $admin->password)) {
                return $this->errorResponse(
                    [],
                    __('admin::app.auth.login.invalid_email_or_password'),
                    401
                );
            }

            // Check admin status
            if (!$admin->status || $admin->blocked) {
                $message = $admin->blocked ? __('admin::app.auth.login.your_account_is_blocked') : __('admin::app.auth.login.your_account_is_inactive');
                return $this->errorResponse(
                    [],
                    $message,
                    400
                );
            }

            // Create Sanctum token
            $tokenName = 'admin-api-token';
            $token = $admin->createToken($tokenName)->plainTextToken;

            $data = [
                'admin' => new AdminResource($admin),
                'token' => $token,
                'token_type' => 'Bearer',
            ];

            return $this->successResponse(
                $data,
                __('admin::app.auth.login.logged_in_successfully'),
                201
            );
        } catch (Exception $e) {
            return $this->errorResponse(
                [$e->getMessage()],
                __('admin::app.something-went-wrong'),
                500
            );
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        try {
            $admin = auth($this->guard)->user();

            // Revoke current tokens
            $admin->tokens()->delete();

            // Create new token
            $tokenName = 'admin-api-token';
            $token = $admin->createToken($tokenName)->plainTextToken;

            $data = [
                'admin' => new AdminResource($admin),
                'token' => $token,
                'token_type' => 'Bearer',
            ];

            return $this->successResponse(
                $data,
                __('admin::app.auth.token_refreshed_successfully'),
                200
            );
        } catch (Exception $e) {
            return $this->errorResponse(
                [$e->getMessage()],
                __('admin::app.something-went-wrong'),
                500
            );
        }
    }

    public function get()
    {
        try {
            $admin = auth($this->guard)->user();
            return $this->successResponse(
                new AdminResource($admin),
                "Logged in successfully.",
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



    public function update(AuthUpdateAdminRequest $request)
    {
        try {
            $admin = auth($this->guard)->user();
            $data = $request->validated();
            if (isset($data['image'])) {
                if ($admin->image) {
                    $this->adminRepository->deleteImage($admin->image);
                }
                $data['image'] = $this->adminRepository->uploadImage($data['image']);
            }

            $updatedAdmin = $this->adminRepository->update($data, $admin->id);
            return $this->successResponse(
                new AdminResource($updatedAdmin),
                "Data updated successfully",
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
    public function updatePassword(AuthUpdatePasswordRequest $request)
    {
        try {
            $admin = auth($this->guard)->user();
            $data = $request->validated();
            if (!Hash::check($data['old_password'], $admin->password)) {
                return $this->errorResponse(
                    [],
                    "Old password is incorrect",
                    400
                );
            }
            $updatedAdmin = $this->adminRepository->update(['password' => $data['password']], $admin->id);

            return $this->successResponse(
                new AdminResource($updatedAdmin),
                "password updated successfully",
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

    public function destroy()
    {
        try {
            $admin = auth($this->guard)->user();

            // Revoke all tokens for the admin
            $admin->tokens()->delete();

            return $this->messageResponse(
                __('admin::app.auth.logout.logout_successfully'),
                true,
                200
            );
        } catch (Exception $e) {
            return $this->errorResponse(
                [$e->getMessage()],
                __('admin::app.something-went-wrong'),
                500
            );
        }
    }
}