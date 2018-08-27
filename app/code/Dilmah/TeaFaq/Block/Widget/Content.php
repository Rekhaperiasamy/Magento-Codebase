<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Netstarter
 * @package     Dilmah_TeaFaq
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\TeaFaq\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Widget\Block\BlockInterface;

/**
 * Class Content
 *
 * @package Dilmah\TeaFaq\Block\Widget
 */
class Content extends Template implements BlockInterface
{
    /**
     * Category Collection Factory
     * @var \Dilmah\TeaFaq\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryColFactory;


    /**
     * Constructor
     * @param Context $context
     * @param \Dilmah\TeaFaq\Model\ResourceModel\Category\CollectionFactory $categoryColFactory
     * @param [] $data
     */
    public function __construct(
        Context $context,
        \Dilmah\TeaFaq\Model\ResourceModel\Category\CollectionFactory $categoryColFactory,
        $data = []
    ) {
        $this->categoryColFactory = $categoryColFactory;
        parent::__construct($context, $data);
    }

    /**
     * Get Categories
     * @return \Dilmah\TeaFaq\Model\ResourceModel\Category\Collection
     */
    public function getCategories()
    {
        if (!$this->getData('categories')) {
            /** @var \Dilmah\TeaFaq\Model\ResourceModel\Category\Collection $categories */
            $categories = $this->categoryColFactory->create();
            $categories->getSelect()->joinLeft(
                ['cs' => 'dilmah_tfaq_category_store'],
                'cs.category_id = main_table.category_id',
                []
            );

            $categories->addFieldToFilter('is_active', 1);
            $categories->addOrder('sort_order', 'asc');
            $categories->addFieldToFilter(
                ['cs.store_id', 'cs.store_id'],
                ['0', $this->_storeManager->getStore()->getStoreId()]
            );
            $this->setData('categories', $categories);
        }
        return $this->getData('categories');
    }

    /**
     * Get Item Html
     * @param \Dilmah\TeaFaq\Model\Category $category
     * @return htmlcontent
     */
    public function getItemHtml(\Dilmah\TeaFaq\Model\Category $category)
    {
        if (!$this->getData('item_block')) {
            $this->setData('item_block', $this->getLayout()->createBlock('Dilmah\TeaFaq\Block\Item'));
        }
        $itemBlock = $this->getData('item_block');
        $itemBlock->setCategory($category);
        return $itemBlock->toHtml();
    }
}
