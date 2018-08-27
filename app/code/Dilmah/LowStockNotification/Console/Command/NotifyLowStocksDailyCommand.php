<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_LowStockNotification
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
namespace Dilmah\LowStockNotification\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NotifyLowStocksDailyCommand extends Command
{
    /**
     * @var \Dilmah\GiftCardAccount\Cron\NotifyLowStocksDaily
     */
    protected $cronProcess;

    /**
     * NotifyLowStocksDailyCommand constructor
     *
     * @param \Dilmah\LowStockNotification\Cron\NotifyLowStocksDaily $cron
     */
    public function __construct(
        \Dilmah\LowStockNotification\Cron\NotifyLowStocksDaily $cron
    ) {
        $this->cronProcess = $cron;
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('cron:notify-stock')
            ->setDescription('Notify low stock products to store admins');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->cronProcess->setAreaCode('crontab');
        $this->cronProcess->execute();
        $output->writeln(".");
        $output->writeln("<info>Finished</info>");
    }
}
