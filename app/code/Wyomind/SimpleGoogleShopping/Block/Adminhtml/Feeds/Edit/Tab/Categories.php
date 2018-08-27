<?php

/**
 * Copyright © 2015 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\SimpleGoogleShopping\Block\Adminhtml\Feeds\Edit\Tab;

/**
 * Categories tab
 */
class Categories extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{

    protected $_directoryList = null;
    protected $_coreHelper = null;
    protected $_objectManager = null;
    protected $_tree = null;

    /**
     * @param \Magento\Backend\Block\Template\Context             $context
     * @param \Magento\Framework\Registry                         $registry
     * @param \Magento\Framework\Data\FormFactory                 $formFactory
     * @param \Magento\Catalog\Model\ResourceModel\Category\Collection $categoryCollection
     * @param \Magento\Framework\View\LayoutFactory               $layoutFactory
     * @param \Magento\Framework\Json\EncoderInterface            $jsonEncoder
     * @param \Magento\Framework\DB\Helper                        $resourceHelper
     * @param \Magento\Framework\App\Filesystem\DirectoryList     $directoryList
     * @param \Wyomind\Core\Helper\Data                           $coreHelper
     * @param array                                               $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Wyomind\Core\Helper\Data $coreHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Wyomind\SimpleGoogleShopping\Helper\CategoryTree $tree,
        array $data = []
    ) {
    
        $this->_directoryList = $directoryList;
        $this->_coreHelper = $coreHelper;
        $this->_objectManager = $objectManager;
        $this->_tree = $tree;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return string
     */
    public function getFeedTaxonomy()
    {
        $model = $this->_coreRegistry->registry('data_feed');
        return $model->getSimplegoogleshoppingFeedTaxonomy();
    }

    /**
     * @return int
     */
    public function getCategoryFilter()
    {
        $model = $this->_coreRegistry->registry('data_feed');
        return $model->getSimplegoogleshoppingCategoryFilter();
    }

    /**
     * @return int
     */
    public function getCategoryType()
    {
        $model = $this->_coreRegistry->registry('data_feed');
        return $model->getSimplegoogleshoppingCategoryType();
    }

    /**
     * @return string
     */
    public function getSGSCategories()
    {
        $model = $this->_coreRegistry->registry('data_feed');
        return $model->getSimplegoogleshoppingCategories();
    }

    /**
     * @param string $directory
     * @return array
     */
    public function dirFiles($directory)
    {
        $dir = dir($directory); //Open Directory
        while (false !== ($file = $dir->read())) { //Reads Directory
            $extension = substr($file, strrpos($file, '.')); // Gets the File Extension
            if ($extension == ".txt") { // Extensions Allowed
                $filesall[$file] = $file;
            } // Store in Array
        }
        $dir->close(); // Close Directory
        asort($filesall); // Sorts the Array
        return $filesall;
    }

    /**
     * @return array
     */
    public function getAvailableTaxonomies()
    {
        $rootDir = $this->_directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::ROOT);
        if (file_exists($rootDir . "/app/code/Wyomind/SimpleGoogleShopping/data/Google/Taxonomies/")) {
            return $this->dirFiles($rootDir . "/app/code/Wyomind/SimpleGoogleShopping/data/Google/Taxonomies/");
        } elseif (file_exists($rootDir . "/vendor/wyomind/simplegoogleshopping/data/Google/Taxonomies/")) {
            return $this->dirFiles($rootDir . "/vendor/wyomind/simplegoogleshopping/data/Google/Taxonomies/");
        } else {
            return [];
        }
    }

    /**
     * @see Magento\Catalog\Block\Adminhtml\Category\Tree
     * @return string
     */
    public function getCategoriesJson()
    {
        return $this->_tree->getTree();
    }

    public function getJsonTree()
    {
        $treeCategories = $this->_tree->getTree();
        return json_encode($treeCategories);
    }

    /**
     * @return $this
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('data_feed');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('');
        $form->setValues($model->getData());
        $this->setForm($form);
        $this->setTemplate('edit/categories.phtml');
        return parent::_prepareForm();
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->_coreRegistry->registry('data_feed')->getStoreId();
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Category\Collection
     */
    public function getCategories()
    {
        $tmp = $this->_categoryCollection->create();
        return $tmp
                        ->setStoreId($this->getStoreId())
                        ->addAttributeToSelect(['name'/* , 'is_active' */])
                        ->addAttributeToSort('path', 'ASC');
    }

    /**
     * @return string
     */
    public function getTabLabel()
    {
        return __('Categories');
    }

    /**
     * @return string
     */
    public function getTabTitle()
    {
        return __('Categories');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
