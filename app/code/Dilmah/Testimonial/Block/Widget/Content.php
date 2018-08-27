<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Testimonial
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Testimonial\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Widget\Block\BlockInterface;

/**
 * Class Content.
 */
class Content extends Template implements BlockInterface
{
    /**
     * @var \Dilmah\Testimonial\Model\ResourceModel\Item\CollectionFactory
     */
    protected $testimonialCollectionFactory;

    /**
     * Content constructor.
     *
     * @param Context                                                        $context
     * @param \Dilmah\Testimonial\Model\ResourceModel\Item\CollectionFactory $itemCollectionFactory
     * @param array                                                          $data
     */
    public function __construct(
        Context $context,
        \Dilmah\Testimonial\Model\ResourceModel\Item\CollectionFactory $itemCollectionFactory,
        array $data = []
    ) {
        $this->testimonialCollectionFactory = $itemCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return \Dilmah\Testimonial\Model\ResourceModel\Item\Collection
     */
    public function getItems()
    {
        if (!$this->getData('testimonial_items')) {
            $itemCount = $this->getData('item_count');
            /** @var \Dilmah\Testimonial\Model\ResourceModel\Item\Collection $items */
            $items = $this->testimonialCollectionFactory->create();
            $items->getSelect()->joinLeft(
                ['ts' => 'dilmah_testimonial_store'],
                'ts.item_id = main_table.item_id',
                []
            );

            $items->addFieldToFilter('is_active', 1);
            $items->addOrder('sort_order', 'asc');
            $items->addFieldToFilter(
                ['ts.store_id', 'ts.store_id'],
                ['0', $this->_storeManager->getStore()->getStoreId()]
            );
            if (!empty($itemCount) && is_numeric($itemCount)) {
                $items->setPageSize($itemCount);
            }

            $this->setData('testimonial_items', $items);
        }

        return $this->getData('testimonial_items');
    }
}
