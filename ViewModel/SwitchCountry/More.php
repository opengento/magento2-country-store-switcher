<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStoreSwitcher\ViewModel\SwitchCountry;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Opengento\CountryStoreSwitcher\Provider\Config\CountryProvider;

final class More implements ArgumentInterface
{
    public function __construct(private CountryProvider $countryProvider) {}

    public function getCountries(): array
    {
        return $this->countryProvider->getList('more');
    }
}
