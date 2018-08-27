<?php
namespace Orange\Reevoofeed\Cron;
use \Psr\Log\LoggerInterface;

class Schedulerevoo {
    protected $logger;

    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
    }

/**
   * Write to system.log
   *
   * @return void
   */

    public function execute() {
	   $objectManager = \Magento\Framework\App\ObjectManager::getInstance();	
	   $objectManager->get('Orange\Catalog\Helper\ReevooFeedCsv')->generatesFeeds();
    }

}