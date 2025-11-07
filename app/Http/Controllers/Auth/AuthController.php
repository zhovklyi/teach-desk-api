<?php

namespace App\Http\Controllers\Auth;

use App\Data\Auth\LoginData;
use App\Data\Auth\RegisterData;
use App\Data\Common\APIResponseData;
use App\Http\Controllers\Controller;
use App\Services\Auth\AuthService;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService) {}

    public function login(LoginData $loginData): APIResponseData
    {
        $data = $this->authService->login($loginData);

        return APIResponseData::success($data);
    }

    public function register(RegisterData $registerData): APIResponseData
    {
        $data = $this->authService->register($registerData);

        return APIResponseData::success($data);
    }

    public function logout(): APIResponseData
    {
        $this->authService->logout();

        return APIResponseData::success(null, 'Logged out successfully.');
    }

    public function user(): APIResponseData
    {
        return APIResponseData::success([
            'user' => $this->authService->currentUser(),
        ]);
    }
}
