<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magmodules\MessageBird\Block\ProductAlert;

use Magento\Customer\Model\Session;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Magento\ProductAlert\Block\Product\View;
use Magento\ProductAlert\Helper\Data;
use Magento\Store\Model\StoreManagerInterface;
use Magmodules\MessageBird\Api\Config\RepositoryInterface as ConfigRepository;

/**
 * Recurring payment view stock
 *
 * @api
 * @since 100.0.2
 */
class Stock extends View
{

    /**
     * @var Session
     */
    private $session;

    /**
     * @var ConfigRepository
     */
    private $configRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Price constructor.
     * @param Context $context
     * @param Data $helper
     * @param Registry $registry
     * @param PostHelper $coreHelper
     * @param Session $session
     * @param ConfigRepository $configRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $helper,
        Registry $registry,
        PostHelper $coreHelper,
        Session $session,
        ConfigRepository $configRepository,
        array $data = []
    ) {
        $this->session = $session;
        $this->configRepository = $configRepository;
        $this->storeManager = $context->getStoreManager();
        parent::__construct(
            $context,
            $helper,
            $registry,
            $coreHelper,
            $data
        );
    }

    /**
     * Prepare stock info
     *
     * @param string $template
     * @return $this|View\Stock
     */
    public function setTemplate($template)
    {
        try {
            $storeId = (int)$this->storeManager->getStore()->getId();
        } catch (\Exception $exception) {
            $storeId = 0;
        }
        if (!$this->_helper->isStockAlertAllowed() || !$this->getProduct() || $this->getProduct()->isAvailable()) {
            $template = '';
        } elseif (!$this->session->isLoggedIn() || !$this->configRepository->isEnabledForOutOfStock($storeId)) {
            $template = 'Magento_ProductAlert::product/view.phtml';
            $this->setData('signup_url', $this->_helper->getSaveUrl('stock'));
        } else {
            $this->setData(
                'signup_url',
                $this->getUrl(
                    'messagebird/addalert/stock',
                    [
                        'product_id' => $this->getProduct()->getId()
                    ]
                )
            );
        }
        return parent::setTemplate($template);
    }
}
