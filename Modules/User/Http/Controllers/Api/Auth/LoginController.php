<?php

namespace Modules\User\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
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
        Auth::setDefaultDriver($this->guard);
        $this->_config = request('_config');
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

            if (!$jwtToken = Auth::guard($this->guard)->attempt($request->only(['email', 'password']))) {
                return $this->errorResponse(
                    [],
                    __('app.auth.login.invalid_email_or_password'),
                    401
                );
            }

            // $user = Auth::user();
            $user = User::where('email', $request->email)
                ->with('profile', 'userAddresses', 'defaultAddress')
                ->withCount('orders')
                ->first();

            if (!$user->status || $user->blocked) {
                $message = $user->blocked ? __('app.auth.login.your_account_is_blocked') : __('app.auth.login.your_account_is_inactive');
                Auth::guard($this->guard)->logout();
                return $this->errorResponse(
                    [],
                    $message,
                    400
                );
            } else {
                $user->last_login_at = Carbon::now();
            }

            $user->save();
            $msg = __('app.auth.login.logged_in_successfully');
            if (!$user->email_verified_at) {
                $msg = __('app.auth.login.logged_in_successfully_and_Verification_code_sent');
                $this->sendOtpCode($user);
            }
            $data = [
                'user' => new UserResource($user),
                'token' => $jwtToken,
                'expires_in_minutes' => Auth::factory()->getTTL(),
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

            $data = [
                'access_token' => Auth::refresh(),
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