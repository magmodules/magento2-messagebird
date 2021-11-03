<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magmodules\MessageBird\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Status
 *
 * Source Class for Type options
 */
class Type implements OptionSourceInterface
{

    /**
     * Type const
     */
    const TYPES =
        [
            'Creditmemo',
            'Invoice',
            'Order',
            'Shipment',
            'Payment Reminder',
            'Price Notification',
            'Stock Notification',
        ];

    /**
     * Return option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->toArray() as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => $label
            ];
        }

        return $options;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return self::TYPES;
    }
}
