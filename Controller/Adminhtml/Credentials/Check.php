<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magmodules\MessageBird\Controller\Adminhtml\Credentials;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magmodules\MessageBird\Api\Config\RepositoryInterface as ConfigRepository;
use Magmodules\MessageBird\Service\Api\Adapter;

/**
 * Class Check
 *
 * AJAX controller to is provided credentials correct
 */
class Check extends Action
{

    /**
     * @var Adapter
     */
    private $adapter;
    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;
    /**
     * @var ConfigRepository
     */
    private $configProvider;

    /**
     * Check constructor.
     *
     * @param Action\Context $context
     * @param JsonFactory $resultJsonFactory
     * @param Adapter $adapter
     */
    public function __construct(
        Action\Context $context,
        JsonFactory $resultJsonFactory,
        Adapter $adapter,
        ConfigRepository $configProvider
    ) {
        $this->adapter = $adapter;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->configProvider = $configProvider;
        parent::__construct($context);
    }

    /**
     * @return Json
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        $result = $this->adapter->execute(
            [
                'access_key' => $this->getApiKey(),
                'originator' => $this->getRequest()->getParam('originator')
            ]
        );
        if ($result['success']) {
            $message = "Success!";
        } else {
            $message = $result['message'];
        }
        return $resultJson->setData($message);
    }

    /**
     * @return string
     */
    private function getApiKey(): string
    {
        $storeId = (int)$this->getRequest()->getParam('store');
        $testMode = (bool)$this->getRequest()->getParam('test_mode');
        $apiKey = $testMode
            ? $this->getRequest()->getParam('api_key_test')
            : $this->getRequest()->getParam('api_key_live');

        if ($apiKey == '******') {
            return $this->configProvider->getApiKey($storeId, $testMode);
        }

        return $apiKey;
    }
}
