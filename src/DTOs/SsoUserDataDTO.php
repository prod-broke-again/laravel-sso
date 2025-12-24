<?php

declare(strict_types=1);

namespace Packages\LaravelSSO\DTOs;

/**
 * SSO User Data Transfer Object
 *
 * Represents user data shared during SSO authentication.
 */
readonly class SsoUserDataDTO
{
    public function __construct(
        public int $id,
        public string $email,
        public string $name,
        public int $timestamp,
        public array $additionalData = [],
    ) {}

    /**
     * Create DTO from user model and additional data.
     *
     * @param \Illuminate\Database\Eloquent\Model $user User model
     * @param array<string, mixed> $additionalData Additional user data
     * @return self
     */
    public static function fromUser(\Illuminate\Database\Eloquent\Model $user, array $additionalData = []): self
    {
        return new self(
            id: (int) $user->getKey(),
            email: $user->email,
            name: $user->name ?? $user->email,
            timestamp: time(),
            additionalData: $additionalData,
        );
    }

    /**
     * Convert to array for storage/serialization.
     *
     * @return array<string, mixed> User data as array
     */
    public function toArray(): array
    {
        return array_merge([
            'id' => $this->id,
            'email' => $this->email,
            'name' => $this->name,
            'timestamp' => $this->timestamp,
        ], $this->additionalData);
    }

    /**
     * Check if the data is still fresh (not too old).
     *
     * @param int $maxAgeSeconds Maximum age in seconds
     * @return bool True if fresh
     */
    public function isFresh(int $maxAgeSeconds = 300): bool
    {
        return (time() - $this->timestamp) <= $maxAgeSeconds;
    }
}
