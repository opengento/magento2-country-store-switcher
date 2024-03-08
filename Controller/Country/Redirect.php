<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStoreSwitcher\Controller\Country;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Opengento\CountryStore\Api\CountryRegistryInterface;
use Opengento\CountryStore\Api\CountryStoreResolverInterface;

class Redirect implements HttpPostActionInterface
{
    public function __construct(
        private RedirectFactory $redirectFactory,
        private RequestInterface $request,
        private ManagerInterface $messageManager,
        private CountryRegistryInterface $countryRegistry,
        private CountryStoreResolverInterface $countryStoreResolver,
        private StoreManagerInterface $storeManager
    ) {}

    public function execute(): ResultInterface
    {
        $resultRedirect = $this->redirectFactory->create()->setRefererOrBaseUrl();
        $countryCode = (string) $this->request->getParam(CountryRegistryInterface::PARAM_KEY);

        if ($this->countryRegistry->get()->getCode() !== $countryCode) {
            $this->countryRegistry->set($countryCode);

            try {
                $store = $this->countryStoreResolver->getStoreAware($this->countryRegistry->get());
                $currentStore = $this->storeManager->getStore();
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addExceptionMessage($e, $e->getMessage());

                return $resultRedirect;
            }

            if ($store->getCode() !== $currentStore->getCode()) {
                $uenc = (string) $this->request->getParam(ActionInterface::PARAM_NAME_URL_ENCODED);

                $resultRedirect->setPath(
                    'stores/store/redirect',
                    [
                        '___store' => $store->getCode(),
                        '___from_store' => $currentStore->getCode(),
                        ActionInterface::PARAM_NAME_URL_ENCODED => $uenc,
                    ]
                );
            }
        }

        return $resultRedirect;
    }
}
