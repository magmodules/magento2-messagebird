<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magmodules\MessageBird\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magmodules\MessageBird\Api\CommunicationLog\RepositoryInterface as CommunicationLogRepository;
use Magmodules\MessageBird\Api\Log\RepositoryInterface as LogRepository;
use Magmodules\MessageBird\Model\Handler\PrepareOrderData;
use Magmodules\MessageBird\Service\Api\Adapter;

/**
 * Class Order
 *
 * Handle credit memo data
 */
class Order implements ObserverInterface
{
    const ERROR = 'Processing of order ID %1 failed: %2';

    /**
     * @var Adapter
     */
    private $adapter;

    /**
     * @var PrepareOrderData
     */
    private $prepareRequestData;

    /**
     * @var LogRepository
     */
    private $logRepository;

    /**
     * @var CommunicationLogRepository
     */
    private $communicationLogRepository;

    /**
     * Order constructor.
     * @param Adapter $adapter
     * @param PrepareOrderData $prepareRequestData
     * @param LogRepository $logRepository
     * @param CommunicationLogRepository $communicationLogRepository
     */
    public function __construct(
        Adapter $adapter,
        PrepareOrderData $prepareRequestData,
        LogRepository $logRepository,
        CommunicationLogRepository $communicationLogRepository
    ) {
        $this->adapter = $adapter;
        $this->prepareRequestData = $prepareRequestData;
        $this->logRepository = $logRepository;
        $this->communicationLogRepository = $communicationLogRepository;
    }

    /**
     * @param EventObserver $observer
     */
    public function execute(EventObserver $observer): void
    {
        /* @var $order \Magento\Sales\Model\Order */
        $order = $observer->getEvent()->getOrder();
        $data = $this->prepareRequestData->execute($order);
        if (!$data['send']) {
            return;
        }
        $result = $this->adapter->execute($data, (int)$order->getStoreId());
        $communication = $this->communicationLogRepository->create();
        $communication->setIncrementId($order->getIncrementId())
            ->setEmail($order->getCustomerEmail())
            ->setType(2)
            ->setFirstname($order->getCustomerFirstname())
            ->setLastname($order->getCustomerLastname());
        if (!$result['success']) {
            $error = self::ERROR;
            $communication->setStatus(0);
            $this->logRepository->addErrorLog(
                'Order',
                __(
                    $error,
                    $order->getIncrementId(),
                    $result['message']
                )
            );
        }
        $communication->setStatus(1);
        try {
            $this->communicationLogRepository->save($communication);
        } catch (LocalizedException $exception) {
            $this->logRepository->addErrorLog('Communication Log', $exception->getMessage());
        }
    }
}
