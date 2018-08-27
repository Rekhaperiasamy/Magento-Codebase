<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Sales
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Sales\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class InstallData
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /**
         * Install enw order statuses
         */
        $data = [];
        $statuses = [
            'paid' => __('Paid'),
            'awaiting_shipment' => __('Awaiting Shipment')
        ];
        foreach ($statuses as $code => $info) {
            $data[] = ['status' => $code, 'label' => $info];
        }
        $setup->getConnection()->insertArray($setup->getTable('sales_order_status'), ['status', 'label'], $data);

        /**
         * Assign new statuses to states
         */
        $data = [
            [
                'status' => 'paid',
                'state' => 'processing',
                'is_default' => 0,
            ],
            [
                'status' => 'awaiting_shipment',
                'state' => 'processing',
                'is_default' => 0,
            ]
        ];

        $setup->getConnection()->insertArray(
            $setup->getTable('sales_order_status_state'),
            ['status', 'state', 'is_default'],
            $data
        );
    }
}
