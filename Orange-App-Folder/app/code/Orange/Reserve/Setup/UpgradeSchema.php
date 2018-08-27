<?php

/**
 * Copyright © 2015 Orange. All rights reserved.
 */

namespace Orange\Reserve\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface {

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context) {

        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'reserve_blacklist'
         */
        $table = $installer->getConnection()->newTable(
                        $installer->getTable('reserve_blacklist')
                )
                ->addColumn(
                        'id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'reserve_blacklist'
                )
                ->addColumn(
                        'name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', [], 'name'
                )
                ->addColumn(
                        'firstname', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', [], 'firstname'
                )
                ->addColumn(
                        'ip_address', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', [], 'ip_address'
                )
                ->addColumn(
                        'email_address', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', [], 'email_address'
                )
                /* {{CedAddTableColumn}}} */
                ->setComment(
                'Orange Reserve reserve_blacklist '
        );

        $installer->getConnection()->createTable($table);
        /* {{CedAddTable}} */

        $installer->endSetup();
    }

}
