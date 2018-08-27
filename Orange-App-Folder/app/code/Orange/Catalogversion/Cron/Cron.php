<?php
namespace Orange\Catalogversion\Cron;
use \Psr\Log\LoggerInterface;

class Scheduledraft {
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
        $this->logger->info('Cron Works');
    }

}