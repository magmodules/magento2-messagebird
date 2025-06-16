<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magmodules\MessageBird\Logger\Handler;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Error extends StreamHandler
{
    public const FILENAME = 'messagebird-error.log';
    public const LEVEL = Logger::ERROR;

    public function __construct()
    {
        /** @phpstan-ignore constant.notFound */
        parent::__construct(BP . '/var/log/' . self::FILENAME, self::LEVEL);
    }
}
