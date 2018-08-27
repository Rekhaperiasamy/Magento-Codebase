<?php
namespace IWD\Opc\Setup;

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
         if (version_compare($context->getVersion(), "2.0.6", "<")) {
        //Upgrade script
            $installer = $setup;

            $installer->startSetup();

            $quoteItemTable = $installer->getTable('quote_item');
			
			$salesOrderItem = $installer->getTable('sales_order_item');

            $couponTableColumns = [
                    'design_sim_number' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                    'comment' => 'sim number yes or no',
                ],
				'design_te_existing_number' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                    'comment' => 'design_te_existing_number',
                ],
				'subscription_type' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                    'comment' => 'subscription_type',
                ],
				'current_operator' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                    'comment' => 'current_operator',
                ],
				'network_customer_number' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                    'comment' => 'network_customer_number',
                ],
				'simcard_number' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                    'comment' => 'simcard_number',
                ],
				'bill_in_name' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                    'comment' => 'bill_in_name',
                ],
				'holders_name' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                    'comment' => 'holders_name',
                ],
				'holder_firstname' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                    'comment' => 'holder_firstname',
                ],
            ];
			
            $connection = $installer->getConnection();
            foreach ($couponTableColumns as $name => $definition) {
                $connection->addColumn($quoteItemTable, $name, $definition);
				$connection->addColumn($salesOrderItem, $name, $definition);
            }
            $installer->endSetup();
        }   
         if (version_compare($context->getVersion(), "2.0.7", "<")) {
        //Upgrade script
            $installer = $setup;

            $installer->startSetup();

            $quoteItemTable = $installer->getTable('quote_item');
			
			$salesOrderItem = $installer->getTable('sales_order_item');

            $couponTableColumns = [
                    'design_te_existing_number_final_validation' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                    'comment' => 'sim number yes or no',
                ],
            ];
			
            $connection = $installer->getConnection();
            foreach ($couponTableColumns as $name => $definition) {
                $connection->addColumn($quoteItemTable, $name, $definition);
				$connection->addColumn($salesOrderItem, $name, $definition);
            }
            $installer->endSetup();
        } 		
    }
}
