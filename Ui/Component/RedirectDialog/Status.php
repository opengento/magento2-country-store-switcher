<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStoreSwitcher\Ui\Component\RedirectDialog;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

final class Status
{
    private const CONFIG_PATH_COUNTRY_REDIRECT_INITIAL = 'country/redirect/enabled';
    private const CONFIG_PATH_COUNTRY_REDIRECT_DIALOG = 'country/redirect/dialog';

    private ScopeConfigInterface $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function isDisabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_PATH_COUNTRY_REDIRECT_INITIAL,
            ScopeInterface::SCOPE_STORE
        ) || !$this->scopeConfig->isSetFlag(
            self::CONFIG_PATH_COUNTRY_REDIRECT_DIALOG,
            ScopeInterface::SCOPE_STORE
        );
    }
}
