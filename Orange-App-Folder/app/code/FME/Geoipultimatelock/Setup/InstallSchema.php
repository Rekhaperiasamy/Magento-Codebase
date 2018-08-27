<?php

/*
 * This file will declare and create your custom table
 */

namespace FME\Geoipultimatelock\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        // Get Geoipultimatelock table
        $tableName = $installer->getTable('fme_geoipultimatelock');
        // Check if the table already exists
        if ($installer->getConnection()->isTableExists($tableName) != true) {
            // Create Geoipultimatelock table
            $table = $installer->getConnection()
                    ->newTable($tableName)
                    ->addColumn(
                        'geoipultimatelock_id', Table::TYPE_INTEGER, null, array(
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                        ), 'ID'
                    )
                    ->addColumn(
                        'title', Table::TYPE_TEXT, null, array(
                        'nullable' => false, 'default' => ''
                        ), 'Title'
                    )
                    ->addColumn(
                        'priority', Table::TYPE_SMALLINT, null, array(
                        'nullable' => false,
                        'default' => 0,
                        ), 'priority'
                    )
                    ->addColumn(
                        'exception_ips', Table::TYPE_TEXT, null, array(
                        'nullable' => false, 'default' => ''
                        ), 'IPs, given exception'
                    )
                    ->addColumn(
                        'cms_page_ids', Table::TYPE_TEXT, null, array(
                        'nullable' => false,
                        'default' => ''
                        ), 'CMS Page Ids'
                    )
                    ->addColumn('conditions_serialized', Table::TYPE_TEXT, '2M', array(), 'Rule conditions')
                    ->addColumn(
                        'error_url_redirect', Table::TYPE_TEXT, null, array(
                        'nullable' => false, 'default' => ''
                        ), 'Error URL Redirect'
                    )
                    ->addColumn('error_msg', Table::TYPE_TEXT, '2M', array(), 'Error Message')
                    ->addColumn('countries_list', Table::TYPE_TEXT, '2M', array(), 'Countries list')
                    ->addColumn(
                        'creation_time',
                        Table::TYPE_TIMESTAMP,
                        null,
                        array('nullable' => false, 'default' => Table::TIMESTAMP_INIT),
                        'Item Creation Time'
                    )->addColumn(
                        'update_time',
                        Table::TYPE_TIMESTAMP,
                        null,
                        array('nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE),
                        'Item Modification Time'
                    )
                    ->addColumn(
                        'is_active', Table::TYPE_SMALLINT, null, array(
                        'nullable' => false, 'default' => '0'
                        ), 'Status'
                    )
                    ->setComment('main Table')
                    ->setOption('type', 'InnoDB')
                    ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($table);
        }
        
        $tableStore = $installer->getTable('fme_geoipultimatelock_store');
        // Check if the table already exists
        if ($installer->getConnection()->isTableExists($tableStore) != true) {
            /**
             * Create table 'fme_geoipultimatelock_store'
             */
            $table = $installer->getConnection()
            ->newTable($tableStore)
            ->addColumn(
                'geoipultimatelock_id', Table::TYPE_INTEGER, null, array(
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                ), 'ID'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                array('unsigned' => true, 'nullable' => false, 'primary' => true),
                'Store ID'
            )->addIndex(
                $installer->getIdxName('fme_geoipultimatelock_store', array('store_id')),
                array('store_id')
            )->addForeignKey(
                $installer->getFkName('fme_geoipultimatelock_store', 'geoipultimatelock_id', 'fme_geoipultimatelock', 'geoipultimatelock_id'),
                'geoipultimatelock_id',
                $installer->getTable('fme_geoipultimatelock'),
                'geoipultimatelock_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName('fme_geoipultimatelock_store', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment(
                'FME Geoipultimatelock To Store Linkage Table'
            );

            $installer->getConnection()->createTable($table);
        }
        
        $tableNameRestrict = $installer->getTable('fme_geoipultimatelock_restrict');
        // Check if the table already exists
        if ($installer->getConnection()->isTableExists($tableNameRestrict) != true) {
            // Create Geoipultimatelock table
            $table = $installer->getConnection()
                    ->newTable($tableNameRestrict)
                    ->addColumn(
                        'blocked_id', Table::TYPE_INTEGER, null, array(
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                        ), 'ID'
                    )
                    ->addColumn(
                        'blocked_ip', Table::TYPE_TEXT, null, array(
                        'nullable' => false, 'default' => ''
                        ), 'Blocked IP'
                    )
                    
                    ->addColumn(
                        'remote_addr', Table::TYPE_TEXT, null, array(
                        'nullable' => false, 'default' => ''
                        ), 'Remote Address'
                    )
                    
                    ->addColumn(
                        'creation_time',
                        Table::TYPE_TIMESTAMP,
                        null,
                        array('nullable' => false, 'default' => Table::TIMESTAMP_INIT),
                        'Item Creation Time'
                    )->addColumn(
                        'update_time',
                        Table::TYPE_TIMESTAMP,
                        null,
                        array('nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE),
                        'Item Modification Time'
                    )
                    ->addColumn(
                        'is_active', Table::TYPE_SMALLINT, null, array(
                        'nullable' => false, 'default' => '0'
                        ), 'Status'
                    )
                    ->setComment('restrict Table')
                    ->setOption('type', 'InnoDB')
                    ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($table);
        }

        $geoipCl = $installer->getTable('geoip_cl');

        if (!$installer->getConnection()->isTableExists($geoipCl)) {
            $tableGeoipCl = $installer->getConnection()
                    ->newTable($geoipCl)
                    ->addColumn(
                        'ci', Table::TYPE_INTEGER, null, array(
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                        ), 'ID'
                    )
                    ->addColumn(
                        'cc', Table::TYPE_TEXT, null, array(
                        'nullable' => false,
                        ), 'country code'
                    )
                    ->addColumn(
                        'cn', Table::TYPE_TEXT, null, array(
                        'nullable' => false,
                        ), 'country name'
                    );
            $installer->getConnection()->createTable($tableGeoipCl);
        }

        $geoipIp = $installer->getTable('geoip_ip');

        if (!$installer->getConnection()->isTableExists($geoipIp)) {
            $tableGeoipIp = $installer->getConnection()
                    ->newTable($geoipIp)
                    ->addColumn(
                        'start', Table::TYPE_INTEGER, null, array(
                        'nullable' => false,
                        ), 'start'
                    )
                    ->addColumn(
                        'end', Table::TYPE_INTEGER, null, array(
                        'nullable' => false,
                        ), 'end'
                    )
                    ->addColumn(
                        'ci', Table::TYPE_INTEGER, null, array(
                        'nullable' => false,
                        ), 'ci '
                    );
            $installer->getConnection()->createTable($tableGeoipIp);
        }

        $geoipCsv = $installer->getTable('geoip_csv');

        if (!$installer->getConnection()->isTableExists($geoipCsv)) {
            $tabelGeoipCsv = $installer->getConnection()
                    ->newTable($geoipCsv)
                    ->addColumn(
                        'start_ip', Table::TYPE_TEXT, null, array(
                        'nullable' => false,
                        ), 'start IP'
                    )
                    ->addColumn(
                        'end_ip', Table::TYPE_TEXT, null, array(
                        'nullable' => false,
                        ), 'end IP'
                    )
                    ->addColumn(
                        'start', Table::TYPE_INTEGER, null, array(
                        'nullable' => false,
                        ), 'start'
                    )
                    ->addColumn(
                        'end', Table::TYPE_INTEGER, null, array(
                        'nullable' => false,
                        ), 'end'
                    )
                    ->addColumn(
                        'cc', Table::TYPE_TEXT, null, array(
                        'nullable' => false,
                        ), 'country code '
                    )
                    ->addColumn(
                        'cn', Table::TYPE_TEXT, null, array(
                        'nullable' => false,
                        ), 'country name '
                    );

            $installer->getConnection()
                    ->createTable($tabelGeoipCsv);
        }

        $installer->endSetup();
    }
}
