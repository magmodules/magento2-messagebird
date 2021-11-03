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
use Magmodules\MessageBird\Api\Log\RepositoryInterface as LogRepository;
use Magmodules\MessageBird\Api\CommunicationLog\RepositoryInterface as CommunicationLogRepository;
use Magmodules\MessageBird\Model\Handler\PrepareCreditmemoData;
use Magmodules\MessageBird\Service\Api\Adapter;

/**
 * Class Creditmemo
 *
 * Handle credit memo data
 */
class Creditmemo implements ObserverInterface
{
    const ERROR = 'Processing of creditmemo ID %1 for order %2 failed: %3';

    /**
     * @var Adapter
     */
    private $adapter;

    /**
     * @var PrepareCreditmemoData
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
     * Creditmemo constructor.
     * @param Adapter $adapter
     * @param PrepareCreditmemoData $prepareRequestData
     * @param LogRepository $logRepository
     * @param CommunicationLogRepository $communicationLogRepository
     */
    public function __construct(
        Adapter $adapter,
        PrepareCreditmemoData $prepareRequestData,
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
     * @throws LocalizedException
     */
    public function execute(EventObserver $observer): void
    {
        /* @var $creditmemo \Magento\Sales\Model\Order\Creditmemo */
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $data = $this->prepareRequestData->execute($creditmemo);
        if (!$data['send'] || $this->communicationLogRepository->isExists($creditmemo->getIncrementId(), 0)) {
            return;
        }
        $order = $creditmemo->getOrder();
        $result = $this->adapter->execute($data, (int)$order->getStoreId());
        $communication = $this->communicationLogRepository->create();
        $communication->setIncrementId($creditmemo->getIncrementId())
            ->setEmail($order->getCustomerEmail())
            ->setType(0)
            ->setFirstname($order->getCustomerFirstname())
            ->setLastname($order->getCustomerLastname());
        if (!$result['success']) {
            $communication->setStatus(0);
            $error = self::ERROR;
            $this->logRepository->addErrorLog(
                'Creditmemo',
                __(
                    $error,
                    $creditmemo->getIncrementId(),
                    $creditmemo->getOrder()->getIncrementId(),
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
