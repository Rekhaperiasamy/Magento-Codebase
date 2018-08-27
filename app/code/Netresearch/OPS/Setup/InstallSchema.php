<?php

namespace Netresearch\OPS\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        /**
         * Prepare database for install
         */
        $installer->startSetup();

        /**
         * Create table 'ops_alias'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('ops_alias')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true],
            'Customer Id'
        )->addColumn(
            'alias',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Alias'
        )->addColumn(
            'card_holder',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Card Holder'
        )->addColumn(
            'brand',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            50,
            [],
            'Brand'
        )->addColumn(
            'billing_address_hash',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Billing Address Hash'
        )->addColumn(
            'shipping_address_hash',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Shipping Address Hash'
        )->addColumn(
            'pseudo_account_or_cc_no',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Pseudo Account or CC No'
        )->addColumn(
            'expiration_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            10,
            [],
            'Expiration Date'
        )->addColumn(
            'payment_method',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            50,
            [],
            'Payment Method'
        )->addColumn(
            'state',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            100,
            ['default' => \Netresearch\OPS\Model\Alias\State::PENDING],
            'State'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true],
            'Store Id'
        )->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Created At'
        );

        $installer->getConnection()->createTable($table);

        /**
         * Create table 'ops_kwixo_category_mapping'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('ops_kwixo_category_mapping')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )->addColumn(
            'kwixo_category_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Kwixo Category Id'
        )->addColumn(
            'category_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Category Id'
        );

        $installer->getConnection()->createTable($table);

        /**
         * Create table 'ops_kwixo_shipping_setting'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('ops_kwixo_shipping_setting')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )->addColumn(
            'shipping_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Shipping Code'
        )->addColumn(
            'kwixo_shipping_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Kwixo Shipping Type'
        )->addColumn(
            'kwixo_shipping_speed',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Kwixo Shipping Speed'
        )->addColumn(
            'kwixo_shipping_details',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            50,
            ['unsigned' => true],
            'Kwixo Shipping Details'
        )->addIndex(
            $installer->getIdxName('ops_kwixo_shipping_setting', ['shipping_code']),
            ['shipping_code']
        );

        $installer->getConnection()->createTable($table);

        /**
         * Prepare database after install
         */
        $installer->endSetup();
    }
}
