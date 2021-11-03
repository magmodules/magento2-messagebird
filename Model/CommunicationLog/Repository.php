<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magmodules\MessageBird\Model\CommunicationLog;

use Exception;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magmodules\MessageBird\Api\CommunicationLog\DataInterface;
use Magmodules\MessageBird\Api\CommunicationLog\DataInterfaceFactory;
use Magmodules\MessageBird\Api\CommunicationLog\RepositoryInterface;
use Magmodules\MessageBird\Api\CommunicationLog\SearchResultsInterface;
use Magmodules\MessageBird\Api\CommunicationLog\SearchResultsInterfaceFactory;

/**
 * CommunicationLog repository class
 */
class Repository implements RepositoryInterface
{

    /**
     * @var CollectionProcessorInterface|null
     */
    private $collectionProcessor;

    /**
     * @var ResourceModel
     */
    private $resourceModel;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var SearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var DataInterfaceFactory
     */
    private $dataFactory;

    /**
     * Repository constructor.
     * @param CollectionFactory $collectionFactory
     * @param ResourceModel $resourceModel
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     * @param DataInterfaceFactory $dataFactory
     * @param CollectionProcessorInterface|null $collectionProcessor
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        ResourceModel $resourceModel,
        SearchResultsInterfaceFactory $searchResultsFactory,
        DataInterfaceFactory $dataFactory,
        CollectionProcessorInterface $collectionProcessor = null
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->resourceModel = $resourceModel;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataFactory = $dataFactory;
        $this->collectionProcessor = $collectionProcessor ?: ObjectManager::getInstance()
            ->get(CollectionProcessorInterface::class);
    }

    /**
     * @inheritDoc
     */
    public function getList($searchCriteria): SearchResultsInterface
    {
        $collection = $this->collectionFactory->create();
        return $this->searchResultsFactory->create()
            ->setSearchCriteria($searchCriteria)
            ->setItems($collection->getItems())
            ->setTotalCount($collection->getSize());
    }

    /**
     * @inheritDoc
     */
    public function deleteById($entityId): bool
    {
        $entity = $this->get($entityId);
        return $this->delete($entity);
    }

    /**
     * @inheritDoc
     */
    public function get(int $entityId): DataInterface
    {
        if (!$entityId) {
            $exceptionMsg = static::INPUT_EXCEPTION;
            throw new InputException(__($exceptionMsg));
        } elseif (!$this->resourceModel->isExists($entityId)) {
            $exceptionMsg = self::NO_SUCH_ENTITY_EXCEPTION;
            throw new NoSuchEntityException(__($exceptionMsg, $entityId));
        }
        return $this->dataFactory->create()
            ->load($entityId);
    }

    /**
     * @inheritDoc
     */
    public function delete(DataInterface $entity): bool
    {
        try {
            $this->resourceModel->delete($entity);
        } catch (Exception $exception) {
            $exceptionMsg = self::COULD_NOT_DELETE_EXCEPTION;
            throw new CouldNotDeleteException(__(
                $exceptionMsg,
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function create()
    {
        return $this->dataFactory->create();
    }

    /**
     * @inheritDoc
     */
    public function save(
        DataInterface $entity
    ): DataInterface {
        try {
            $this->resourceModel->save($entity);
        } catch (Exception $exception) {
            $exceptionMsg = self::COULD_NOT_SAVE_EXCEPTION;
            throw new CouldNotSaveException(__(
                $exceptionMsg,
                $exception->getMessage()
            ));
        }
        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function isExists(string $incrementId, int $type): bool
    {
        return (bool)$this->collectionFactory->create()
            ->addFieldToFilter('increment_id', $incrementId)
            ->addFieldToFilter('type', $type)->getSize();
    }
}
