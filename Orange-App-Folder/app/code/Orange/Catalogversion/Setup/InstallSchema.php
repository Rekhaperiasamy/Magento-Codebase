<?php
/**
 * Copyright © 2015 Orange. All rights reserved.
 */

namespace Orange\Catalogversion\Setup;

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
         * Create table 'catalogversion_catalogversion'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('catalogversion_catalogversion')
        )
		->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'catalogversion_catalogversion'
        )
		->addColumn(
            'productid',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Product ID'
        )
		->addColumn(
            'sku',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'SKU'
        )
		->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'Name'
        )
		->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'Status'
        )
		->addColumn(
            'price',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'Price'
        )
		->addColumn(
            'quantity',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Quantity'
        )
		->addColumn(
            'custom_attribue_info',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '',
            [],
            'Custom Attribue Info'
        )
		/* 	->addColumn(
            'stock',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'stock'
        ) */
		/* ->addColumn(
            'categories',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'categories'
        ) */
/* 			->addColumn(
            'visibility',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'visibility'
        ) */
/* 			->addColumn(
            'image',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'image'
        ) */
		/* 	->addColumn(
            'smallimage',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'smallimage'
        ) */
		/* ->addColumn(
            'thumbnail',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'thumbnail'
        ) */
		/* 	->addColumn(
            'swatchimage',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'swatchimage'
        ) */
	/* 	->addColumn(
            'urlkey',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'urlkey'
        ) */
	/* 	->addColumn(
            'metatitle',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'metatitle'
        ) */
	/* 	->addColumn(
            'metakeyword',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'metakeyword'
        ) */
		/* 	->addColumn(
            '	',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'metadescription'
        ) */
		/*{{CedAddTableColumn}}}*/
		
		
        ->setComment(
            'Orange Catalogversion catalogversion_catalogversion'
        );
		
		$installer->getConnection()->createTable($table);
		/*{{CedAddTable}}*/

        $installer->endSetup();

    }
}
