<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magmodules\MessageBird\Block\Adminhtml\MessageBird;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * System Configration Module information Block
 */
class Header extends Field
{

    /**
     * @var string
     */
    protected $_template = 'Magmodules_MessageBird::system/config/fieldset/header.phtml';

    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    public function render(AbstractElement $element): string
    {
        $element->addClass('magmodules');

        return $this->toHtml();
    }
}
