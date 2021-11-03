<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magmodules\MessageBird\Model\Handler;

use Magento\Customer\Api\AddressRepositoryInterface as AddressRepository;
use Magento\Customer\Model\Session;
use Magento\Framework\DataObject;
use Magmodules\MessageBird\Api\Config\RepositoryInterface as ConfigRepository;

/**
 * Class PrepareProductAlertData
 *
 * Handle product alert data
 */
class PrepareProductAlertData
{

    /**
     * @var ConfigRepository
     */
    private $configRepository;

    /**
     * @var DataObject
     */
    private $preparator;

    /**
     * @var AddressRepository
     */
    private $addressRepository;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * PrepareProductAlertData constructor.
     * @param ConfigRepository $configRepository
     * @param AddressRepository $addressRepository
     * @param DataObject $preparator
     * @param Session $customerSession
     */
    public function __construct(
        ConfigRepository $configRepository,
        AddressRepository $addressRepository,
        DataObject $preparator,
        Session $customerSession
    ) {
        $this->configRepository = $configRepository;
        $this->addressRepository = $addressRepository;
        $this->preparator = $preparator;
        $this->customerSession = $customerSession;
    }

    /**
     * @param array $products
     * @param int $storeId
     * @param string $billingAddressId
     * @param string $type
     * @return array
     */
    public function execute(
        array $products,
        int $storeId,
        string $billingAddressId,
        string $type
    ): array {
        if ($type == 'price') {
            $enType = $this->configRepository->isEnabledForPriceChanges($storeId);
            $message = $this->configRepository->getPriceChangesMessage($storeId);
        } else {
            $enType = $this->configRepository->isEnabledForOutOfStock($storeId);
            $message = $this->configRepository->getOutOfStockMessage($storeId);
        }
        if (!$this->configRepository->isEnabled($storeId)
            || !$enType
            || (
                !in_array(
                    $this->customerSession->getCustomer()->getGroupId(),
                    $this->configRepository->getRestrictionGeneral($storeId)
                ) && !in_array(32000, $this->configRepository->getRestrictionGeneral($storeId))
            )
        ) {
            return [
                'send' => false
            ];
        }
        try {
            $address = $this->addressRepository->getById($billingAddressId);
            $recipient = $address->getTelephone();
            $countryCode = $address->getCountryId();
        } catch (\Exception $e) {
            return [
                'send' => false
            ];
        }

        $data = [
            'send' => true,
            'access_key' => $this->configRepository->getApiKey($storeId),
            'originator' => $this->configRepository->getOriginator($storeId),
            'recipients' => $recipient,
            'country_code' => $countryCode
        ];

        $productName = [];
        foreach ($products as $product) {
            $productName[] = $product->getName();
        }
        $this->preparator->setData('product_name', implode(', ', $productName));
        $data['message_body'] = $this->preparator->toString($message);
        return $data;
    }
}
