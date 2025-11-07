<?php

namespace App\Services\Auth;

use App\Data\Auth\LoginData;
use App\Data\Auth\RegisterData;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class AuthService
{
    /**
     * @return array{token: string, user: User}
     */
    public function login(LoginData $loginData): array
    {
        $authenticated = Auth::attempt([
            'email' => $loginData->email,
            'password' => $loginData->password,
        ]);

        if (! $authenticated) {
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        /** @var User $user */
        $user = Auth::user();
        $token = $user->createToken('default')->plainTextToken;

        return $this->formatTokenResponse($user, $token);
    }

    /**
     * @return array{token: string, user: User}
     */
    public function register(RegisterData $registerData): array
    {
        $user = User::query()->create([
            'name' => $registerData->name,
            'email' => $registerData->email,
            'password' => $registerData->password,
        ]);

        $token = $user->createToken('default')->plainTextToken;

        return $this->formatTokenResponse($user, $token);
    }

    public function logout(): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        $token = $user?->currentAccessToken();

        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        }
    }

    public function currentUser(): ?User
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user;
    }

    /**
     * @return array{token: string, user: User}
     */
    private function formatTokenResponse(User $user, string $token): array
    {
        return [
            'token' => $token,
            'user' => $user,
        ];
    }
}
