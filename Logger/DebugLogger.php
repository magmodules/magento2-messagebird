<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magmodules\MessageBird\Logger;

use Monolog\Logger;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * DebugLogger logger class
 */
class DebugLogger extends Logger
{

    /**
     * @var Json
     */
    private $json;

    /**
     * DebugLogger constructor.
     *
     * @param Json $json
     * @param string $name
     * @param array $handlers
     * @param array $processors
     */
    public function __construct(
        Json $json,
        string $name,
        array $handlers = [],
        array $processors = []
    ) {
        $this->json = $json;
        parent::__construct($name, $handlers, $processors);
    }

    /**
     * Add debug data to messagebird Log
     *
     * @param string $type
     * @param mixed $data
     *
     */
    public function addLog(string $type, $data)
    {
        if (is_array($data) || is_object($data)) {
            $this->addDebug($type . ': ' . $this->json->serialize($data));
        } else {
            $this->addDebug($type . ': ' . $data);
        }
    }
}