<?php

/* *
 * Copyright © 2016 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\SimpleGoogleShopping\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * $ bin/magento help wyomind:sgs:list
 * Usage:
 * wyomind:sgs:list
 *
 * Options:
 * --help (-h)           Display this help message
 * --quiet (-q)          Do not output any message
 * --verbose (-v|vv|vvv) Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
 * --version (-V)        Display this application version
 * --ansi                Force ANSI output
 * --no-ansi             Disable ANSI output
 * --no-interaction (-n) Do not ask any interactive question
 */
class Listing extends Command
{

    protected $_feedsCollectionFactory = null;
    protected $_state = null;

    public function __construct(
        \Wyomind\SimpleGoogleShopping\Model\ResourceModel\Feeds\CollectionFactory $feedsCollectionFactory,
        \Magento\Framework\App\State $state
    ) {
    
        $this->_state = $state;
        $this->_feedsCollectionFactory = $feedsCollectionFactory;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('wyomind:sgs:list')
                ->setDescription(__('Simple Google Shopping : get list of available feeds'))
                ->setDefinition([]);
        parent::configure();
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
    

        try {
            $this->_state->setAreaCode('adminhtml');

            $collection = $this->_feedsCollectionFactory->create();
            foreach ($collection as $feed) {
                $row = sprintf(
                    "%-6d %-45s %-22s",
                    $feed->getSimplegoogleshoppingId(),
                    $feed->getSimplegoogleshoppingPath() . $feed->getSimplegoogleshoppingFilename() . ".xml",
                    $feed->getSimplegoogleshoppingTime()
                );
                $output->writeln($row);
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $output->writeln($e->getMessage());
            $returnValue = \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }

        $returnValue = \Magento\Framework\Console\Cli::RETURN_FAILURE;

        return $returnValue;
    }
}
