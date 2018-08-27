<?php
namespace Orange\Coupon\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
class UpgradeSchema implements UpgradeSchemaInterface
{
	public function Upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
	{
		if (version_compare($context->getVersion(), '1.0.1') < 0) {
			$installer = $setup;
			$installer->startSetup();
			$connection = $installer->getConnection();
			$table_orange_quote_coupon = $installer->getTable('orange_quote_coupon');
			$quoteCouponColumns = [
				'item_id' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					'nullable' => false,					
					'comment' => 'Item Id',
				],
			];
			foreach ($quoteCouponColumns as $name => $definition) {
				$connection->addColumn($table_orange_quote_coupon, $name, $definition);
			}
	        $installer->endSetup();
		}
                /* Added Coupon code field for coupon report generation */
                if (version_compare($context->getVersion(), '1.0.2') < 0) {
			$installer = $setup;
			$installer->startSetup();
			$connection = $installer->getConnection();
			$table_orange_quote_coupon = $installer->getTable('orange_quote_coupon');
			$quoteCouponColumns = [
				'CouponCode' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => false,					
					'comment' => 'Coupon Code',
				],
			];
			foreach ($quoteCouponColumns as $name => $definition) {
				$connection->addColumn($table_orange_quote_coupon, $name, $definition);
			}
	        $installer->endSetup();
		}
	}
}