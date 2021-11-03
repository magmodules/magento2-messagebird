<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magmodules\MessageBird\Api\CommunicationLog;

use Magento\Framework\Api\SearchResultsInterface as FrameworkSearchResultsInterface;

/**
 * Interface for communication log search results.
 *
 * @api
 */
interface SearchResultsInterface extends FrameworkSearchResultsInterface
{

    /**
     * Gets items
     *
     * @return DataInterface[]
     */
    public function getItems(): array;

    /**
     * Sets items
     *
     * @param DataInterface[] $items
     *
     * @return $this
     */
    public function setItems(array $items): self;
}
