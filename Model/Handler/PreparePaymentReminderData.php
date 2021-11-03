<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magmodules\MessageBird\Model\Handler;

use Magento\Framework\DataObject;
use Magento\Sales\Model\Order;
use Magmodules\MessageBird\Api\Config\RepositoryInterface as ConfigRepository;

/**
 * Class PreparePaymentReminderData
 *
 * Handle of Mollie payment reminder data
 */
class PreparePaymentReminderData
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
     * PreparePaymentReminderData constructor.
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
     * @param Order $order
     * @param string $link
     * @return array
     */
    public function execute(Order $order, string $link): array
    {
        $storeId = (int)$order->getStoreId();
        if (!$this->configRepository->isEnabled($storeId)
            || !$this->configRepository->isEnabledForPaymentReminder($storeId)
            || (!in_array(
                $order->getCustomerGroupId(),
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
            'recipients' => $order->getBillingAddress()->getTelephone(),
            'country_code' => $order->getBillingAddress()->getCountryId()
        ];
        $message = $this->configRepository->getPaymentReminderMessage($storeId);
        $this->preparator->setData('order_id', $order->getIncrementId())
            ->setData('amount', $order->getGrandTotal())
            ->setData('currency', $order->getOrderCurrencyCode())
            ->setData('link', $link);
        $data['message_body'] = $this->preparator->toString($message);
        return $data;
    }
}
