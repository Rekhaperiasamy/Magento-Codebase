<?php
/**
 * Copyright © 2015 Orange. All rights reserved.
 */
namespace Orange\Abandonexport\Setup;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();
        $table  = $installer->getConnection()
            ->newTable($installer->getTable('orange_abandonexport_items'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )
			->addColumn(
                'quote_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null],
                'quote_id'
            )
			->addColumn(
                'ipaddress',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null],
                'ipaddress'
            )
			->addColumn(
                'stepfirst',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null],
                'stepfirst'
            )
			->addColumn(
                'stepsecond',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null],
                'stepsecond'
            )
			->addColumn(
                'ipaddress',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null],
                'ipaddress'
            )
            ->addColumn(
                'create_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['default' => null],
                'create_at'
            );
        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }
}
