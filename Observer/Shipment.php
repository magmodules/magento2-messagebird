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
use Magmodules\MessageBird\Model\Handler\PrepareShipmentData;
use Magmodules\MessageBird\Service\Api\Adapter;

/**
 * Class Shipment
 *
 * Handle credit memo data
 */
class Shipment implements ObserverInterface
{
    public const ERROR = 'Processing of shipment ID %1 for order %2 failed: %3';

    /**
     * @var Adapter
     */
    private $adapter;

    /**
     * @var PrepareShipmentData
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
     * Shipment constructor.
     * @param Adapter $adapter
     * @param PrepareShipmentData $prepareRequestData
     * @param LogRepository $logRepository
     * @param CommunicationLogRepository $communicationLogRepository
     */
    public function __construct(
        Adapter $adapter,
        PrepareShipmentData $prepareRequestData,
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
        /* @var $shipment \Magento\Sales\Model\Order\Shipment */
        $shipment = $observer->getEvent()->getShipment();
        $data = $this->prepareRequestData->execute($shipment);
        if (!$data['send'] || $this->communicationLogRepository->isExists($shipment->getIncrementId(), 3)) {
            return;
        }
        $result = $this->adapter->execute($data, (int)$shipment->getStoreId());
        $order = $shipment->getOrder();
        $communication = $this->communicationLogRepository->create();
        $communication->setIncrementId($shipment->getIncrementId())
            ->setEmail($order->getCustomerEmail())
            ->setType(3)
            ->setFirstname($order->getCustomerFirstname())
            ->setLastname($order->getCustomerLastname());
        if (!$result['success']) {
            $error = self::ERROR;
            $communication->setStatus(0);
            $this->logRepository->addErrorLog(
                'Shipment',
                __(
                    $error,
                    $shipment->getIncrementId(),
                    $shipment->getOrder()->getIncrementId(),
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
