<?php
namespace Orange\StockManagement\Setup;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
class UpgradeSchema implements  UpgradeSchemaInterface
{
	public function upgrade(SchemaSetupInterface $setup,
                            ModuleContextInterface $context){
		if (version_compare($context->getVersion(), '1.0.1') < 0) {
			$installer = $setup;
			$installer->startSetup();
			$tableName = $installer->getTable('stockmanagement_stock');
				$orderItemColumns = [
				'valid_from' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
					'nullable' => true,
					'default' => '',
					'comment' => 'valid from',
				],

			];
			$connection = $installer->getConnection();
			foreach ($orderItemColumns as $name => $definition) {
			$connection->changeColumn($tableName, $name,$name, $definition);
			}
		}
		$installer->endSetup();
	}
}