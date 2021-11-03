<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magmodules\MessageBird\Block\Adminhtml\System\Config\Button;

use Exception;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Button;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magmodules\MessageBird\Api\Log\RepositoryInterface as LogRepository;

/**
 * Class Credentials
 *
 * Credentials validation
 */
class Credentials extends Field
{

    /**
     * @var string
     */
    protected $_template = 'Magmodules_MessageBird::system/config/button/credentials.phtml';

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;
    /**
     * @var LogRepository
     */
    private $logger;

    /**
     * Credentials constructor.
     *
     * @param Context $context
     * @param LogRepository $logger
     * @param array $data
     */
    public function __construct(
        Context $context,
        LogRepository $logger,
        array $data = []
    ) {
        $this->request = $context->getRequest();
        $this->logger = $logger;
        parent::__construct($context, $data);
    }

    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    public function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }

    /**
     * @return string
     */
    public function getApiCheckUrl()
    {
        return $this->getUrl('messagebird/credentials/check');
    }

    /**
     * @return mixed
     */
    public function getButtonHtml()
    {
        $buttonData = ['id' => 'messagebird-button_credentials', 'label' => __('Check Credentials')];
        try {
            $button = $this->getLayout()->createBlock(
                Button::class
            )->setData($buttonData);
            return $button->toHtml();
        } catch (Exception $e) {
            $this->logger->addErrorLog('LocalizedException', $e->getMessage());
            return false;
        }
    }
}
