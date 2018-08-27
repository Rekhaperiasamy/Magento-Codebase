<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Cron;

/**
 * Class Cron
 * @package Scommerce\Gdpr\Cron
 */
class Cron
{
    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    /** @var \Scommerce\Gdpr\Model\Service\QuoteReset */
    private $service;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Scommerce\Gdpr\Model\Service\QuoteReset $service
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Scommerce\Gdpr\Model\Service\QuoteReset $service
    ) {
        $this->logger = $logger;
        $this->service = $service;
    }

    /**
     * Process quote and quote_address tables
     *
     * @return void
     */
    public function execute()
    {
        try {
            $this->service->execute();
        } catch(\Exception $e) {
            $this->logger->error('[Scommerce_Gdpr][Error][Cron]');
            $this->logger->error($e->getMessage());
        }
    }
}
