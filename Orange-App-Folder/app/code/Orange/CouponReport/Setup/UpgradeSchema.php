<?php
namespace Orange\CouponReport\Setup;

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
			$table_orange_quote_coupon = $setup->getConnection()
			->newTable($setup->getTable('orange_coupon_report'));
			$table_orange_quote_coupon->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            array('identity' => true,'nullable' => false,'primary' => true,
			'unsigned' => true,'auto_increment' => true,),
            'Entity ID'
			);
			$table_orange_quote_coupon->addColumn(
            'order_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            32,
            [],
            'Order ID'
			);
            $table_orange_quote_coupon->addColumn(
            'quote_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            [],
            'Quote Id'
			);
			$table_orange_quote_coupon->addColumn(
            'coupon_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Coupon Code'
			);
            $table_orange_quote_coupon->addColumn(
            'discount_amount',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            [],
            'Discount Amount'
			);
			$table_orange_quote_coupon->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            32,
            [],
            'Status'
			);
            $setup->getConnection()->createTable($table_orange_quote_coupon);
			$setup->endSetup();
		}
		if (version_compare($context->getVersion(), '1.0.2') < 0) {
			$installer = $setup;
			$installer->startSetup();
			$connection = $installer->getConnection();
			$table_orange_quote_coupon = $installer->getTable('orange_coupon_report');
			$connection->changeColumn(
			$table_orange_quote_coupon,
			'coupon_code',
			'ocr_coupon_code',
			['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'length' => 255, 'nullable' => false],
			'Coupon Code'
			);
			$connection->changeColumn(
			$table_orange_quote_coupon,
			'status',
			'order_status',
			['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'length' => 32, 'nullable' => false],
			'Order Status'
			);
			$connection->changeColumn(
			$table_orange_quote_coupon,
			'discount_amount',
			'ocr_discount_amount',
			['type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, 'length' => '12,4', 'nullable' => false],
			'Discount Amount'
			);
			$installer->endSetup();
		}
    }
}