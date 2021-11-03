<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magmodules\MessageBird\Model\Handler;

use Magento\Framework\DataObject;
use Magento\Sales\Model\Order\Creditmemo;
use Magmodules\MessageBird\Api\Config\RepositoryInterface as ConfigRepository;

/**
 * Class PrepareCreditmemoData
 *
 * Handle credit memo data
 */
class PrepareCreditmemoData
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
     * PrepareCreditmemoData constructor.
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
     * @param Creditmemo $creditmemo
     * @return array
     */
    public function execute(Creditmemo $creditmemo): array
    {
        $storeId = (int)$creditmemo->getStoreId();
        if (!$this->configRepository->isEnabled($storeId)
            || !$this->configRepository->isEnabledForCredit($storeId)
            || !in_array(
                $creditmemo->getOrder()->getStatus(),
                $this->configRepository->getRestrictionCreditmemo($storeId)
            )
            || (
                !in_array(
                    $creditmemo->getOrder()->getCustomerGroupId(),
                    $this->configRepository->getRestrictionGeneral($storeId)
                ) && !in_array(32000, $this->configRepository->getRestrictionGeneral($storeId))
            )
        ) {
            return [
                'send' => false
            ];
        }
        $data = [
            'send' => true,
            'access_key' => $this->configRepository->getApiKey($storeId),
            'originator' => $this->configRepository->getOriginator($storeId),
            'recipients' => $creditmemo->getOrder()->getBillingAddress()->getTelephone(),
            'country_code' => $creditmemo->getOrder()->getBillingAddress()->getCountryId()
        ];
        $message = $this->configRepository->getCreditMessage($storeId);
        $this->preparator->setData('order_id', $creditmemo->getOrder()->getIncrementId());
        $data['message_body'] = $this->preparator->toString($message);
        return $data;
    }
}
