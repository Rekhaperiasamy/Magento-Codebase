<?php

namespace Orange\Promo\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;


class UpgradeSchema implements UpgradeSchemaInterface {

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            $tableName = $setup->getTable('promodescription_promotiondescription');
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $connection = $setup->getConnection();
                $connection->addColumn(
                        $tableName, 'lob_product', ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,  'comment' => 'Lob Product Group']
                );
            }
        }
      
        
        $setup->endSetup();
    }

}
