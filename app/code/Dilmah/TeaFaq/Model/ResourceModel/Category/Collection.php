<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Netstarter
 * @package     Dilmah_TeaFaq
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\TeaFaq\Model\ResourceModel\Category;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 *
 * @package Dilmah\TeaFaq\Model\ResourceModel\Category
 */
class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    public function getLabels()
    {
        $labels = $this->setOrder('title', \Magento\Framework\Data\Collection::SORT_ORDER_ASC);
        return $labels->toOptionArray();
    }

    /**
     * Return array for select field
     *
     * @return []
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('category_id', 'title');
    }

    /**
     * Return array for grid column
     *
     * @return []
     */
    public function toOptionHash()
    {
        return $this->_toOptionHash('category_id', 'title');
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Dilmah\TeaFaq\Model\Category', 'Dilmah\TeaFaq\Model\ResourceModel\Category');
    }
}
