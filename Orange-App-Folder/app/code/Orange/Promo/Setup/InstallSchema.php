<?php
/**
 * Copyright © 2015 Orange. All rights reserved.
 */

namespace Orange\Promo\Setup;

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
            $installer->getTable('promodescription_promotiondescription')
        )
		->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'promodescription_promotiondescription'
        )
		->addColumn(
            'sku',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'sku'
        )
		->addColumn(
            'family',
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
		->addColumn(
            'html_content',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '500',
            [],
            'html_content'
        )
		/*{{CedAddTableColumn}}}*/
		
		
        ->setComment(
            'Orange PromoDescription promodescription_promotiondescription'
        );
		
		$installer->getConnection()->createTable($table);
		/*{{CedAddTable}}*/

        $installer->endSetup();

    }
}
