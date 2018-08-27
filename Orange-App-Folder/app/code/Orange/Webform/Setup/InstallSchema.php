<?php
/**
 * Copyright © 2015 Orange. All rights reserved.
 */

namespace Orange\Webform\Setup;

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
         * Create table 'mnpform'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('webform_mnpform')
        )
		->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'webform_mnp'
        )
		->addColumn(
            'title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '50',
            [],
            'title'
        )
		->addColumn(
            'firstname',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '200',
            [],
            'firstname'
        )
		->addColumn(
            'lastname',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '200',
            [],
            'lastname'
        )
		->addColumn(
            'email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '200',
            [],
            'email'
        )
		->addColumn(
            'vatnumber',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '30',
            [],
            'vatnumber'
        )
		
		
		->addColumn(
            'what_is_your_current_operator_make_your_choice',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '50',
            [],
            'what_is_your_current_operator_make_your_choice'
        )
		
		->addColumn(
            'current_mobile_phone_number',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '100',
            [],
            'current_mobile_phone_number'
        )
		->addColumn(
            'current_sim_card_number',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '100',
            [],
            'current_sim_card_number'
        )
		->addColumn(
            'card_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '200',
            [],
            'card_type'
        )
		
		->addColumn(
            'orange_mobile_phone_number',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '200',
            [],
            'orange_mobile_phone_number'
        )
		
		->addColumn(
            'sim_card_number_orange',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '200',
            [],
            'sim_card_number_orange'
        )
        ->addColumn(
            'do_you_want_to_receive_interesting_offers_and_the_latest_Orange',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '10',
            [],
            'do_you_want_to_receive_interesting_offers_and_the_latest_Orange'
        )
        ->setComment(
            'Orange webform mnp'
        );
		
		$installer->getConnection()->createTable($table);
		
		
		
		/**
         * Create table 'activerform'
         */
        $activerform = $installer->getConnection()->newTable(
            $installer->getTable('webform_activerform')
        )
		->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'webform_activerform'
        )
		->addColumn(
            'order_number',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '100',
            [],
            'order_number'
        )
		->addColumn(
            'date_of_birth',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false],
            'date_of_birth'
        )
		->addColumn(
            'firstname',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '200',
            [],
            'firstname'
        )
		->addColumn(
            'lastname',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '200',
            [],
            'lastname'
        )
		->addColumn(
            'email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '200',
            [],
            'email'
        )
        ->setComment(
            'Orange activer mnp'
        );
		
		$installer->getConnection()->createTable($activerform);

        $installer->endSetup();

    }
}
