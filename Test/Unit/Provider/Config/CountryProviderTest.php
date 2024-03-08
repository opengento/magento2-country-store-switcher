<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStoreSwitcher\Test\Unit\Provider\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Opengento\CountryStore\Api\CountryRepositoryInterface;
use Opengento\CountryStore\Api\Data\CountryInterface;
use Opengento\CountryStoreSwitcher\Provider\Config\CountryProvider;
use Opengento\CountryStoreSwitcher\ViewModel\SwitchCountry\More;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Opengento\CountryStoreSwitcher\Provider\Config\CountryProvider
 */
class CountryProviderTest extends TestCase
{
    private MockObject|ScopeConfigInterface $scopeConfig;
    private MockObject|CountryRepositoryInterface $countryRepository;
    private CountryProvider $countryProvider;

    /**
     * @var MockObject[]
     */
    private static array $instances;

    protected function setUp(): void
    {
        $this->scopeConfig = $this->getMockForAbstractClass(ScopeConfigInterface::class);
        $this->countryRepository = $this->getMockForAbstractClass(CountryRepositoryInterface::class);
        $this->countryProvider = new CountryProvider($this->scopeConfig, $this->countryRepository);
    }

    /**
     * @dataProvider countriesData
     */
    public function testGetCountries(string $key, string $config, array $countries): void
    {
        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with('country/switcher/' . $key, 'website')
            ->willReturn($config);
        $this->countryRepository->expects($this->exactly(count($countries)))
            ->method('get')
            ->willReturnCallback(function (string $countryCode): MockObject {
                return $this->createCountryMock($countryCode);
            });

        $this->assertSame($countries, $this->countryProvider->getList($key));
    }

    public function countriesData(): array
    {
        return [
            [
                'a',
                'US,CA,FR',
                [
                    $this->createCountryMock('US'),
                    $this->createCountryMock('CA'),
                    $this->createCountryMock('FR'),
                ],
            ],
            [
                'a',
                ' US, CA , FR ',
                [
                    $this->createCountryMock('US'),
                    $this->createCountryMock('CA'),
                    $this->createCountryMock('FR'),
                ],
            ],
            [
                'a',
                '',
                [],
            ],
            [
                'b',
                'US,CA,FR',
                [
                    $this->createCountryMock('US'),
                    $this->createCountryMock('CA'),
                    $this->createCountryMock('FR'),
                ],
            ],
            [
                'b',
                ' US, CA , FR ',
                [
                    $this->createCountryMock('US'),
                    $this->createCountryMock('CA'),
                    $this->createCountryMock('FR'),
                ],
            ],
            [
                'b',
                '',
                [],
            ]
        ];
    }

    private function createCountryMock(string $countryCode): MockObject
    {
        if (!isset(self::$instances[$countryCode])) {
            $country = $this->getMockForAbstractClass(CountryInterface::class);
            $country->method('getName')->willReturn($countryCode);
            self::$instances[$countryCode] = $country;
        }

        return self::$instances[$countryCode];
    }
}
