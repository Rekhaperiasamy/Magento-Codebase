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

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Catalog\Model\Product\Type;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Cms\Model\BlockFactory;

/**
 * Upgrade Data script
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var \Magento\Cms\Model\BlockFactory
     */
    protected $blockFactory;

    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
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
        $setup->startSetup();
        $dbVersion = $context->getVersion();

        if (version_compare($dbVersion, '1.0.4', '<')) {
            /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);


            // creaating product attribute
            $eavSetup->addAttribute(
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

            // creaating product attribute
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY, 'global_hreflang', [
                    'type' => 'varchar',
                    'label' => 'Store Global URL ',
                    'input' => 'text',
                    'required' => false,
                    'sort_order' => 60,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'group' => 'Seo',
                    'default' => ''
                ]
            );
        }
        
        $setup->endSetup();
    }
}