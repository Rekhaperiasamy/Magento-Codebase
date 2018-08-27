<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Csblock\Model\ResourceModel\Content;

/**
 * Class Collection
 * @package Aheadworks\Csblock\Model\ResourceModel\Content
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    public function _construct()
    {
        $this->_init('Magento\Framework\DataObject', 'Aheadworks\Csblock\Model\ResourceModel\Content');
    }

    public function addBlockIdFilter($blockId)
    {
        $this->addFieldToFilter('csblock_id', ['eq' => $blockId]);
        return $this;
    }

    public function addStoreFilter($storeId)
    {
        $this->addFieldToFilter('store_id', [['eq' => $storeId], ['eq' => '0']]);
        return $this;
    }

    public function getMaxId()
    {
        $allIds = $this->getAllIds();
        $result = 0;
        if ($allIds) {
            $result = max($allIds);
        }
        return ++$result;
    }
}