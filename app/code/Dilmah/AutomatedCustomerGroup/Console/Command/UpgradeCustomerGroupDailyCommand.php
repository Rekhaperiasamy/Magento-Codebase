<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_AutomatedCustomerGroup
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
namespace Dilmah\AutomatedCustomerGroup\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpgradeCustomerGroupDailyCommand extends Command
{
    /**
     * @var \Dilmah\GiftCardAccount\Cron\UpgradeCustomerGroupDaily
     */
    protected $cronProcess;

    /**
     * NotifyLowStocksDailyCommand constructor
     *
     * @param \Dilmah\AutomatedCustomerGroup\Cron\UpgradeCustomerGroupDaily $cron
     */
    public function __construct(
        \Dilmah\AutomatedCustomerGroup\Cron\UpgradeCustomerGroupDaily $cron
    ) {
        $this->cronProcess = $cron;
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('cron:customer-upgrade')
            ->setDescription('Upgrade customer groups by checking reward points');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->cronProcess->execute();
        $output->writeln(".");
        $output->writeln("<info>Finished</info>");
    }
}
