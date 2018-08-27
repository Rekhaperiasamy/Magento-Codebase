<?php
namespace Orange\Abandonexport\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
class UpgradeSchema implements UpgradeSchemaInterface
{

public function Upgrade(SchemaSetupInterface $setup, ModuleContextInterface    $context)
{

if (version_compare($context->getVersion(), '1.0.0') < 0) {
		$installer = $setup;

		$installer->startSetup();

		$orderTable = $installer->getTable('orange_abandonexport_items');

		$orderColumns = [
			'check_step_stat' => [
				'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				'nullable' => true,
				'default' => null,		
				'comment' => 'current step status',
			],
		];
		$connection = $installer->getConnection();
		foreach ($orderColumns as $name => $definition) {			
			$connection->addColumn($orderTable, $name, $definition);
		}
		$installer->endSetup();
	}
	if (version_compare($context->getVersion(), '1.0.1') < 0) {
		$installer = $setup;

		$installer->startSetup();

		$orderTable = $installer->getTable('orange_abandonexport_items');

		$orderColumns = [
			'order_status' => [
				'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				'nullable' => true,
				'default' => null,		
				'comment' => 'abandon order status',
			],
		];
		$connection = $installer->getConnection();
		foreach ($orderColumns as $name => $definition) {			
			$connection->addColumn($orderTable, $name, $definition);
		}
		$installer->endSetup();
	}
	
}
}