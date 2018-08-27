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

use Magento\Cms\Model\Page;
use Magento\Cms\Model\PageFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Cms\Model\BlockFactory;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * Page factory
     *
     * @var \Magento\Cms\Model\PageFactory
     */
    private $pageFactory;

    /**
     * BlockFactory
     * @var \Magento\Cms\Model\BlockFactory
     */
    protected $_blockFactory;

    /**
     * Init
     *
     * @param PageFactory $pageFactory
     * @param BlockFactory $modelBlockFactory
     */
    public function __construct(
        PageFactory $pageFactory,
        BlockFactory $modelBlockFactory
    ) {
        $this->pageFactory = $pageFactory;
        $this->_blockFactory = $modelBlockFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {

        $cmsPages = [
            [
                'title' => 'Dilmah Tea FAQ Page',
                'page_layout' => '1column',
                'identifier' => 'dtfaq',
                'content_heading' => 'FAQs',
                'content' => "
                    <div class=\"dilmah-tfaq cms-content\">
                        {{widget type=\"Dilmah\\TeaFaq\\Block\\Widget\\Content\" template=\"widget/content.phtml\"}}
                    </div>",
                'is_active' => 1,
                'stores' => [0],
                'sort_order' => 0
            ]
        ];

        /**
         * Insert default and system pages
         */
        foreach ($cmsPages as $data) {
            $this->createPage()->setData($data)->save();
        }

        $cmsBlocks = [
            'title' => 'Dilmah Tea FAQ Block',
            'identifier' => 'dt_faq_content',
            'content' => '{{widget type="Dilmah\TeaFaq\Block\Widget\Content" template="widget/content.phtml"}}',
            'is_active' => 1,
            'stores' => 0,
        ];
        /** @var \Magento\Cms\Model\Block $block */
        $block = $this->_blockFactory->create();
        $block->setData($cmsBlocks)->save();


    }

    /**
     * Create page
     *
     * @return Page
     */
    public function createPage()
    {
        return $this->pageFactory->create();
    }
}
