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

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Cms\Model\BlockFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

/**
 * Class UpgradeData.
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Magento\Cms\Model\BlockFactory
     */
    protected $blockFactory;

    /**
     * UpgradeData constructor.
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
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $dbVersion = $context->getVersion();

        if (version_compare($dbVersion, '1.0.1', '<')) {
            $cmsBlocks = [
                [
                    'title' => 'Checkout Top Links',
                    'identifier' => 'checkout_top_links',
                    'content' => '
                    <div class="checkout-top-lnks">
                        <span class="phone">+94 114 822000</span>
                        <a href="#">Contact Us</a>
                        <a href="#">FAQs</a>
                    </div>
                    ',
                    'is_active' => 1,
                    'stores' => 0,
                ]
            ];
            /** @var \Magento\Cms\Model\Block $block */
            $block = $this->blockFactory->create();
            foreach ($cmsBlocks as $data) {
                $block->setData($data)->save();
            }
        }

        if (version_compare($dbVersion, '1.0.2', '<')) {
            $cmsBlocks = [
                [
                    'title' => 'Shopping Cart Information',
                    'identifier' => 'shopping_cart_information',
                    'content' => '<div class="shopping-cart-info-wrapper">
                        <div class="payment">
                            <span>Secure Payment</span>
                            <span class="icon-paypal"></span>
                            <span class="icon-visa"></span>
                            <span class="icon-mastercard"></span>
                            <span class="icon-amex"></span>
                        </div>
                        <div class="delivery">
                            <span>Free Delivery for All Orders</span>
                        </div>
                        <div class="time">
                            <span>All Goods Delivered within 2 weeks</span>
                        </div>
                        <div class="help">
                            <span>Need Help? Call Us Now<span class="telephone">+94 114 822000</span> </span>
                        </div>
                    </div>',
                    'is_active' => 1,
                    'stores' => 0,
                ]
            ];
            /** @var \Magento\Cms\Model\Block $block */
            $block = $this->blockFactory->create();
            foreach ($cmsBlocks as $data) {
                $block->setData($data)->save();
            }
        }

        if (version_compare($dbVersion, '1.0.3', '<')) {
            $cmsBlocks = [
                [
                    'title' => 'Checkout Confirmation Link Order',
                    'identifier' => 'checkout_confirmation_link_order',
                    'content' => '<div class="checkout-confirmation-link-order">
                        <div class="title">
                            <span>Linking your order to your account allows you to</span>
                        </div>
                        <div class="rewards-points">
                            <span>Earn rewards points for every purchase!</span>
                        </div>
                        <div class="earn">
                            <span>Review our products and earn 50 points per review!</span>
                        </div>
                        <div class="newsletter">
                            <span>Sign up for our newsletter and earn 10 points!</span>
                        </div>
                        <div class="store-credits">
                            <span>Convert your rewards points into store credits and get discounts!</span>
                        </div>
                    </div>',
                    'is_active' => 1,
                    'stores' => 0,
                ],
                [
                    'title' => 'Checkout Confirmation Logged User',
                    'identifier' => 'checkout_confirmation_logged_user',
                    'content' => '<div class="checkout-confirmation-logged-user">
                        <div class="title">
                            <span>Creating an account allows you to</span>
                        </div>
                        <div class="rewards-points">
                            <span>Earn rewards points with every purchase!</span>
                        </div>
                        <div class="earn">
                            <span>Review our products and earn 50 points per review!</span>
                        </div>
                        <div class="newsletter">
                            <span>Sign up for our newsletter and earn 10 points!</span>
                        </div>
                        <div class="store-credits">
                            <span>Convert your rewards points into store credits and get discounts!</span>
                        </div>
                    </div>',
                    'is_active' => 1,
                    'stores' => 0,
                ],
                [
                    'title' => 'Checkout Confirmation Create Account',
                    'identifier' => 'checkout_confirmation_create_account',
                    'content' => '<div class="checkout-confirmation-create-account">
                        <div class="title">
                            <span>Creating an account allows you to</span>
                        </div>
                        <div class="track">
                            <span>Track your orders online</span>
                        </div>
                        <div class="checkout">
                            <span>Checkout faster next time</span>
                        </div>
                        <div class="offers">
                            <span>Be the first to hear about our exclusive offers</span>
                        </div>
                        <div class="store-credits">
                            <span>Earn rewards points and get store credit by converting your points</span>
                        </div>
                    </div>',
                    'is_active' => 1,
                    'stores' => 0,
                ]
            ];
            /** @var \Magento\Cms\Model\Block $block */
            $block = $this->blockFactory->create();
            foreach ($cmsBlocks as $data) {
                $block->setData($data)->save();
            }
        }
    }
}
