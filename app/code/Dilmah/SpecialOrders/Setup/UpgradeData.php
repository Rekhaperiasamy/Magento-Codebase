<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Netstarter
 * @package     Dilmah_SpecialOrders
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\SpecialOrders\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Cms\Model\BlockFactory;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Magento\Cms\Model\BlockFactory
     */
    protected $blockFactory;

    /**
     * UpgradeData constructor.
     *
     * @param BlockFactory $modelBlockFactory
     */
    public function __construct(
        BlockFactory $modelBlockFactory
    ) {
        $this->blockFactory = $modelBlockFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $dbVersion = $context->getVersion();

        if (version_compare($dbVersion, '1.0.1', '<')) {
            // TODO - Delete 'special_orders_right_info' static block
            /** @var \Magento\Cms\Model\Block $block */
            //$block = $this->blockFactory->create();
            //$block->setData($data)->save();
        }
    }
}
