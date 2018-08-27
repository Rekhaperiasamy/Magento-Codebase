<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_ProductSlider
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\ProductSlider\Block\Widget;

use Magento\Catalog\Block\Product\Widget\NewWidget;
use Magento\Catalog\Model\Category;

/**
 * Class ProductWidget.
 */
class ProductWidget extends NewWidget
{
    /**
     * Default featured product page size.
     */
    const FEATURED_PRODUCTS_DEFAULT_PAGE_SIZE = 10;

    /*
     * Product Collection
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $productCollection = null;

    /*
     * Category Data
     *
     * @var \Magento\Catalog\Model\Category
     */
    protected $categoryData = null;

    /**
     * ProductWidget constructor.
     *
     * @param \Magento\Catalog\Block\Product\Context                         $context
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility                      $catalogProductVisibility
     * @param \Magento\Framework\App\Http\Context                            $httpContext
     * @param array                                                          $data
     * @param Category                                                       $categoryData
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data,
        Category $categoryData
    ) {
        parent::__construct($context, $productCollectionFactory, $catalogProductVisibility, $httpContext, $data);
        $this->categoryData = $categoryData;
    }

    /**
     * Get Key pieces for caching block content.
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return [
            'CATALOG_PRODUCT_BY_ATTRIBUTE_LIST',
            $this->_storeManager->getStore()->getId(),
            $this->_design->getDesignTheme()->getId(),
            $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP),
            'template' => $this->getTemplate(),
            $this->getProductsCount(),
        ];
    }

    /**
     * Prepare and return product collection.
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|object|\Magento\Framework\Data\Collection
     */
    protected function _getProductCollection()
    {
        if (!$this->productCollection) {
            /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
            $collection = $this->_productCollectionFactory->create();

            $categoryId = $this->getData('featured_category_id');
            $size = $this->getData('products_count');
            if (!empty($categoryId) && is_numeric($categoryId)) {
                $this->categoryData->setId($categoryId);
                $collection->addCategoryFilter($this->categoryData);
            }
            if (empty($size) || !is_numeric($size)) {
                $size = self::FEATURED_PRODUCTS_DEFAULT_PAGE_SIZE;
            }
            $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());

            $collection = $this->_addProductAttributesAndPrices($collection)
                ->addStoreFilter()
                ->setPageSize($size)
                ->addAttributeToSort('position', 'asc')
                ->setCurPage($this->getCurrentPage());

            $this->productCollection = $collection;
        }

        return $this->productCollection;
    }

    /**
     * Prepare collection with new products.
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _beforeToHtml()
    {
        $this->_eventManager->dispatch(
            'catalog_block_product_list_collection',
            ['collection' => $this->_getProductCollection()]
        );
        $this->_getProductCollection()->load();

        return parent::_beforeToHtml();
    }
}
