<?php

declare(strict_types=1);

namespace Packages\LaravelSSO\Services;

use Packages\LaravelSSO\Contracts\PartnerServiceInterface;
use Packages\LaravelSSO\Models\Partner;
use Packages\LaravelSSO\Exceptions\PartnerNotFoundException;
use Packages\LaravelSSO\Exceptions\PartnerDisabledException;
use Illuminate\Database\Eloquent\Collection;

/**
 * Partner Service
 *
 * Handles partner-related operations.
 */
readonly class PartnerService implements PartnerServiceInterface
{
    /**
     * Get all enabled partners.
     *
     * @return Collection<int, Partner> Collection of enabled partners
     */
    public function getEnabledPartners(): Collection
    {
        return Partner::enabled()->get();
    }

    /**
     * Find a partner by identifier.
     *
     * @param string $identifier Partner identifier
     * @return Partner|null The partner or null if not found
     */
    public function findByIdentifier(string $identifier): ?Partner
    {
        return Partner::findByIdentifier($identifier);
    }

    /**
     * Validate that a partner exists and is enabled.
     *
     * @param string $identifier Partner identifier
     * @return Partner The validated partner
     * @throws PartnerNotFoundException
     * @throws PartnerDisabledException
     */
    public function validatePartner(string $identifier): Partner
    {
        $partner = $this->findByIdentifier($identifier);

        if (!$partner) {
            throw new PartnerNotFoundException($identifier);
        }

        if (!$partner->enabled) {
            throw new PartnerDisabledException($identifier);
        }

        return $partner;
    }

    /**
     * Get partner's login URL.
     *
     * @param string $identifier Partner identifier
     * @return string Login URL
     * @throws PartnerNotFoundException
     */
    public function getPartnerLoginUrl(string $identifier): string
    {
        $partner = $this->validatePartner($identifier);

        return $partner->getLoginUrl();
    }
}
