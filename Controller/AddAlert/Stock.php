<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magmodules\MessageBird\Controller\AddAlert;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\ProductAlert\Controller\Add as AddController;
use Magento\Store\Model\StoreManagerInterface;
use Magmodules\MessageBird\Api\CommunicationLog\RepositoryInterface as CommunicationLogRepository;

/**
 * Controller for notifying about stock.
 */
class Stock extends AddController implements HttpGetActionInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var CommunicationLogRepository
     */
    private $communicationLogRepository;

    /**
     * @param Action\Context $context
     * @param CustomerSession $customerSession
     * @param StoreManagerInterface|null $storeManager
     * @param ProductRepositoryInterface $productRepository
     * @param JsonFactory $resultJsonFactory
     * @param CommunicationLogRepository $communicationLogRepository
     */
    public function __construct(
        Action\Context $context,
        CustomerSession $customerSession,
        StoreManagerInterface $storeManager,
        ProductRepositoryInterface $productRepository,
        JsonFactory $resultJsonFactory,
        CommunicationLogRepository $communicationLogRepository
    ) {
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->communicationLogRepository = $communicationLogRepository;
        parent::__construct($context, $customerSession);
        $this->storeManager = $storeManager ?: $this->_objectManager
            ->get(\Magento\Store\Model\StoreManagerInterface::class);
    }

    /**
     * Method for adding info about product alert stock.
     *
     * @return Json
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function execute()
    {
        $productId = (int)$this->getRequest()->getParam('product_id');
        $store = $this->storeManager->getStore();
        $communication = $this->communicationLogRepository->create();
        $communication->setIncrementId('')
            ->setType(6);
        try {
            /* @var $product \Magento\Catalog\Model\Product */
            $product = $this->productRepository->getById($productId);
            /** @var \Magento\ProductAlert\Model\Price $model */
            $customer = $this->customerSession->getCustomer();
            $communication->setEmail($customer->getEmail())
                ->setFirstname($customer->getDefaultBillingAddress()->getFirstname())
                ->setLastname($customer->getDefaultBillingAddress()->getLastname());
            $phone = $customer->getDefaultBillingAddress()->getTelephone();
            $model = $this->_objectManager->create(\Magento\ProductAlert\Model\Stock::class)
                ->setCustomerId($this->customerSession->getCustomerId())
                ->setProductId($product->getId())
                ->setPrice($product->getFinalPrice())
                ->setWebsiteId($store->getWebsiteId())
                ->setStoreId($store->getId());
            $model->save();
            if (!$phone) {
                $message =
                    __('You saved the alert subscription.
                    Please set phone number in your account so we could notify you');
            } else {
                $message = __('You saved the alert subscription. We will notify you on phone number %1', $phone);
            }
            $communication->setStatus(1);
        } catch (NoSuchEntityException $noEntityException) {
            $message = __('There are not enough parameters.');
        } catch (\Exception $e) {
            $message = __("The alert subscription couldn't update at this time. Please try again later.");
        }
        $this->communicationLogRepository->save($communication);
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($message);
    }
}
