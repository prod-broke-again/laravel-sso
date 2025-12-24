<?php

declare(strict_types=1);

namespace Packages\LaravelSSO\Contracts;

use Packages\LaravelSSO\Models\Partner;
use Illuminate\Database\Eloquent\Collection;

/**
 * Partner Service Interface
 *
 * Defines the contract for partner management operations.
 */
interface PartnerServiceInterface
{
    /**
     * Get all enabled partners.
     *
     * @return Collection<int, Partner> Collection of enabled partners
     */
    public function getEnabledPartners(): Collection;

    /**
     * Find a partner by identifier.
     *
     * @param string $identifier Partner identifier
     * @return Partner|null The partner or null if not found
     */
    public function findByIdentifier(string $identifier): ?Partner;

    /**
     * Validate that a partner exists and is enabled.
     *
     * @param string $identifier Partner identifier
     * @return Partner The validated partner
     * @throws \Packages\LaravelSSO\Exceptions\PartnerNotFoundException
     * @throws \Packages\LaravelSSO\Exceptions\PartnerDisabledException
     */
    public function validatePartner(string $identifier): Partner;

    /**
     * Get partner's login URL.
     *
     * @param string $identifier Partner identifier
     * @return string Login URL
     */
    public function getPartnerLoginUrl(string $identifier): string;
}
