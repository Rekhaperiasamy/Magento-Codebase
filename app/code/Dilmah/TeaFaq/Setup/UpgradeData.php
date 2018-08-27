<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Netstarter
 * @package     Dilmah_TeaFaq
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\TeaFaq\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Cms\Model\BlockFactory;
use Magento\Store\Model\StoreManagerInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * Declare
     * @var \Magento\Cms\Model\BlockFactory
     */
    protected $_blockFactory;

    /**
     * Store Manager
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Constructor
     * @param BlockFactory $modelBlockFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        BlockFactory $modelBlockFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->_blockFactory = $modelBlockFactory;
        $this->_storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();
        $dbVersion = $context->getVersion();
        $basuUrl = $this->_storeManager->getStore()->getBaseUrl();
        if (version_compare($dbVersion, '100.0.1', '<')) {
            $cmsBlocks = [
                [
                    'title' => 'Dilmah Tea FAQ Sidebar Menu Links Block',
                    'identifier' => 'dt_faq_sidebar_links_block',
                    'content' => '
                            <ul class="links">
                                <li class="nav item">
                                    <a href="' . $basuUrl . 'stores">Stores</a>
                                </li>
                                <li class="nav item">
                                    <a href="' . $basuUrl . 'faq">FAQs</a>
                                </li>
                                <li class="nav item">
                                    <a href="' . $basuUrl . 'contact">Contact Us</a>
                                </li>
                                <li class="nav item">
                                    <a href="' . $basuUrl . 'shipping">Shipping</a>
                                </li>
                                <li class="nav item">
                                    <a href="' . $basuUrl . 'returns">Returns</a>
                                </li>
                            </ul>',
                    'is_active' => 1,
                    'stores' => 0,
                ],
                [
                    'title' => 'Dilmah Tea FAQ Sidebar Content Block',
                    'identifier' => 'dt_faq_sidebar_content_block',
                    'content' => '
                            <p>Need help with your order?</p>
                            <p><strong>Phone</strong> 1300003672</p>
                            <p><strong>Email</strong> faq@netstarter.com.au</p>',
                    'is_active' => 1,
                    'stores' => 0,
                ]
            ];
            /** @var \Magento\Cms\Model\Block $block */
            $block = $this->_blockFactory->create();
            foreach ($cmsBlocks as $data) {
                $block->setData($data)->save();
            }
        }
        $setup->endSetup();
    }
}
