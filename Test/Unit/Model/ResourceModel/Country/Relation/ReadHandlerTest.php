<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStoreSwitcher\Test\Unit\Model\Country;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\Store;
use Opengento\CountryStore\Api\CountryStoreResolverInterface;
use Opengento\CountryStore\Api\Data\CountryExtensionInterface;
use Opengento\CountryStore\Api\Data\CountryInterface;
use Opengento\CountryStoreSwitcher\Model\ResourceModel\Country\Relation\ReadHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @covers \Opengento\CountryStoreSwitcher\Model\ResourceModel\Country\Relation\ReadHandler
 */
class ReadHandlerTest extends TestCase
{
    /**
     * @var MockObject|CountryStoreResolverInterface
     */
    private $countryStoreResolver;

    private ReadHandler $readHandler;

    protected function setUp(): void
    {
        $this->countryStoreResolver = $this->getMockForAbstractClass(CountryStoreResolverInterface::class);

        $this->readHandler = new ReadHandler(
            $this->countryStoreResolver,
            $this->getMockForAbstractClass(LoggerInterface::class)
        );
    }

    /**
     * @dataProvider readHandlerData
     */
    public function testExecute(Stub $store, string $baseUrl): void
    {
        $extensionAttributesMock = $this->getMockForAbstractClass(CountryExtensionInterface::class);
        $extensionAttributesMock->expects($this->once())->method('getBaseUrl')->willReturn($baseUrl);
        $extensionAttributesMock->expects($this->once())
            ->method('setBaseUrl')
            ->with($baseUrl)
            ->willReturn($extensionAttributesMock);

        $countryMock = $this->getMockForAbstractClass(CountryInterface::class);
        $countryMock->expects($this->exactly(2))
            ->method('getExtensionAttributes')
            ->willReturn($extensionAttributesMock);

        $this->countryStoreResolver->method('getStoreAware')->will($store);

        /** @var CountryInterface $extensionCountry */
        $extensionCountry = $this->readHandler->execute($countryMock);

        $this->assertSame($countryMock, $extensionCountry);
        $this->assertSame($baseUrl, $extensionCountry->getExtensionAttributes()->getBaseUrl());
    }

    public function readHandlerData(): array
    {
        return [
            [
                $this->createStoreStub('https://us.website.org/'),
                'https://us.website.org/',
            ],
            [
                $this->createStoreStub(null),
                '/',
            ],
            [
                $this->createStoreStub('https://eu.website.org/'),
                'https://eu.website.org/',
            ],
        ];
    }

    private function createStoreStub(?string $baseUrl): Stub
    {
        return $this->returnCallback(function () use ($baseUrl) {
            if ($baseUrl) {
                return $this->createStoreMock($baseUrl);
            }
            throw new NoSuchEntityException();
        });
    }

    private function createStoreMock(string $baseUrl): MockObject
    {
        $storeMock = $this->createMock(Store::class);
        $storeMock->method('getBaseUrl')->willReturn($baseUrl);

        return $storeMock;
    }
}
