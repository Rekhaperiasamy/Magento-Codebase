<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Seo
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2017 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Seo\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Setup\CategorySetupFactory;


/**
 * Class InstallData
 * @package Dilmah\Seo\Setup
 */
class InstallData implements InstallDataInterface
{

    /**
     * categorySetupFactory is used for
     *
     * @var CategorySetupFactory
     */
    private $categorySetupFactory;

    /**
     * InstallData constructor.
     *
     * @param CategorySetupFactory $categorySetupFactory
     */
    public function __construct(CategorySetupFactory $categorySetupFactory)
    {
        $this->categorySetupFactory = $categorySetupFactory;
    }

    /**
     * Installs data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface   $context
     *
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
        $entityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Category::ENTITY);
        $attributeSetId = $categorySetup->getDefaultAttributeSetId($entityTypeId);
        $seoAttributeGroupId = $categorySetup->getAttributeGroupId($entityTypeId, $attributeSetId, 'Seo');

        $categorySetup->removeAttribute(\Magento\Catalog\Model\Category::ENTITY, 'canonical_url' );
        $categorySetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY, 'store_hreflang', [
                'type' => 'varchar',
                'label' => 'Store Hreflang URL ',
                'input' => 'text',
                'required' => false,
                'sort_order' => 50,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Seo',
                'default' => ''
            ]
        );

        $categorySetup->addAttributeToGroup(
            $entityTypeId,
            $attributeSetId,
            $seoAttributeGroupId,
            'store_hreflang',
            10
        );

        $categorySetup->removeAttribute(\Magento\Catalog\Model\Category::ENTITY, 'canonical_url' );
        $categorySetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY, 'global_hreflang', [
                'type' => 'varchar',
                'label' => 'Global Hreflang URL ',
                'input' => 'text',
                'required' => false,
                'sort_order' => 60,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Seo',
                'default' => ''
            ]
        );

        $categorySetup->addAttributeToGroup(
            $entityTypeId,
            $attributeSetId,
            $seoAttributeGroupId,
            'global_hreflang',
            20
        );

        $setup->endSetup();
    }
}