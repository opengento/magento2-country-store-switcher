<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStoreSwitcher\Test\Unit\ViewModel\SwitchCountry;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Opengento\CountryStore\Api\CountryRepositoryInterface;
use Opengento\CountryStore\Api\Data\CountryInterface;
use Opengento\CountryStoreSwitcher\ViewModel\SwitchCountry\More;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Opengento\CountryStoreSwitcher\ViewModel\SwitchCountry\More
 */
class MoreTest extends TestCase
{
    /**
     * @var MockObject|ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var MockObject|CountryRepositoryInterface
     */
    private $countryRepository;

    private More $more;

    /**
     * @var MockObject[]
     */
    private static array $instances;

    protected function setUp(): void
    {
        $this->scopeConfig = $this->getMockForAbstractClass(ScopeConfigInterface::class);
        $this->countryRepository = $this->getMockForAbstractClass(CountryRepositoryInterface::class);

        $this->more = new More($this->scopeConfig, $this->countryRepository);
    }

    /**
     * @dataProvider countriesData
     */
    public function testGetCountries(string $config, array $countries): void
    {
        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with('country/switcher/more', 'website')
            ->willReturn($config);
        $this->countryRepository->expects($this->exactly(count($countries)))
            ->method('get')
            ->willReturnCallback(function (string $countryCode): MockObject {
                return $this->createCountryMock($countryCode);
            });

        $this->assertSame($countries, $this->more->getCountries());
    }

    public function countriesData(): array
    {
        return [
            [
                'US,CA,FR',
                [
                    $this->createCountryMock('US'),
                    $this->createCountryMock('CA'),
                    $this->createCountryMock('FR'),
                ],
            ],
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
