<?php

namespace Netresearch\OPS\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        // Add quote_id column to sales order grid table
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order_grid'),
            'quote_id',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'nullable' => false,
                'default'  => 0,
                'unsigned' => true,
                'comment' => 'Id Of Related Quote'
            ]
        );

        // Add key to table for this field,
        // it will improve the speed of searching & sorting by the field
        $installer->getConnection()->addIndex(
            $installer->getTable('sales_order_grid'),
            $installer->getIdxName('sales_order_grid', ['quote_id']),
            ['quote_id']
        );

        $installer->getConnection()->changeColumn(
            $installer->getTable('ops_alias'),
            'alias',
            'alias',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'comment' => 'Alias'
            ]
        );

        $installer->endSetup();
    }
}
