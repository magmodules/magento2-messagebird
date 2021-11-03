<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magmodules\MessageBird\Api\CommunicationLog;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 *  Interface for communication log data model
 */
interface DataInterface extends ExtensibleDataInterface
{

    /**
     * ID of entity
     */
    const ENTITY_ID = 'entity_id';

    /**
     * Increment ID of related entity
     */
    const INCREMENT_ID = 'increment_id';

    /**
     * Customer's firstname
     */
    const FIRSTNAME = 'firstname';

    /**
     * Customer's lastname
     */
    const LASTNAME = 'lastname';

    /**
     * Customer's email
     */
    const EMAIL = 'email';

    /**
     * Entity's status
     */
    const STATUS = 'status';

    /**
     * Type of communication event
     */
    const TYPE = 'type';

    /**
     * Entity's creation time
     */
    const CREATED_AT = 'created_at';

    /**
     * Getter for entity_id
     * @return int
     */
    public function getEntityId(): int;

    /**
     * Setter for entity_id
     * @param int $entityId
     *
     * @return $this
     */
    public function setEntityId($entityId): self;

    /**
     * Getter for increment ID
     *
     * @return string
     */
    public function getIncrementId(): string;

    /**
     * Setter for increment ID
     * @param string $incrementId
     *
     * @return $this
     */
    public function setIncrementId(string $incrementId): self;

    /**
     * Getter for firstname
     *
     * @return string
     */
    public function getFirstname(): string;

    /**
     * Setter for firstname
     * @param string $firstname
     *
     * @return $this
     */
    public function setFirstname(string $firstname): self;

    /**
     * Getter for lastname
     *
     * @return string
     */
    public function getLastname(): string;

    /**
     * Setter for lastname
     * @param string $lastname
     *
     * @return $this
     */
    public function setLastname(string $lastname): self;

    /**
     * Getter for email
     *
     * @return string
     */
    public function getEmail(): string;

    /**
     * Setter for email
     * @param string $email
     *
     * @return $this
     */
    public function setEmail(string $email): self;

    /**
     * Getter for status
     *
     * @return int
     */
    public function getStatus(): int;

    /**
     * Setter for status
     * @param int $status
     *
     * @return $this
     */
    public function setStatus(int $status): self;

    /**
     * Getter for type
     *
     * @return int
     */
    public function getType(): int;

    /**
     * Setter for type
     * @param int $type
     *
     * @return $this
     */
    public function setType(int $type): self;

    /**
     * Getter for created_at
     *
     * @return string
     */
    public function getCreatedAt(): string;

    /**
     * Setter for created_at
     * @param string $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(string $createdAt): self;
}
