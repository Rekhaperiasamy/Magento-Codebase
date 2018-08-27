<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_MixAndMatch
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2017 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\MixAndMatch\Setup;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Cms\Model\BlockFactory;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * EAV setup factory.
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var \Magento\Cms\Model\BlockFactory
     */
    protected $blockFactory;

    /**
     * UpgradeData constructor.
     *
     * @param EavSetupFactory $eavSetupFactory
     * @param BlockFactory    $modelBlockFactory
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        BlockFactory $modelBlockFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->blockFactory = $modelBlockFactory;
    }


    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $dbVersion = $context->getVersion();

        if (version_compare($dbVersion, '1.0.1', '<')) {
            /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            $eavSetup->addAttribute(
                Product::ENTITY,
                'mix_and_match_promotion',
                [
                    'type' => 'int',
                    'label' => 'Is Mix and Match Promotion',
                    'input' => 'boolean',
                    'sort_order' => 20,
                    'required' => false,
                    'user_defined' => true,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'used_in_product_listing' => true,
                    'global' => Attribute::SCOPE_WEBSITE,
                    'apply_to' => implode(',', [Type::TYPE_BUNDLE]),
                    'group' => 'Dilmah',
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false,
                ]
            );
        }
    }
}