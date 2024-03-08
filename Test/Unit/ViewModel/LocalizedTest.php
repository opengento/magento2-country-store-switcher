<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStoreSwitcher\Test\Unit\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Api\Data\StoreInterface;
use Opengento\CountryStore\Api\CountryStoreResolverInterface;
use Opengento\CountryStore\Api\Data\CountryInterface;
use Opengento\CountryStoreSwitcher\ViewModel\Localized;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @covers \Opengento\CountryStoreSwitcher\ViewModel\Localized
 */
class LocalizedTest extends TestCase
{
    private MockObject|ScopeConfigInterface $scopeConfig;
    private MockObject|CountryStoreResolverInterface $countryStoreResolver;
    private Localized $localized;

    protected function setUp(): void
    {
        $this->scopeConfig = $this->getMockForAbstractClass(ScopeConfigInterface::class);
        $this->countryStoreResolver = $this->getMockForAbstractClass(CountryStoreResolverInterface::class);
        $this->localized = new Localized(
            $this->scopeConfig,
            $this->countryStoreResolver,
            $this->getMockForAbstractClass(LoggerInterface::class)
        );
    }

    /**
     * @dataProvider localizedData
     */
    public function testGetCountryName(bool $enabled, array $config, array $stores, array $assertions): void
    {
        $this->scopeConfig->method('isSetFlag')->with('country/switcher/localized', 'website')->willReturn($enabled);
        $this->scopeConfig->method('getValue')->willReturnMap($config);
        $this->countryStoreResolver->method('getStoreAware')->willReturnMap($stores);

        foreach ($assertions as $assertion) {
            $this->assertSame($assertion['name'], $this->localized->getCountryName($assertion['country']));
        }
    }

    public function localizedData(): array
    {
        $localizedCountryUs = $this->getMockForAbstractClass(CountryInterface::class);
        $localizedCountryUs->method('getLocalizedName')->willReturnMap([['en_US', 'United States'], ['fr_FR', 'États-Unis']]);
        $localizedCountryFr = $this->getMockForAbstractClass(CountryInterface::class);
        $localizedCountryFr->method('getLocalizedName')->willReturnMap([['en_US', 'France'], ['fr_FR', 'France']]);
        $countryUs = $this->getMockForAbstractClass(CountryInterface::class);
        $countryUs->method('getName')->willReturn('United States');
        $countryFr = $this->getMockForAbstractClass(CountryInterface::class);
        $countryFr->method('getName')->willReturn('France');

        return [
            [
                true,
                [
                    ['general/locale/code', 'store', 'store_us', 'en_US'],
                    ['general/locale/code', 'store', 'store_fr', 'fr_FR'],
                ],
                [
                    [$localizedCountryUs, $this->createStoreMock('store_us')],
                    [$localizedCountryFr, $this->createStoreMock('store_fr')],
                ],
                [
                    [
                        'country' => $localizedCountryUs,
                        'name' => 'United States',
                    ],
                    [
                        'country' => $localizedCountryFr,
                        'name' => 'France',
                    ],
                ],
            ],
            [
                true,
                [
                    ['general/locale/code', 'store', 'store_us', 'fr_FR'],
                    ['general/locale/code', 'store', 'store_fr', 'fr_FR'],
                ],
                [
                    [$localizedCountryUs, $this->createStoreMock('store_us')],
                    [$localizedCountryFr, $this->createStoreMock('store_fr')],
                ],
                [
                    [
                        'country' => $localizedCountryUs,
                        'name' => 'États-Unis',
                    ],
                    [
                        'country' => $localizedCountryFr,
                        'name' => 'France',
                    ],
                ],
            ],
            [
                false,
                [],
                [],
                [
                    [
                        'country' => $countryUs,
                        'name' => 'United States',
                    ],
                    [
                        'country' => $countryFr,
                        'name' => 'France',
                    ],
                ],
            ],
        ];
    }

    private function createStoreMock(string $code): MockObject
    {
        $storeMock = $this->getMockForAbstractClass(StoreInterface::class);
        $storeMock->method('getCode')->willReturn($code);

        return $storeMock;
    }
}
