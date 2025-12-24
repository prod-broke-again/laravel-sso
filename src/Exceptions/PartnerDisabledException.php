<?php

declare(strict_types=1);

namespace Packages\LaravelSSO\Exceptions;

/**
 * Partner Disabled Exception
 *
 * Thrown when attempting to use a disabled partner.
 */
class PartnerDisabledException extends SsoException
{
    /**
     * Create a new exception instance.
     *
     * @param string $partnerIdentifier Partner identifier that is disabled
     * @param array<string, mixed> $context Additional context
     */
    public function __construct(
        string $partnerIdentifier,
        array $context = [],
    ) {
        $message = "Partner '{$partnerIdentifier}' is disabled.";
        $context = array_merge($context, ['partner_identifier' => $partnerIdentifier]);

        parent::__construct($message, $context, 403);
    }
}
