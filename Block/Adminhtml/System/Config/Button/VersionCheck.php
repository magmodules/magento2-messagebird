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
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magmodules\MessageBird\Model\Config\Repository as ConfigRepository;

/**
 * Version check button class
 */
class VersionCheck extends Field
{

    /**
     * @var string
     */
    protected $_template = 'Magmodules_MessageBird::system/config/button/version.phtml';
    /**
     * @var ConfigRepository
     */
    private $configRepository;
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * VersionCheck constructor.
     * @param Context $context
     * @param ConfigRepository $configRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        ConfigRepository $configRepository,
        array $data = []
    ) {
        $this->configRepository = $configRepository;
        $this->request = $context->getRequest();
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->configRepository->getExtensionVersion();
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
    public function getVersionCheckUrl()
    {
        return $this->getUrl('messagebird/versioncheck/index');
    }

    /**
     * @return string
     */
    public function getChangeLogUrl()
    {
        return $this->getUrl('messagebird/versioncheck/changelog');
    }

    /**
     * @return mixed
     */
    public function getButtonHtml()
    {
        $buttonData = ['id' => 'magmodules-messagebird-button_version', 'label' => __('Check for latest versions')];
        try {
            $button = $this->getLayout()->createBlock(
                Button::class
            )->setData($buttonData);
            return $button->toHtml();
        } catch (Exception $e) {
            return false;
        }
    }
}
