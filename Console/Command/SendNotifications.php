<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magmodules\MessageBird\Console\Command;

use Magento\Framework\App\State as AppState;
use Magento\Framework\Console\Cli;
use Magento\ProductAlert\Model\Observer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to force send notification
 */
class SendNotifications extends Command
{

    /**
     * Create feed command
     */
    const COMMAND_NAME = 'messagebird:notifications:run';

    /**
     * @var Observer
     */
    private $observer;

    /**
     * @var AppState
     */
    private $appState;

    /**
     * SendNotifications constructor.
     * @param Observer $observer
     * @param AppState $appState
     */
    public function __construct(
        Observer $observer,
        AppState $appState
    ) {
        $this->observer = $observer;
        $this->appState = $appState;
        parent::__construct();
    }

    /**
     *  {@inheritdoc}
     */
    public function configure()
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription('Generate feed file');
        parent::configure();
    }

    /**
     *  {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->appState->setAreaCode('adminhtml');
            $this->observer->process();
            return Cli::RETURN_SUCCESS;
        } catch (\Exception $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");
            return Cli::RETURN_FAILURE;
        }
    }
}
