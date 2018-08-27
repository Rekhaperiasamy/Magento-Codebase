<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Rules
 */

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Amasty\Rules\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\Backend\Media;
use Magento\Catalog\Model\Product\Attribute\Backend\Media\ImageEntryConverter;

/**
 * Upgrade the Catalog module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $compare = version_compare($context->getVersion(), '1.1.0', '<');


        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->addAmrulesTable($setup);
        }

        $setup->endSetup();
    }

    protected function addAmrulesTable(SchemaSetupInterface $setup)
    {
        /**
         * Create table 'amasty_amrules_rule'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('amasty_amrules_rule'))
            ->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )
            ->addColumn(
                'salesrule_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Salesrule Entity Id'
            )
            ->addColumn(
                'eachm',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Each M Product'
            )
            ->addColumn(
                'priceselector',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false],
                'Price Base On'
            )
            ->addColumn(
                'promo_cats',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Additional Y cats'
            )
            ->addColumn(
                'promo_skus',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Additional Y skus'
            )
            ->addColumn(
                'nqty',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'N Qty'
            )
            ->addColumn(
                'skip_rule',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Skip Rule'
            )
            ->addColumn(
                'max_discount',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Max Discount Amount'
            )

            ->addIndex(
                $setup->getIdxName('amasty_amrules_rule', ['salesrule_id']),
                ['salesrule_id']
            )
            ->addForeignKey(
                $setup->getFkName(
                    'amasty_amrules_rule',
                    'salesrule_id',
                    'salesrule',
                    'rule_id'
                ),
                'salesrule_id',
                $setup->getTable('salesrule'),
                'rule_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('Amasty Promotions Rules Table');
        $setup->getConnection()->createTable($table);
    }



}
