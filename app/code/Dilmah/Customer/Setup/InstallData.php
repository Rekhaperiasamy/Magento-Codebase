<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Customer
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Customer\Setup;

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
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $cmsBlocks = [
            [
                'title' => 'Login/Register Page Information',
                'identifier' => 'login_register_page_info',
                'content' => '
                    <h3>Not a member yet?</h3>
                    <span>When you create an account you can:</span>
                    <ul>
                        <li class="track-order">Track your orders online</li>
                        <li class="time">Checkout faster next time</li>
                        <li class="offers">Hear about our exclusive offers first!</li>
                        <li class="reward-review">Earn 50 reward points each time you review a product!</li>
                        <li class="reward-purchase">Earn reward points with every purchase</li>
                        <li class="store-credit">Convert reward points into store credits</li>
                    </ul>
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
