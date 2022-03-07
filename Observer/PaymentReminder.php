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
use Magmodules\MessageBird\Model\Handler\PreparePaymentReminderData;
use Magmodules\MessageBird\Service\Api\Adapter;

/**
 * Class Order
 *
 * Handle Mollie payment reminder data
 */
class PaymentReminder implements ObserverInterface
{
    public const ERROR = 'Processing of payment reminding for order ID %1 failed: %2';

    /**
     * @var Adapter
     */
    private $adapter;

    /**
     * @var PreparePaymentReminderData
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
        PreparePaymentReminderData $prepareRequestData,
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
        $order = $observer->getEvent()->getVariables()->getOrder();
        $data = $this->prepareRequestData->execute($order, $observer->getEvent()->getVariables()->getLink());
        if (!$data['send']) {
            return;
        }
        $result = $this->adapter->execute($data, (int)$order->getStoreId());
        $communication = $this->communicationLogRepository->create();
        $communication->setIncrementId($order->getIncrementId())
            ->setEmail($order->getCustomerEmail())
            ->setType(4)
            ->setFirstname($order->getCustomerFirstname())
            ->setLastname($order->getCustomerLastname());
        if (!$result['success']) {
            $error = self::ERROR;
            $communication->setStatus(0);
            $this->logRepository->addErrorLog(
                'Payment Reminder',
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
