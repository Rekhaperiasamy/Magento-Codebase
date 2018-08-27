<?php
/**
 * Copyright © 2017 Magentix. All rights reserved.
 *
 * NOTICE OF LICENSE
 * This source file is subject to commercial licence, do not copy or distribute without authorization
 */
namespace Colissimo\Rule\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class InstallSchema
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
         * Create table 'colissimo_rule'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('colissimo_rule')
        )->addColumn(
            'rule_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Rule Id'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            [],
            'Name'
        )->addColumn(
            'description',
            Table::TYPE_TEXT,
            '64k',
            [],
            'Description'
        )->addColumn(
            'from_date',
            Table::TYPE_DATE,
            null,
            ['nullable' => true, 'default' => null],
            'From'
        )->addColumn(
            'to_date',
            Table::TYPE_DATE,
            null,
            ['nullable' => true, 'default' => null],
            'To'
        )->addColumn(
            'is_active',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '0'],
            'Is Active'
        )->addColumn(
            'conditions_serialized',
            Table::TYPE_TEXT,
            '2M',
            [],
            'Conditions Serialized'
        )->addColumn(
            'sort_order',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Sort Order'
        )->addColumn(
            'shipping_amount',
            Table::TYPE_DECIMAL,
            [12, 4],
            ['nullable' => false, 'default' => '0.0000'],
            'Shipping Amount'
        )->addColumn(
            'shipping_method',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Shipping Method'
        )->addColumn(
            'shipping_action',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => 'price'],
            'Shipping Action'
        )->addIndex(
            $installer->getIdxName('colissimo_rule', ['is_active', 'sort_order', 'to_date', 'from_date']),
            ['is_active', 'sort_order', 'to_date', 'from_date']
        )->setComment(
            'Colissimo Rule'
        );
        $installer->getConnection()->createTable($table);

        $websitesTable = $installer->getTable('store_website');
        $customerGroupsTable = $installer->getTable('customer_group');
        $rulesWebsitesTable = $installer->getTable('colissimo_rule_website');
        $rulesCustomerGroupsTable = $installer->getTable('colissimo_rule_customer_group');

        /**
         * Create table 'colissimo_rule_website' if not exists. This table will be used instead of
         * column website_ids of main catalog rules table
         */
        $table = $installer->getConnection()->newTable(
            $rulesWebsitesTable
        )->addColumn(
            'rule_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Rule Id'
        )->addColumn(
            'website_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Website Id'
        )->addIndex(
            $installer->getIdxName('colissimo_rule_website', ['website_id']),
            ['website_id']
        )->addForeignKey(
            $installer->getFkName('colissimo_rule_website', 'rule_id', 'colissimo_rule', 'rule_id'),
            'rule_id',
            $installer->getTable('colissimo_rule'),
            'rule_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('colissimo_rule_website', 'website_id', 'store_website', 'website_id'),
            'website_id',
            $websitesTable,
            'website_id',
            Table::ACTION_CASCADE
        )->setComment(
            'Colissimo Rules To Websites Relations'
        );

        $installer->getConnection()->createTable($table);

        /**
         * Create table 'colissimo_rule_customer_group' if not exists. This table will be used instead of
         * column customer_group_ids of main catalog rules table
         */
        $table = $installer->getConnection()->newTable(
            $rulesCustomerGroupsTable
        )->addColumn(
            'rule_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Rule Id'
        )->addColumn(
            'customer_group_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Customer Group Id'
        )->addIndex(
            $installer->getIdxName('colissimo_rule_customer_group', ['customer_group_id']),
            ['customer_group_id']
        )->addForeignKey(
            $installer->getFkName('colissimo_rule_customer_group', 'rule_id', 'colissimo_rule', 'rule_id'),
            'rule_id',
            $installer->getTable('colissimo_rule'),
            'rule_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName(
                'colissimo_rule_customer_group',
                'customer_group_id',
                'customer_group',
                'customer_group_id'
            ),
            'customer_group_id',
            $customerGroupsTable,
            'customer_group_id',
            Table::ACTION_CASCADE
        )->setComment(
            'Colissimo Rules To Customer Groups Relations'
        );

        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
