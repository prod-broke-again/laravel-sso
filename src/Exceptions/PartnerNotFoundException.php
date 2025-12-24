<?php

declare(strict_types=1);

namespace Packages\LaravelSSO\Exceptions;

/**
 * Partner Not Found Exception
 *
 * Thrown when a requested partner cannot be found.
 */
class PartnerNotFoundException extends SsoException
{
    /**
     * Create a new exception instance.
     *
     * @param string $partnerIdentifier Partner identifier that was not found
     * @param array<string, mixed> $context Additional context
     */
    public function __construct(
        string $partnerIdentifier,
        array $context = [],
    ) {
        $message = "Partner '{$partnerIdentifier}' not found.";
        $context = array_merge($context, ['partner_identifier' => $partnerIdentifier]);

        parent::__construct($message, $context, 404);
    }
}
