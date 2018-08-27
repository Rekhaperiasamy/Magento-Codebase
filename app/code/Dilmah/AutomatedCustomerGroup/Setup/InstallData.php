<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_AutomatedCustomerGroup
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\AutomatedCustomerGroup\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    /**
     * {@inheritdoc}
     *
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        // insert default customer groups
        $setup->getConnection()->insertForce(
            $setup->getTable('customer_group'),
            ['customer_group_id' => 5, 'customer_group_code' => 'Tea Connoisseur', 'tax_class_id' => 3]
        );
        $setup->endSetup();
    }
}
