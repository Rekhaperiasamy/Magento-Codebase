<?php

/**
 * Copyright © 2015 Orange. All rights reserved.
 */

namespace Orange\Priority\Setup;

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
        $this->table1($setup, $context);
        $this->tableData2($setup, $context);
    }

    public function table1($setup, $context) {
        $installer = $setup;
        $installer->startSetup();
        try {
            $table1 = $installer->getConnection()->newTable(
                            $installer->getTable('family_priority')
                    )
                    ->addColumn(
                            'id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'family_priority'
                    )
                    ->addColumn(
                            'family_name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', [], 'Family Name'
                    )
                    ->addColumn(
                            'priority', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, '10', [], 'Priority'
                    )
                    ->addColumn(
                            'store_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, '10', [], 'Store Id'
                    )
                    ->addColumn(
                            'customer_group', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, '10', [], 'Customer Group'
                    )
                    ->addColumn(
                            'created_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT], 'Created At'
                    )
                    ->addColumn(
                            'updated_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE], 'Updated At'
                    )
                    /* {{CedAddTableColumn}}} */
                    ->setComment(
                    'Products Priority'
            );
            $installer->getConnection()->createTable($table1);
            $installer->endSetup();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function tableData2($setup, $context) {
        
        $installer = $setup;
        $installer->startSetup();
        try {
            $tablePopu = $installer->getConnection()->newTable(
                            $installer->getTable('catalog_product_family_popularity')
                    )
                    ->addColumn(
                            'id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false,
                        'primary' => true], 'catalog_product_family_popularity'
                    )
                    ->addColumn(
                            'product_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, '10', [], 'Product Id'
                    )
                    ->addColumn(
                            'family', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', [], 'Family'
                    )
                    ->addColumn(
                            'family_type', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', [], 'Family Type'
                    )
                    ->addColumn(
                            'store_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, '10', [], 'Store Id'
                    )
                    ->addColumn(
                            'logged_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT], 'Logged At'
                    )
                    /* {{CedAddTableColumn}}} */
                    ->setComment(
                    'Products Popularity'
            );

            $installer->getConnection()->createTable($tablePopu);
            $installer->endSetup();
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }

}
