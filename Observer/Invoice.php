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
use Magmodules\MessageBird\Model\Handler\PrepareInvoiceData;
use Magmodules\MessageBird\Service\Api\Adapter;

/**
 * Class Invoice
 *
 * Handle credit memo data
 */
class Invoice implements ObserverInterface
{
    const ERROR = 'Processing of invoice ID %1 for order %2 failed: %3';

    /**
     * @var Adapter
     */
    private $adapter;

    /**
     * @var PrepareInvoiceData
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
     * Invoice constructor.
     * @param Adapter $adapter
     * @param PrepareInvoiceData $prepareRequestData
     * @param LogRepository $logRepository
     * @param CommunicationLogRepository $communicationLogRepository
     */
    public function __construct(
        Adapter $adapter,
        PrepareInvoiceData $prepareRequestData,
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
        /* @var $invoice \Magento\Sales\Model\Order\Invoice */
        $invoice = $observer->getEvent()->getInvoice();
        $data = $this->prepareRequestData->execute($invoice);
        if (!$data['send'] || $this->communicationLogRepository->isExists($invoice->getIncrementId(), 1)) {
            return;
        }
        $order = $invoice->getOrder();
        $result = $this->adapter->execute($data, (int)$order->getStoreId());
        $communication = $this->communicationLogRepository->create();
        $communication->setIncrementId($invoice->getIncrementId())
            ->setEmail($order->getCustomerEmail())
            ->setType(1)
            ->setFirstname($order->getCustomerFirstname())
            ->setLastname($order->getCustomerLastname());
        if (!$result['success']) {
            $communication->setStatus(0);
            $error = self::ERROR;
            $this->logRepository->addErrorLog(
                'Invoice',
                __(
                    $error,
                    $invoice->getIncrementId(),
                    $invoice->getOrder()->getIncrementId(),
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
