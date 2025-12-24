<?php

declare(strict_types=1);

namespace Packages\LaravelSSO\DTOs;

/**
 * SSO Token Data Transfer Object
 *
 * Represents an SSO token with its metadata.
 */
readonly class SsoTokenDTO
{
    public function __construct(
        public string $token,
        public int $userId,
        public string $partnerIdentifier,
        public string $sourceApp,
        public \DateTimeImmutable $expiresAt,
        public array $userData,
        public array $metadata = [],
    ) {}

    /**
     * Create DTO from array data.
     *
     * @param array<string, mixed> $data Token data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        // Handle different datetime formats
        $expiresAt = $data['expires_at'];
        if ($expiresAt instanceof \Illuminate\Support\Carbon) {
            $expiresAt = $expiresAt->toDateTimeImmutable();
        } elseif (is_string($expiresAt)) {
            $expiresAt = new \DateTimeImmutable($expiresAt);
        } elseif (!$expiresAt instanceof \DateTimeImmutable) {
            throw new \InvalidArgumentException('Invalid expires_at format');
        }

        return new self(
            token: $data['token'],
            userId: $data['user_id'],
            partnerIdentifier: $data['partner_identifier'],
            sourceApp: $data['source_app'],
            expiresAt: $expiresAt,
            userData: $data['user_data'],
            metadata: $data['metadata'] ?? [],
        );
    }

    /**
     * Check if the token is expired.
     *
     * @return bool True if expired
     */
    public function isExpired(): bool
    {
        return $this->expiresAt < new \DateTimeImmutable();
    }

    /**
     * Get remaining lifetime in seconds.
     *
     * @return int Seconds until expiration
     */
    public function getRemainingLifetime(): int
    {
        $now = new \DateTimeImmutable();
        $diff = $this->expiresAt->getTimestamp() - $now->getTimestamp();

        return max(0, $diff);
    }
}
