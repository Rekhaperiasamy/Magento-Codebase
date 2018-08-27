<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_SpecialOrders
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\SpecialOrders\Setup;

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
        // @codingStandardsIgnoreStart
        $cmsBlocks = [
            [
                'title'      => 'Special Orders Top Content',
                'identifier' => 'special_orders_top_content',
                'content'    => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean euismod bibendum laoreet. Proin gravida dolor sit amet lacus accumsan et viverra justo commodo. Proin sodales pulvinar tempor. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nam fermentum, nulla luctus pharetra vulputate, felis tellus mollis orci, sed rhoncus sapien nunc eget odio.</p>',
                'is_active'  => 1,
                'stores'     => 0,
            ],
            [
                'title'      => 'Special Orders Right Information',
                'identifier' => 'special_orders_right_info',
                'content'    => '
                    <ul>
                        <li class="telephone">
                            <span>Phone: <span>+94 114 822000</span></span>
                            <span class="time">Monday - Friday 9am to 5:30pm AUS EST</span>
                        </li>
                        <li class="email">
                            <span>Email: <span><a href="mailto:contact@dilmah.com">contact@dilmah.com</a></span></span>
                        </li>
                        <li class="address">
                            <span class="head">MFJ Group Marketing and Operations</span>
                            <span>111 Negombo Road</span>
                            <span>Peliyagoda</span>
                            <span>Sri Lanka</span>
                        </li>
                    </ul>
                    ',
                'is_active'  => 1,
                'stores'     => 0,
            ],
        ];
        // @codingStandardsIgnoreEnd
        /** @var \Magento\Cms\Model\Block $block */
        $block = $this->blockFactory->create();
        foreach ($cmsBlocks as $data) {
            $block->setData($data)->save();
        }
    }
}
