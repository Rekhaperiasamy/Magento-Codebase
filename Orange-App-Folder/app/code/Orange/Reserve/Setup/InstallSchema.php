<?php

/**
 * Copyright © 2015 Orange. All rights reserved.
 */

namespace Orange\Reserve\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface {

    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {

        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'reserve_reserve'
         */
        $table = $installer->getConnection()->newTable(
                        $installer->getTable('reserve_reserve')
                )
                ->addColumn(
                        'id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'reserve_reserve'
                )
                ->addColumn(
                        'reserve_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['nullable' => false], 'reserve_id'
                )
                ->addColumn(
                        'customer_name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', [], 'customer_name'
                )
                ->addColumn(
                        'sku', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', [], 'sku'
                )
                ->addColumn(
                        'shop_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['nullable' => false], 'shop_id'
                )
                ->addColumn(
                        'created_date', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => false], 'created_date'
                )
                ->addColumn(
                        'other_details', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', [], 'other_details'
                )
                /* {{CedAddTableColumn}}} */
                ->setComment(
                'Orange Reserve reserve_reserve'
        );

        $installer->getConnection()->createTable($table);
        /* {{CedAddTable}} */

        $installer->endSetup();
    }

}
