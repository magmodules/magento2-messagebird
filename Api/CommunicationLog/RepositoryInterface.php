<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magmodules\MessageBird\Api\CommunicationLog;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface Repository
 *
 */
interface RepositoryInterface
{

    /**
     * Input exception text
     */
    const INPUT_EXCEPTION = 'An ID is needed. Set the ID and try again.';

    /**
     * "No such entity" exception text
     */
    const NO_SUCH_ENTITY_EXCEPTION = 'The entity with id "%1" does not exist.';
    /**
     * "Could not delete" exception text
     */
    const COULD_NOT_DELETE_EXCEPTION = 'Could not delete the entity: %1';

    /**
     * "Could not save" exception text
     */
    const COULD_NOT_SAVE_EXCEPTION = 'Could not save the entity: %1';

    /**
     * Loads a specified entity
     *
     * @param int $entityId
     *
     * @return DataInterface
     * @throws LocalizedException
     */
    public function get(int $entityId): DataInterface;

    /**
     * Return new entity object
     *
     * @return DataInterface
     */
    public function create();

    /**
     * Retrieves an entities matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return SearchResultsInterface
     * @throws LocalizedException
     */
    public function getList($searchCriteria): SearchResultsInterface;

    /**
     * Register entity to delete
     *
     * @param DataInterface $entity
     *
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(
        DataInterface $entity
    ): bool;

    /**
     * Deletes an entity by ID
     *
     * @param int $entityId
     *
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($entityId): bool;

    /**
     * Perform persist operations for one entity
     *
     * @param DataInterface $entity
     *
     * @return DataInterface
     * @throws LocalizedException
     */
    public function save(
        DataInterface $entity
    ): DataInterface;

    /**
     * Check is record exists
     *
     * @param string $incrementId
     * @param int $type
     *
     * @return bool
     */
    public function isExists(
        string $incrementId,
        int $type
    ): bool;
}
