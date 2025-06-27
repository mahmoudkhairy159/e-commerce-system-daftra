<?php

namespace Modules\User\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Support\Facades\Auth;
use Modules\User\Repositories\UserRepository;

class LogoutController extends Controller
{
    use ApiResponseTrait;

    protected $userRepository;

    protected $_config;
    protected $guard;

    public function __construct(UserRepository $userRepository)
    {
        $this->guard = 'user-api';
        $this->_config = request('_config');
        Auth::setDefaultDriver($this->guard);
        $this->userRepository = $userRepository;
        $this->middleware('auth:' . $this->guard)->only(['refresh', 'logout']);
    }

    public function logout()
    {
        try {
            $user = auth($this->guard)->user();

            // Revoke all tokens for the user
            $user->tokens()->delete();

            return $this->messageResponse(
                __('app.auth.logout.logout_successfully'),
                true,
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