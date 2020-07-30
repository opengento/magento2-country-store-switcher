<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStoreSwitcher\ViewModel;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\Store;
use Opengento\CountryStore\Api\CountryRegistryInterface;
use Opengento\CountryStore\Api\CountryStoreResolverInterface;
use Opengento\CountryStore\Api\Data\CountryInterface;
use Psr\Log\LoggerInterface;

final class SwitcherUrlProvider implements ArgumentInterface
{
    private EncoderInterface $encoder;

    private CountryStoreResolverInterface $countryStoreResolver;

    private LoggerInterface $logger;

    public function __construct(
        EncoderInterface $encoder,
        CountryStoreResolverInterface $countryStoreResolver,
        LoggerInterface $logger
    ) {
        $this->encoder = $encoder;
        $this->countryStoreResolver = $countryStoreResolver;
        $this->logger = $logger;
    }

    public function getTargetParams(CountryInterface $country): array
    {
        $targetParams = [CountryRegistryInterface::PARAM_KEY => $country->getCode()];

        try {
            $store = $this->countryStoreResolver->getStoreAware($country);

            if ($store instanceof Store) {
                $targetParams[ActionInterface::PARAM_NAME_URL_ENCODED] = $this->encoder->encode(
                    $store->getCurrentUrl(false)
                );
            }
        } catch (NoSuchEntityException $e) {
            $this->logger->error($e->getLogMessage(), $e->getTrace());
        }

        return $targetParams;
    }
}
