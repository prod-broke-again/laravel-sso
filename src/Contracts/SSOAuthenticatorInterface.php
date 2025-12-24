<?php

declare(strict_types=1);

namespace Packages\LaravelSSO\Contracts;

use Illuminate\Database\Eloquent\Model;

/**
 * SSO Authenticator Interface
 *
 * Defines the contract for user authentication via SSO.
 */
interface SSOAuthenticatorInterface
{
    /**
     * Authenticate a user from SSO token data.
     *
     * @param array<string, mixed> $tokenData Token data containing user information
     * @return Model|null The authenticated user or null if authentication failed
     */
    public function authenticateFromTokenData(array $tokenData): ?Model;
}
