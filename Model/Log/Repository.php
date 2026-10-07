<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magmodules\MessageBird\Model\Log;

use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magmodules\MessageBird\Api\Log\RepositoryInterface as LogRepository;
use Magmodules\MessageBird\Api\Log\RepositoryInterface as LogRepositoryInterface;
use Magmodules\MessageBird\Logger\DebugLogger;
use Magmodules\MessageBird\Logger\ErrorLogger;
use Magmodules\MessageBird\Api\Config\RepositoryInterface as Config;

/**
 * Logs repository class
 */
class Repository implements LogRepositoryInterface
{

    /**
     * @var DebugLogger
     */
    private $debugLogger;
    /**
     * @var ErrorLogger
     */
    private $errorLogger;
    /**
     * @var DirectoryList
     */
    private $dir;
    /**
     * @var DateTime
     */
    private $dateTime;
    /**
     * @var File
     */
    private $file;
    /**
     * @var Config
     */
    private $config;

    /**
     * Repository constructor.
     *
     * @param DebugLogger $debugLogger
     * @param ErrorLogger $errorLogger
     * @param DirectoryList $dir
     * @param File $file
     * @param DateTime $dateTime
     * @param Config $config
     */
    public function __construct(
        DebugLogger $debugLogger,
        ErrorLogger $errorLogger,
        DirectoryList $dir,
        File $file,
        DateTime $dateTime,
        Config $config
    ) {
        $this->debugLogger = $debugLogger;
        $this->errorLogger = $errorLogger;
        $this->dir = $dir;
        $this->file = $file;
        $this->dateTime = $dateTime;
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function addErrorLog(string $type, $data): void
    {
        $this->errorLogger->addLog($type, $data);
    }

    /**
     * @inheritDoc
     */
    public function addDebugLog(string $type, $data): void
    {
        $this->debugLogger->addLog($type, $data);
    }

    /**
     * @inheritDoc
     */
    public function addLog(string $type, $data): void
    {
        if ($this->config->isDebugMode()) {
            $this->addDebugLog($type, $data);
        } else {
            $this->addErrorLog($type, $data);
        }
    }

    /**
     * @inheritDoc
     */
    public function getLogFilePath(string $type): ?string
    {
        try {
            return sprintf(LogRepositoryInterface::LOG_FILE, $this->dir->getPath('var'), $type);
        } catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    public function getLogEntriesAsArray(string $path, ?int $limit = null): ?array
    {
        try {
            if (!$this->file->isExists($path)) {
                return null;
            }

            $stream = $this->file->fileOpen($path, 'r');
            $this->file->fileSeek($stream, 0, SEEK_END);
            $pos = $this->file->fileTell($stream);
            $numberOfLines = LogRepository::STREAM_DEFAULT_LIMIT;
            while ($pos >= 0 && $numberOfLines > 0) {
                $this->file->fileSeek($stream, $pos);
                $char = $this->file->fileRead($stream, 1);
                if ($char === "\n") {
                    $numberOfLines--;
                }
                $pos--;
            }

            $result = [];
            $lines = [];
            if ($numberOfLines < LogRepository::STREAM_DEFAULT_LIMIT) {
                $offset = $this->file->fileTell($stream);
                $this->file->fileSeek($stream, 0, SEEK_END);
                $length = $this->file->fileTell($stream) - $offset;
                $this->file->fileSeek($stream, $offset);
                $lines = $length > 0 ? explode("\n", $this->file->fileRead($stream, $length)) : [];
            }
            foreach ($lines as $line) {
                if ($line !== '') {
                    $data = explode('] ', $line);
                    $date = ltrim(array_shift($data), '[');
                    $data = implode('] ', $data);
                    $data = explode(': ', $data);
                    unset($data[0]);
                    $type = $data[1] ?? '--';
                    array_shift($data);

                    $result[] = [
                        'date' => $this->dateTime->date('Y-m-d H:i:s', $date) . ' - ' . $type,
                        'msg' => implode(': ', $data)
                    ];
                }
            }

            $this->file->fileClose($stream);
            return array_reverse($result);
        } catch (\Exception $exception) {
            return null;
        }
    }
}
