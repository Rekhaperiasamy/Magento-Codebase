<?php

namespace Orange\Checkout\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface {

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $installer = $setup;
        $installer->startSetup();

        $installer->getConnection()->addColumn(
                $installer->getTable('quote'), 'subscription_amount', [
            'type' => 'text',
            'nullable' => false,
            'comment' => 'subscription_amount',
                ]
        );
        $installer->getConnection()->addColumn(
                $installer->getTable('quote'), 'base_subscription_amount', [
            'type' => 'text',
            'nullable' => false,
            'comment' => 'base_subscription_amount',
                ]
        );

        $installer->getConnection()->addColumn(
                $installer->getTable('sales_order'), 'subscription_amount', [
            'type' => 'text',
            'nullable' => false,
            'comment' => 'subscription_amount',
                ]
        );
        $installer->getConnection()->addColumn(
                $installer->getTable('sales_order'), 'base_subscription_amount', [
            'type' => 'text',
            'nullable' => false,
            'comment' => 'base_subscription_amount',
                ]
        );


        $setup->endSetup();
    }

}
