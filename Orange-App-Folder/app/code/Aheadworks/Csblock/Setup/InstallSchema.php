<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/


namespace Aheadworks\Csblock\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;


/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /**
         * Create table 'aw_autorelated_rule'
         */

        /** @var \Magento\Framework\DB\Ddl\Table $ruleTable */
        $blockTable = $installer->getConnection()->newTable($installer->getTable('aw_csblock_block'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Csblock ID'
            )
            ->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Csblock Name'
            )
            ->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                1,
                ['nullable' => false],
                'Status'
            )
            ->addColumn(
                'customer_groups',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Customer Groups'
            )
            ->addColumn(
                'page_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false],
                'Csblock Type'
            )
            ->addColumn(
                'position',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Position'
            )
            ->addColumn(
                'pattern',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Schedule Pattern'
            )
            ->addColumn(
                'date_from',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                [],
                'From Date'
            )
            ->addColumn(
                'date_to',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                [],
                'From Date'
            )
            ->addColumn(
                'time_from',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [],
                'From Time'
            )
            ->addColumn(
                'time_to',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [],
                'To Date'
            )
            ->addColumn(
                'product_condition',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Product Condition'
            )
            ->addColumn(
                'category_ids',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Category IDs'
            )
        ;
        $installer->getConnection()->createTable($blockTable);

        /** @var \Magento\Framework\DB\Ddl\Table $viewInfoTable */
        $contentTable = $installer->getConnection()->newTable($installer->getTable('aw_csblock_content'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true,'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )
            ->addColumn(
                'csblock_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Csblock ID'
            )
            ->addColumn(
                'static_block_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Static block ID'
            )
            ->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Store ID'
            )
        ;
        $installer->getConnection()->createTable($contentTable);

        $installer->endSetup();
    }
}
