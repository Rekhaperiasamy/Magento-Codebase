<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Acart
 */

namespace Amasty\Acart\Cron;

use Magento\Framework\App\ResourceConnection;

class RefreshHistory
{
    protected $_indexerFactory;

    public function __construct(
        \Amasty\Acart\Model\IndexerFactory $indexerFactory
    ){
        $this->_indexerFactory = $indexerFactory;
    }

    public function execute()
    {
        $this->_indexerFactory->create()->run();
    }
}