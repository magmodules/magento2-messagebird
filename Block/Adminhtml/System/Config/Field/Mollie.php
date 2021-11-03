<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magmodules\MessageBird\Block\Adminhtml\System\Config\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\App\RequestInterface;
use Magmodules\MessageBird\Api\Config\RepositoryInterface as ConfigRepository;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class Mollie
 *
 * Mollie check
 */
class Mollie extends Field
{

    /**
     * @var string
     */
    protected $_template = 'Magmodules_MessageBird::system/config/fieldset/mollie.phtml';

    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var ConfigRepository
     */
    private $configRepository;

    /**
     * Credentials constructor.
     *
     * @param Context $context
     * @param ConfigRepository $configRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        ConfigRepository $configRepository,
        array $data = []
    ) {
        $this->request = $context->getRequest();
        $this->configRepository = $configRepository;
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
     * @return bool
     */
    public function isMollieEnabled()
    {
        $storeId = (int)$this->getRequest()->getParam('store');
        return $this->configRepository->isMollieEnabled($storeId);
    }
}
