<?php

declare(strict_types=1);

namespace Packages\LaravelSSO\Services;

use Packages\LaravelSSO\Contracts\SSOServiceInterface;
use Packages\LaravelSSO\Contracts\PartnerServiceInterface;
use Packages\LaravelSSO\Contracts\SSOAuthenticatorInterface;
use Packages\LaravelSSO\DTOs\SsoTokenDTO;
use Packages\LaravelSSO\DTOs\SsoLoginUrlDTO;
use Packages\LaravelSSO\DTOs\SsoUserDataDTO;
use Packages\LaravelSSO\Models\SsoToken;
use Packages\LaravelSSO\Exceptions\InvalidSsoTokenException;
use Illuminate\Database\Eloquent\Model;

/**
 * SSO Service
 *
 * Main service for handling SSO operations. Coordinates between
 * partner management, token handling, and user authentication.
 */
readonly class SSOService implements SSOServiceInterface
{
    public function __construct(
        private PartnerServiceInterface $partnerService,
        private SSOAuthenticatorInterface $authenticator,
    ) {}

    /**
     * Generate an SSO token for a user to authenticate with a partner.
     *
     * @param Model $user The user to generate token for
     * @param string $partnerIdentifier Partner identifier
     * @param array<string, mixed> $additionalData Additional data to include
     * @return SsoTokenDTO Generated token data
     */
    public function generateToken(Model $user, string $partnerIdentifier, array $additionalData = []): SsoTokenDTO
    {
        // Validate partner exists and is enabled
        $this->partnerService->validatePartner($partnerIdentifier);

        $token = SsoToken::generateToken();
        $expiresAt = new \DateTimeImmutable('+' . config('sso.token_lifetime', 5) . ' minutes');

        // Prepare user data to share
        $userData = SsoUserDataDTO::fromUser($user, $additionalData);

        // Create token in database
        $ssoToken = SsoToken::create([
            'token' => $token,
            'user_id' => $user->getKey(),
            'partner_identifier' => $partnerIdentifier,
            'source_app' => config('sso.app_identifier'),
            'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
            'user_data' => $userData->toArray(),
            'metadata' => $this->getRequestMetadata(),
        ]);

        return new SsoTokenDTO(
            token: $ssoToken->token,
            userId: $ssoToken->user_id,
            partnerIdentifier: $ssoToken->partner_identifier,
            sourceApp: $ssoToken->source_app,
            expiresAt: $expiresAt,
            userData: $ssoToken->user_data,
            metadata: $ssoToken->metadata ?? [],
        );
    }

    /**
     * Validate an SSO token and return user data.
     *
     * @param string $token The SSO token to validate
     * @return array<string, mixed>|null User data if valid, null otherwise
     * @throws InvalidSsoTokenException
     */
    public function validateToken(string $token): ?array
    {
        $ssoToken = SsoToken::with('user')->where('token', $token)->first();

        if (!$ssoToken) {
            throw new InvalidSsoTokenException($token, 'Token not found');
        }

        if (!$ssoToken->isValid()) {
            $reason = $ssoToken->isUsed() ? 'Token already used' : 'Token expired';
            throw new InvalidSsoTokenException($token, $reason);
        }

        // Mark token as used
        $ssoToken->markAsUsed();

        return $ssoToken->user_data;
    }

    /**
     * Build SSO login URL with token.
     *
     * @param string $partnerIdentifier Partner identifier
     * @param Model $user The user to authenticate
     * @return SsoLoginUrlDTO Login URL data
     */
    public function buildLoginUrl(string $partnerIdentifier, Model $user): SsoLoginUrlDTO
    {
        $partner = $this->partnerService->validatePartner($partnerIdentifier);
        $tokenDto = $this->generateToken($user, $partnerIdentifier);

        return SsoLoginUrlDTO::create(
            baseUrl: $partner->url,
            token: $tokenDto->token,
            partnerIdentifier: $partnerIdentifier,
            sourceApp: config('sso.app_identifier'),
        );
    }

    /**
     * Authenticate a user from SSO token data.
     *
     * @param array<string, mixed> $tokenData Token data containing user information
     * @return Model|null The authenticated user or null if authentication failed
     */
    public function authenticateFromTokenData(array $tokenData): ?Model
    {
        return $this->authenticator->authenticateFromTokenData($tokenData);
    }

    /**
     * Get request metadata for token creation.
     *
     * @return array<string, string> Request metadata
     */
    private function getRequestMetadata(): array
    {
        $request = request();

        return [
            'user_agent' => $request->userAgent() ?? '',
            'ip_address' => $request->ip() ?? '',
            'created_at' => now()->toISOString(),
        ];
    }
}
