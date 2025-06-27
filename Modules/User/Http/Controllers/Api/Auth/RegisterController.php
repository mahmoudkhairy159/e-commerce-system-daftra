<?php

namespace Modules\User\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\User\Http\Requests\Api\Auth\UserRegisterRequest;
use Modules\User\Repositories\UserOTPRepository;
use Modules\User\Repositories\UserProfileRepository;
use Modules\User\Repositories\UserRepository;
use Modules\User\Traits\UserOtpTrait;
use Modules\User\Transformers\Api\User\UserResource;

class RegisterController extends Controller
{

    use ApiResponseTrait, UserOtpTrait;

    protected $userRepository;
    protected $otpRepository;
    protected $userProfileRepository;

    protected $_config;
    protected $guard;

    public function __construct(UserRepository $userRepository, UserProfileRepository $userProfileRepository, UserOTPRepository $otpRepository)
    {
        $this->guard = 'user-api';
        $this->_config = request('_config');
        Auth::setDefaultDriver($this->guard);
        $this->userRepository = $userRepository;
        $this->userProfileRepository = $userProfileRepository;
        $this->otpRepository = $otpRepository;

        $this->middleware('auth:' . $this->guard)->only(['update', 'me']);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     */
    protected function create(UserRegisterRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $user = $this->userRepository->create($data);
            $userProfile = $this->userProfileRepository->create(['user_id' => $user->id]);
            DB::commit();

            // Create Sanctum token
            $tokenName = 'user-api-token';
            $token = $user->createToken($tokenName)->plainTextToken;

            $this->sendOtpCode($user);
            $user->load('profile', 'userAddresses', 'defaultAddress')->withCount('orders');
            $data = [
                'user' => new UserResource($user),
                'token' => $token,
                'token_type' => 'Bearer',
            ];

            return $this->successResponse(
                $data,
                __('app.auth.register.success_register_message'),
                201
            );
        } catch (Exception $e) {
            // return  $this->messageResponse( $e->getMessage());
            DB::rollBack();

            return $this->errorResponse(
                [$e->getMessage()],
                __('app.something-went-wrong'),
                500
            );
        }
    }

    /**
     */
    public function me()
    {
        try {
            $user = auth($this->guard)->user();
            return $this->successResponse(
                new UserResource($user),
                __('app.auth.login.logged_in_successfully'),
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
}