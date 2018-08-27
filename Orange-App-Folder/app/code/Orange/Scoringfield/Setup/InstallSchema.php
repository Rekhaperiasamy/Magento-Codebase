<?php

namespace Orange\Scoringfield\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface {

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $installer = $setup;
        $installer->startSetup();
		$installer->getConnection()->addColumn(
                $installer->getTable('sales_order'), 'score', 
				[
           'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'unsigned' => true,
                'nullable' => false,
                'default' => '0',
               'comment' => 'postpaid_order_score',
                ]
        );
       
        $setup->endSetup();
    }

}
