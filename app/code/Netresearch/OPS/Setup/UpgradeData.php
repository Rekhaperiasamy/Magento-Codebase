<?php

namespace Netresearch\OPS\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        // Fulfill existing rows with data from address table
        $select = $setup->getConnection()->select()->join(
            ['flat_order' => $setup->getTable('sales_order')],
            'flat_order.entity_id = order_grid.entity_id',
            ['quote_id' => 'quote_id']
        );

        $updateQuery = $select->crossUpdateFromSelect(['order_grid' => $setup->getTable('sales_order_grid')]);
        $setup->getConnection()->query($updateQuery);
        $setup->endSetup();
    }
}
