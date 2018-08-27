<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Netstarter
 * @package     Dilmah_TeaFaq
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\TeaFaq\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Widget\Block\BlockInterface;

/**
 * Class Item
 *
 * @package Dilmah\TeaFaq\Block
 */
class Item extends Template implements BlockInterface
{
    /**
     * CollectionFactory
     * @var \Dilmah\TeaFaq\Model\ResourceModel\Item\CollectionFactory
     */
    protected $itemColFactory;

    /**
     * Constructor
     * @param Context $context
     * @param \Dilmah\TeaFaq\Model\ResourceModel\Item\CollectionFactory $itemColFactory
     */
    public function __construct(
        Context $context,
        \Dilmah\TeaFaq\Model\ResourceModel\Item\CollectionFactory $itemColFactory
    ) {
        $this->itemColFactory = $itemColFactory;
        parent::__construct($context);
        $this->setTemplate('faqs.phtml');

    }

    /**
     * Get Items
     * @return \Dilmah\TeaFaq\Model\ResourceModel\Item\Collection
     */
    public function getItems()
    {
        /** @var \Dilmah\TeaFaq\Model\ResourceModel\Item\Collection $items */
        $items = $this->itemColFactory->create();
        $items->getSelect()->joinLeft(
            ['is' => 'dilmah_tfaq_item_store'],
            'is.item_id = main_table.item_id',
            []
        );

        $items->addFieldToFilter('category_id', $this->getCategory()->getId());
        $items->addFieldToFilter('is_active', 1);
        $items->addFieldToFilter(['is.store_id', 'is.store_id'], ['0', $this->_storeManager->getStore()->getStoreId()]);
        $items->addOrder('sort_order', 'asc');
        return $items;
    }
}
