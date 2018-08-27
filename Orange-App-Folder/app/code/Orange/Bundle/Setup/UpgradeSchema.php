<?php

namespace Orange\Bundle\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface {

    public function Upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $installer = $setup;
        $installer->startSetup();

        // Get tutorial_simplenews table
        $tableName = $installer->getTable('bundle_color_swatch_mapping');
        // Check if the table already exists
        if ($installer->getConnection()->isTableExists($tableName) != true) {
            // Create tutorial_simplenews table
            $table = $installer->getConnection()
                    ->newTable($tableName)
                    ->addColumn(
                            'id', Table::TYPE_INTEGER, null, [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                            ], 'ID'
                    )
                    ->addColumn(
                            'simple_product_sku', Table::TYPE_TEXT, null, ['nullable' => false], 'Simple Product Id'
                    )
                    ->addColumn(
                            'virtual_product_sku', Table::TYPE_TEXT, null, ['nullable' => false], 'Virtual Product Id'
                    )
                    ->addColumn(
                            'handset_family', Table::TYPE_TEXT, null, ['nullable' => false], 'Handset Family'
                    )
                    ->addColumn(
                            'bundled_product_url', Table::TYPE_TEXT, null, ['nullable' => false], 'Bundled Product Url'
                    )
                    ->addColumn(
                            'other_info', Table::TYPE_TEXT, null, ['nullable' => false], 'Other Info'
                    )
                    ->addColumn(
                            'color', Table::TYPE_TEXT, null, ['nullable' => false], 'Color'
                    )
                    ->setComment('Bunlded Product Color Mapping')
                    ->setOption('type', 'InnoDB')
                    ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }

}
