<?php

declare(strict_types=1);

namespace Packages\LaravelSSO\Services;

use Packages\LaravelSSO\Contracts\SSOAuthenticatorInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * SSO Authenticator
 *
 * Handles user authentication from SSO token data.
 */
readonly class SSOAuthenticator implements SSOAuthenticatorInterface
{
    /**
     * Authenticate a user from SSO token data.
     *
     * @param array<string, mixed> $tokenData Token data containing user information
     * @return Model|null The authenticated user or null if authentication failed
     */
    public function authenticateFromTokenData(array $tokenData): ?Model
    {
        $userModel = config('sso.user_model', 'App\\Models\\User');

        // Find or create user based on token data
        $user = $userModel::where('email', $tokenData['email'])->first();

        if (!$user) {
            $user = $this->createUserFromTokenData($tokenData);
        }

        if (!$user) {
            return null;
        }

        // Log the user in
        Auth::login($user);

        return $user;
    }

    /**
     * Create a new user from SSO token data.
     *
     * @param array<string, mixed> $tokenData Token data
     * @return Model|null Created user or null if creation failed
     */
    private function createUserFromTokenData(array $tokenData): ?Model
    {
        try {
            $userModel = config('sso.user_model', 'App\\Models\\User');

            return $userModel::create([
                'name' => $tokenData['name'],
                'email' => $tokenData['email'],
                'password' => Hash::make(Str::random(32)), // Random password
                'email_verified_at' => now(), // Trust SSO partner
            ]);
        } catch (\Exception $e) {
            // Log the error but don't throw - authentication should continue
            \Illuminate\Support\Facades\Log::error('Failed to create user from SSO token data', [
                'token_data' => $tokenData,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
