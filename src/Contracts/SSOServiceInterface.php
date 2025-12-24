<?php

declare(strict_types=1);

namespace Packages\LaravelSSO\Contracts;

use Packages\LaravelSSO\DTOs\SsoTokenDTO;
use Packages\LaravelSSO\DTOs\SsoLoginUrlDTO;
use Illuminate\Database\Eloquent\Model;

/**
 * SSO Service Interface
 *
 * Defines the contract for SSO operations.
 */
interface SSOServiceInterface
{
    /**
     * Generate an SSO token for a user to authenticate with a partner.
     *
     * @param Model $user The user to generate token for
     * @param string $partnerIdentifier Partner identifier
     * @param array<string, mixed> $additionalData Additional data to include
     * @return SsoTokenDTO Generated token data
     */
    public function generateToken(Model $user, string $partnerIdentifier, array $additionalData = []): SsoTokenDTO;

    /**
     * Validate an SSO token and return user data.
     *
     * @param string $token The SSO token to validate
     * @return array<string, mixed>|null User data if valid, null otherwise
     */
    public function validateToken(string $token): ?array;

    /**
     * Build SSO login URL with token.
     *
     * @param string $partnerIdentifier Partner identifier
     * @param Model $user The user to authenticate
     * @return SsoLoginUrlDTO Login URL data
     */
    public function buildLoginUrl(string $partnerIdentifier, Model $user): SsoLoginUrlDTO;
}
