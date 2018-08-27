<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class QuoteResetCommand
 * @package Scommerce\Gdpr\Console\Command
 */
class QuoteResetCommand extends \Symfony\Component\Console\Command\Command
{
    /* @var \Magento\Framework\Model\Context */
    private $context;

    /** @var \Scommerce\Gdpr\Model\Service\QuoteReset */
    private $service;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Scommerce\Gdpr\Model\Service\QuoteReset $service
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Scommerce\Gdpr\Model\Service\QuoteReset $service
    ) {
        parent::__construct();
        $this->context = $context;
        $this->service = $service;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('scommerce:gdpr:reset')
            ->setDescription('Reset quote data. Depends on quote expire module setting')
            ->setDefinition([
                //
            ])
        ;

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $state = $this->context->getAppState();
        $state->emulateAreaCode('adminhtml', function ($state) use ($input, $output) {
            /* @var InputInterface $input */
            /* @var OutputInterface $output */
            try {
                $this->service->execute();
                $output->writeln($this->success('Done'));
            } catch (\Exception $e) {
                $output->writeln($this->error(sprintf('Error: %s', $e->getMessage())));
            }
        }, ['state' => $state]);
    }

    /**
     * Success message
     *
     * @param string $message
     * @return string
     */
    private function success($message)
    {
        return sprintf('<info>%s</info>', $message);
    }

    /**
     * Error message
     *
     * @param string $message
     * @return string
     */
    private function error($message)
    {
        return sprintf('<error>%s</error>', $message);
    }
}
