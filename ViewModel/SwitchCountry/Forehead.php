<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStoreSwitcher\ViewModel\SwitchCountry;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;
use Opengento\CountryStore\Api\CountryRepositoryInterface;
use function explode;

final class Forehead implements ArgumentInterface
{
    private const CONFIG_PATH_COUNTRY_SWITCHER_FOREHEAD_COUNTRIES = 'country/switcher/forehead';

    private ScopeConfigInterface $scopeConfig;

    private CountryRepositoryInterface $countryRepository;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        CountryRepositoryInterface $countryRepository
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->countryRepository = $countryRepository;
    }

    public function getCountries(): array
    {
        $countries = [];

        foreach ($this->resolveForeheadCountries() as $countryCode) {
            $countries[] = $this->countryRepository->get($countryCode);
        }

        return $countries;
    }

    private function resolveForeheadCountries(): array
    {
        return explode(
            ',',
            $this->scopeConfig->getValue(
                self::CONFIG_PATH_COUNTRY_SWITCHER_FOREHEAD_COUNTRIES,
                ScopeInterface::SCOPE_WEBSITE
            ) ?? ''
        );
    }
}
