<?php
/**
 * Copyright © 2015 Orange. All rights reserved.
 */

namespace Orange\Limitqty\Setup;

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
         * Create table 'promodescription_promotiondescription'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('limitqty_limitqty')
        )
		->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'limitqty_limitqty'
        )
		->addColumn(
            'limitquantity',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'sku'
        )
		->addColumn(
            'errormessage',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'family'
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
		
		/*{{CedAddTableColumn}}}*/
		
		
        ->setComment(
            'Orange Limitqty limitqty_limitqty'
        );
		
		$installer->getConnection()->createTable($table);
		/*{{CedAddTable}}*/

        $installer->endSetup();

    }
}
