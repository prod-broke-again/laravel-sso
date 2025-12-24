<?php

declare(strict_types=1);

namespace Packages\LaravelSSO\Models;

use Packages\LaravelSSO\Enums\SsoTokenStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * SSO Token Model
 *
 * Represents an SSO token for cross-application authentication.
 */
class SsoToken extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'token',
        'user_id',
        'partner_identifier',
        'source_app',
        'expires_at',
        'used',
        'used_at',
        'user_data',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
        'used' => 'boolean',
        'user_data' => 'encrypted:array',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the table name from config.
     */
    public function getTable(): string
    {
        return config('sso.tables.tokens', 'sso_tokens');
    }

    /**
     * Get the user that owns the token.
     *
     * @return BelongsTo<Model, self>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('sso.user_model', 'App\\Models\\User'), 'user_id');
    }

    /**
     * Scope to get unused tokens.
     *
     * @param Builder<SsoToken> $query
     * @return Builder<SsoToken>
     */
    public function scopeUnused(Builder $query): Builder
    {
        return $query->where('used', false);
    }

    /**
     * Scope to get unexpired tokens.
     *
     * @param Builder<SsoToken> $query
     * @return Builder<SsoToken>
     */
    public function scopeUnexpired(Builder $query): Builder
    {
        return $query->where('expires_at', '>', now());
    }

    /**
     * Scope to get valid tokens (unused and unexpired).
     *
     * @param Builder<SsoToken> $query
     * @return Builder<SsoToken>
     */
    public function scopeValid(Builder $query): Builder
    {
        return $query->unused()->unexpired();
    }

    /**
     * Scope to get tokens by status.
     *
     * @param Builder<SsoToken> $query
     * @param SsoTokenStatus $status Token status
     * @return Builder<SsoToken>
     */
    public function scopeByStatus(Builder $query, SsoTokenStatus $status): Builder
    {
        return match ($status) {
            SsoTokenStatus::UNUSED => $query->unused()->unexpired(),
            SsoTokenStatus::USED => $query->where('used', true),
            SsoTokenStatus::EXPIRED => $query->where('expires_at', '<=', now()),
        };
    }

    /**
     * Get the token status.
     *
     * @return SsoTokenStatus Current token status
     */
    public function getStatus(): SsoTokenStatus
    {
        if ($this->used) {
            return SsoTokenStatus::USED;
        }

        if ($this->isExpired()) {
            return SsoTokenStatus::EXPIRED;
        }

        return SsoTokenStatus::UNUSED;
    }

    /**
     * Check if the token is expired.
     *
     * @return bool True if expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if the token is used.
     *
     * @return bool True if used
     */
    public function isUsed(): bool
    {
        return $this->used;
    }

    /**
     * Check if the token is valid.
     *
     * @return bool True if valid
     */
    public function isValid(): bool
    {
        return !$this->isUsed() && !$this->isExpired();
    }

    /**
     * Mark the token as used.
     *
     * @return bool True if successfully marked as used
     */
    public function markAsUsed(): bool
    {
        return $this->update([
            'used' => true,
            'used_at' => now(),
        ]);
    }

    /**
     * Get remaining lifetime in seconds.
     *
     * @return int Seconds until expiration
     */
    public function getRemainingLifetime(): int
    {
        if ($this->isExpired()) {
            return 0;
        }

        return now()->diffInSeconds($this->expires_at, false);
    }

    /**
     * Find a token by its value.
     *
     * @param string $token Token value
     * @return self|null The token or null if not found
     */
    public static function findByToken(string $token): ?self
    {
        return static::where('token', $token)->first();
    }

    /**
     * Generate a unique token.
     *
     * @return string Unique token
     */
    public static function generateToken(): string
    {
        do {
            $token = bin2hex(random_bytes(32));
        } while (static::where('token', $token)->exists());

        return $token;
    }
}
