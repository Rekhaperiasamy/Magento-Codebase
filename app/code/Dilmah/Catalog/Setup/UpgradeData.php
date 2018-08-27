<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Ns
 * @package     Dilmah_Catalog
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Catalog\Setup;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Cms\Model\BlockFactory;

/**
 * Class UpgradeData.
 */
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

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
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
                'tea_type',
                [
                    'type' => 'int',
                    'label' => 'Tea Types',
                    'input' => 'select',
                    'sort_order' => 1,
                    'required' => false,
                    'user_defined' => true,
                    'searchable' => true,
                    'position' => 1,
                    'filterable' => true,
                    'filterable_in_search' => true,
                    'comparable' => false,
                    'used_in_product_listing' => true,
                    'global' => Attribute::SCOPE_WEBSITE,
                    'option' => [
                        'values' => [
                            'Black Tea',
                            'Flavoured Black Tea',
                            'Green Tea',
                            'Flavoured Green Tea',
                            'Earl Grey',
                            'Chai & Herbal Infusion',
                            'Organic & Decaffeinated Tea',
                            'Oolong Tea',
                        ],
                    ],
                    'apply_to' => '',
                    'group' => 'Dilmah',
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false,
                ]
            );
            $eavSetup->addAttribute(
                Product::ENTITY,
                'tea_format',
                [
                    'type' => 'int',
                    'label' => 'Tea Formats',
                    'input' => 'select',
                    'sort_order' => 2,
                    'required' => false,
                    'user_defined' => true,
                    'searchable' => true,
                    'position' => 2,
                    'filterable' => true,
                    'filterable_in_search' => true,
                    'comparable' => false,
                    'used_in_product_listing' => true,
                    'global' => Attribute::SCOPE_WEBSITE,
                    'option' => [
                        'values' => [
                            'Tea cup bags',
                            'Loose Leaf Tea',
                            'Tagless Tea Bags',
                            'Individually Wrapped Tea Bags',
                            'Luxury Leaf Tea Bags',
                        ],
                    ],
                    'apply_to' => '',
                    'group' => 'Dilmah',
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false,
                ]
            );
            $eavSetup->addAttribute(
                Product::ENTITY,
                'tea_range',
                [
                    'type' => 'int',
                    'label' => 'Tea Ranges',
                    'input' => 'select',
                    'sort_order' => 3,
                    'required' => false,
                    'user_defined' => true,
                    'searchable' => true,
                    'position' => 3,
                    'filterable' => true,
                    'filterable_in_search' => true,
                    'comparable' => false,
                    'used_in_product_listing' => true,
                    'global' => Attribute::SCOPE_WEBSITE,
                    'option' => [
                        'values' => [
                            'Premium Ceylon Single Origin Tea',
                            'Exceptional Range',
                            'Single Region Selection',
                            'Watte',
                            'Fun Tea Selection',
                            'Infusion',
                            'Organic Tea Selection',
                            'Natural Green Tea Selection',
                            'Vivid Tea Selection',
                            'Silver Jubilee Gourmet',
                            'Tea Maker\'s Private Reserve',
                            'Ceylon Orange Pekoe',
                            'Gift of Tea',
                            't-Series, Designer Gourmet Teas',
                            'Decaffeinated Tea',
                            'Ceylon Silver tips',
                        ],
                    ],
                    'default' => '4',
                    'apply_to' => '',
                    'group' => 'Dilmah',
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false,
                ]
            );
            $eavSetup->addAttribute(
                Product::ENTITY,
                'tea_flavour',
                [
                    'type' => 'int',
                    'label' => 'Tea Flavours',
                    'input' => 'select',
                    'sort_order' => 4,
                    'required' => false,
                    'user_defined' => true,
                    'searchable' => true,
                    'position' => 4,
                    'filterable' => true,
                    'filterable_in_search' => true,
                    'comparable' => false,
                    'used_in_product_listing' => true,
                    'global' => Attribute::SCOPE_WEBSITE,
                    'option' => ['values' => ['Fruity', 'Spicy', 'Mint', 'Citrus', 'Sweet']],
                    'apply_to' => '',
                    'group' => 'Dilmah',
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false,
                ]
            );
            $eavSetup->addAttribute(
                Product::ENTITY,
                'tea_strength',
                [
                    'type' => 'int',
                    'label' => 'Tea Strength',
                    'input' => 'select',
                    'sort_order' => 5,
                    'required' => false,
                    'user_defined' => true,
                    'searchable' => true,
                    'position' => 5,
                    'filterable' => true,
                    'filterable_in_search' => true,
                    'comparable' => false,
                    'used_in_product_listing' => true,
                    'global' => Attribute::SCOPE_WEBSITE,
                    'option' => [
                        'values' => [
                            'Very Light',
                            'Light',
                            'Medium',
                            'Strong',
                            'Very Strong',
                        ],
                    ],
                    'apply_to' => '',
                    'group' => 'Dilmah',
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false,
                ]
            );
            $eavSetup->addAttribute(
                Product::ENTITY,
                'time_of_day',
                [
                    'type' => 'int',
                    'label' => 'Time of The Day',
                    'input' => 'multiselect',
                    'sort_order' => 6,
                    'required' => false,
                    'user_defined' => true,
                    'searchable' => true,
                    'position' => 6,
                    'filterable' => true,
                    'filterable_in_search' => true,
                    'comparable' => false,
                    'used_in_product_listing' => true,
                    'global' => Attribute::SCOPE_WEBSITE,
                    'option' => ['values' => ['Morning', 'Afternoon', 'Evening', 'Night', 'All Day']],
                    'apply_to' => '',
                    'group' => 'Dilmah',
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false,
                ]
            );
            $eavSetup->addAttribute(
                Product::ENTITY,
                'feeling',
                [
                    'type' => 'int',
                    'label' => 'I\'m Feeling',
                    'input' => 'multiselect',
                    'sort_order' => 7,
                    'required' => false,
                    'user_defined' => true,
                    'searchable' => true,
                    'position' => 7,
                    'filterable' => true,
                    'filterable_in_search' => true,
                    'comparable' => false,
                    'used_in_product_listing' => true,
                    'global' => Attribute::SCOPE_WEBSITE,
                    'option' => [
                        'values' => [
                            'Energetic - Bring it on!',
                            'Contented-Life\'s great!',
                            'Calm-Contemplating life!',
                            'Chilled out - taking it easy!',
                            'Anxious-totally stressed!',
                            'Intense - working or studying.',
                        ],
                    ],
                    'apply_to' => '',
                    'group' => 'Dilmah',
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false,
                ]
            );
            $eavSetup->addAttribute(
                Product::ENTITY,
                'caffeine_level',
                [
                    'type' => 'int',
                    'label' => 'Caffeine Level',
                    'input' => 'select',
                    'sort_order' => 8,
                    'required' => false,
                    'user_defined' => true,
                    'searchable' => true,
                    'position' => 8,
                    'filterable' => true,
                    'filterable_in_search' => true,
                    'comparable' => false,
                    'used_in_product_listing' => true,
                    'global' => Attribute::SCOPE_WEBSITE,
                    'option' => ['values' => ['High', 'Medium', 'Low']],
                    'apply_to' => '',
                    'group' => 'Dilmah',
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false,
                ]
            );
            $eavSetup->addAttribute(
                Product::ENTITY,
                'recommendation',
                [
                    'type' => 'int',
                    'label' => 'Recommendations',
                    'input' => 'multiselect',
                    'sort_order' => 9,
                    'required' => false,
                    'user_defined' => true,
                    'searchable' => true,
                    'position' => 9,
                    'filterable' => true,
                    'filterable_in_search' => true,
                    'comparable' => false,
                    'used_in_product_listing' => true,
                    'global' => Attribute::SCOPE_WEBSITE,
                    'option' => [
                        'values' => [
                            'Tea mocktails / Cocktails',
                            'Cooking with Tea',
                            'Tea pairing',
                        ],
                    ],
                    'apply_to' => '',
                    'group' => 'Dilmah',
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false,
                ]
            );
            $eavSetup->addAttribute(
                Product::ENTITY,
                'colour',
                [
                    'type' => 'int',
                    'label' => 'Colours',
                    'input' => 'select',
                    'sort_order' => 10,
                    'required' => false,
                    'user_defined' => true,
                    'searchable' => true,
                    'position' => 10,
                    'filterable' => true,
                    'filterable_in_search' => true,
                    'comparable' => false,
                    'used_in_product_listing' => true,
                    'global' => Attribute::SCOPE_GLOBAL,
                    'option' => [
                        'values' => [
                            'Green',
                            'White',
                            'Black',
                            'Red',
                            'Blue',
                            'Brown',
                            'Burgundy',
                            'Orange',
                            'Pink',
                        ],
                    ],
                    'apply_to' => '',
                    'group' => 'Dilmah',
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false,
                ]
            );
            $eavSetup->addAttribute(
                Product::ENTITY,
                'brewing_water',
                [
                    'type' => 'varchar',
                    'label' => 'Brewing Water (step 1)',
                    'input' => 'text',
                    'sort_order' => 11,
                    'required' => false,
                    'user_defined' => true,
                    'searchable' => true,
                    'filterable' => false,
                    'comparable' => false,
                    'used_in_product_listing' => true,
                    'global' => Attribute::SCOPE_STORE,
                    'apply_to' => implode(',', [Type::TYPE_SIMPLE, Type::TYPE_BUNDLE]),
                    'group' => 'Dilmah',
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false,
                ]
            );
            $eavSetup->addAttribute(
                Product::ENTITY,
                'brewing_temp',
                [
                    'type' => 'varchar',
                    'label' => 'Brewing Temperature (step 2)',
                    'input' => 'text',
                    'sort_order' => 12,
                    'required' => false,
                    'user_defined' => true,
                    'searchable' => true,
                    'filterable' => false,
                    'comparable' => false,
                    'used_in_product_listing' => true,
                    'global' => Attribute::SCOPE_STORE,
                    'apply_to' => implode(',', [Type::TYPE_SIMPLE, Type::TYPE_BUNDLE]),
                    'group' => 'Dilmah',
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false,
                ]
            );
            $eavSetup->addAttribute(
                Product::ENTITY,
                'brewing_tea',
                [
                    'type' => 'varchar',
                    'label' => 'Brewing Tea (step 3)',
                    'input' => 'text',
                    'sort_order' => 13,
                    'required' => false,
                    'user_defined' => true,
                    'searchable' => true,
                    'filterable' => false,
                    'comparable' => false,
                    'used_in_product_listing' => true,
                    'global' => Attribute::SCOPE_STORE,
                    'apply_to' => implode(',', [Type::TYPE_SIMPLE, Type::TYPE_BUNDLE]),
                    'group' => 'Dilmah',
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false,
                ]
            );
            $eavSetup->addAttribute(
                Product::ENTITY,
                'brewing_vol',
                [
                    'type' => 'varchar',
                    'label' => 'Brewing Volume (step 4)',
                    'input' => 'text',
                    'sort_order' => 14,
                    'required' => false,
                    'user_defined' => true,
                    'searchable' => true,
                    'filterable' => false,
                    'comparable' => false,
                    'used_in_product_listing' => true,
                    'global' => Attribute::SCOPE_STORE,
                    'apply_to' => implode(',', [Type::TYPE_SIMPLE, Type::TYPE_BUNDLE]),
                    'group' => 'Dilmah',
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false,
                ]
            );
            $eavSetup->addAttribute(
                Product::ENTITY,
                'brewing_duration',
                [
                    'type' => 'varchar',
                    'label' => 'Brewing Duration (step 5)',
                    'input' => 'text',
                    'sort_order' => 15,
                    'required' => false,
                    'user_defined' => true,
                    'searchable' => true,
                    'filterable' => false,
                    'comparable' => false,
                    'used_in_product_listing' => true,
                    'global' => Attribute::SCOPE_STORE,
                    'apply_to' => implode(',', [Type::TYPE_SIMPLE, Type::TYPE_BUNDLE]),
                    'group' => 'Dilmah',
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false,
                ]
            );
            $eavSetup->addAttribute(
                Product::ENTITY,
                'brewing_info',
                [
                    'type' => 'varchar',
                    'label' => 'Brewing Information',
                    'input' => 'textarea',
                    'sort_order' => 16,
                    'required' => false,
                    'user_defined' => true,
                    'searchable' => true,
                    'filterable' => false,
                    'comparable' => false,
                    'used_in_product_listing' => true,
                    'global' => Attribute::SCOPE_STORE,
                    'apply_to' => implode(',', [Type::TYPE_SIMPLE, Type::TYPE_BUNDLE]),
                    'group' => 'Dilmah',
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false,
                ]
            );
            $eavSetup->addAttribute(
                Product::ENTITY,
                'recipes',
                [
                    'type' => 'text',
                    'label' => 'Recipes',
                    'input' => 'textarea',
                    'sort_order' => 17,
                    'required' => false,
                    'user_defined' => true,
                    'searchable' => true,
                    'filterable' => false,
                    'comparable' => false,
                    'used_in_product_listing' => true,
                    'wysiwyg_enabled' => true,
                    'is_html_allowed_on_front' => true,
                    'global' => Attribute::SCOPE_STORE,
                    'apply_to' => implode(',', [Type::TYPE_SIMPLE, Type::TYPE_BUNDLE, Configurable::TYPE_CODE]),
                    'group' => 'Dilmah',
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false,
                ]
            );
            $eavSetup->addAttribute(
                Product::ENTITY,
                'videos',
                [
                    'type' => 'text',
                    'label' => 'Videos',
                    'input' => 'textarea',
                    'sort_order' => 18,
                    'required' => false,
                    'user_defined' => true,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'used_in_product_listing' => true,
                    'wysiwyg_enabled' => true,
                    'is_html_allowed_on_front' => true,
                    'global' => Attribute::SCOPE_STORE,
                    'apply_to' => '',
                    'group' => 'Dilmah',
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false,
                ]
            );
            $eavSetup->addAttribute(
                Product::ENTITY,
                'is_combo',
                [
                    'type' => 'int',
                    'label' => 'Combo Product',
                    'input' => 'boolean',
                    'sort_order' => 19,
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
            $eavSetup->addAttribute(
                Product::ENTITY,
                'is_pack',
                [
                    'type' => 'int',
                    'label' => 'Pack Product',
                    'input' => 'boolean',
                    'sort_order' => 20,
                    'required' => false,
                    'user_defined' => true,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'used_in_product_listing' => true,
                    'global' => Attribute::SCOPE_WEBSITE,
                    'apply_to' => implode(',', [Type::TYPE_SIMPLE]),
                    'group' => 'Dilmah',
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false,
                ]
            );
            $eavSetup->addAttribute(
                Product::ENTITY,
                'pack_size',
                [
                    'type' => 'varchar',
                    'label' => 'Pack Size',
                    'input' => 'text',
                    'sort_order' => 21,
                    'required' => false,
                    'user_defined' => true,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'used_in_product_listing' => true,
                    'global' => Attribute::SCOPE_WEBSITE,
                    'apply_to' => implode(',', [Type::TYPE_SIMPLE, Type::TYPE_BUNDLE]),
                    'group' => 'Dilmah',
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false,
                    'frontend_class' => 'validate-digits',
                    'note' => 'Amount of the packs sold',
                ]
            );
            $eavSetup->addAttribute(
                Product::ENTITY,
                'pack_price',
                [
                    'type' => 'decimal',
                    'label' => 'Pack Price',
                    'input' => 'price',
                    'sort_order' => 22,
                    'required' => false,
                    'user_defined' => true,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'used_in_product_listing' => true,
                    'backend' => 'Magento\Catalog\Model\Product\Attribute\Backend\Price',
                    'global' => Attribute::SCOPE_WEBSITE,
                    'apply_to' => implode(',', [Type::TYPE_SIMPLE, Type::TYPE_BUNDLE]),
                    'group' => 'Dilmah',
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false,
                    'note' => 'Price of the pack (single price x pack size)',
                ]
            );
            $eavSetup->addAttribute(
                Product::ENTITY,
                'is_engravable',
                [
                    'type' => 'int',
                    'label' => 'Engravable',
                    'input' => 'boolean',
                    'sort_order' => 23,
                    'required' => false,
                    'user_defined' => true,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'used_in_product_listing' => true,
                    'global' => Attribute::SCOPE_WEBSITE,
                    'apply_to' => '',
                    'group' => 'Dilmah',
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false,
                ]
            );
            $eavSetup->addAttribute(
                Product::ENTITY,
                'related_sku',
                [
                    'type' => 'varchar',
                    'label' => 'Related SKU',
                    'input' => 'text',
                    'sort_order' => 24,
                    'required' => false,
                    'user_defined' => true,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'used_in_product_listing' => true,
                    'global' => Attribute::SCOPE_WEBSITE,
                    'apply_to' => implode(',', [Type::TYPE_SIMPLE, Type::TYPE_BUNDLE]),
                    'group' => 'Dilmah',
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false,
                    'note' => 'SKU of the upsell product for notifications',
                ]
            );
            $eavSetup->addAttribute(
                Product::ENTITY,
                'bag_qty',
                [
                    'type' => 'varchar',
                    'label' => 'Bag Quantity',
                    'input' => 'text',
                    'sort_order' => 25,
                    'required' => false,
                    'user_defined' => true,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'used_in_product_listing' => true,
                    'global' => Attribute::SCOPE_WEBSITE,
                    'apply_to' => implode(',', [Type::TYPE_SIMPLE, Type::TYPE_BUNDLE]),
                    'group' => 'Dilmah',
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false,
                    'frontend_class' => 'validate-digits',
                    'note' => 'Number of tea bags. Keep empty for loose tea',
                ]
            );
            $eavSetup->addAttribute(
                Product::ENTITY,
                'grammage',
                [
                    'type' => 'varchar',
                    'label' => 'Grammage',
                    'input' => 'text',
                    'sort_order' => 26,
                    'required' => false,
                    'user_defined' => true,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'used_in_product_listing' => true,
                    'global' => Attribute::SCOPE_WEBSITE,
                    'apply_to' => implode(',', [Type::TYPE_SIMPLE, Type::TYPE_BUNDLE]),
                    'group' => 'Dilmah',
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false,
                    'frontend_class' => 'validate-digits',
                    'note' => 'Grammage of a pack. keep empty is sold as tea bags',
                ]
            );
            $eavSetup->addAttribute(
                Product::ENTITY,
                'servings',
                [
                    'type' => 'varchar',
                    'label' => 'Servings',
                    'input' => 'text',
                    'sort_order' => 27,
                    'required' => false,
                    'user_defined' => true,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'used_in_product_listing' => true,
                    'global' => Attribute::SCOPE_WEBSITE,
                    'apply_to' => implode(',', [Type::TYPE_SIMPLE, Type::TYPE_BUNDLE]),
                    'group' => 'Dilmah',
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false,
                    'frontend_class' => 'validate-digits',
                    'note' => 'Number of servings per pack',
                ]
            );
            $eavSetup->addAttribute(
                Product::ENTITY,
                'is_promotion',
                [
                    'type' => 'int',
                    'label' => 'Promotion',
                    'input' => 'boolean',
                    'sort_order' => 28,
                    'required' => false,
                    'user_defined' => true,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'used_in_product_listing' => true,
                    'global' => Attribute::SCOPE_WEBSITE,
                    'apply_to' => '',
                    'group' => 'Dilmah',
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false,
                ]
            );
            $eavSetup->addAttribute(
                Product::ENTITY,
                'is_sale',
                [
                    'type' => 'int',
                    'label' => 'Sale',
                    'input' => 'boolean',
                    'sort_order' => 29,
                    'required' => false,
                    'user_defined' => true,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'used_in_product_listing' => true,
                    'global' => Attribute::SCOPE_WEBSITE,
                    'apply_to' => '',
                    'group' => 'Dilmah',
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false,
                ]
            );
            $eavSetup->addAttribute(
                Product::ENTITY,
                'is_new',
                [
                    'type' => 'int',
                    'label' => 'New',
                    'input' => 'boolean',
                    'sort_order' => 30,
                    'required' => false,
                    'user_defined' => true,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'used_in_product_listing' => true,
                    'global' => Attribute::SCOPE_WEBSITE,
                    'apply_to' => '',
                    'group' => 'Dilmah',
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false,
                ]
            );
        }

        if (version_compare($dbVersion, '1.0.2', '<')) {
            $installer = $setup;
            // Adding new rating attributes
            $data = [
                \Magento\Review\Model\Rating::ENTITY_PRODUCT_CODE => [
                    ['rating_code' => 'Quality of the Product', 'position' => 1],
                    ['rating_code' => 'Value for Money', 'position' => 2],
                    ['rating_code' => 'Ease of Use', 'position' => 3],
                    ['rating_code' => 'Visual Appearance', 'position' => 4],
                    ['rating_code' => 'Overall Rating', 'position' => 5],
                ],
                \Magento\Review\Model\Rating::ENTITY_REVIEW_CODE => [],
                \Magento\Review\Model\Rating::ENTITY_PRODUCT_REVIEW_CODE => [],
            ];

            foreach ($data as $entityCode => $ratings) {
                $select = $installer->getConnection()->select()->from(
                    $installer->getTable('rating_entity'),
                    'entity_id'
                )->where(
                    'entity_code = ?',
                    $entityCode
                );
                $entityId = $installer->getConnection()->fetchOne($select);

                foreach ($ratings as $bind) {
                    //Fill table rating/rating
                    $bind['entity_id'] = $entityId;
                    $installer->getConnection()->insert($installer->getTable('rating'), $bind);

                    //Fill table rating/rating_option
                    $ratingId = $installer->getConnection()->lastInsertId($installer->getTable('rating'));
                    $optionData = [];
                    for ($i = 1; $i <= 5; ++$i) {
                        $optionData[] = [
                            'rating_id' => $ratingId,
                            'code' => (string) $i,
                            'value' => $i,
                            'position' => $i,
                        ];
                    }
                    $installer->getConnection()->insertMultiple($installer->getTable('rating_option'), $optionData);
                }
            }

            // remove existing rating attributes
            $deleteData = [
                \Magento\Review\Model\Rating::ENTITY_PRODUCT_CODE => [
                    ['rating_code' => 'Quality'],
                    ['rating_code' => 'Value'],
                    ['rating_code' => 'Price'],
                ],
            ];

            foreach ($deleteData as $entityCode => $ratings) {
                foreach ($ratings as $bind) {
                    $installer->getConnection()
                        ->delete($installer->getTable('rating'), ['rating_code = ?' => $bind['rating_code']]);
                }
            }
        }

        if (version_compare($dbVersion, '1.0.3', '<')) {
            /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            $attributes = ['is_combo', 'is_pack', 'is_engravable', 'is_promotion', 'is_sale', 'is_new'];
            foreach ($attributes as $attribute) {
                $eavSetup->updateAttribute(
                    Product::ENTITY,
                    $attribute,
                    'source_model',
                    'Magento\Eav\Model\Entity\Attribute\Source\Boolean'
                );
            }
        }

        if (version_compare($dbVersion, '1.0.4', '<')) {
            $data = ['title' => 'Product Promo Infomation',
                'identifier' => 'product_promo_info',
                'content' => '
                    <ul class="promo-items">
                        <li>Free Shipping</li>
                        <li>Ethically grown Single Origin Tea</li>
                    </ul>',
                'is_active' => 1,
                'stores' => 0,
            ];
            /** @var \Magento\Cms\Model\Block $block */
            $block = $this->blockFactory->create();
            $block->setData($data)->save();
        }

        if (version_compare($dbVersion, '1.0.5', '<')) {
            /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            $attributes = ['recommendation', 'feeling', 'time_of_day'];
            foreach ($attributes as $attribute) {
                $eavSetup->updateAttribute(Product::ENTITY, $attribute, 'backend_type', 'varchar');
                $eavSetup->updateAttribute(
                    Product::ENTITY,
                    $attribute,
                    'backend_model',
                    'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend'
                );
            }
        }

        if (version_compare($dbVersion, '1.0.6', '<')) {
            /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            $attributes = ['tea_format', 'time_of_day', 'recommendation'];
            foreach ($attributes as $attribute) {
                $eavSetup->updateAttribute(Product::ENTITY, $attribute, 'is_visible_on_front', 1);
            }
        }

        if (version_compare($dbVersion, '1.0.7', '<')) {
            /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            $eavSetup->addAttribute(
                Product::ENTITY,
                'size',
                [
                    'type' => 'int',
                    'label' => 'Size',
                    'input' => 'select',
                    'sort_order' => 10,
                    'required' => false,
                    'user_defined' => true,
                    'searchable' => true,
                    'position' => 11,
                    'filterable' => true,
                    'filterable_in_search' => true,
                    'comparable' => false,
                    'used_in_product_listing' => true,
                    'visible_on_front' => true,
                    'global' => Attribute::SCOPE_GLOBAL,
                    'option' => [
                        'values' => [
                            'S',
                            'M',
                            'L',
                            'XL',
                        ],
                    ],
                    'apply_to' => implode(',', [Type::TYPE_SIMPLE, Configurable::TYPE_CODE]),
                    'group' => 'Dilmah',
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false,
                ]
            );

            $eavSetup->updateAttribute(Product::ENTITY, 'colour', 'is_visible_on_front', 1);
        }

        if (version_compare($dbVersion, '1.0.8', '<')) {
            /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            $eavSetup->addAttribute(
                Product::ENTITY,
                'material',
                [
                    'type' => 'int',
                    'label' => 'Material',
                    'input' => 'select',
                    'sort_order' => 10,
                    'required' => false,
                    'user_defined' => true,
                    'searchable' => true,
                    'position' => 11,
                    'filterable' => true,
                    'filterable_in_search' => true,
                    'comparable' => false,
                    'used_in_product_listing' => true,
                    'visible_on_front' => false,
                    'global' => Attribute::SCOPE_GLOBAL,
                    'option' => [],
                    'apply_to' => implode(',', [Type::TYPE_SIMPLE]),
                    'group' => 'Dilmah',
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false,
                ]
            );
        }

        if (version_compare($dbVersion, '1.0.9', '<')) {
            /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            $attributes = ['is_pack', 'pack_size', 'pack_price', 'is_combo'];
            foreach ($attributes as $attribute) {
                $eavSetup->updateAttribute(Product::ENTITY, $attribute, 'apply_to', implode(',', [Type::TYPE_BUNDLE]));
            }

            $attributes = ['size', 'colour'];
            foreach ($attributes as $attribute) {
                $eavSetup->updateAttribute(Product::ENTITY, $attribute, 'apply_to', implode(',', [Type::TYPE_SIMPLE]));
            }
        }

        if (version_compare($dbVersion, '1.0.10', '<')) {
            /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            $eavSetup->removeAttribute(Product::ENTITY, 'pack_price');
        }

        if (version_compare($dbVersion, '1.0.11', '<')) {
            /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            $eavSetup->updateAttribute(Product::ENTITY, 'tea_format', 'frontend_label', 'Tea Format');
        }

        if (version_compare($dbVersion, '1.0.12', '<')) {
            $data = ['title' => 'Mix & Match Instruction',
                'identifier' => 'mix_match_instruction',
                'content' => '
                    <strong> With Mix & Match, allow you to select your favourite teas in any quantity you wish to buy.
                    Here is how it work:</strong></br></br>
        1. Select your favourite teas and their quantities from the list below:</br>
        2. Ensure your total basket quantity is a multiplication of six</br>
        3. Once the total quantity is correct “Add to Cart” button will appear,</br>
        4. Click on the Add to cart button to add the teas to your basket.</br>',
                'is_active' => 1,
                'stores' => 0,
            ];
            /** @var \Magento\Cms\Model\Block $block */
            $block = $this->blockFactory->create();
            $block->setData($data)->save();
        }

        if (version_compare($dbVersion, '1.0.13', '<')) {
            /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $eavSetup->addAttribute(
                Product::ENTITY,
                'shipping_availability',
                [
                    'type' => 'int',
                    'label' => 'Shipping Availability',
                    'input' => 'select',
                    'sort_order' => 10,
                    'required' => false,
                    'user_defined' => true,
                    'searchable' => true,
                    'position' => 2,
                    'filterable' => true,
                    'filterable_in_search' => true,
                    'comparable' => false,
                    'used_in_product_listing' => true,
                    'global' => Attribute::SCOPE_WEBSITE,
                    'option' => [
                        'values' => [
                            'Immediate shipping',
                            'Ship within 3 weeks',
                        ],
                    ],
                    'apply_to' => '',
                    'group' => 'Dilmah',
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false,
                    'visible_on_front' => true,
                ]
            );
        }

        if (version_compare($dbVersion, '1.0.14', '<')) {
            /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $eavSetup->addAttribute(
                Product::ENTITY,
                'gtin',
                [
                    'type' => 'varchar',
                    'label' => 'GTIN',
                    'input' => 'text',
                    'sort_order' => 99,
                    'required' => false,
                    'user_defined' => true,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'used_in_product_listing' => false,
                    'global' => Attribute::SCOPE_STORE,
                    'group' => 'Dilmah',
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false,
                ]
            );
        }
    }
}
