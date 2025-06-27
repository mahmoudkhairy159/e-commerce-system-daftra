<?php

namespace Modules\User\Http\Controllers\Api\Auth;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as ProviderUser;
use Modules\User\Http\Requests\Api\Auth\UserSocialLoginRequest;
use Modules\User\Models\LinkedSocialAccount;
use Modules\User\Models\User;
use Modules\User\Repositories\UserOTPRepository;
use Modules\User\Repositories\UserProfileRepository;
use Modules\User\Repositories\UserRepository;
use Modules\User\Traits\UserOtpTrait;
use Modules\User\Transformers\Admin\User\UserResource;

class SocialiteController extends Controller
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
    }

    /*  Full Flow
  The frontend sends this token to your Laravel API in a request to the /login endpoint.
  The backend uses the access_token with Socialite to retrieve the user's data from Google.
  If the user is successfully authenticated, the backend generates a Sanctum token and returns it to the frontend, which can store it for subsequent requests.
  */
    //for Spa login

    public function login(UserSocialLoginRequest $request)
    {
        try {
            $accessToken = $request->get('access_token');
            $provider = $request->get('provider');

            $providerUser = Socialite::driver($provider)->userFromToken($accessToken);

            if ($providerUser) {
                $user = $this->findOrCreate($providerUser, $provider);

                // Create Sanctum token
                $tokenName = 'user-api-token';
                $token = $user->createToken($tokenName)->plainTextToken;

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
            } else {
                return $this->errorResponse(
                    ['provider_user_error' => 'Unable to retrieve user from provider.'],
                    __('app.something-went-wrong'),
                    400
                );
            }

        } catch (Exception $exception) {
            return $this->errorResponse(
                [$exception->getMessage()],
                __('app.something-went-wrong'),
                500
            );
        }
    }

    protected function findOrCreate(ProviderUser $providerUser, string $provider)
    {
        try {
            DB::beginTransaction();

            // Check if the user is already linked with the social account
            $linkedSocialAccount = LinkedSocialAccount::where('provider_name', $provider)
                ->where('provider_id', $providerUser->getId())
                ->first();

            if ($linkedSocialAccount) {
                DB::commit();
                return $linkedSocialAccount->user;
            }

            // Try to find the user by email
            $user = null;
            if ($email = $providerUser->getEmail()) {
                $user = User::where('email', $email)->first();
            }

            // If user does not exist, create a new user
            if (!$user) {
                $user = User::create([
                    'name' => $providerUser->getName(),
                    'email' => $providerUser->getEmail(),
                    'image' => $providerUser->getAvatar(),
                    'password' => Str::random(24), // Use a random password for social login
                ]);
                $user->markEmailAsVerified();
            }

            // Link the social account to the user
            $user->linkedSocialAccounts()->create([
                'provider_id' => $providerUser->getId(),
                'provider_name' => $provider,
            ]);

            // Create a user profile
            $this->userProfileRepository->create(['user_id' => $user->id]);

            DB::commit();

            return $user;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e; // Re-throw the exception for the calling function to handle
        }
    }



    //for web login
    public function redirect(string $provider)
    {
        if (!in_array($provider, ['google', 'facebook', 'github'])) {
            return $this->errorResponse(
                ['Invalid provider'],
                __('app.auth.invalid_provider'),
                400
            );
        }
        $redirectUri = url("/api/user/auth/login/{$provider}/callback");
        return Socialite::driver($provider)->stateless()->redirectUrl('http://localhost/api/user/auth/login/google/callback')->redirect();
    }
    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     */
    protected function callback(string $provider)
    {
        try {
            if (!in_array($provider, ['google', 'facebook', 'github'])) {
                return $this->errorResponse(
                    ['Invalid provider'],
                    __('app.auth.invalid_provider'),
                    400
                );
            }
            DB::beginTransaction();

            $socialUser = Socialite::driver($provider)->stateless()->user();
            //to get access token
            // $accessToken = $socialUser->token;
            // dd($accessToken);
            //to get access token
            $user = User::firstOrCreate(
                ['email' => $socialUser->getEmail()],
                [
                    'name' => $socialUser->getName(),
                    'password' => Str::random(24),
                    'provider_id' => $socialUser->getId(),
                    'image' => $socialUser->getAvatar(),
                ]
            );
            $user->markEmailAsVerified();
            // Link the social account to the user
            $user->linkedSocialAccounts()->create([
                'provider_id' => $socialUser->getId(),
                'provider_name' => $provider,
            ]);

            $userProfile = $this->userProfileRepository->create(['user_id' => $user->id]);
            DB::commit();

            // Create Sanctum token
            $tokenName = 'user-api-token';
            $token = $user->createToken($tokenName)->plainTextToken;

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

}