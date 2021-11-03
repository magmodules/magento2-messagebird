<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magmodules\MessageBird\Model\CommunicationLog;

use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Framework\Model\AbstractModel;
use Magmodules\MessageBird\Api\CommunicationLog\DataInterface;

/**
 * CommunicationLog data class
 */
class Data extends AbstractModel implements ExtensibleDataInterface, DataInterface
{
    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * @inheritDoc
     */
    public function getEntityId(): int
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * @inheritDoc
     */
    public function setEntityId($entityId): DataInterface
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * @inheritDoc
     */
    public function getIncrementId(): string
    {
        return $this->getData(self::INCREMENT_ID);
    }

    /**
     * @inheritDoc
     */
    public function setIncrementId(string $incrementId): DataInterface
    {
        return $this->setData(self::INCREMENT_ID, $incrementId);
    }

    /**
     * @inheritDoc
     */
    public function getFirstname(): string
    {
        return $this->getData(self::FIRSTNAME);
    }

    /**
     * @inheritDoc
     */
    public function setFirstname(string $firstname): DataInterface
    {
        return $this->setData(self::FIRSTNAME, $firstname);
    }

    /**
     * @inheritDoc
     */
    public function getLastname(): string
    {
        return $this->getData(self::LASTNAME);
    }

    /**
     * @inheritDoc
     */
    public function setLastname(string $lastname): DataInterface
    {
        return $this->setData(self::LASTNAME, $lastname);
    }

    /**
     * @inheritDoc
     */
    public function getEmail(): string
    {
        return $this->getData(self::EMAIL);
    }

    /**
     * @inheritDoc
     */
    public function setEmail(string $email): DataInterface
    {
        return $this->setData(self::EMAIL, $email);
    }

    /**
     * @inheritDoc
     */
    public function getStatus(): int
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setStatus(int $status): DataInterface
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @inheritDoc
     */
    public function getType(): int
    {
        return $this->getData(self::TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setType(int $type): DataInterface
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt(): string
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt(string $createdAt): DataInterface
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }
}
