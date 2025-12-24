<?php

declare(strict_types=1);

namespace Packages\LaravelSSO\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * Partner Model
 *
 * Represents a partner application in the SSO system.
 */
class Partner extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'identifier',
        'name',
        'url',
        'public_key',
        'private_key',
        'enabled',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'enabled' => 'boolean',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the table name from config.
     */
    public function getTable(): string
    {
        return config('sso.tables.partners', 'sso_partners');
    }

    /**
     * Scope to get only enabled partners.
     *
     * @param Builder<Partner> $query
     * @return Builder<Partner>
     */
    public function scopeEnabled(Builder $query): Builder
    {
        return $query->where('enabled', true);
    }

    /**
     * Find a partner by identifier.
     *
     * @param string $identifier Partner identifier
     * @return self|null The partner or null if not found
     */
    public static function findByIdentifier(string $identifier): ?self
    {
        return static::where('identifier', $identifier)->first();
    }

    /**
     * Get the partner's login URL.
     *
     * @return string Login URL
     */
    public function getLoginUrl(): string
    {
        return rtrim($this->url, '/') . '/sso/login';
    }

    /**
     * Get the partner's callback URL.
     *
     * @return string Callback URL
     */
    public function getCallbackUrl(): string
    {
        return rtrim($this->url, '/') . '/sso/callback';
    }

    /**
     * Check if the partner is enabled.
     *
     * @return bool True if enabled
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Get the partner's display name.
     *
     * @return string Display name
     */
    public function getDisplayName(): string
    {
        return $this->name;
    }

    /**
     * Get the partner's base URL.
     *
     * @return string Base URL
     */
    public function getBaseUrl(): string
    {
        return rtrim($this->url, '/');
    }
}
