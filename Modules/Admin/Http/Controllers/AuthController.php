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

        request()->merge(['token' => 'true']);

        Auth::setDefaultDriver($this->guard);


        $this->_config = request('_config');

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
            $request->validated();

            if (!$jwtToken = auth()->guard($this->guard)->attempt($request->only(['email', 'password']))) {
                return $this->errorResponse(
                    [],
                    "Invalid email or Password",
                    401
                );
            }

            $admin = auth($this->guard)->user();
            if (!$admin->status || $admin->blocked) {
                $message = $admin->blocked ? "Your Account Has Been Blocked" : "Your Account Is Inactive";
                auth()->guard($this->guard)->logout();
                return $this->errorResponse(
                    [],
                    $message,
                    400
                );
            } else

                $data = [
                    'admin' => new AdminResource($admin),
                    'token' => $jwtToken,
                    'expires_in_minutes' => Auth::factory()->getTTL()
                ];

            return $this->successResponse(
                $data,
                "Logged in successfully.",
                201
            );
        } catch (Exception $e) {
            dd($e->getMessage());
            return $this->errorResponse(
                [],
                __('app.something-went-wrong'),
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
            auth()->guard($this->guard)->logout();
            return $this->messageResponse(
                "Logged out successfully.",
                true,
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



    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        try {

            $data = [
                'access_token' => Auth::guard($this->guard)->refresh(),
                'expires_in_minutes' => Auth::factory()->getTTL(),
            ];
            return $this->successResponse(
                $data,
                "",
                201
            );
        } catch (Exception $e) {
            return $this->errorResponse(
                [],
                __('app.something-went-wrong'),
                500
            );
        }
    }
}