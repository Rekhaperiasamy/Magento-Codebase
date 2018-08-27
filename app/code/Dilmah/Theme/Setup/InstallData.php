<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Theme
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Theme\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Cms\Model\BlockFactory;

/**
 * Class InstallData.
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
     * @param BlockFactory $modelBlockFactory
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
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $cmsBlocks = [
            [
                'title' => 'Footer Links - About Us Section',
                'identifier' => 'footer_about_links',
                'content' => '
                    <div class="about-us col-4"><h3>About Us</h3>
                    <ul>
                        <li><a class="" href="/dilmah-story/" target="_self">Dilmah Story</a></li>
                        <li><a class="" href="/dilmah-difference/" target="_self">Dilmah Difference</a></li>
                        <li><a class="" href="/testimonials/" target="_self">Testimonials</a></li>
                    </ul></div>
                    ',
                'is_active' => 1,
                'stores' => 0,
            ],
            [
                'title' => 'Footer Links - Customer Services Section',
                'identifier' => 'footer_customer_services_links',
                'content' => '
                    <div class="customer-services col-4"><h3>Customer Services</h3>
                    <ul>
                        <li><a class="" href="/shipping-n-delivery/" target="_self">Shipping & Delivery</a></li>
                        <li><a class="" href="/track-order/" target="_self">Track Order</a></li>
                        <li><a class="" href="/faq/" target="_self">FAQ</a></li>
                        <li><a class="" href="/contact-us/" target="_self">Contact Us</a></li>
                        <li><a class="" href="/login/" target="_self">Login</a></li>
                        <li><a class="" href="/join-our-community/" target="_self">Join Our Community</a></li>
                    </ul></div>
                    ',
                'is_active' => 1,
                'stores' => 0,
            ],
            [
                'title' => 'Footer Links - Shop Section',
                'identifier' => 'footer_shop_links',
                'content' => '
                    <div class="shop col-4"><h3>Shop</h3>
                    <span class="sub-link-footer">By Range</span>
                    <ul>
                        <li><a class="" href="/shipping-n-delivery/" target="_self">Ceylon Silver Tips</a></li>
                        <li><a class="" href="/track-order/" target="_self">Ceylon Orange Pekoe</a></li>
                        <li><a class="" href="/faq/" target="_self">Decafinated Tea</a></li>
                        <li><a class="" href="/contact-us/" target="_self">Fun Tea Selection</a></li>
                        <li><a class="" href="/login/" target="_self">Gift of Tea</a></li>
                    </ul>
                    <span class="view-all-footer"><a href="">View All</a></span>
                    <span class="sub-link-footer">Gifts</span>
                    <ul>
                        <li><a class="" href="/shipping-n-delivery/" target="_self">Gift of Tea</a></li>
                        <li><a class="" href="/track-order/" target="_self">Tea Vouchers</a></li>
                    </ul>
                    <ul>
                    <li><a href="#">Accessories</a></li>
                    <li><a href="#">Books</a></li>
                    <li><a href="#">Promotions</a></li>
                    <li><a href="#">Tea & Health</a></li>
                    <li><a href="#">Tea Club</a></li>
                    </ul>
                    </div>
                    ',
                'is_active' => 1,
                'stores' => 0,

            ],
            [
                'title' => 'Footer Links - Connect With Us Section',
                'identifier' => 'footer_right_connect',
                'content' => '
                    <div class="connect-with-us col-4"><h3>Connect With Us</h3>
                    <span class="short-desc-footer">Connect, follow and have a conversation with us.</span>
                    <div class="social-media-icon">
                        <a href="https://www.facebook.com/dilmah" target="_blank">
                            <span class="icon-fb"></span>
                        </a>
                        <a href="https://twitter.com/Dilmah" target="_blank">
                            <span class="icon-twitter"></span>
                        </a>
                        <a href="https://plus.google.com/+dilmahtea" target="_blank">
                            <span class="icon-gplus"></span>
                        </a>
                        <a href="https://www.pinterest.com/dilmah" target="_blank">
                            <span class="icon-pinterest"></span>
                        </a>
                    </div>
                    </div>
                    ',
                'is_active' => 1,
                'stores' => 0,
            ],
            [
                'title' => 'Footer Links - Keep In Touch Section',
                'identifier' => 'footer_right_kit',
                'content' => '
                    <div class="connect-with-us col-4"><h3>Keep In Touch</h3>
                    <span class="short-desc-footer">
                        Subscribe to get to know more about us and our exclusive offers.
                    </span>
                    <div class="newsletter-box">
                    </div>
                    </div>
                    ',
                'is_active' => 1,
                'stores' => 0,
            ],
        ];
        /** @var \Magento\Cms\Model\Block $block */
        $block = $this->blockFactory->create();
        foreach ($cmsBlocks as $data) {
            $block->setData($data)->save();
        }
    }
}
