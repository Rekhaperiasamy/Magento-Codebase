<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Csblock\Model\Rule;

/**
 * Class Product
 * @package Aheadworks\Csblock\Model\Rule
 */
class Product extends \Magento\Rule\Model\AbstractModel
{
    /**
     * Store matched product Ids
     *
     * @var array
     */
    protected $_productIds = null;

    /**
     * @var \Magento\CatalogRule\Model\Rule\Condition\CombineFactory
     */
    protected $_combineFactory;

    /**
     * @var \Magento\CatalogRule\Model\Rule\Action\CollectionFactory
     */
    protected $_actionCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Aheadworks\Csblock\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var \Magento\Framework\Model\ResourceModel\Iterator
     */
    protected $_resourceIterator;

    /**
     * Limitation for products collection
     *
     * @var int|array|null
     */
    protected $_productsFilter = null;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Aheadworks\Csblock\Model\Rule\Condition\CombineFactory $combineFactory
     * @param \Magento\CatalogRule\Model\Rule\Action\CollectionFactory $actionCollectionFactory
     * @param \Aheadworks\Csblock\Model\ResourceModel\Product\CollectionFactory$productCollectionFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Framework\Model\ResourceModel\Iterator $resourceIterator
     * @param array $relatedCacheTypes
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Aheadworks\Csblock\Model\Rule\Condition\CombineFactory $combineFactory,
        \Magento\CatalogRule\Model\Rule\Action\CollectionFactory $actionCollectionFactory,
        \Aheadworks\Csblock\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Model\ResourceModel\Iterator $resourceIterator,
        array $relatedCacheTypes = [],
        array $data = []
    ) {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_productFactory = $productFactory;
        $this->_resourceIterator = $resourceIterator;
        $this->_combineFactory = $combineFactory;
        $this->_actionCollectionFactory = $actionCollectionFactory;
        parent::__construct($context, $registry, $formFactory, $localeDate, null, null, $data);
    }

    /**
     * Getter for rule conditions collection
     * @return \Magento\CatalogRule\Model\Rule\Condition\Combine|\Magento\Rule\Model\Condition\Combine
     */
    public function getConditionsInstance()
    {
        return $this->_combineFactory->create();
    }

    /**
     * Getter for rule actions collection
     *
     * @return \Magento\CatalogRule\Model\Rule\Action\Collection
     */
    public function getActionsInstance()
    {
        return $this->_actionCollectionFactory->create();
    }

    /**
     * Reset rule combine conditions
     * @param null $conditions
     * @return $this
     */
    protected function _resetConditions($conditions = null)
    {
        parent::_resetConditions($conditions);
        $this->getConditions($conditions)
            ->setId('1')
            ->setPrefix('csblock');
        return $this;
    }

    /**
     * Get validated product ids
     *
     * @return array
     */
    public function getMatchingProductIds()
    {
        if ($this->_productIds === null) {
            $this->_productIds = [];
            $this->setCollectedAttributes([]);

            /** @var $productCollection \Aheadworks\Csblock\Model\ResourceModel\Product\Collection */
            $productCollection = $this->_productCollectionFactory->create();
            $this->getConditions()->collectValidatedAttributes($productCollection);
            $productIds = $productCollection->getAllIds();
            $this->_productIds = array_unique($productIds);
        }
        return $this->_productIds;
    }
}
