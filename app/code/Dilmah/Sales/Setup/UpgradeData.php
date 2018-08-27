<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Ns
 * @package     Dilmah_Sales
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Sales\Setup;

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

        // @codingStandardsIgnoreStart
        if (version_compare($dbVersion, '1.0.1', '<')) {
            $cmsBlocks = [
                [
                    'title' => 'Order Email Text',
                    'identifier' => 'order_email_text',
                    'content' => '<p>We are now processing your order. As we are a family company committed to freshness
                                and quality, we begin producing your teas only once you place an order. Therefore, we 
                                need upto 1 week (7 days) to bring you your teas, fresh and straight from our tea 
                                gardens to your cup. Once we dispatch your order, we will let you know via email along 
                                with all details regarding your package. </p>',
                    'is_active' => 1,
                    'stores' => 0,
                ],
                [
                    'title' => 'Order Email Text - AU',
                    'identifier' => 'order_email_text',
                    'content' => '<p>We are now processing your order. As we are a family company committed to freshness
                                and quality, we begin producing your teas only once you place an order. Therefore, we 
                                need upto 2 weeks (14 days) to bring you your teas, fresh and straight from our tea 
                                gardens to your cup. Once we dispatch your order, we will let you know via email along 
                                with all details regarding your package. </p>',
                    'is_active' => 1,
                    'stores' => 6,
                ]
            ];
            /** @var \Magento\Cms\Model\Block $block */
            $block = $this->blockFactory->create();
            foreach ($cmsBlocks as $data) {
                $block->setData($data)->save();
            }
        }

        // @codingStandardsIgnoreEnd
    }
}
