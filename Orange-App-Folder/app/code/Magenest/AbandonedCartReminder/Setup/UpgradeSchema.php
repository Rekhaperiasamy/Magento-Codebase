<?php
namespace Magenest\AbandonedCartReminder\Setup;

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
        if (version_compare($context->getVersion(), "2.0.2", "<")) {
        //Upgrade script
            $installer = $setup;

            $installer->startSetup();

            $couponTable = $installer->getTable('magenest_abandonedcartreminder_mail_log');

            $couponTableColumns = [
                    'quote_id' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'cart quote item id',
                ],
				'resume_link' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'quoteUrl',
                ],
            ];
            $connection = $installer->getConnection();
            foreach ($couponTableColumns as $name => $definition) {
                $connection->addColumn($couponTable, $name, $definition);
            }
            $installer->endSetup();
        }

         if (version_compare($context->getVersion(), "2.0.3", "<")) {
        //Upgrade script
            $installer = $setup;

            $installer->startSetup();

            $couponTable = $installer->getTable('magenest_abandonedcartreminder_mail_log');
			
			$abandoncartTable = $installer->getTable('magenest_abandonedcartreminder_guest_abandoned_cart');

            $couponTableColumns = [
                    'is_deleted' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                    'comment' => 'flush abandon cart after 30 days',
                ],
            ];
			
            $connection = $installer->getConnection();
            foreach ($couponTableColumns as $name => $definition) {
                $connection->addColumn($couponTable, $name, $definition);
				$connection->addColumn($abandoncartTable, $name, $definition);
            }
            $installer->endSetup();
        }
        if (version_compare($context->getVersion(), "2.0.4", "<")) {
        //Upgrade script
            $installer = $setup;

            $installer->startSetup();

            $abandonedTable = $installer->getTable('magenest_abandonedcartreminder_guest_capture');

            $abandonedTableColumns = [
                    'customer_group' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                    'comment' => 'saving customer group',
                ],
            ];
            $connection = $installer->getConnection();
            foreach ($abandonedTableColumns as $name => $definition) {
                $connection->addColumn($abandonedTable, $name, $definition);
            }
            $installer->endSetup();
        }        
    }
}
