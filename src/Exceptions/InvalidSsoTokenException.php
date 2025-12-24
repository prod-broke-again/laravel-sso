<?php

declare(strict_types=1);

namespace Packages\LaravelSSO\Exceptions;

/**
 * Invalid SSO Token Exception
 *
 * Thrown when an SSO token is invalid or cannot be validated.
 */
class InvalidSsoTokenException extends SsoTokenException
{
    /**
     * Create a new exception instance.
     *
     * @param string $token Invalid token
     * @param string $reason Reason why the token is invalid
     * @param array<string, mixed> $context Additional context
     */
    public function __construct(
        string $token,
        string $reason = 'Token is invalid or expired',
        array $context = [],
    ) {
        $message = "SSO token validation failed: {$reason}";
        $context = array_merge($context, ['reason' => $reason]);

        parent::__construct($token, $message, $context, 401);
    }
}
