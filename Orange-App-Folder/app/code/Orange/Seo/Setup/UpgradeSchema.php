<?php

namespace Orange\Seo\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;


class UpgradeSchema implements UpgradeSchemaInterface {
	
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $setup->startSetup();		
        if (version_compare($context->getVersion(), '1.0.9') < 0) {
			$tableName = $setup->getTable('sales_order');
			if ($setup->getConnection()->isTableExists($tableName) == true) {
				$connection = $setup->getConnection();
                $connection->addColumn(
                    $tableName, 'fraud_capture', [
						'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
						'unsigned' => true,
						'nullable' => false,
						'default' => '0',
						'comment' => 'Flag value for fraud report'
					]
                );
			}			
        }        
        $setup->endSetup();
    }

}
