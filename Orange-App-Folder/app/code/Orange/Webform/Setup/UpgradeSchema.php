<?php

namespace Orange\Webform\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface {

    public function Upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            $installer = $setup;

            $installer->startSetup();

            $orderTable = $installer->getTable('sales_order');

            $orderColumns = [
                'Active' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'Active',
                ],
            ];
            $connection = $installer->getConnection();
            foreach ($orderColumns as $name => $definition) {
                $connection->addColumn($orderTable, $name, $definition);
            }
            $installer->endSetup();
        }
        if (version_compare($context->getVersion(), '1.0.2') < 0) {
            $installer = $setup;

            $installer->startSetup();

            //$orderTable = $installer->getTable('webform_activerform');

            $installer->getConnection()->changeColumn(
                    $setup->getTable('webform_activerform'), 'date_of_birth', 'date_of_birth', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                'nullable' => false,
                'comment' => 'Date of Birth'
                    ]
            );
            $installer->endSetup();
        }
        if (version_compare($context->getVersion(), '1.0.3') < 0) {
            $installer = $setup;

            $installer->startSetup();

            $orderTable = $installer->getTable('sales_order');

            $orderColumns = [
                'StatusFlag' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => false,
                    'default' => 1,
                    'comment' => 'StatusFlag',
                ],
            ];
            $connection = $installer->getConnection();
            foreach ($orderColumns as $name => $definition) {
                $connection->addColumn($orderTable, $name, $definition);
            }
            $installer->endSetup();
        }
        if (version_compare($context->getVersion(), '1.0.4') < 0) {
            $installer = $setup;

            $installer->startSetup();

            $tableName = $installer->getTable('webform_mnpform');

            $connection = $installer->getConnection();
            $connection->addColumn(
                    $tableName, 'network_customer_number', ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'nullable' => false, 'comment' => 'network_customer_number']
            );
            $connection->addColumn(
                    $tableName, 'bill_in_name', ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'nullable' => false, 'comment' => 'bill_in_name']
            );
            $connection->addColumn(
                    $tableName, 'holders_name', ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'nullable' => false, 'comment' => 'holders_name']
            );
            $connection->addColumn(
                    $tableName, 'holder_name', ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'nullable' => false, 'comment' => 'holder_name']
            );

            $installer->endSetup();
        }
        if (version_compare($context->getVersion(), '1.0.5') < 0) {
            $installer = $setup;

            $installer->startSetup();

            $orderTable = $installer->getTable('webform_activerform');

            $orderColumns = [
                'ActiverFormDate' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    'nullable' => false,
                    'comment' => 'ActiverFormDate',
                ],
            ];
            $connection = $installer->getConnection();
            foreach ($orderColumns as $name => $definition) {
                $connection->addColumn($orderTable, $name, $definition);
            }
            $installer->endSetup();

            $installer->startSetup();
            $tableName = $installer->getTable('webform_mnpform');

            $connection = $installer->getConnection();
            $connection->addColumn(
                    $tableName, 'create_date', ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, 'nullable' => false, 'comment' => 'create_date']
            );
            $installer->endSetup();
        }
        if (version_compare($context->getVersion(), '1.0.6') < 0) {
            $installer = $setup;

            $installer->startSetup();

            $orderTable = $installer->getTable('webform_activerform');

            $orderColumns = [
                'create_date' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                    'nullable' => false,
                    'comment' => 'Create Date',
                ],
                'create_time' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                    'comment' => 'Create Time',
                ],
            ];
            $connection = $installer->getConnection();
            foreach ($orderColumns as $name => $definition) {
                $connection->addColumn($orderTable, $name, $definition);
            }
            $installer->endSetup();
        }
		if (version_compare($context->getVersion(), '1.0.7') < 0) {
            $installer = $setup;

            $installer->startSetup();

            $orderTable = $installer->getTable('webform_mnpform');

            $orderColumns = [
                'create_at' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                    'nullable' => false,
                    'comment' => 'Create Date',
                ],
                'create_time' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                    'comment' => 'Create Time',
                ],
            ];
            $connection = $installer->getConnection();
            foreach ($orderColumns as $name => $definition) {
                $connection->addColumn($orderTable, $name, $definition);
}
            $installer->endSetup();
        }
		/* bi report tracking flag added */
		if (version_compare($context->getVersion(), '1.0.8') < 0) {
            $installer = $setup;

            $installer->startSetup();

            $orderTable = $installer->getTable('sales_order');

            $orderColumns = [
                'bi_tracking_flag' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'BITrackingFlag',
                ],
            ];
            $connection = $installer->getConnection();
            foreach ($orderColumns as $name => $definition) {
                $connection->addColumn($orderTable, $name, $definition);
            }
            $installer->endSetup();
        }
		/* end bi report tracking flag*/
}
}
