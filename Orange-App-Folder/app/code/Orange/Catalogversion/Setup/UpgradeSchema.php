<?php
namespace Orange\Catalogversion\Setup;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
class UpgradeSchema implements  UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup,
                            ModuleContextInterface $context){
							
		$setup->startSetup();
		
        if (version_compare($context->getVersion(), '1.0.6') < 0) {
		    $tableName = $setup->getTable('catalogversion_catalogversion');
	        // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $connection = $setup->getConnection();
				$connection->addColumn(
                    $tableName,
                    'stock',
                    ['type' => Table::TYPE_TEXT,'nullable' => false,  'afters' => 'custom_attribue_info','comment' => 'Product Stock']
									
                );
				$connection->addColumn(
                    $tableName,
                    'categories',
                    ['type' => Table::TYPE_TEXT,'nullable' => false,  'afters' => 'stock','comment' => 'Product Categories']
                );
				$connection->addColumn(
                    $tableName,
                    'visibility',
                    ['type' => Table::TYPE_TEXT,'nullable' => false, 'afters' => 'categories','comment' => 'Product Visibility']
                );
				$connection->addColumn(
                    $tableName,
                    'urlkey',
                    ['type' => Table::TYPE_TEXT,'nullable' => false,  'afters' => 'swatchimage','comment' => 'Product URLKey']
                 
                );
				$connection->addColumn(
                    $tableName,
                    'metatitle',
                    ['type' => Table::TYPE_TEXT,'nullable' => false,  'afters' => 'urlkey','comment' => 'Product MetaTitle']
                    
                );
				$connection->addColumn(
                    $tableName,
                    'metakeyword',
                    ['type' => Table::TYPE_TEXT,'nullable' => false, 'afters' => 'metatitle','comment' => 'Product MetaKeyword']
                   
                );
				$connection->addColumn(
                    $tableName,
                    'metadescription',
                    ['type' => Table::TYPE_TEXT,'nullable' => false,  'afters' => 'Product MetaDescription','comment' => 'Product MetaDescription']
                 
                ); 
				
				$connection->addColumn(
                    $tableName,
                    'description',
                    ['type' => Table::TYPE_TEXT,'nullable' => false,  'afters' => 'metadescription','comment' => 'Product description']
                 
                ); 
				$connection->addColumn(
                    $tableName,
                    'shortdescription',
                    ['type' => Table::TYPE_TEXT,'nullable' => false,  'afters' => 'description','comment' => 'Product shortdescription']
                 
                ); 
				$connection->addColumn(
                    $tableName,
                    'imageurls',
                    ['type' => Table::TYPE_TEXT,'nullable' => false, 'afters' => 'shortdescription','comment' => 'Product imageurl']
                   
                );
				$connection->addColumn(
                    $tableName,
                    'related_product_info',
                    ['type' => Table::TYPE_TEXT,'nullable' => false, 'afters' => 'imageurls','comment' => 'RelatedProductSKU']
                   
                );
				$connection->addColumn(
                    $tableName,
                    'upsell_product_info',
                    ['type' => Table::TYPE_TEXT,'nullable' => false, 'afters' => 'related_product_info','comment' => 'upsellProductSKU']
                   
                );
				$connection->addColumn(
                    $tableName,
                    'crosssell_product_info',
                    ['type' => Table::TYPE_TEXT,'nullable' => false, 'afters' => 'upsell_product_info','comment' => 'CrossProductSKU']
                   
                );
            }
        }
		if (version_compare($context->getVersion(), "2.0.0", "<")) {
		     $tableName = $setup->getTable('catalogversion_catalogversion');
             $connection = $setup->getConnection();
			  if ($setup->getConnection()->isTableExists($tableName) == true) {
				$connection->addColumn(
                    $tableName,
                    'revision_number',
                    ['type' =>  Table::TYPE_INTEGER,'nullable' => false,  'afters' => 'custom_attribue_info','comment' => 'Product revision number']
									
                );
				$connection->addColumn(
                    $tableName,
                    'store_id',
                    ['type' =>  Table::TYPE_INTEGER,'nullable' => false,  'afters' => 'stock','comment' => 'Product data store id']
									
                );
				$connection->addColumn(
                    $tableName,
                    'attribute_set_id',
                    ['type' =>  Table::TYPE_INTEGER,'nullable' => false,  'afters' => 'revision_number','comment' => 'product attribute set']
									
                );
				$connection->addColumn(
                    $tableName,
                    'created',
                    ['type' =>  Table::TYPE_TIMESTAMP,'nullable' => false,  'afters' => 'attribute_set_id','comment' => 'Revision created date']
									
                );
			}	
		}
		if (version_compare($context->getVersion(), "2.0.1", "<")) {
		   $installer = $setup;
		   $installer->startSetup();
           $table = $installer->getConnection()
                   ->newTable($installer->getTable('catalogversion_draft_schedule'))
				   ->addColumn(
								'id',
								\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
								null,
								['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
								'catalogversion_catalogversion'
				             )
                   ->addColumn(
								'product_id',
								\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
								null,
								[],
								'Product id'
				             )
					->addColumn(
								'name',
								 \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
								null,
								[],
								'Draft product name'
				             )		 
				   ->addColumn(
								'start_time',
								\Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
								null,
								[],
								'Update start time'
			       )
				   ->addColumn(
								'created',
								\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
								null,
								[],
								'Draft created date'
					);
           $installer->getConnection()->createTable($table);
           $installer->endSetup();
		}
		if (version_compare($context->getVersion(), "2.0.2", "<")) {
		      $tableName = $setup->getTable('catalogversion_draft_schedule');
              $connection = $setup->getConnection();
			  if ($setup->getConnection()->isTableExists($tableName) == true) {
			     $connection->addColumn(
                    $tableName,
                    'status',
                    ['type' =>  Table::TYPE_INTEGER,'nullable' => false,  'afters' => 'start_time','comment' => 'Product draft status']
									
                );
			  }
		}
		if (version_compare($context->getVersion(), "2.0.3", "<")) {
		   $installer = $setup;
		   $installer->startSetup();
           $table = $installer->getConnection()
                   ->newTable($installer->getTable('catalogversion_price_version'))
				   ->addColumn(
								'id',
								\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
								null,
								['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
								'catalogversion_catalogversion'
				             )
					 ->addColumn(
								'product_id',
								\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
								null,
								[],
								'Product id'
				             )		 
                   ->addColumn(
								'product_name',
								\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
								null,
								[],
								'product_name'
				             )
					->addColumn(
								'sku',
								 \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
								null,
								[],
								'sku'
				             )		 
				   ->addColumn(
								'revison_number',
								\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
								null,
								[],
								'Revision Number'
			       )
				   ->addColumn(
								'created',
								\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
								null,
								[],
								'Price revison created date'
					);
           $installer->getConnection()->createTable($table);
           $installer->endSetup();
		}
		if (version_compare($context->getVersion(), "2.0.4", "<")) {
		    
		}
		if (version_compare($context->getVersion(), "2.0.5", "<")) {
		    
		}
		if (version_compare($context->getVersion(), "2.0.6", "<")) {
		   
		}
		if (version_compare($context->getVersion(), "2.0.7", "<")) {
		     $tableName = $setup->getTable('catalogversion_price_version');
              $connection = $setup->getConnection();
			  if ($setup->getConnection()->isTableExists($tableName) == true) {
			     $connection->addColumn(
                    $tableName,
                    'price',
                    ['type' =>  Table::TYPE_DECIMAL,'scale'=>3,'precision'=>10,'nullable' => false,'afters' => 'sku','comment' => 'Product price']
									
                );
			  }
		} 
        $setup->endSetup();
    }
}