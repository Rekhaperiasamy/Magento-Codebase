<?php
/* app/code/Orange/ProductNewAttribute/Setup/InstallData.php */
 
namespace Orange\ProductNewAttribute\Setup;
 
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
                \Magento\Catalog\Model\Product::ENTITY, 'tealium_page_name', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Page Name',
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
            'apply_to' => '',
			'attribute_set' => array ('handset', 'Accessories', 'IEW', 'Postpaid', 'Prepaid', 'Simcard', 'Default'),
			'group' => 'Tealium Information'
                ]
        );
					
		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'tealium_product_category', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Product Category',
            'input' => 'select',
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
            'apply_to' => '',
			'attribute_set' => array ('handset', 'Accessories', 'IEW', 'Postpaid', 'Prepaid', 'Simcard', 'Default'),
			'group' => 'Tealium Information'
                ]
        );
		
		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'tealium_product_type', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Product Type',
            'input' => 'select',
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
            'apply_to' => '',
			'attribute_set' => array ('handset', 'Accessories', 'IEW', 'Postpaid', 'Prepaid', 'Simcard', 'Default'),
			'group' => 'Tealium Information'
                ]
        );
					
    }
}