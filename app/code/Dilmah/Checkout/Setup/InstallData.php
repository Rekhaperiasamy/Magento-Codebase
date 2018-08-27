<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Checkout
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Checkout\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Cms\Model\BlockFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class InstallData
 *
 * @package Dilmah\Checkout\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var \Magento\Cms\Model\BlockFactory
     */
    protected $blockFactory;

    /**
     * InstallData constructor.
     *
     * @param BlockFactory          $modelBlockFactory
     */
    public function __construct(
        BlockFactory $modelBlockFactory
    ) {
        $this->blockFactory = $modelBlockFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        // @codingStandardsIgnoreStart
        $cmsBlocks = [
            [
                'title' => 'Shopping Cart Banner',
                'identifier' => 'shopping_cart_banner',
                'content' => '
                    <div class="cart-banner col-4"><img src="{{media url="wysiwyg/cart_banner.jpg"}}" alt="Shopping Cart Banner" /></div>
                    ',
                'is_active' => 1,
                'stores' => 0,
            ]
            ,[
                'title' => 'Shopping Cart Banner Mobile',
                'identifier' => 'shopping_cart_banner_mobile',
                'content' => '
                    <div class="cart-banner col-4"><img src="{{media url="wysiwyg/cart_banner_mobile.jpg"}}" alt="Shopping Cart Banner" /></div>
                    ',
                'is_active' => 1,
                'stores' => 0,
            ]
        ];
        // @codingStandardsIgnoreEnd
        /** @var \Magento\Cms\Model\Block $block */
        $block = $this->blockFactory->create();
        foreach ($cmsBlocks as $data) {
            $block->setData($data)->save();
        }
    }
}
