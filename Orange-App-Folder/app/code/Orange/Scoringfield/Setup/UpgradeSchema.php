<?php
namespace Orange\Scoringfield\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{

   public function Upgrade(SchemaSetupInterface $setup, ModuleContextInterface    $context)
   {
		$installer = $setup;
		$installer->startSetup();
		if (version_compare($context->getVersion(), '1.0.9') < 0) {
			

			$table = $installer->getConnection()
				->newTable($installer->getTable('scoringresponse'))
				->addColumn(
					'id',
					Table::TYPE_INTEGER,
					null,
					['identity' => true, 'nullable' => false, 'primary' => true],
					'ID'
				)
				->addColumn(
					'ws_response_content',
					Table::TYPE_TEXT,
					null,
					['nullable' => false],
					'Service Content'
				)
				  ->addColumn(
					'content_nl',
					Table::TYPE_TEXT,
					null,
					['nullable' => false],
					'Custom Content NL'
				)
				->addColumn(
					'content_fr',
					Table::TYPE_TEXT,
					null,
					['nullable' => false],
					'Custom Content FR'
				)
				->setComment('scoring ws response custom content');
			$installer->getConnection()->createTable($table);

			
		}
		if (version_compare($context->getVersion(), '1.0.10') < 0) {		
			$orderTable = $setup->getTable('sales_order');
			$connection = $setup->getConnection();
			$connection->addColumn(
				$orderTable, 'score_data', ['type' => Table::TYPE_TEXT, 'nullable' => true, 'default' => null, 'comment' => 'Scoring WS Response']
			);
			$quoteTable = $installer->getTable('quote');
			$connection->addColumn(
				$quoteTable, 'score_data', ['type' => Table::TYPE_TEXT, 'nullable' => true, 'default' => null, 'comment' => 'Scoring WS Response']
			);
		}
		$installer->endSetup();
	}
}