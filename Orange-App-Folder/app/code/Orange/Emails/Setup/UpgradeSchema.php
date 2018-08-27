<?php

namespace Orange\Emails\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;


class UpgradeSchema implements UpgradeSchemaInterface {

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            $tableName = $setup->getTable('sales_order');
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $connection = $setup->getConnection();
                $connection->addColumn(
                        $tableName, 'routing_flag', ['type' => Table::TYPE_SMALLINT, 'nullable' => false, 'default' => 0, 'comment' => 'Routing export flag']
                );
            }
        }
            if (version_compare($context->getVersion(), '1.0.2') < 0) {
            $tableName = $setup->getTable('sales_order');
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $connection = $setup->getConnection();
                $connection->addColumn(
                        $tableName, 'routing_flag_prepaid', ['type' => Table::TYPE_SMALLINT, 'nullable' => false, 'default' => 0, 'comment' => 'Routing Prepaid export flag']
                );
            }
        }
        
        $setup->endSetup();
    }

}
