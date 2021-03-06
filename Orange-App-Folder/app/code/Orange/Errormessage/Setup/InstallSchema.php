<?php
/**
 * Copyright © 2015 Orange. All rights reserved.
 */

namespace Orange\Errormessage\Setup;

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
     */
 public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
	
        $installer = $setup;

        $installer->startSetup();

		/**
         * Create table 'errormessage_errormessage'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('errormessage_errormessage')
        )
		->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'errormessage_errormessage'
        )
		->addColumn(
            'code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'code'
        )
		->addColumn(
            'message',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'message'
        )
		->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'store_id'
        )
		->addColumn(
            'customer_group',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'customer_group'
        )
		->addColumn(
            'html_content',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '500',
            [],
            'html_content'
        )
		/*{{CedAddTableColumn}}}*/
		
		
        ->setComment(
            'Orange errormessage_errormessage errormessage_errormessage'
        );
		
		$installer->getConnection()->createTable($table);
		/*{{CedAddTable}}*/

        $installer->endSetup();

    }
}
