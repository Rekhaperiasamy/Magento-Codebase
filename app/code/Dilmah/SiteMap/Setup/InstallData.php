<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_SiteMap
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\SiteMap\Setup;

use Magento\Cms\Model\BlockFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

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
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $cmsBlocks = [
            [
                'title' => 'Sitemap Bottom Content',
                'identifier' => 'sitemap_bottom_content',
                'content' => '
                    <div class="bottom-nav-item">
                        <div class="sub-title">Need Help?</div>
                        <ul>
                            <li><a href="#">FAQ</a></li>
                            <li><a href="#">Customer service</a></li>
                            <li><a href="#">Terms</a></li>
                            <li><a href="#">Privacy policy</a></li>
                            <li><a href="#">Payment</a></li>
                            <li><a href="#">Delivery</a></li>
                            <li><a href="#">Returns</a></li>
                        </ul>
                    </div>
                    <div class="bottom-nav-item">
                        <div class="sub-title">More About Us</div>
                        <ul>
                            <li><a href="#">Page name</a></li>
                            <li><a href="#">Egestas vulputate eros</a></li>
                            <li><a href="#">Semper at aliquet eget eross</a></li>
                        </ul>
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
