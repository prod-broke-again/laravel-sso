<?php

declare(strict_types=1);

namespace Packages\LaravelSSO\Exceptions;

/**
 * SSO Token Exception
 *
 * Base exception for token-related errors.
 */
abstract class SsoTokenException extends SsoException
{
    /**
     * Create a new exception instance.
     *
     * @param string $token Token that caused the error
     * @param string $message Error message
     * @param array<string, mixed> $context Additional context
     * @param int $code Error code
     * @param \Throwable|null $previous Previous exception
     */
    public function __construct(
        protected string $token,
        string $message,
        array $context = [],
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        $context = array_merge($context, ['token' => $this->token]);
        parent::__construct($message, $context, $code, $previous);
    }

    /**
     * Get the token that caused the exception.
     *
     * @return string Token value
     */
    public function getToken(): string
    {
        return $this->token;
    }
}
