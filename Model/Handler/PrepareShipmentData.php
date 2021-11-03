<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magmodules\MessageBird\Model\Handler;

use Magento\Framework\DataObject;
use Magento\Sales\Model\Order\Shipment;
use Magmodules\MessageBird\Api\Config\RepositoryInterface as ConfigRepository;

/**
 * Class PrepareShipmentData
 *
 * Handle shipment data
 */
class PrepareShipmentData
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
     * PrepareShipmentData constructor.
     * @param ConfigRepository $configRepository
     * @param DataObject $preparator
     */
    public function __construct(
        ConfigRepository $configRepository,
        DataObject $preparator
    ) {
        $this->configRepository = $configRepository;
        $this->preparator = $preparator;
    }

    /**
     * @param Shipment $shipment
     * @return array
     */
    public function execute(Shipment $shipment): array
    {
        $storeId = (int)$shipment->getStoreId();
        if (!$this->configRepository->isEnabled($storeId)
            || !$this->configRepository->isEnabledForShipping($storeId)
            || !in_array(
                $shipment->getOrder()->getStatus(),
                $this->configRepository->getRestrictionShipment($storeId)
            )
            || (!in_array(
                $shipment->getOrder()->getCustomerGroupId(),
                $this->configRepository->getRestrictionGeneral($storeId)
            ) && !in_array(
                32000,
                $this->configRepository->getRestrictionGeneral($storeId)
            ))
        ) {
            return [
                'send' => false
            ];
        }
        $data = [
            'send' => true,
            'access_key' => $this->configRepository->getApiKey($storeId),
            'originator' => $this->configRepository->getOriginator($storeId),
            'recipients' => $shipment->getOrder()->getBillingAddress()->getTelephone(),
            'country_code' => $shipment->getOrder()->getBillingAddress()->getCountryId()
        ];

        $address = $shipment->getOrder()->getShippingAddress();
        $message = $this->configRepository->getShippingMessage($storeId);
        $this->preparator->setData('address', implode(' ', $address->getStreet()) . ' ' . $address->getCity());
        $data['message_body'] = $this->preparator->toString($message);
        return $data;
    }
}
