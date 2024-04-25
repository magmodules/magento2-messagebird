<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magmodules\MessageBird\Api\Log;

/**
 * Config repository interface
 */
interface RepositoryInterface
{

    /**
     * Limit stream size to 100 lines
     */
    public const STREAM_DEFAULT_LIMIT = 100;

    /**
     * Log file path pattern
     */
    public const LOG_FILE = '%s/log/messagebird-%s.log';

    /**
     * Add record to error log
     *
     * @param string $type
     * @param mixed $data
     */
    public function addErrorLog(string $type, $data): void;

    /**
     * Add record to debug log
     *
     * @param string $type
     * @param mixed $data
     */
    public function addDebugLog(string $type, $data): void;

    /**
     * Add record to log
     *
     * @param string $type
     * @param mixed $data
     */
    public function addLog(string $type, $data): void;

    /**
     * Returns path of logfile
     *
     * @param string $type
     * @return string
     */
    public function getLogFilePath(string $type): ?string;

    /**
     * Return log entries as sorted array
     *
     * @param string $path
     * @param int|null $limit
     * @return array|null
     */
    public function getLogEntriesAsArray(string $path, ?int $limit = null): ?array;
}
