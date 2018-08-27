<?php
namespace Orange\Checkout\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
class UpgradeSchema implements UpgradeSchemaInterface
{

	public function Upgrade(SchemaSetupInterface $setup, ModuleContextInterface    $context)
  	{
		if (version_compare($context->getVersion(), '1.0.1') < 0) {
			$installer = $setup;

			$installer->startSetup();

			$quoteItemTable = $installer->getTable('quote_item');

			$quoteItemColumns = [
				'subscription_amount' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
					'nullable' => false,
					'default' => 0.0000,
					'comment' => 'Subscription Amount',
				],

			];
			$orderTable = $installer->getTable('sales_order_item');

			$orderColumns = [
				'subscription_amount' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
					'nullable' => false,
					'default' => 0.0000,
					'comment' => 'Subscription Amount',
				],

			];
			
			$quoteTable = $installer->getTable('quote');

			$quoteColumns = [
				'subscription_total' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
					'nullable' => false,
					'default' => 0.0000,
					'comment' => 'Subscription Total',
				],

			];

			$connection = $installer->getConnection();
			foreach ($quoteItemColumns as $name => $definition) {
				$connection->addColumn($quoteItemTable, $name, $definition);
			}
			foreach ($orderColumns as $name => $definition) {
				$connection->addColumn($orderTable, $name, $definition);
			}
			foreach ($quoteColumns as $name => $definition) {
				$connection->addColumn($quoteTable, $name, $definition);
			}

			$installer->endSetup();
		}
		if (version_compare($context->getVersion(), '1.0.2') < 0) {
			$installer = $setup;

			$installer->startSetup();
		
			$quoteTable = $installer->getTable('quote');

			$quoteColumns = [
				'subsidy_discount' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
					'nullable' => false,
					'default' => 0.0000,
					'comment' => 'Subsidy Discount',
				],

			];
			
			$orderTable = $installer->getTable('sales_order');

			$orderColumns = [
				'subscription_total' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
					'nullable' => false,
					'default' => 0.0000,
					'comment' => 'Subscription Total',
				],
				'subsidy_discount' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
					'nullable' => false,
					'default' => 0.0000,
					'comment' => 'Subsidy Discount',
				],

			];

			$connection = $installer->getConnection();
			foreach ($quoteColumns as $name => $definition) {
				$connection->addColumn($quoteTable, $name, $definition);
			}
			foreach ($orderColumns as $name => $definition) {
				$connection->addColumn($orderTable, $name, $definition);
			}

			$installer->endSetup();
		}
		if (version_compare($context->getVersion(), '1.0.3') < 0) {
			$installer = $setup;

			$installer->startSetup();
		
			$quoteTable = $installer->getTable('quote');

			$quoteColumns = [
				'subsidy_discount' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
					'length' => '12,4',
					'nullable' => false,
					'default' => 0.0000,
					'comment' => 'Subsidy Discount',
				],
				'subscription_total' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
					'length' => '12,4',
					'nullable' => false,
					'default' => 0.0000,
					'comment' => 'Subscription Total',
				],
				'subscription_amount' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
					'length' => '12,4',
					'nullable' => false,
					'default' => 0.0000,
					'comment' => 'Subscription Amount',
				],
				'base_subscription_amount' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
					'length' => '12,4',
					'nullable' => false,
					'default' => 0.0000,
					'comment' => 'Base Subscription Amount',
				],

			];
			
			$orderTable = $installer->getTable('sales_order');

			$orderColumns = [
				'subscription_total' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
					'length' => '12,4',
					'nullable' => false,
					'default' => 0.0000,
					'comment' => 'Subscription Total',
				],
				'subsidy_discount' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
					'length' => '12,4',
					'nullable' => false,
					'default' => 0.0000,
					'comment' => 'Subsidy Discount',
				],

			];
			
			$quoteItemTable = $installer->getTable('quote_item');

			$quoteItemColumns = [
				'subscription_amount' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
					'length' => '12,4',
					'nullable' => false,
					'default' => 0.0000,
					'comment' => 'Subscription Amount',
				],

			];
			
			$orderItemTable = $installer->getTable('sales_order_item');

			$orderItemColumns = [
				'subscription_amount' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
					'length' => '12,4',
					'nullable' => false,
					'default' => 0.0000,
					'comment' => 'Subscription Amount',
				],

			];

			$connection = $installer->getConnection();
			foreach ($quoteColumns as $name => $definition) {
				$connection->changeColumn($quoteTable, $name, $name, $definition);
			}
			foreach ($orderColumns as $name => $definition) {
				$connection->changeColumn($orderTable, $name,$name, $definition);
			}
			foreach ($quoteItemColumns as $name => $definition) {
				$connection->changeColumn($quoteItemTable, $name,$name, $definition);
			}
			foreach ($orderItemColumns as $name => $definition) {
				$connection->changeColumn($orderItemTable, $name,$name, $definition);
			}

			$installer->endSetup();
		}
		if (version_compare($context->getVersion(), '1.0.4') < 0) {
			$installer = $setup;

			$installer->startSetup();
			$quoteItemTable = $installer->getTable('quote_item');
			$orderItemTable = $installer->getTable('sales_order_item');
			$quoteItemColumns = [
				'ori_subscription_amount' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
					'length' => '12,4',
					'nullable' => false,
					'default' => 0.0000,
					'comment' => 'Original Subscription Amount',
				],

			];
			$orderItemColumns = [
				'ori_subscription_amount' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
					'length' => '12,4',
					'nullable' => false,
					'default' => 0.0000,
					'comment' => 'Original Subscription Amount',
				],

			];
			
			$connection = $installer->getConnection();
			foreach ($quoteItemColumns as $name => $definition) {			
				$connection->addColumn($quoteItemTable, $name, $definition);
			}
			foreach ($orderItemColumns as $name => $definition) {			
				$connection->addColumn($orderItemTable, $name, $definition);
			}
			
		}
		if (version_compare($context->getVersion(), '1.0.5') < 0) {
			$installer = $setup;

			$installer->startSetup();
			$orderTable = $installer->getTable('sales_order');

			$orderColumns = [
				'ori_subscription_total' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
					'length' => '12,4',
					'nullable' => false,
					'default' => 0.0000,
					'comment' => 'Original Subscription Total',
				],
			];
			
			$quoteTable = $installer->getTable('quote');
			$quoteColumns = [
				'ori_subscription_total' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
					'length' => '12,4',
					'nullable' => false,
					'default' => 0.0000,
					'comment' => 'Original Subscription Total',
				],
			];
			
			$connection = $installer->getConnection();
			foreach ($orderColumns as $name => $definition) {			
				$connection->addColumn($orderTable, $name, $definition);
			}
			foreach ($quoteColumns as $name => $definition) {			
				$connection->addColumn($quoteTable, $name, $definition);
			}
		}

		if (version_compare($context->getVersion(), '1.0.6') < 0) {
			$installer = $setup;

			$installer->startSetup();
			$quoteTable = $installer->getTable('quote');

			$quoteColumns = [
				'coupon_description' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => true,	
					'default' => null,			
					'comment' => 'Coupon Description',
				],

			];
			$orderTable = $installer->getTable('sales_order');

			$orderColumns = [
				'coupon_description' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => true,
					'default' => null,		
					'comment' => 'Coupon Description',
				],
			];

			$connection = $installer->getConnection();
			foreach ($quoteColumns as $name => $definition) {
				$connection->addColumn($quoteTable, $name, $definition);
			}
			foreach ($orderColumns as $name => $definition) {
				$connection->addColumn($orderTable, $name, $definition);
			}
			$installer->endSetup();
		}
		if (version_compare($context->getVersion(), '1.0.7') < 0) {
			$installer = $setup;

			$installer->startSetup();
			$quoteItemTable = $installer->getTable('quote_item');
			$orderItemTable = $installer->getTable('sales_order_item');
			$quoteItemColumns = [
				'eagle_type' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => true,
					'default' => null,
					'comment' => 'Eagle Type',
				],
				'propacks' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => true,
					'default' => null,
					'comment' => 'Pro Packs',
				]
			];
			$orderItemColumns = [
				'eagle_type' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => true,
					'default' => null,
					'comment' => 'Eagle Type',
				],
				'propacks' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => true,
					'default' => null,
					'comment' => 'Pro Packs',
				]

			];
			
			$connection = $installer->getConnection();
			foreach ($quoteItemColumns as $name => $definition) {			
				$connection->addColumn($quoteItemTable, $name, $definition);
			}
			foreach ($orderItemColumns as $name => $definition) {			
				$connection->addColumn($orderItemTable, $name, $definition);
			}
			
		}
		if (version_compare($context->getVersion(), '1.0.8') < 0) {
			$installer = $setup;

			$installer->startSetup();
			$quoteItemTable = $installer->getTable('quote_item');
			$orderItemTable = $installer->getTable('sales_order_item');
			$quoteItemColumns = [
				'iew_is_teneuro' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => true,
					'default' => null,
					'comment' => 'Ten Euro Subscription',
				],
				'iew_telephone' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => true,
					'default' => null,
					'comment' => 'IEW Contact',
				],
				'iew_contract' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => true,
					'default' => null,
					'comment' => 'Contract',
				],
				'iew_firstname' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => true,
					'default' => null,
					'comment' => 'IEW Firstname',
				],
				'iew_lastname' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => true,
					'default' => null,
					'comment' => 'IEW Lastname',
				],
				'iew_dob' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => true,
					'default' => null,
					'comment' => 'IEW DOB',
				]
			];
			$orderItemColumns = [
				'iew_is_teneuro' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => true,
					'default' => null,
					'comment' => 'Ten Euro Subscription',
				],
				'iew_telephone' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => true,
					'default' => null,
					'comment' => 'IEW Contact',
				],
				'iew_contract' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => true,
					'default' => null,
					'comment' => 'Contract',
				],
				'iew_firstname' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => true,
					'default' => null,
					'comment' => 'IEW Firstname',
				],
				'iew_lastname' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => true,
					'default' => null,
					'comment' => 'IEW Lastname',
				],
				'iew_dob' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => true,
					'default' => null,
					'comment' => 'IEW DOB',
				]

			];
			
			$connection = $installer->getConnection();
			foreach ($quoteItemColumns as $name => $definition) {			
				$connection->addColumn($quoteItemTable, $name, $definition);
			}
			foreach ($orderItemColumns as $name => $definition) {			
				$connection->addColumn($orderItemTable, $name, $definition);
			}
		}
		if (version_compare($context->getVersion(), '1.0.9') < 0) {
			$installer = $setup;

			$installer->startSetup();
			$quoteTable = $installer->getTable('quote');

			$quoteColumns = [
				'account_number' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => true,	
					'default' => null,			
					'comment' => 'Account number',
				],

			];
			$orderTable = $installer->getTable('sales_order');

			$orderColumns = [
				'account_number' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => true,
					'default' => null,		
					'comment' => 'Account number',
				],
			];

			$connection = $installer->getConnection();
			foreach ($quoteColumns as $name => $definition) {
				$connection->addColumn($quoteTable, $name, $definition);
			}
			foreach ($orderColumns as $name => $definition) {
				$connection->addColumn($orderTable, $name, $definition);
			}
			$installer->endSetup();
		}
		if (version_compare($context->getVersion(), '1.1.0') < 0) {
			$installer = $setup;

			$installer->startSetup();
			$quoteTable = $installer->getTable('quote');

			$quoteColumns = [
				'date_of_birth' => [
					'type' =>   \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
					'nullable' => false,	
					'comment' => 'Date of Birth',
				],

			];
			$orderTable = $installer->getTable('sales_order');

			$orderColumns = [
				'date_of_birth' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
					'nullable' => false,
					'comment' => 'Date of Birth',
				],
			];

			$connection = $installer->getConnection();
			foreach ($quoteColumns as $name => $definition) {
				$connection->addColumn($quoteTable, $name, $definition);
			}
			foreach ($orderColumns as $name => $definition) {
				$connection->addColumn($orderTable, $name, $definition);
			}
			$installer->endSetup();
		}
		
		if (version_compare($context->getVersion(), '1.2.0') < 0) {
			$installer = $setup;

			$installer->startSetup();
			$quoteTable = $installer->getTable('quote');
			
			
			$installer->getConnection()->changeColumn(
					$setup->getTable('quote'),
					'date_of_birth',
					'date_of_birth',
					[
						'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
						'nullable' => false,
						'comment' => 'Date of Birth'
					]
				);
			$installer->getConnection()->changeColumn(
					$setup->getTable('sales_order'),
					'date_of_birth',
					'date_of_birth',
					[
						'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
						'nullable' => false,
						'comment' => 'Date of Birth'
					]
				);
			$installer->endSetup();
		}
		 if (version_compare($context->getVersion(), '1.3.0') < 0) {
			$installer = $setup;
			//order import
			$installer->startSetup();
			$table = $installer->getConnection()
				->newTable($installer->getTable('importorder'))
				->addColumn(
					'entity_id',
					Table::TYPE_INTEGER,
					null,
					['identity' => true, 'nullable' => false, 'primary' => true],
					'ID'
				)
				->addColumn(
					'orderid',
					Table::TYPE_INTEGER,
					null,
					['nullable' => false],
					'Orderid'
				)
				->addIndex(
					$installer->getIdxName(
						'importorder',
						['orderid'],
						\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
					),
					['orderid'],
					['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
				)
				  ->addColumn(
					'firstname',
					Table::TYPE_TEXT,
					null,
					['nullable' => false],
					'Firstname'
				)
				  ->addColumn(
					'lastname',
					Table::TYPE_TEXT,
					null,
					['nullable' => false],
					'Lastname'
				)
				->addColumn(
					'email',
					Table::TYPE_TEXT,
					null,
					['nullable' => false],
					'Email'
				)
				->addColumn(
					'dateofbirth',
					Table::TYPE_DATETIME,
					null,
					['nullable' => false],
					'Date of Birth'
				)
				->setComment('import order');
			$installer->getConnection()->createTable($table);
			 
			$installer->endSetup();
		}
		if (version_compare($context->getVersion(), '1.3.1') < 0) {
			$installer = $setup;
			$installer->startSetup();
			
			$orderTable = $installer->getTable('sales_order');
			
			$scoreColumn = [
				'score' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => true,
					'default' => null,		
					'comment' => 'Scoring Status',
				],
			];
				
			$connection = $installer->getConnection();
			foreach ($scoreColumn as $name => $definition) {
				$connection->changeColumn($orderTable, $name,$name, $definition);
			}
			
			$orderColumns = [
				'entra_status' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					'nullable' => false,
					'default' => 0,		
					'comment' => 'Entra Mail Status',
				],
			];

			foreach ($orderColumns as $name => $definition) {
				$connection->addColumn($orderTable, $name, $definition);
			}
			$installer->endSetup();
			
		}
		
		if (version_compare($context->getVersion(), '1.4.0') < 0) {
			$installer = $setup;
			//order import
			$installer->startSetup();
			$table = $installer->getConnection()
				->newTable($installer->getTable('orange_multiple_postpaid'))
				->addColumn(
					'entity_id',
					Table::TYPE_INTEGER,
					null,
					['identity' => true, 'nullable' => false, 'primary' => true],
					'ID'
				)
				->addColumn(
					'quote_id',
					Table::TYPE_INTEGER,
					null,
					['nullable' => false],
					'quote id'
				)
				->addColumn(
					'item_id',
					Table::TYPE_INTEGER,
					null,
					['nullable' => false],
					'Item id'
				)
				->addColumn(
					'qty',
					Table::TYPE_INTEGER,
					null,
					['nullable' => false],
					'qty'
				)
				->addColumn(
					'design_sim_number',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					null,
					['nullable' => false],
					'design_sim_number'
				)
				  ->addColumn(
					'design_te_existing_number',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					null,
					['nullable' => false],
					'design_te_existing_number'
				)
				  ->addColumn(
					'subscription_type',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					null,
					['nullable' => false],
					'subscription_type'
				)
				->addColumn(
					'current_operator',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					null,
					['nullable' => false],
					'current_operator'
				)
				->addColumn(
					'network_customer_number',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					null,
					['nullable' => false],
					'network_customer_number'
				)
				->addColumn(
					'simcard_number',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					null,
					['nullable' => false],
					'simcard_number'
				)
				->addColumn(
					'bill_in_name',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					null,
					['nullable' => false],
					'bill_in_name'
				)
				->addColumn(
					'holders_name',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					null,
					['nullable' => false],
					'holders_name'
				)
				->addColumn(
					'holder_firstname',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					null,
					['nullable' => false],
					'holder_firstname'
				)
				->addColumn(
					'design_te_existing_number_final_validation',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					null,
					['nullable' => false],
					'design_te_existing_number_final_validation'
				)
				->setComment('post paid Product');
			$installer->getConnection()->createTable($table);
			 
			$installer->endSetup();
		}
		
		//change the length 20 to 50
		if (version_compare($context->getVersion(), '1.5.0') < 0) {
			$installer = $setup;
			$installer->startSetup();
			$installer->getConnection()->changeColumn(
				$installer->getTable('quote_address'),
				'firstname',
				'firstname',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'length' => 50,
					'comment' => 'firstname'
				]
			);
			
			
			$installer->getConnection()->changeColumn(
				$installer->getTable('quote_address'),
				'lastname',
				'lastname',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'length' => 50,
					'comment' => 'lastname'
				]
			);
			 
			$installer->endSetup();
		}

		if (version_compare($context->getVersion(), '1.6.0') < 0) {
			$installer = $setup;

			$installer->startSetup();
			$multiplePostpaidTable = $installer->getTable('orange_multiple_postpaid');
			$multiplePostpaidColumns = [				
				'pro_packs' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => true,
					'default' => null,
					'comment' => 'Propack Subscription',
				]
			];
			$connection = $installer->getConnection();
			foreach ($multiplePostpaidColumns as $name => $definition) {
				$connection->addColumn($multiplePostpaidTable, $name, $definition);
			}
			$installer->endSetup();
		}
		//Added bank transfer in quote and order level for P3 – 39196493 – information not retained when hitting Back on Ogone page
		if (version_compare($context->getVersion(), '1.6.2') < 0) {
			$installer = $setup;

			$installer->startSetup();
			$quoteTable = $installer->getTable('quote');

			$quoteColumns = [
				'bank_transfer_type' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => true,	
					'default' => null,			
					'comment' => 'Bank Transfer Type',
				],

			];
			$orderTable = $installer->getTable('sales_order');

			$orderColumns = [
				'bank_transfer_type' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => true,
					'default' => null,		
					'comment' => 'Bank Transfer Type',
				],
			];

			$connection = $installer->getConnection();
			foreach ($quoteColumns as $name => $definition) {
				$connection->addColumn($quoteTable, $name, $definition);
			}
			foreach ($orderColumns as $name => $definition) {
				$connection->addColumn($orderTable, $name, $definition);
			}
			$installer->endSetup();
		}
	    if (version_compare($context->getVersion(), '1.6.3') < 0) {
			$installer = $setup;
			$installer->startSetup();
			
			$orderTable = $installer->getTable('sales_order');
			
			$scoreColumn = [
				'mail_status' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => true,
					'default' => null,		
					'comment' => 'Mail Status',
				],
			];
				
			$connection = $installer->getConnection();
			foreach ($scoreColumn as $name => $definition) {
				$connection->addColumn($orderTable, $name, $definition);
			}
			
			$orderColumns = [
				'uncertain_payment' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					'nullable' => false,
					'default' => 0,		
					'comment' => 'Uncertain Payment',
				],
			];

			foreach ($orderColumns as $name => $definition) {
				$connection->addColumn($orderTable, $name, $definition);
			}
			$installer->endSetup();
			
		}
	
	}
}