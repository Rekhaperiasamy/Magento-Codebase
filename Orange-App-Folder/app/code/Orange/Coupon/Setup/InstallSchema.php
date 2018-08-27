<?php


namespace Orange\Coupon\Setup;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();

        $table_orange_quote_coupon = $setup->getConnection()->newTable($setup->getTable('orange_quote_coupon'));

        
        $table_orange_quote_coupon->addColumn(
            'quote_coupon_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,'auto_increment' => true,),
            'Entity ID'
        );
        

        
        $table_orange_quote_coupon->addColumn(
            'quote_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Quote ID'
        );
        

        
        $table_orange_quote_coupon->addColumn(
            'rule_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Cart Rule ID'
        );
        

        
        $table_orange_quote_coupon->addColumn(
            'discount_amount',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            null,
            ['default' => '0.0000','precision' => 12,'scale' => 4],
            'Discount Amount'
        );
        

        
        $table_orange_quote_coupon->addColumn(
            'subscription_discount_amount',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            null,
            ['default' => '0.0000','precision' => 12,'scale' => 4],
            'Subscription Discount Amount'
        );
        

        $setup->getConnection()->createTable($table_orange_quote_coupon);

        $setup->endSetup();
    }
}
