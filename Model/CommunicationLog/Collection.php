<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magmodules\MessageBird\Model\CommunicationLog;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * CommunicationLog collection class
 */
class Collection extends AbstractCollection
{

    /**
     * @inheritDoc
     */
    protected $_idFieldName = ResourceModel::PRIMARY;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            Data::class,
            ResourceModel::class
        );
    }
}
