<?php
/* app/code/Atwix/TestAttribute/Setup/InstallData.php */
 
namespace Orange\ProductAttributeRework\Setup;
 
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
 
/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;
 
    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }
 
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
 
        /**
         * Add attributes to the eav/attribute
         */
		
		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'european_article_number_1', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'European Article Number 1',
            'input' => 'text',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
		
		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'european_article_number_2', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'European Article Number 2',
            'input' => 'text',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
		
		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'european_article_number_3', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'European Article Number 3',
            'input' => 'text',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );

		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'length', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Length (mm)',
            'input' => 'text',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
		
		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'width', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Width (mm)',
            'input' => 'text',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
		
		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'thickness_height', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Thickness / Height (mm)',
            'input' => 'text',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );

		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'accessories_in_box', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Accessories in Box',
            'input' => 'text',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );

		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'screen_resolution', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Screen Resolution',
            'input' => 'text',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
		
				$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'screen_size', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Screen Size (Inch)',
            'input' => 'text',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
		
				$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'number_of_batteries', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Number of Batteries',
            'input' => 'text',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
		
				$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'battery_charge', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Battery Charge (mAh)',
            'input' => 'text',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
		
				$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'sar_dar_details', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'SAR/DAS Details',
            'input' => 'text',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
		
				$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'markerting_message', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Marketing Message',
            'input' => 'text',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
		
				$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'commercial_name', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Commercial name',
            'input' => 'text',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
		
				$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'linked_hero_tariff_b2c', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Linked Hero tariff B2C',
            'input' => 'text',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
				$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'linked_hero_tariff_soho', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Linked Hero tariff SOHO',
            'input' => 'text',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
		
				$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'sticker_b2c', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Sticker B2C',
            'input' => 'text',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
				$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'sticker_soho', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Sticker SOHO',
            'input' => 'text',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
				$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'cashback_stand_alone_b2c', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Cash Back Stand Alone B2C',
            'input' => 'text',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
				$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'cashback_stand_alone_soho', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Cash Back Stand Alone SOHO',
            'input' => 'text',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
				$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'cashback_subsidy_b2c', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Cash Back Subsidy B2C',
            'input' => 'text',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );

				$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'cashback_subsidy_soho', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Cash Back Subsidy SOHO',
            'input' => 'text',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
		
		
		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'effective_start_date', [
            'type' => 'int',
            'backend' => '',
            'frontend' => '',
            'label' => 'Effective Start Date',
            'input' => 'date',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
		
		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'effective_end_date', [
            'type' => 'int',
            'backend' => '',
            'frontend' => '',
            'label' => 'Effective End Date',
            'input' => 'date',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
		
		
		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'marketing_description', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Marketing Description',
            'input' => 'textarea',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );


		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'physical_keyboard', [
            'type' => 'int',
            'backend' => '',
            'frontend' => '',
            'label' => 'Physical Keyboard',
            'input' => 'boolean',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
		
		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'touchsreen', [
            'type' => 'int',
            'backend' => '',
            'frontend' => '',
            'label' => 'Touchscreen',
            'input' => 'boolean',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'volte', [
            'type' => 'int',
            'backend' => '',
            'frontend' => '',
            'label' => 'VoLTE',
            'input' => 'boolean',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'wifi', [
            'type' => 'int',
            'backend' => '',
            'frontend' => '',
            'label' => 'WiFi',
            'input' => 'boolean',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'bluetooth', [
            'type' => 'int',
            'backend' => '',
            'frontend' => '',
            'label' => 'Bluetooth',
            'input' => 'boolean',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'nfc', [
            'type' => 'int',
            'backend' => '',
            'frontend' => '',
            'label' => 'NFC',
            'input' => 'boolean',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'gps', [
            'type' => 'int',
            'backend' => '',
            'frontend' => '',
            'label' => 'GPS',
            'input' => 'boolean',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'usb_port', [
            'type' => 'int',
            'backend' => '',
            'frontend' => '',
            'label' => 'USB Port',
            'input' => 'boolean',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'dual_sim', [
            'type' => 'int',
            'backend' => '',
            'frontend' => '',
            'label' => 'Dual SIM',
            'input' => 'boolean',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'external_slot', [
            'type' => 'int',
            'backend' => '',
            'frontend' => '',
            'label' => 'External Slot',
            'input' => 'boolean',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'sdcard_included', [
            'type' => 'int',
            'backend' => '',
            'frontend' => '',
            'label' => 'SDCard Included',
            'input' => 'boolean',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'video_capture', [
            'type' => 'int',
            'backend' => '',
            'frontend' => '',
            'label' => 'Video Capture',
            'input' => 'boolean',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'flash', [
            'type' => 'int',
            'backend' => '',
            'frontend' => '',
            'label' => 'Flash',
            'input' => 'boolean',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'mp3_player', [
            'type' => 'int',
            'backend' => '',
            'frontend' => '',
            'label' => 'MP3 Player',
            'input' => 'boolean',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'fm_radio', [
            'type' => 'int',
            'backend' => '',
            'frontend' => '',
            'label' => 'FM Radio',
            'input' => 'boolean',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'hd_voice', [
            'type' => 'int',
            'backend' => '',
            'frontend' => '',
            'label' => 'HD Voice',
            'input' => 'boolean',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'removable_battery', [
            'type' => 'int',
            'backend' => '',
            'frontend' => '',
            'label' => 'Removeable Battery',
            'input' => 'boolean',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'wireless_charging', [
            'type' => 'int',
            'backend' => '',
            'frontend' => '',
            'label' => 'Wireless Charging',
            'input' => 'boolean',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'is_subsidy_hero_b2c', [
            'type' => 'int',
            'backend' => '',
            'frontend' => '',
            'label' => 'Is subsidy Hero_B2C',
            'input' => 'boolean',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'is_subsidy_hero_soho', [
            'type' => 'int',
            'backend' => '',
            'frontend' => '',
            'label' => 'Is subsidy Hero_SOHO',
            'input' => 'boolean',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
		
	$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'brand', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Brand',
            'input' => 'select',
            'class' => '',
            'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
			'option' => array (
								'values' => array(
									0 => 'Apple',
									1 => 'Samsung',
									2 => 'Huawei',
								)
							),
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );

		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'screen_colors', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Screen Colors',
            'input' => 'select',
            'class' => '',
            'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
			'option' => array (
								'values' => array(
									0 => '16M',
								)
							),
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );

		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'mobile_network_type', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Mobile Network Type',
            'input' => 'select',
            'class' => '',
            'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
			'option' => array (
								'values' => array(
									0 => '4G',
									1 => '3G',
									2 => '2G',
								)
							),
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );

		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'bands_2g', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Bands 2G',
            'input' => 'select',
            'class' => '',
            'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
			'option' => array (
								'values' => array(
									0 => 'Quad band (850 / 900 / 1800 / 1900)',
								)
							),
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
	$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'bands_3g', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Bands 3G',
            'input' => 'select',
            'class' => '',
            'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
			'option' => array (
								'values' => array(
									0 => 'Quad band (850 / 900 / 1900 / 2100)',
								)
							),
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );

		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'bands_4g', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Bands 4G',
            'input' => 'text',
            'class' => '',
            'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );

		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'sim_type', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'SIM Type',
            'input' => 'select',
            'class' => '',
            'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
			'option' => array (
								'values' => array(
									0 => 'Micro SIM',
									1 => 'Standard',
								)
							),
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );

		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'internal_memory', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Internal Memory',
            'input' => 'select',
            'class' => '',
            'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
			'option' => array (
								'values' => array(
									0 => '32GB',
									1 => '64GB',
									2 => '128GB',
								)
							),
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
			$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'ram', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'RAM',
            'input' => 'select',
            'class' => '',
            'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
			'option' => array (
								'values' => array(
									0 => '1GB',
									1 => '2GB',
									2 => '4GB',
								)
							),
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );

		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'processor', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Processor',
            'input' => 'text',
            'class' => '',
            'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );

		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'processor_speed', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Processor Speed',
            'input' => 'select',
            'class' => '',
            'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
			'option' => array (
								'values' => array(
									0 => '1.3GHz',
									
								)
							),
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );

		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'os_details', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'OS Details',
            'input' => 'select',
            'class' => '',
            'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
			'option' => array (
								'values' => array(
									0 => 'iOS',
									1 => 'Andriod',
									2 => 'Windows',
								)
							),
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
			$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'secondary_camera_resolution', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Secondary Camera Resolution (MP)',
            'input' => 'select',
            'class' => '',
            'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
			'option' => array (
								'values' => array(
									0 => '1.2 Mp',
									1 => '4 Mp',
									
								)
							),
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );

		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'primary_camera_resolution', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Primary Camera Resolution (MP)',
            'input' => 'select',
            'class' => '',
            'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
			'option' => array (
								'values' => array(
									0 => '8 Mp',
									1 => '12 Mp',
									2 => '16 Mp',
								)
							),
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );

		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'handset_family', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Handset family',
            'input' => 'select',
            'class' => '',
            'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
			'option' => array (
								'values' => array(
									0 => 'Apple iPhone 5c 32GB',
								)
							),
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );

		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'type', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Type',
            'input' => 'select',
            'class' => '',
            'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
			'option' => array (
								'values' => array(
									0 => 'Smartphone',
									1 => 'Tablets',
									
								)
							),
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );

		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'is_hero_handset_b2c', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Is Hero handset_B2C',
            'input' => 'select',
            'class' => '',
            'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
			'option' => array (
								'values' => array(
									0 => '1',
									1 => '2',
									
								)
							),
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
		
		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'is_hero_handset_soho', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Is Hero handset_SOHO',
            'input' => 'select',
            'class' => '',
            'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
			'option' => array (
								'values' => array(
									0 => '1',
									1 => '2',
									
								)
							),
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
                ]
        );
    }
}