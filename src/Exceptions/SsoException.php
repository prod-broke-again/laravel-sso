<?php

declare(strict_types=1);

namespace Packages\LaravelSSO\Exceptions;

/**
 * Base SSO Exception
 *
 * Base exception class for all SSO-related errors.
 */
abstract class SsoException extends \Exception
{
    /**
     * Create a new exception instance.
     *
     * @param string $message Error message
     * @param array<string, mixed> $context Additional context
     * @param int $code Error code
     * @param \Throwable|null $previous Previous exception
     */
    public function __construct(
        string $message,
        protected array $context = [],
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the exception context.
     *
     * @return array<string, mixed> Context data
     */
    public function getContext(): array
    {
        return $this->context;
    }
}
