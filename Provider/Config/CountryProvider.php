<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStoreSwitcher\Provider\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Opengento\CountryStore\Api\CountryRepositoryInterface;
use Opengento\CountryStore\Api\Data\CountryInterface;

use function array_filter;
use function array_map;
use function explode;
use function preg_replace;

class CountryProvider
{
    private const CONFIG_PATH_COUNTRY_SWITCHER = 'country/switcher/';

    public function __construct(
        private ScopeConfigInterface $scopeConfig,
        private CountryRepositoryInterface $countryRepository
    ) {}

    public function getList(string $key): array
    {
        return array_map(
            fn (string $countryCode): CountryInterface => $this->countryRepository->get($countryCode),
            $this->resolveMoreCountries($key)
        );
    }

    private function resolveMoreCountries(string $key): array
    {
        return array_filter(explode(',', preg_replace('/\s+/', '', $this->resolveConfigValue($key))));
    }

    private function resolveConfigValue(string $key): string
    {
        return (string) $this->scopeConfig->getValue(
            self::CONFIG_PATH_COUNTRY_SWITCHER . $key,
            ScopeInterface::SCOPE_WEBSITE
        );
    }
}
