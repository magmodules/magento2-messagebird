<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magmodules\MessageBird\Model;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Helper\View;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\ProductAlert\Helper\Data;
use Magento\ProductAlert\Model\Email as MagentoAlertEmail;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;
use Magmodules\MessageBird\Api\CommunicationLog\RepositoryInterface as CommunicationLogRepository;
use Magmodules\MessageBird\Api\Log\RepositoryInterface as LogRepository;
use Magmodules\MessageBird\Model\Handler\PrepareProductAlertData;
use Magmodules\MessageBird\Service\Api\Adapter;

/**
 * ProductAlert Email processor
 */
class Email extends MagentoAlertEmail
{
    public const ERROR = 'Product %1 notification failed: %2';

    /**
     * @var PrepareProductAlertData
     */
    private $prepareProductAlertData;

    /**
     * @var Adapter
     */
    private $adapter;

    /**
     * @var LogRepository
     */
    private $logRepository;

    /**
     * @var CommunicationLogRepository
     */
    private $communicationLogRepository;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param Data $productAlertData
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param CustomerRepositoryInterface $customerRepository
     * @param View $customerHelper
     * @param Emulation $appEmulation
     * @param TransportBuilder $transportBuilder
     * @param LogRepository $logRepository
     * @param Adapter $adapter
     * @param PrepareProductAlertData $prepareProductAlertData
     * @param CommunicationLogRepository $communicationLogRepository
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Data $productAlertData,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        CustomerRepositoryInterface $customerRepository,
        View $customerHelper,
        Emulation $appEmulation,
        TransportBuilder $transportBuilder,
        LogRepository $logRepository,
        Adapter $adapter,
        PrepareProductAlertData $prepareProductAlertData,
        CommunicationLogRepository $communicationLogRepository,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->adapter = $adapter;
        $this->prepareProductAlertData = $prepareProductAlertData;
        $this->logRepository = $logRepository;
        $this->communicationLogRepository = $communicationLogRepository;
        parent::__construct(
            $context,
            $registry,
            $productAlertData,
            $scopeConfig,
            $storeManager,
            $customerRepository,
            $customerHelper,
            $appEmulation,
            $transportBuilder,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * @inheritDoc
     */
    public function send()
    {
        $parent = parent::send();
        if (!$parent) {
            return $parent;
        }
        $products = $this->_type === 'price'
            ? $this->_priceProducts
            : $this->_stockProducts;
        $billingAddressId = $this->_customer->getDefaultBilling();
        $data = $this->prepareProductAlertData->execute(
            $products,
            (int)$this->getStoreId(),
            $billingAddressId,
            $this->_type
        );
        if (!$data['send']) {
            return $parent;
        }
        $result = $this->adapter->execute($data, (int)$this->getStoreId());
        $communication = $this->communicationLogRepository->create();
        $communication->setEmail($this->_customer->getEmail())
            ->setFirstname($this->_customer->getFirstname())
            ->setLastname($this->_customer->getLastname());
        if ($this->_type === 'price') {
            $communication->setType(6);
        } else {
            $communication->setType(7);
        }
        if (!$result['success']) {
            $communication->setStatus(0);
            $error = self::ERROR;
            $this->logRepository->addErrorLog(
                'Product notification',
                __(
                    $error,
                    $this->_type,
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
        return $parent;
    }
}
