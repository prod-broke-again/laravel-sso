<?php

declare(strict_types=1);

namespace Packages\LaravelSSO\Enums;

/**
 * SSO Token Status Enum
 *
 * Represents the possible states of an SSO token.
 */
enum SsoTokenStatus: string
{
    case UNUSED = 'unused';
    case USED = 'used';
    case EXPIRED = 'expired';

    /**
     * Get human-readable label for the status.
     *
     * @return string Status label
     */
    public function label(): string
    {
        return match ($this) {
            self::UNUSED => 'Unused',
            self::USED => 'Used',
            self::EXPIRED => 'Expired',
        };
    }

    /**
     * Check if the status allows token usage.
     *
     * @return bool True if token can be used
     */
    public function canBeUsed(): bool
    {
        return $this === self::UNUSED;
    }

    /**
     * Check if the status represents a final state.
     *
     * @return bool True if status is final (cannot change)
     */
    public function isFinal(): bool
    {
        return $this === self::USED || $this === self::EXPIRED;
    }
}
