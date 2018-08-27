<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 *
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\MoneticoOrderCancel\Console\Command;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ObjectManagerFactory;
use Magento\Framework\ObjectManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DraftQuoteCommand.
 */
class OrderCancelCommand extends Command
{
    /**
     * area code.
     *
     * @var string
     */
    const FRONTEND = 'frontend';
    /**
     * Send review emails objectManagerFactory.
     *
     * @var ObjectManagerFactory
     */
    private $objectManagerFactory;

    /**
     * Send review emails objectManager.
     *
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * Send review emails constructor.
     *
     * @param ObjectManagerFactory $objectManagerFactory
     */
    public function __construct(ObjectManagerFactory $objectManagerFactory)
    {
        $this->objectManagerFactory = $objectManagerFactory;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('dilmah:cron:monetico-order-cancel');
        $this->setDescription('Monetico Order Cancel');
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var \Dilmah\MoneticoOrderCancel\Cron\CancelOrder $cronProcess */
        $cronProcess = $this->getObjectManager()->create('Dilmah\MoneticoOrderCancel\Cron\CancelOrder');

        $cronProcess->execute();
    }

    /**
     * Gets initialized object manager.
     *
     * @return ObjectManagerInterface
     */
    protected function getObjectManager()
    {
        if (null == $this->objectManager) {
            $area = self::FRONTEND;
            $this->objectManager = $this->objectManagerFactory->create($_SERVER);
            /** @var \Magento\Framework\App\State $appState */
            $appState = $this->objectManager->get('Magento\Framework\App\State');
            $appState->setAreaCode($area);
            $configLoader = $this->objectManager->get('Magento\Framework\ObjectManager\ConfigLoaderInterface');
            $this->objectManager->configure($configLoader->load($area));
        }

        return $this->objectManager;
    }
}
