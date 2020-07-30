<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStoreSwitcher\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;
use Opengento\CountryStore\Api\CountryStoreResolverInterface;
use Opengento\CountryStore\Api\Data\CountryInterface;
use Psr\Log\LoggerInterface;

final class Localized implements ArgumentInterface
{
    private const CONFIG_PATH_LOCALIZED_COUNTRY_NAME = 'country/switcher/localized';
    private const CONFIG_PATH_LOCALE_CODE = 'general/locale/code';

    private ScopeConfigInterface $scopeConfig;

    private CountryStoreResolverInterface $countryStoreResolver;

    private LoggerInterface $logger;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        CountryStoreResolverInterface $countryStoreResolver,
        LoggerInterface $logger
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->countryStoreResolver = $countryStoreResolver;
        $this->logger = $logger;
    }

    public function getCountryName(CountryInterface $country): string
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_PATH_LOCALIZED_COUNTRY_NAME, ScopeInterface::SCOPE_WEBSITE)
                ? $country->getLocalizedName($this->resolveLocale($country))
                : $country->getName();
    }

    private function resolveLocale(CountryInterface $country): string
    {
        try {
            return $this->scopeConfig->getValue(
                self::CONFIG_PATH_LOCALE_CODE,
                ScopeInterface::SCOPE_STORE,
                $this->countryStoreResolver->getStoreAware($country)->getCode()
            );
        } catch (NoSuchEntityException $e) {
            $this->logger->error($e->getLogMessage(), $e->getTrace());

            return $this->scopeConfig->getValue(self::CONFIG_PATH_LOCALE_CODE, ScopeInterface::SCOPE_STORE);
        }
    }
}
