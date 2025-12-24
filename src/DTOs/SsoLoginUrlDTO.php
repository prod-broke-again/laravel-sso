<?php

declare(strict_types=1);

namespace Packages\LaravelSSO\DTOs;

/**
 * SSO Login URL Data Transfer Object
 *
 * Represents a complete SSO login URL with all necessary parameters.
 */
readonly class SsoLoginUrlDTO
{
    public function __construct(
        public string $url,
        public string $token,
        public string $partnerIdentifier,
        public string $sourceApp,
    ) {}

    /**
     * Create DTO from components.
     *
     * @param string $baseUrl Base URL of the partner
     * @param string $token SSO token
     * @param string $partnerIdentifier Partner identifier
     * @param string $sourceApp Source application identifier
     * @return self
     */
    public static function create(
        string $baseUrl,
        string $token,
        string $partnerIdentifier,
        string $sourceApp,
    ): self {
        $queryParams = [
            'token' => $token,
            'app' => $sourceApp,
        ];

        $url = rtrim($baseUrl, '/') . '/sso/callback?' . http_build_query($queryParams);

        return new self(
            url: $url,
            token: $token,
            partnerIdentifier: $partnerIdentifier,
            sourceApp: $sourceApp,
        );
    }

    /**
     * Get the URL with additional parameters.
     *
     * @param array<string, string> $additionalParams Additional query parameters
     * @return string Modified URL
     */
    public function getUrlWithParams(array $additionalParams): string
    {
        if (empty($additionalParams)) {
            return $this->url;
        }

        $parsedUrl = parse_url($this->url);
        $existingParams = [];

        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $existingParams);
        }

        $allParams = array_merge($existingParams, $additionalParams);
        $queryString = http_build_query($allParams);

        return $parsedUrl['scheme'] . '://' . $parsedUrl['host'] .
               ($parsedUrl['port'] ? ':' . $parsedUrl['port'] : '') .
               ($parsedUrl['path'] ?? '') . '?' . $queryString;
    }
}
