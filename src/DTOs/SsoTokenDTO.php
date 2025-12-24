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
        return new self(
            token: $data['token'],
            userId: $data['user_id'],
            partnerIdentifier: $data['partner_identifier'],
            sourceApp: $data['source_app'],
            expiresAt: new \DateTimeImmutable($data['expires_at']),
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
