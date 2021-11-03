<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magmodules\MessageBird\Model\Handler;

use Magento\Framework\DataObject;
use Magento\Sales\Model\Order\Invoice;
use Magmodules\MessageBird\Api\Config\RepositoryInterface as ConfigRepository;

/**
 * Class PrepareInvoiceData
 *
 * Handle invoice data
 */
class PrepareInvoiceData
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
     * PrepareInvoiceData constructor.
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
     * @param Invoice $invoice
     * @return array
     */
    public function execute(Invoice $invoice): array
    {
        $storeId = (int)$invoice->getStoreId();
        if (!$this->configRepository->isEnabled($storeId)
            || !$this->configRepository->isEnabledForInvoice($storeId)
            || !in_array(
                $invoice->getOrder()->getStatus(),
                $this->configRepository->getRestrictionInvoice($storeId)
            )
            || (!in_array(
                $invoice->getOrder()->getCustomerGroupId(),
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
            'recipients' => $invoice->getOrder()->getBillingAddress()->getTelephone(),
            'country_code' => $invoice->getOrder()->getBillingAddress()->getCountryId()
        ];
        $message = $this->configRepository->getInvoiceMessage($storeId);
        $this->preparator->setData('amount', $invoice->getGrandTotal())
            ->setData('currency', $invoice->getOrderCurrencyCode())
            ->setData('order_id', $invoice->getOrder()->getIncrementId());
        $data['message_body'] = $this->preparator->toString($message);
        return $data;
    }
}
