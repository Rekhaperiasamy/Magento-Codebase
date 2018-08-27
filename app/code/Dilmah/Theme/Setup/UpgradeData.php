<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Netstarter
 * @package     Netstarter_Theme
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Theme\Setup;

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
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $dbVersion = $context->getVersion();

        // @codingStandardsIgnoreStart
        if (version_compare($dbVersion, '1.0.1', '<')) {
            $data = ['title' => 'Footer Links - Secure Shopping Section',
                'identifier' => 'footer_right_secure',
                'content' => '
                    <div class="connect-with-us col-4"><h3>Secure Shopping</h3>
                    <span class="short-desc-footer">We provide you with the safest, most secure shopping experience possible</span>
                    <div class="social-media-icon">
                        <span class="icon-paypal"></span>
                        <span class="icon-visa"></span>
                        <span class="icon-mastercard"></span>
                        <span class="icon-amex"></span>
                        <span class="icon-padlock"></span>
                    </div>
                    </div>
                    ',
                'is_active' => 1,
                'stores' => 0,
            ];
            /** @var \Magento\Cms\Model\Block $block */
            $block = $this->blockFactory->create();
            $block->setData($data)->save();
        }

        if (version_compare($dbVersion, '1.0.2', '<')) {
            $data = ['title' => 'Footer Bottom Links',
                'identifier' => 'bottom.links.section',
                'content' => '
                    <div class="bottom-left-links">
                        <span><a href="#">Sitemap</a></span>
                        <span><a href="#">Privacy Ploicy</a></span>
                        <span><a href="#">Terms & Conditions</a></span>
                        <span><a href="#">Security</a></span>
                    </div>
                    ',
                'is_active' => 1,
                'stores' => 0,
            ];
            /** @var \Magento\Cms\Model\Block $block */
            $block = $this->blockFactory->create();
            $block->setData($data)->save();
        }

        if (version_compare($dbVersion, '1.0.3', '<')) {
            $data = ['title' => 'Homepage Seo Content',
                'identifier' => 'home_seo_content',
                'content' => '
                    <div class="home-seo-title">
                        The Single Origin Tea 100% Pure Ceylon
                    </div>
                    <div class="home-seo-left-wrapper">
                        <div class="home-seo-left">
                            <span class="seo-title">Dilmah is unique; a brand that is founded on a passionate commitment to
                            quality and authenticity in tea, it is also a part of a philosophy that goes beyond commerce
                            in seeing business as a matter of human service.</span>
                            <span class="seo-content">Tea is Nature’s gift to mankind. A beverage that heals, protects and
                            refreshes, it is also infinite in variety, changing Subtlely with the natural alchemy of
                            sunshine, soils, wind, rain and temperature. That beautiful variety in tea is as much a
                            challenge as a deliciously indulgent reward for whilst nature gives us a tea to suit every
                            mood, and desire, she demands expertise in understanding and selecting the finest. That
                            expertise can only come from passionate commitment to tea.</span>
                        </div>
                        <div class="home-seo-right">
                        </div>
                    </div>

                    ',
                'is_active' => 1,
                'stores' => 0,
            ];
            /** @var \Magento\Cms\Model\Block $block */
            $block = $this->blockFactory->create();
            $block->setData($data)->save();
        }

        if (version_compare($dbVersion, '1.0.4', '<')) {
            $data = ['title' => 'Homepage Spotlight',
                'identifier' => 'home_spotlights',
                'content' => "
                    <div class='spotlight-inner'>
                        <div class='spotlight-title'>
                            <span>Ceylon's finest tea, picked, packed and shipped direct from origin</span>
                        </div>
                        <div class='spotlight-items'>
                            <div class='item-tea-ranges'>
                                <span class='tea-ranges'></span>
                                <button class='spotlight-button'>Shop Now</button>
                            </div>
                            <div class='item-gifts'>
                                <span class='tea-gifts'></span>
                                <button class='spotlight-button'>Shop Now</button>
                            </div>
                            <div class='item-tea-club'>
                                <span class='tea-club'></span>
                                <button class='spotlight-button'>Shop Now</button>
                            </div>
                        </div>
                    </div>
                    ",
                'is_active' => 1,
                'stores' => 0,
            ];
            /** @var \Magento\Cms\Model\Block $block */
            $block = $this->blockFactory->create();
            $block->setData($data)->save();
        }

        if (version_compare($dbVersion, '1.0.5', '<')) {
            $cmsBlocks = [
                [
                    'title' => 'Header Customer Services Icon',
                    'identifier' => 'header_customer_services_icon',
                    'content' => '<li>
                            <a href="{{store url="customer-services"}}" class="customer-service"></a>
                        </li>',
                    'is_active' => 1,
                    'stores' => 0,
                ],
                [
                    'title' => 'Top Navigating Custom Links',
                    'identifier' => 'main_nav_custom_links',
                    'content' => '<li class="level0 nav-6 level-top ui-menu-item" role="presentation">
                            <a href="{{store url="tea-club"}}" class="level-top ui-corner-all" tabindex="-1" role="menuitem">
                                <span>Tea Club</span>
                            </a>
                        </li>
                        <li class="level0 nav-7 last level-top ui-menu-item" role="presentation">
                            <a href="{{store url="tea-faq"}}" class="level-top ui-corner-all" tabindex="-1" role="menuitem">
                                <span>Tea FAQ</span>
                            </a>
                        </li>',
                    'is_active' => 1,
                    'stores' => 0,
                ],
                [
                    'title' => 'Main Navigating Tea Ranges Menu Banner',
                    'identifier' => 'main_nav_tea_ranges_banner',
                    'content' => 'tea ranges menu banner image goes here',
                    'is_active' => 1,
                    'stores' => 0,
                ],
                [
                    'title' => 'Main Navigating Tea Ranges Custom Links',
                    'identifier' => 'main_nav_tea_ranges_custom_links',
                    'content' => '<ul>
                            <li class="tea-ranges-sale">
                                {{widget type="Magento\Catalog\Block\Category\Widget\Link" template="category/widget/link/link_inline.phtml" id_path="category/38"}}
                            </li>
                            <li class="tea-ranges-new">
                                {{widget type="Magento\Catalog\Block\Category\Widget\Link" template="category/widget/link/link_inline.phtml" id_path="category/39"}}
                            </li>
                        </ul>',
                    'is_active' => 1,
                    'stores' => 0,
                ],
                [
                    'title' => 'Header Tea By Types',
                    'identifier' => 'header_tea_by_types',
                    'content' => '<ul class="tea-types">
                            <li class="black">
                                {{widget type="Magento\Catalog\Block\Category\Widget\Link" anchor_text="Black" template="category/widget/link/link_inline.phtml" id_path="category/29"}}
                            </li>
                            <li class="flavours">
                                {{widget type="Magento\Catalog\Block\Category\Widget\Link" anchor_text="Flavours" template="category/widget/link/link_inline.phtml" id_path="category/30"}}
                            </li>
                            <li class="chai">
                                {{widget type="Magento\Catalog\Block\Category\Widget\Link" anchor_text="Chai" template="category/widget/link/link_inline.phtml" id_path="category/31"}}
                            </li>
                            <li class="herbal">
                                {{widget type="Magento\Catalog\Block\Category\Widget\Link" anchor_text="Herbal" template="category/widget/link/link_inline.phtml" id_path="category/32"}}
                            </li>
                            <li class="oolong">
                                {{widget type="Magento\Catalog\Block\Category\Widget\Link" anchor_text="Oolong" template="category/widget/link/link_inline.phtml" id_path="category/33"}}
                            </li>
                            <li class="white">
                                {{widget type="Magento\Catalog\Block\Category\Widget\Link" anchor_text="White" template="category/widget/link/link_inline.phtml" id_path="category/34"}}
                            </li>
                            <li class="green">
                                {{widget type="Magento\Catalog\Block\Category\Widget\Link" anchor_text="Green" template="category/widget/link/link_inline.phtml" id_path="category/35"}}
                            </li>
                            <li class="decaf">
                                {{widget type="Magento\Catalog\Block\Category\Widget\Link" anchor_text="Decaf" template="category/widget/link/link_inline.phtml" id_path="category/36"}}
                            </li>
                            <li class="organic">
                                {{widget type="Magento\Catalog\Block\Category\Widget\Link" anchor_text="Organic" template="category/widget/link/link_inline.phtml" id_path="category/37"}}
                            </li>
                        </ul>',
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

        if (version_compare($dbVersion, '1.0.7', '<')) {
            $data = [
                'title' => 'Header Shipping Text',
                'identifier' => 'header_shipping_text',
                'content' => '
                        <li><a>Free Shipping Worldwide</a></li>
                        ',
                'is_active' => 1,
                'stores' => 1,
            ];
            /** @var \Magento\Cms\Model\Block $block */
            $block = $this->blockFactory->create();
            $block->load($data['identifier']);
            if ($block->isObjectNew()) {
                $block->setData($data)->save();
            } else {
                $block->setTitle($data['title'])
                    ->setIdentifier($data['identifier'])
                    ->setContent($data['content'])
                    ->setIsActive(true)
                    ->setStores($data['stores']);

                $block->save();
            }
        }
        // @codingStandardsIgnoreEnd
    }
}
