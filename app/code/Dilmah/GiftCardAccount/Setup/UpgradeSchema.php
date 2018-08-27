<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_GiftCardAccount
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
namespace Dilmah\GiftCardAccount\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Class UpgradeSchema
 *
 * Used to create/modify DB tables
 *
 * Fresh install processing order:
 * - InstallSchema
 * - UpgradeSchema (installed version will be equal to '' on fresh install)
 *
 * Upgrade processing order:
 * - UpgradeSchema(Runs if version in module.xml is greater than installed version)
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            $installer->getConnection()->addColumn(
                $installer->getTable('magento_giftcardaccount'),
                'recipient_email',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Giftcard recipient email address',
                    'default' => null
                ]
            );
        }
        if (version_compare($context->getVersion(), '1.0.2') < 0) {
            $installer->getConnection()->addColumn(
                $installer->getTable('magento_giftcardaccount'),
                'store_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Giftcard recipient store',
                    'default' => null
                ]
            );
        }
        $installer->endSetup();
    }
}
