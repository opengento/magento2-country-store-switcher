<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStoreSwitcher\Test\Unit\ViewModel;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Url\EncoderInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\Store;
use Opengento\CountryStore\Api\CountryRegistryInterface;
use Opengento\CountryStore\Api\CountryStoreResolverInterface;
use Opengento\CountryStore\Api\Data\CountryInterface;
use Opengento\CountryStoreSwitcher\ViewModel\SwitcherUrlProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use function base64_encode;

/**
 * @covers \Opengento\CountryStoreSwitcher\ViewModel\SwitcherUrlProvider
 */
class SwitcherUrlProviderTest extends TestCase
{
    private MockObject|EncoderInterface $urlEncoder;
    private MockObject|CountryStoreResolverInterface $countryStoreResolver;
    private SwitcherUrlProvider $switcherUrlProvider;

    protected function setUp(): void
    {
        $this->urlEncoder = $this->getMockForAbstractClass(EncoderInterface::class);
        $this->countryStoreResolver = $this->getMockForAbstractClass(CountryStoreResolverInterface::class);
        $this->switcherUrlProvider = new SwitcherUrlProvider(
            $this->urlEncoder,
            $this->countryStoreResolver,
            $this->getMockForAbstractClass(LoggerInterface::class)
        );
    }

    /**
     * @dataProvider targetParams
     */
    public function testGetTargetParams(string $countryCode, ?StoreInterface $store): void
    {
        $country = $this->createCountryMock($countryCode);
        $targetParams = [CountryRegistryInterface::PARAM_KEY => $countryCode];

        if ($store) {
            $this->countryStoreResolver->expects($this->once())
                ->method('getStoreAware')
                ->with($country)
                ->willReturn($store);

            if ($store instanceof Store) {
                $urlEncoded = strtr(base64_encode($store->getCurrentUrl(false)), '+/=', '-_,');
                $this->urlEncoder->expects($this->once())
                    ->method('encode')
                    ->with($store->getCurrentUrl(false))
                    ->willReturn($urlEncoded);
                $targetParams[ActionInterface::PARAM_NAME_URL_ENCODED] = $urlEncoded;
            }
        } else {
            $this->countryStoreResolver->expects($this->once())
                ->method('getStoreAware')
                ->with($country)
                ->willThrowException(new NoSuchEntityException());
        }

        $this->assertSame($targetParams, $this->switcherUrlProvider->getTargetParams($country));
    }

    public function targetParams(): array
    {
        return [
            ['FR', null],
            ['FR', $this->createStoreMock('fr_FR', null, false)],
            ['FR', $this->createStoreMock('fr_FR', 'https://hosts.org/my-current-path', true)],
        ];
    }

    private function createStoreMock(string $code, ?string $currentUrl, bool $concrete): MockObject
    {
        if ($concrete) {
            $storeMock = $this->getMockBuilder(Store::class)->disableOriginalConstructor()->getMock();
            $storeMock->method('getCurrentUrl')->with(false)->willReturn($currentUrl);
        } else {
            $storeMock = $this->getMockForAbstractClass(StoreInterface::class);
        }
        $storeMock->method('getCode')->willReturn($code);

        return $storeMock;
    }

    private function createCountryMock(string $countryCode): MockObject
    {
        $countryMock = $this->getMockForAbstractClass(CountryInterface::class);
        $countryMock->method('getCode')->willReturn($countryCode);

        return $countryMock;
    }
}
