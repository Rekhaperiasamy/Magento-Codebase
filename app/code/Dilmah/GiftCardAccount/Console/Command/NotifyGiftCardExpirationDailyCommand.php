<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_GiftCardAccount
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
namespace Dilmah\GiftCardAccount\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NotifyGiftCardExpirationDailyCommand extends Command
{
    /**
     * @var AppState
     */
    protected $appState;

    /**
     * @var \Dilmah\GiftCardAccount\Cron\NotifyGiftCardExpirationDaily
     */
    protected $cronProcess;

    /**
     * NotifyGiftCardExpirationDailyCommand constructor.
     * @param \Magento\Framework\App\State $appState
     * @param \Dilmah\GiftCardAccount\Cron\NotifyGiftCardExpirationDaily $cron
     */
    public function __construct(
        \Magento\Framework\App\State $appState,
        \Dilmah\GiftCardAccount\Cron\NotifyGiftCardExpirationDaily $cron
    ) {
        $this->appState = $appState;
        $this->cronProcess = $cron;
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('cron:notify-giftcards')
            ->setDescription('Notify customers on unused gift-cards');
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
