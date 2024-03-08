<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStoreSwitcher\Model\ResourceModel\Country\Relation;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Url\ScopeInterface;
use Opengento\CountryStore\Api\CountryStoreResolverInterface;
use Opengento\CountryStore\Api\Data\CountryInterface;
use Psr\Log\LoggerInterface;

final class ReadHandler implements ExtensionInterface
{
    public function __construct(
        private CountryStoreResolverInterface $countryStoreResolver,
        private LoggerInterface $logger
    ) {}

    public function execute($entity, $arguments = null): CountryInterface
    {
        if ($entity instanceof CountryInterface) {
            $entity->setExtensionAttributes(
                $entity->getExtensionAttributes()->setBaseUrl($this->resolveBaseUrl($entity))
            );
        }

        return $entity;
    }

    private function resolveBaseUrl(CountryInterface $country): string
    {
        try {
            $store = $this->countryStoreResolver->getStoreAware($country);
        } catch (NoSuchEntityException $e) {
            $this->logger->error($e->getLogMessage(), $e->getTrace());

            return '/';
        }

        return $store instanceof ScopeInterface ? $store->getBaseUrl() : '/';
    }
}
