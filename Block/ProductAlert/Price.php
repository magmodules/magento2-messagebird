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
 * Product view price
 *
 * @api
 * @since 100.0.2
 */
class Price extends View
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
     * Prepare price info
     *
     * @param string $template
     * @return $this|View\Price
     */
    public function setTemplate($template)
    {
        try {
            $storeId = (int)$this->storeManager->getStore()->getId();
        } catch (\Exception $exception) {
            $storeId = 0;
        }
        if (!$this->_helper->isPriceAlertAllowed()
            || !$this->getProduct() ||
            false === $this->getProduct()->getCanShowPrice()
        ) {
            $template = '';
        } elseif (!$this->session->isLoggedIn() || !$this->configRepository->isEnabledForPriceChanges($storeId)) {
            $template = 'Magento_ProductAlert::product/view.phtml';
            $this->setSignupUrl($this->_helper->getSaveUrl('price'));
        } else {
            $this->setSignupUrl(
                $this->getUrl(
                    'messagebird/addalert/price',
                    [
                        'product_id' => $this->getProduct()->getId()
                    ]
                )
            );
        }
        return parent::setTemplate($template);
    }
}
