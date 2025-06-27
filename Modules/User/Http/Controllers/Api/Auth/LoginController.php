<?php

namespace Modules\User\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\User\Http\Requests\Api\Auth\UserLoginRequest;
use Modules\User\Models\User;
use Modules\User\Repositories\UserRepository;
use Modules\User\Traits\UserOtpTrait;
use Modules\User\Transformers\Api\User\UserResource;

class LoginController extends Controller
{
    use ApiResponseTrait, UserOtpTrait;

    protected $userRepository;

    protected $_config;
    protected $guard;

    public function __construct(UserRepository $userRepository)
    {
        $this->guard = 'user-api';
        $this->_config = request('_config');
        Auth::setDefaultDriver($this->guard);
        $this->userRepository = $userRepository;
        $this->middleware('auth:' . $this->guard)->only(['refresh']);
    }

    /**
     * Handle user login.
     *
     * @param UserLoginRequest $request
     */
    public function login(UserLoginRequest $request)
    {
        try {
            $request->validated();



            // Find user by email with relationships
            $user = User::where('email', $request->email)
                ->with('profile', 'userAddresses', 'defaultAddress')
                ->withCount('orders')
                ->first();

            // Check if user exists and password is correct
            if (!$user || !Hash::check($request->password, $user->password)) {
                return $this->errorResponse(
                    [],
                    __('app.auth.login.invalid_email_or_password'),
                    401
                );
            }

            // Check user status
            if (!$user->status || $user->blocked) {
                $message = $user->blocked ? __('app.auth.login.your_account_is_blocked') : __('app.auth.login.your_account_is_inactive');
                return $this->errorResponse(
                    [],
                    $message,
                    400
                );
            }

            // Update last login time
            $user->last_login_at = Carbon::now();
            $user->save();

            // Create Sanctum token
            $tokenName = 'user-api-token';
            $token = $user->createToken($tokenName)->plainTextToken;

            $msg = __('app.auth.login.logged_in_successfully');
            if (!$user->email_verified_at) {
                $msg = __('app.auth.login.logged_in_successfully_and_Verification_code_sent');
                $this->sendOtpCode($user);
            }
            $data = [
                'user' => new UserResource($user),
                'token' => $token,
                'token_type' => 'Bearer',
            ];

            return $this->successResponse(
                $data,
                $msg,
                201
            );
        } catch (Exception $e) {
            return $this->errorResponse(
                [$e->getMessage()],
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
            $user = auth($this->guard)->user();

            // Revoke current tokens
            $user->tokens()->delete();

            // Create new token
            $tokenName = 'user-api-token';
            $token = $user->createToken($tokenName)->plainTextToken;

            $data = [
                'user' => new UserResource($user),
                'token' => $token,
                'token_type' => 'Bearer',
            ];

            return $this->successResponse(
                $data,
                __('app.auth.token_refreshed_successfully'),
                200
            );
        } catch (Exception $e) {
            return $this->errorResponse(
                [$e->getMessage()],
                __('app.something-went-wrong'),
                500
            );
        }
    }
}