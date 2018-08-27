<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Scommerce\Gdpr\Api\Data\ConsentInterface;

/**
 * Class UpgradeSchema
 * @package Scommerce\Gdpr\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.4') < 0) {
            $tables = ['quote', 'quote_address'];

            foreach ($tables as $table) {
                $installer->getConnection()->addColumn(
                    $installer->getTable($table),
                    'scommerce_gdpr_processed_value',
                    [
                        'type' => Table::TYPE_SMALLINT,
                        'nullable' => true,
                        'comment' => 'GDPR processed value'
                    ]
                );
            }
        }

        if (version_compare($context->getVersion(), '1.0.5') < 0) {
            $table = $this->getConsentTable($installer);
            $installer->getConnection()->createTable($table);
        }
		
		if (version_compare($context->getVersion(), '1.0.6') < 0) {
			$installer->getConnection()->addColumn(
				$installer->getTable('sales_order'),
				'scommerce_gdpr_processed_value',
				[
					'type' => Table::TYPE_SMALLINT,
					'nullable' => true,
					'comment' => 'GDPR processed value'
				]
			);
        }
		
        $installer->endSetup();
    }

    /**
     * @param SchemaSetupInterface $installer
     * @return Table
     * @throws \Zend_Db_Exception
     */
    private function getConsentTable($installer)
    {
        return $installer->getConnection()->newTable(
            $installer->getTable(ConsentInterface::TABLE)
        )->addColumn(
            ConsentInterface::CONSENT_ID,
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Consent ID'
        )->addColumn(
            ConsentInterface::CUSTOMER_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => true],
            'Customer ID'
        )->addColumn(
            ConsentInterface::WEBSITE_ID,
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Website ID'
        )->addColumn(
            ConsentInterface::GUEST_EMAIL,
            Table::TYPE_TEXT,
            50,
            ['nullable' => false],
            'Guest Email'
        )->addColumn(
            ConsentInterface::SOURCE,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Source'
        )->addColumn(
            ConsentInterface::REMOTE_IP,
            Table::TYPE_TEXT,
            50,
            ['nullable' => false],
            'Remote Ip'
        )->addColumn(
            ConsentInterface::CREATED_AT,
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Creation Time'
        )->addColumn(
            ConsentInterface::UPDATED_AT,
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
            'Modification Time'
        )->addIndex(
            $installer->getIdxName(ConsentInterface::TABLE, [ConsentInterface::CUSTOMER_ID]),
            [ConsentInterface::CUSTOMER_ID]
        )->addIndex(
            $installer->getIdxName(ConsentInterface::TABLE, [ConsentInterface::WEBSITE_ID]),
            [ConsentInterface::WEBSITE_ID]
        )->addIndex(
            $installer->getIdxName(ConsentInterface::TABLE, [ConsentInterface::GUEST_EMAIL]),
            [ConsentInterface::GUEST_EMAIL]
        )->addIndex(
            $installer->getIdxName(ConsentInterface::TABLE, [ConsentInterface::REMOTE_IP]),
            [ConsentInterface::REMOTE_IP]
        )->addIndex(
            $installer->getIdxName(ConsentInterface::TABLE, [ConsentInterface::CREATED_AT]),
            [ConsentInterface::CREATED_AT]
        )->addIndex(
            $installer->getIdxName(ConsentInterface::TABLE, [ConsentInterface::UPDATED_AT]),
            [ConsentInterface::UPDATED_AT]
        )->addForeignKey(
            $installer->getFkName(ConsentInterface::TABLE, ConsentInterface::CUSTOMER_ID, 'customer_entity', 'entity_id'),
            ConsentInterface::CUSTOMER_ID,
            $installer->getTable('customer_entity'),
            'entity_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName(ConsentInterface::TABLE, ConsentInterface::WEBSITE_ID, 'store_website', 'website_id'),
            ConsentInterface::WEBSITE_ID,
            $installer->getTable('store_website'),
            'website_id',
            Table::ACTION_CASCADE
        )->setComment('Scommerce Gdpr Consent');
    }
}
