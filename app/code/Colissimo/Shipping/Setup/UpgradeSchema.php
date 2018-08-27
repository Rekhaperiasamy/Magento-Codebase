<?php
/**
 * Colissimo Shipping Module
 *
 * @author    Magentix
 * @copyright Copyright © 2017 Magentix. All rights reserved.
 * @license   https://www.magentix.fr/en/licence.html Magentix Software Licence
 * @link      https://colissimo.magentix.fr/
 */
namespace Colissimo\Shipping\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * Class UpgradeSchema
 */
class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * @var string
     */
    private static $salesConnection = 'sales';

    /**
     * @var string
     */
    private static $checkoutConnection = 'checkout';

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var \Magento\Setup\Module\Setup $installer */
        $installer = $setup;
        $installer->startSetup();

        $salesConnection    = $installer->getConnection(self::$salesConnection);
        $checkoutConnection = $installer->getConnection(self::$checkoutConnection);

        if (version_compare($context->getVersion(), '1.0.0', '<=')) {
            /* Order address */
            $salesConnection->addColumn(
                $installer->getTable('sales_order_address'),
                'colissimo_pickup_id',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 6,
                    'nullable' => true,
                    'comment' => 'Colissimo Pickup Id'
                ]
            );
            $salesConnection->addColumn(
                $installer->getTable('sales_order_address'),
                'colissimo_product_code',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 4,
                    'nullable' => true,
                    'comment' => 'Colissimo Product Code'
                ]
            );
            $salesConnection->addColumn(
                $installer->getTable('sales_order_address'),
                'colissimo_network_code',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 6,
                    'nullable' => true,
                    'comment' => 'Colissimo Network Code'
                ]
            );

            /* Add Monaco and Dom-Tom */
            $bind = [
                ['country_id' => 'FR', 'code' => 'OM', 'default_name' => 'Outre-Mer'],
                ['country_id' => 'FR', 'code' => '98', 'default_name' => 'Monaco']
            ];
            foreach ($bind as $data) {
                $installer->getConnection()->insert(
                    $setup->getTable('directory_country_region'),
                    $data
                );
            }
        }

        if (version_compare($context->getVersion(), '1.1.0', '<=')) {
            $tableName = $installer->getTable('quote_colissimo_pickup');

            if (!$checkoutConnection->isTableExists($tableName)) {
                $table = $checkoutConnection
                    ->newTable($tableName)
                    ->addColumn(
                        'quote_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['unsigned' => true, 'nullable' => false],
                        'Quote Id'
                    )->addColumn(
                        'pickup_id',
                        Table::TYPE_TEXT,
                        10,
                        [],
                        'Pickup Id'
                    )->addColumn(
                        'network_code',
                        Table::TYPE_TEXT,
                        6,
                        [],
                        'Network Code'
                    )->addIndex(
                        $installer->getIdxName(
                            'quote_colissimo_pickup',
                            ['quote_id'],
                            AdapterInterface::INDEX_TYPE_UNIQUE
                        ),
                        ['quote_id'],
                        ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                    )->addForeignKey(
                        $installer->getFkName('quote_colissimo_pickup', 'quote_id', 'quote', 'entity_id'),
                        'quote_id',
                        $installer->getTable('quote'),
                        'entity_id',
                        Table::ACTION_CASCADE
                    )->setComment(
                        'Quote Colissimo Pickup Data'
                    );

                $checkoutConnection->createTable($table);
            }
        }

        $installer->endSetup();
    }
}