<?php


namespace Orange\SalesRule\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        if (version_compare($context->getVersion(), "1.0.1", "<")) {
        //Upgrade script
            $installer = $setup;

            $installer->startSetup();

            $couponTable = $installer->getTable('salesrule_coupon');

            $couponTableColumns = [
                'order_ids' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Order Increment Id',
                ],
            ];
            $connection = $installer->getConnection();
            foreach ($couponTableColumns as $name => $definition) {
                $connection->addColumn($couponTable, $name, $definition);
            }
            $installer->endSetup();
        }        
    }
}
