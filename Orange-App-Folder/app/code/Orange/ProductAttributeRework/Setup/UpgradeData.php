<?php
namespace Orange\ProductAttributeRework\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{

   /**
     * @var CustomerSetupFactory
    */
   private $eavSetupFactory;

   public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * Upgrades data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
		/** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
 
        /**
         * Add attributes to the eav/attribute
         */
		//starts creating missing attribute
		if ($context->getVersion() && version_compare($context->getVersion(), '1.0.5') < 0 ) {
			$eavSetup->addAttribute(
				\Magento\Catalog\Model\Product::ENTITY,
				'na_code',
				[
					'type' => 'varchar',
					'backend' => '',
					'frontend' => '',
					'label' => 'NA Code',
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
					'visible_on_front' => true,
					'used_in_product_listing' => true,
					'unique' => false,
					'apply_to' => ''
				]
			);
		}
		if ($context->getVersion() && version_compare($context->getVersion(), '1.0.5') < 0 ) {
		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'available_start_date', [
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
		}
		if ($context->getVersion() && version_compare($context->getVersion(), '1.0.5') < 0 ) {
		$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'available_end_date', [
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
		}
		if ($context->getVersion() && version_compare($context->getVersion(), '1.0.5') < 0 ) {
			$eavSetup->addAttribute(
				\Magento\Catalog\Model\Product::ENTITY,
				'battery_autonomy_in_use',
				[
					'type' => 'varchar',
					'backend' => '',
					'frontend' => '',
					'label' => 'Battery Autonomy In Use',
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
					'visible_on_front' => true,
					'used_in_product_listing' => true,
					'unique' => false,
					'apply_to' => ''
				]
			);
		}  
		if ($context->getVersion() && version_compare($context->getVersion(), '1.0.5') < 0 ) {
			$eavSetup->addAttribute(
				\Magento\Catalog\Model\Product::ENTITY,
				'battery_autonomy_standby',
				[
					'type' => 'varchar',
					'backend' => '',
					'frontend' => '',
					'label' => 'Battery Autonomy Standby',
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
					'visible_on_front' => true,
					'used_in_product_listing' => true,
					'unique' => false,
					'apply_to' => ''
				]
			);
		}  
		if ($context->getVersion() && version_compare($context->getVersion(), '1.0.5') < 0 ) {
			$eavSetup->addAttribute(
				\Magento\Catalog\Model\Product::ENTITY,
				'markteing_segement',
				[
					'type' => 'varchar',
					'backend' => '',
					'frontend' => '',
					'label' => 'Marketing Segment',
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
					'visible_on_front' => true,
					'used_in_product_listing' => true,
					'unique' => false,
					'apply_to' => ''
				]
			);
		}  
		if ($context->getVersion() && version_compare($context->getVersion(), '1.0.5') < 0 ) {
			$eavSetup->addAttribute(
				\Magento\Catalog\Model\Product::ENTITY,
				'sales_channel',
				[
					'type' => 'varchar',
					'backend' => '',
					'frontend' => '',
					'label' => 'Sales Channel',
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
					'visible_on_front' => true,
					'used_in_product_listing' => true,
					'unique' => false,
					'apply_to' => ''
				]
			);
		}  
		if ($context->getVersion() && version_compare($context->getVersion(), '1.0.5') < 0 ) {
			$eavSetup->addAttribute(
				\Magento\Catalog\Model\Product::ENTITY,
				'recupel',
				[
					'type' => 'varchar',
					'backend' => '',
					'frontend' => '',
					'label' => 'Recupel',
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
					'visible_on_front' => true,
					'used_in_product_listing' => true,
					'unique' => false,
					'apply_to' => ''
				]
			);
		}  
		if ($context->getVersion() && version_compare($context->getVersion(), '1.0.5') < 0 ) {
			$eavSetup->addAttribute(
				\Magento\Catalog\Model\Product::ENTITY,
				'bebat',
				[
					'type' => 'varchar',
					'backend' => '',
					'frontend' => '',
					'label' => 'Bebat',
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
					'visible_on_front' => true,
					'used_in_product_listing' => true,
					'unique' => false,
					'apply_to' => ''
				]
			);
		} 
	if ($context->getVersion() && version_compare($context->getVersion(), '1.0.5') < 0 ) {
			$eavSetup->addAttribute(
				\Magento\Catalog\Model\Product::ENTITY,
				'auvibel',
				[
					'type' => 'varchar',
					'backend' => '',
					'frontend' => '',
					'label' => 'Auvibel',
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
					'visible_on_front' => true,
					'used_in_product_listing' => true,
					'unique' => false,
					'apply_to' => ''
				]
			);
		} 		
		if ($context->getVersion() && version_compare($context->getVersion(), '1.0.5') < 0 ) {
			$eavSetup->addAttribute(
				\Magento\Catalog\Model\Product::ENTITY,
				'standard_margin',
				[
					'type' => 'varchar',
					'backend' => '',
					'frontend' => '',
					'label' => 'Standard Margin',
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
					'visible_on_front' => true,
					'used_in_product_listing' => true,
					'unique' => false,
					'apply_to' => ''
				]
			);
		} 		
		if ($context->getVersion() && version_compare($context->getVersion(), '1.0.5') < 0 ) {
			$eavSetup->addAttribute(
				\Magento\Catalog\Model\Product::ENTITY,
				'b2b_points',
				[
					'type' => 'varchar',
					'backend' => '',
					'frontend' => '',
					'label' => 'B2b Points',
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
					'visible_on_front' => true,
					'used_in_product_listing' => true,
					'unique' => false,
					'apply_to' => ''
				]
			);
		} 	
		//ends missing attributes
		if ($context->getVersion() && version_compare($context->getVersion(), '1.0.6') < 0 ) {
			$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'marketing_description_soho', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Marketing Description SOHO',
            'input' => 'textarea',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
			'wysiwyg_enabled' => true,
            'unique' => false,
            'apply_to' => ''
                ]
			);
			$attribute=$eavSetup->getAttribute('catalog_product','marketing_description_soho');
			$attributeId = $attribute['attribute_id'];					
			$accessoriesAttributeSetId=$eavSetup->getAttributeSetId('catalog_product','Accessories');
			$handsetAttributeSetId=$eavSetup->getAttributeSetId('catalog_product','handset');
			//Get attribute group info
			$accessoriesattributeGroup=$eavSetup->getAttributeGroup('catalog_product',$accessoriesAttributeSetId,'Accessories General');
			$accessoriesattributeGroupId = $accessoriesattributeGroup['attribute_group_id'];					
			$handsetattributeGroup=$eavSetup->getAttributeGroup('catalog_product',$accessoriesAttributeSetId,'Device Others');
			$handsetattributeGroupId = $handsetattributeGroup['attribute_group_id'];
	
			//add attribute to a set
			$eavSetup->addAttributeToSet('catalog_product',$accessoriesAttributeSetId,$accessoriesattributeGroupId,$attributeId);
			$eavSetup->addAttributeToSet('catalog_product',$handsetAttributeSetId,$handsetattributeGroupId,$attributeId);
		}
		
		if ($context->getVersion() && version_compare($context->getVersion(), '1.0.7') < 0 ) {
			$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'short_description_soho', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Short Description SOHO',
            'input' => 'textarea',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
			'wysiwyg_enabled' => true,
            'unique' => false,
            'apply_to' => ''
                ]
			);
			$attribute=$eavSetup->getAttribute('catalog_product','short_description_soho');
			$attributeId = $attribute['attribute_id'];					
			$accessoriesAttributeSetId=$eavSetup->getAttributeSetId('catalog_product','Accessories');
			$handsetAttributeSetId=$eavSetup->getAttributeSetId('catalog_product','handset');
			$prepaidAttributeSetId=$eavSetup->getAttributeSetId('catalog_product','Prepaid');
			$postpaidAttributeSetId=$eavSetup->getAttributeSetId('catalog_product','Postpaid');
			$simcardAttributeSetId=$eavSetup->getAttributeSetId('catalog_product','Simcard');
			
			//Get attribute group info
			$accessoriesattributeGroup=$eavSetup->getAttributeGroup('catalog_product',$accessoriesAttributeSetId,'Content');
			$accessoriesattributeGroupId = $accessoriesattributeGroup['attribute_group_id'];					
			$handsetattributeGroup=$eavSetup->getAttributeGroup('catalog_product',$handsetAttributeSetId,'Content');
			$handsetattributeGroupId = $handsetattributeGroup['attribute_group_id'];
			$prepaidattributeGroup=$eavSetup->getAttributeGroup('catalog_product',$prepaidAttributeSetId,'Content');
			$prepaidattributeGroupId = $prepaidattributeGroup['attribute_group_id'];
			$postpaidattributeGroup=$eavSetup->getAttributeGroup('catalog_product',$postpaidAttributeSetId,'Content');
			$postpaidattributeGroupId = $postpaidattributeGroup['attribute_group_id'];
			$simcardattributeGroup=$eavSetup->getAttributeGroup('catalog_product',$simcardAttributeSetId,'Content');
			$simcardattributeGroupId = $simcardattributeGroup['attribute_group_id'];
	
			//add attribute to a set
			$eavSetup->addAttributeToSet('catalog_product',$accessoriesAttributeSetId,$accessoriesattributeGroupId,$attributeId);
			$eavSetup->addAttributeToSet('catalog_product',$handsetAttributeSetId,$handsetattributeGroupId,$attributeId);
			$eavSetup->addAttributeToSet('catalog_product',$prepaidAttributeSetId,$prepaidattributeGroupId,$attributeId);
			$eavSetup->addAttributeToSet('catalog_product',$postpaidAttributeSetId,$postpaidattributeGroupId,$attributeId);
			$eavSetup->addAttributeToSet('catalog_product',$simcardAttributeSetId,$simcardattributeGroupId,$attributeId);
		}
		if ($context->getVersion() && version_compare($context->getVersion(), '1.0.8') < 0 ) {
			$eavSetup->addAttribute(
				\Magento\Catalog\Model\Product::ENTITY, 'eagle_type', [
				'type' => 'varchar',
				'backend' => '',
				'frontend' => '',
				'label' => 'Eagle Type',
				'input' => 'select',
				'class' => '',
				'source' => '',
				'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
				'option' => array (
									'values' => array(
										0 => 'Eagle',
										1 => 'Eagle Smartphone',
										2 => 'Eagle premium',
										3 => 'Eagle premium Smartphone',
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
			$attribute=$eavSetup->getAttribute('catalog_product','eagle_type');
			$attributeId = $attribute['attribute_id'];					
			$postpaidAttributeSetId=$eavSetup->getAttributeSetId('catalog_product','Postpaid');
			$postpaidattributeGroup=$eavSetup->getAttributeGroup('catalog_product',$postpaidAttributeSetId,'Product Details');
			$postpaidattributeGroupId = $postpaidattributeGroup['attribute_group_id'];
			$eavSetup->addAttributeToSet('catalog_product',$postpaidAttributeSetId,$postpaidattributeGroupId,$attributeId);
		}
		
		if ($context->getVersion() && version_compare($context->getVersion(), '1.0.9') < 0 ) {
			$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'custom_url_virtual', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Nitendo URL',
            'input' => 'text',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
			'wysiwyg_enabled' => true,
            'unique' => false,
            'apply_to' => ''
                ]
			);
			$attribute=$eavSetup->getAttribute('catalog_product','custom_url_virtual');
			$attributeId = $attribute['attribute_id'];					
			$prepaidAttributeSetId=$eavSetup->getAttributeSetId('catalog_product','Prepaid');
			$postpaidAttributeSetId=$eavSetup->getAttributeSetId('catalog_product','Postpaid');
			$simcardAttributeSetId=$eavSetup->getAttributeSetId('catalog_product','Simcard');
			
			//Get attribute group info
			$prepaidattributeGroup=$eavSetup->getAttributeGroup('catalog_product',$prepaidAttributeSetId,'Content');
			$prepaidattributeGroupId = $prepaidattributeGroup['attribute_group_id'];
			$postpaidattributeGroup=$eavSetup->getAttributeGroup('catalog_product',$postpaidAttributeSetId,'Content');
			$postpaidattributeGroupId = $postpaidattributeGroup['attribute_group_id'];
			$simcardattributeGroup=$eavSetup->getAttributeGroup('catalog_product',$simcardAttributeSetId,'Content');
			$simcardattributeGroupId = $simcardattributeGroup['attribute_group_id'];
	
			//add attribute to a set
			$eavSetup->addAttributeToSet('catalog_product',$prepaidAttributeSetId,$prepaidattributeGroupId,$attributeId);
			$eavSetup->addAttributeToSet('catalog_product',$postpaidAttributeSetId,$postpaidattributeGroupId,$attributeId);
			$eavSetup->addAttributeToSet('catalog_product',$simcardAttributeSetId,$simcardattributeGroupId,$attributeId);
		}
		if ($context->getVersion() && version_compare($context->getVersion(), '1.0.10') < 0 ) {
			$eavSetup->addAttribute(
				\Magento\Catalog\Model\Product::ENTITY, 'meta_product_title', [
				'type' => 'varchar',
				'backend' => '',
				'frontend' => '',
				'label' => 'Meta Product Title',
				'input' => 'select',
				'class' => '',
				'source' => '',
				'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
				'option' => array (
									'values' => array(
										0 => 'Nintendo',
										1 => 'Smartphones',
										2 => 'Shop-accessories',
										3 => 'Tablet',
										4 => 'Postpaid',
										5 => 'Simcard',
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
			$attribute=$eavSetup->getAttribute('catalog_product','meta_product_title');
			$attributeId = $attribute['attribute_id'];	
			/** For Postpaid **/
			$postpaidAttributeSetId=$eavSetup->getAttributeSetId('catalog_product','Postpaid');
			$postpaidattributeGroup=$eavSetup->getAttributeGroup('catalog_product',$postpaidAttributeSetId,'Search Engine Optimization');
			$postpaidattributeGroupId = $postpaidattributeGroup['attribute_group_id'];
			$eavSetup->addAttributeToSet('catalog_product',$postpaidAttributeSetId,$postpaidattributeGroupId,$attributeId);
			/** For Prepaid **/
			$prepaidAttributeSetId=$eavSetup->getAttributeSetId('catalog_product','Postpaid');
			$prepaidattributeGroup=$eavSetup->getAttributeGroup('catalog_product',$prepaidAttributeSetId,'Search Engine Optimization');
			$prepaidattributeGroupId = $prepaidattributeGroup['attribute_group_id'];
			$eavSetup->addAttributeToSet('catalog_product',$prepaidAttributeSetId,$prepaidattributeGroupId,$attributeId);
			/** For Simcard **/
			$simcardAttributeSetId=$eavSetup->getAttributeSetId('catalog_product','Simcard');
			$simcardattributeGroup=$eavSetup->getAttributeGroup('catalog_product',$simcardAttributeSetId,'Search Engine Optimization');
			$simcardattributeGroupId = $simcardattributeGroup['attribute_group_id'];
			$eavSetup->addAttributeToSet('catalog_product',$simcardAttributeSetId,$simcardattributeGroupId,$attributeId);
			/** For Handset **/
			$handsetAttributeSetId=$eavSetup->getAttributeSetId('catalog_product','handset');
			$handsetattributeGroup=$eavSetup->getAttributeGroup('catalog_product',$handsetAttributeSetId,'Search Engine Optimization');
			$handsetattributeGroupId = $handsetattributeGroup['attribute_group_id'];
			$eavSetup->addAttributeToSet('catalog_product',$handsetAttributeSetId,$handsetattributeGroupId,$attributeId);
			/** For Accessory **/
			$accessoryAttributeSetId=$eavSetup->getAttributeSetId('catalog_product','Accessories');
			$accessoryattributeGroup=$eavSetup->getAttributeGroup('catalog_product',$accessoryAttributeSetId,'Search Engine Optimization');
			$accessoryattributeGroupId = $accessoryattributeGroup['attribute_group_id'];
			$eavSetup->addAttributeToSet('catalog_product',$accessoryAttributeSetId,$accessoryattributeGroupId,$attributeId);
			/** For IEW **/
			$iewAttributeSetId=$eavSetup->getAttributeSetId('catalog_product','IEW');
			$iewattributeGroup=$eavSetup->getAttributeGroup('catalog_product',$iewAttributeSetId,'Search Engine Optimization');
			$iewattributeGroupId = $iewattributeGroup['attribute_group_id'];
			$eavSetup->addAttributeToSet('catalog_product',$iewAttributeSetId,$iewattributeGroupId,$attributeId);
			/** For Nintendo **/
			$defaultAttributeSetId=$eavSetup->getAttributeSetId('catalog_product','Default');
			$defaultattributeGroup=$eavSetup->getAttributeGroup('catalog_product',$defaultAttributeSetId,'Search Engine Optimization');
			$defaultattributeGroupId = $defaultattributeGroup['attribute_group_id'];
			$eavSetup->addAttributeToSet('catalog_product',$defaultAttributeSetId,$defaultattributeGroupId,$attributeId);
			
		}
		if ($context->getVersion() && version_compare($context->getVersion(), '1.0.11') < 0 ) {
			$eavSetup->addAttribute(
				\Magento\Catalog\Model\Product::ENTITY,
				'soho_price',
				[
					'type' => 'decimal',
					'backend' => '',
					'frontend' => '',
					'label' => 'SOHO Price',
					'input' => 'price',
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
					'visible_on_front' => true,
					'used_in_product_listing' => true,
					'unique' => false,
					'apply_to' => ''
				]
			);
			$attribute=$eavSetup->getAttribute('catalog_product','soho_price');
			$attributeId = $attribute['attribute_id'];	
			/** For Nintendo **/
			$defaultAttributeSetId=$eavSetup->getAttributeSetId('catalog_product','Default');
			$defaultattributeGroup=$eavSetup->getAttributeGroup('catalog_product',$defaultAttributeSetId,'Product Details');
			$defaultattributeGroupId = $defaultattributeGroup['attribute_group_id'];
			$eavSetup->addAttributeToSet('catalog_product',$defaultAttributeSetId,$defaultattributeGroupId,$attributeId);
			
		}
		
		/* Remove unused attributes */
		if ($context->getVersion() && version_compare($context->getVersion(), '1.0.12') < 0 ) {
			$eavSetup = $this->eavSetupFactory->create();
            $entityTypeId = 4; 
            $eavSetup->removeAttribute($entityTypeId, 'is_subsidy_hero_soho');
			$eavSetup->removeAttribute($entityTypeId, 'is_subsidy_hero_b2c');
		}
      
		if ($context->getVersion() && version_compare($context->getVersion(), '1.0.13') < 0 ) {
			$eavSetup->addAttribute(
				\Magento\Catalog\Model\Category::ENTITY,
				'drupal_url',
				[
					'type' => 'text',
					'label' => 'Drupal URL',
					'input' => 'text',
					'required' => false,
					'sort_order' => 4,
					'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
					'is_html_allowed_on_front' => true,
					'group' => 'General Information',
				]
			);
		}
    	if ($context->getVersion() && version_compare($context->getVersion(), '1.0.14') < 0 ) {
			$eavSetup->addAttribute(
				\Magento\Catalog\Model\Product::ENTITY, 'context_visibility', [
				'type' => 'varchar',
				'backend' => '',
				'frontend' => '',
				'label' => 'Context Visibility',
				'input' => 'select',
				'class' => '',
				'source' => '',
				'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
				'option' => array ('values' => array()),
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
			$attribute=$eavSetup->getAttribute('catalog_product','context_visibility');
			$attributeId = $attribute['attribute_id'];	
			/** For Postpaid **/
			$postpaidAttributeSetId=$eavSetup->getAttributeSetId('catalog_product','Postpaid');
			$postpaidattributeGroup=$eavSetup->getAttributeGroup('catalog_product',$postpaidAttributeSetId,'Product Details');
			$postpaidattributeGroupId = $postpaidattributeGroup['attribute_group_id'];
			$eavSetup->addAttributeToSet('catalog_product',$postpaidAttributeSetId,$postpaidattributeGroupId,$attributeId);
			/** For Prepaid **/
			$prepaidAttributeSetId=$eavSetup->getAttributeSetId('catalog_product','Postpaid');
			$prepaidattributeGroup=$eavSetup->getAttributeGroup('catalog_product',$prepaidAttributeSetId,'Product Details');
			$prepaidattributeGroupId = $prepaidattributeGroup['attribute_group_id'];
			$eavSetup->addAttributeToSet('catalog_product',$prepaidAttributeSetId,$prepaidattributeGroupId,$attributeId);
			/** For Simcard **/
			$simcardAttributeSetId=$eavSetup->getAttributeSetId('catalog_product','Simcard');
			$simcardattributeGroup=$eavSetup->getAttributeGroup('catalog_product',$simcardAttributeSetId,'Product Details');
			$simcardattributeGroupId = $simcardattributeGroup['attribute_group_id'];
			$eavSetup->addAttributeToSet('catalog_product',$simcardAttributeSetId,$simcardattributeGroupId,$attributeId);
			/** For Handset **/
			$handsetAttributeSetId=$eavSetup->getAttributeSetId('catalog_product','handset');
			$handsetattributeGroup=$eavSetup->getAttributeGroup('catalog_product',$handsetAttributeSetId,'Product Details');
			$handsetattributeGroupId = $handsetattributeGroup['attribute_group_id'];
			$eavSetup->addAttributeToSet('catalog_product',$handsetAttributeSetId,$handsetattributeGroupId,$attributeId);
			/** For Accessory **/
			$accessoryAttributeSetId=$eavSetup->getAttributeSetId('catalog_product','Accessories');
			$accessoryattributeGroup=$eavSetup->getAttributeGroup('catalog_product',$accessoryAttributeSetId,'Product Details');
			$accessoryattributeGroupId = $accessoryattributeGroup['attribute_group_id'];
			$eavSetup->addAttributeToSet('catalog_product',$accessoryAttributeSetId,$accessoryattributeGroupId,$attributeId);
			/** For IEW **/
			$iewAttributeSetId=$eavSetup->getAttributeSetId('catalog_product','IEW');
			$iewattributeGroup=$eavSetup->getAttributeGroup('catalog_product',$iewAttributeSetId,'Product Details');
			$iewattributeGroupId = $iewattributeGroup['attribute_group_id'];
			$eavSetup->addAttributeToSet('catalog_product',$iewAttributeSetId,$iewattributeGroupId,$attributeId);
			/** For Nintendo **/
			$defaultAttributeSetId=$eavSetup->getAttributeSetId('catalog_product','Default');
			$defaultattributeGroup=$eavSetup->getAttributeGroup('catalog_product',$defaultAttributeSetId,'Product Details');
			$defaultattributeGroupId = $defaultattributeGroup['attribute_group_id'];
			$eavSetup->addAttributeToSet('catalog_product',$defaultAttributeSetId,$defaultattributeGroupId,$attributeId);
   		}
   		if ($context->getVersion() && version_compare($context->getVersion(), '1.0.15') < 0 ) {
			$eavSetup->addAttribute(
				\Magento\Catalog\Model\Product::ENTITY, 'context_visibility', [
				'type' => 'varchar',
				'backend' => '',
				'frontend' => '',
				'label' => 'Context Visibility',
				'input' => 'select',
				'class' => '',
				'source' => 'Orange\Catalog\Model\Context\Config\Source\Options',
				'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
				'option' => array ('values' => array()),
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
			$attribute=$eavSetup->getAttribute('catalog_product','context_visibility');
			$attributeId = $attribute['attribute_id'];	
			/** For Postpaid **/
			$postpaidAttributeSetId=$eavSetup->getAttributeSetId('catalog_product','Postpaid');
			$postpaidattributeGroup=$eavSetup->getAttributeGroup('catalog_product',$postpaidAttributeSetId,'Product Details');
			$postpaidattributeGroupId = $postpaidattributeGroup['attribute_group_id'];
			$eavSetup->addAttributeToSet('catalog_product',$postpaidAttributeSetId,$postpaidattributeGroupId,$attributeId);
			/** For Prepaid **/
			$prepaidAttributeSetId=$eavSetup->getAttributeSetId('catalog_product','Postpaid');
			$prepaidattributeGroup=$eavSetup->getAttributeGroup('catalog_product',$prepaidAttributeSetId,'Product Details');
			$prepaidattributeGroupId = $prepaidattributeGroup['attribute_group_id'];
			$eavSetup->addAttributeToSet('catalog_product',$prepaidAttributeSetId,$prepaidattributeGroupId,$attributeId);
			/** For Simcard **/
			$simcardAttributeSetId=$eavSetup->getAttributeSetId('catalog_product','Simcard');
			$simcardattributeGroup=$eavSetup->getAttributeGroup('catalog_product',$simcardAttributeSetId,'Product Details');
			$simcardattributeGroupId = $simcardattributeGroup['attribute_group_id'];
			$eavSetup->addAttributeToSet('catalog_product',$simcardAttributeSetId,$simcardattributeGroupId,$attributeId);
			/** For Handset **/
			$handsetAttributeSetId=$eavSetup->getAttributeSetId('catalog_product','handset');
			$handsetattributeGroup=$eavSetup->getAttributeGroup('catalog_product',$handsetAttributeSetId,'Product Details');
			$handsetattributeGroupId = $handsetattributeGroup['attribute_group_id'];
			$eavSetup->addAttributeToSet('catalog_product',$handsetAttributeSetId,$handsetattributeGroupId,$attributeId);
			/** For Accessory **/
			$accessoryAttributeSetId=$eavSetup->getAttributeSetId('catalog_product','Accessories');
			$accessoryattributeGroup=$eavSetup->getAttributeGroup('catalog_product',$accessoryAttributeSetId,'Product Details');
			$accessoryattributeGroupId = $accessoryattributeGroup['attribute_group_id'];
			$eavSetup->addAttributeToSet('catalog_product',$accessoryAttributeSetId,$accessoryattributeGroupId,$attributeId);
			/** For IEW **/
			$iewAttributeSetId=$eavSetup->getAttributeSetId('catalog_product','IEW');
			$iewattributeGroup=$eavSetup->getAttributeGroup('catalog_product',$iewAttributeSetId,'Product Details');
			$iewattributeGroupId = $iewattributeGroup['attribute_group_id'];
			$eavSetup->addAttributeToSet('catalog_product',$iewAttributeSetId,$iewattributeGroupId,$attributeId);
			/** For Nintendo **/
			$defaultAttributeSetId=$eavSetup->getAttributeSetId('catalog_product','Default');
			$defaultattributeGroup=$eavSetup->getAttributeGroup('catalog_product',$defaultAttributeSetId,'Product Details');
			$defaultattributeGroupId = $defaultattributeGroup['attribute_group_id'];
			$eavSetup->addAttributeToSet('catalog_product',$defaultAttributeSetId,$defaultattributeGroupId,$attributeId);
   		}
		if ($context->getVersion() && version_compare($context->getVersion(), '1.0.16') < 0 ) {
			$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'tariff_plan_bottom_description', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Tariff Plan Legal Description',
            'input' => 'textarea',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
			'wysiwyg_enabled' => true,
            'unique' => false,
            'apply_to' => ''
                ]
			);
			$attribute = $eavSetup->getAttribute('catalog_product','tariff_plan_bottom_description');
			$attributeId = $attribute['attribute_id'];					
			$postpaidAttributeSetId = $eavSetup->getAttributeSetId('catalog_product','Postpaid');
			
			//Get attribute group info
			$postpaidattributeGroup = $eavSetup->getAttributeGroup('catalog_product',$postpaidAttributeSetId,'Content');
			$postpaidattributeGroupId = $postpaidattributeGroup['attribute_group_id'];
	
			$eavSetup->addAttributeToSet('catalog_product',$postpaidAttributeSetId,$postpaidattributeGroupId,$attributeId);
   		}
		if ($context->getVersion() && version_compare($context->getVersion(), '1.0.17') < 0 ) {
			$eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'tariff_plan_bottom_description', [
            'type' => 'text',
            'backend' => '',
            'frontend' => '',
            'label' => 'Tariff Plan Legal Description',
            'input' => 'textarea',
            'class' => '',
             'source' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' =>true,
            'used_in_product_listing' => true,
			'wysiwyg_enabled' => true,
            'unique' => false,
            'apply_to' => ''
                ]
			);
			$attribute = $eavSetup->getAttribute('catalog_product','tariff_plan_bottom_description');
			$attributeId = $attribute['attribute_id'];					
			$postpaidAttributeSetId = $eavSetup->getAttributeSetId('catalog_product','Postpaid');
			
			//Get attribute group info
			$postpaidattributeGroup = $eavSetup->getAttributeGroup('catalog_product',$postpaidAttributeSetId,'Content');
			$postpaidattributeGroupId = $postpaidattributeGroup['attribute_group_id'];
	
			$eavSetup->addAttributeToSet('catalog_product',$postpaidAttributeSetId,$postpaidattributeGroupId,$attributeId);
   		}
		
		if ($context->getVersion() && version_compare($context->getVersion(), '1.0.18') < 0 ) {
			$eavSetup->addAttribute(
				\Magento\Catalog\Model\Category::ENTITY,
				'characteristics',
				[
					'type' => 'text',
					'label' => 'Characteristics Code',
					'input' => 'text',
					'required' => false,
					'sort_order' => 7,
					'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
					'is_html_allowed_on_front' => true,
					'group' => 'General Information',
				]
			);
		}
		
		if ($context->getVersion() && version_compare($context->getVersion(), '1.0.19') < 0 ) {
			$eavSetup->addAttribute(
				\Magento\Catalog\Model\Product::ENTITY, 'orange_manage_stock', [
				'type' => 'varchar',
				'backend' => '',
				'frontend' => '',
				'label' => 'Orange Manage Stock',
				'input' => 'select',
				'class' => '',
				'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
				'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
				'visible' => true,
				'required' => false,
				'user_defined' => true,
				'default' => 1,
				'searchable' => false,
				'filterable' => false,
				'comparable' => false,
				'visible_on_front' =>false,
				'used_in_product_listing' => false,
				'unique' => false,
				'apply_to' => ''
				]
			);
			$attribute=$eavSetup->getAttribute('catalog_product','orange_manage_stock');
			$attributeId = $attribute['attribute_id'];	
			
			/** For Postpaid **/
			$postpaidAttributeSetId=$eavSetup->getAttributeSetId('catalog_product','Postpaid');
			$postpaidattributeGroup=$eavSetup->getAttributeGroup('catalog_product',$postpaidAttributeSetId,'Product Details');
			$postpaidattributeGroupId = $postpaidattributeGroup['attribute_group_id'];
			$eavSetup->addAttributeToSet('catalog_product',$postpaidAttributeSetId,$postpaidattributeGroupId,$attributeId);
			/** For Prepaid **/
			$prepaidAttributeSetId=$eavSetup->getAttributeSetId('catalog_product','Prepaid');
			$prepaidattributeGroup=$eavSetup->getAttributeGroup('catalog_product',$prepaidAttributeSetId,'Product Details');
			$prepaidattributeGroupId = $prepaidattributeGroup['attribute_group_id'];
			$eavSetup->addAttributeToSet('catalog_product',$prepaidAttributeSetId,$prepaidattributeGroupId,$attributeId);
			/** For Simcard **/
			$simcardAttributeSetId=$eavSetup->getAttributeSetId('catalog_product','Simcard');
			$simcardattributeGroup=$eavSetup->getAttributeGroup('catalog_product',$simcardAttributeSetId,'Product Details');
			$simcardattributeGroupId = $simcardattributeGroup['attribute_group_id'];
			$eavSetup->addAttributeToSet('catalog_product',$simcardAttributeSetId,$simcardattributeGroupId,$attributeId);
			/** For Handset **/
			$handsetAttributeSetId=$eavSetup->getAttributeSetId('catalog_product','handset');
			$handsetattributeGroup=$eavSetup->getAttributeGroup('catalog_product',$handsetAttributeSetId,'Product Details');
			$handsetattributeGroupId = $handsetattributeGroup['attribute_group_id'];
			$eavSetup->addAttributeToSet('catalog_product',$handsetAttributeSetId,$handsetattributeGroupId,$attributeId);
			/** For Accessory **/
			$accessoryAttributeSetId=$eavSetup->getAttributeSetId('catalog_product','Accessories');
			$accessoryattributeGroup=$eavSetup->getAttributeGroup('catalog_product',$accessoryAttributeSetId,'Product Details');
			$accessoryattributeGroupId = $accessoryattributeGroup['attribute_group_id'];
			$eavSetup->addAttributeToSet('catalog_product',$accessoryAttributeSetId,$accessoryattributeGroupId,$attributeId);
			/** For IEW **/
			$iewAttributeSetId=$eavSetup->getAttributeSetId('catalog_product','IEW');
			$iewattributeGroup=$eavSetup->getAttributeGroup('catalog_product',$iewAttributeSetId,'Product Details');
			$iewattributeGroupId = $iewattributeGroup['attribute_group_id'];
			$eavSetup->addAttributeToSet('catalog_product',$iewAttributeSetId,$iewattributeGroupId,$attributeId);
			/** For Nintendo **/
			$defaultAttributeSetId=$eavSetup->getAttributeSetId('catalog_product','Default');
			$defaultattributeGroup=$eavSetup->getAttributeGroup('catalog_product',$defaultAttributeSetId,'Product Details');
			$defaultattributeGroupId = $defaultattributeGroup['attribute_group_id'];
			$eavSetup->addAttributeToSet('catalog_product',$defaultAttributeSetId,$defaultattributeGroupId,$attributeId);
		}
		
	}
}