<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/


namespace Aheadworks\Csblock\Model\ResourceModel\Product;

/**
 * Class Collection
 * @package Aheadworks\Csblock\Model\Resource\Product
 */
class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{
    /**
     * Overwrite parent getAllIds method. Delete resetJoinLeft.
     *
     * @param null $limit
     * @param null $offset
     * @return array
     */
    public function getAllIds($limit = null, $offset = null)
    {
        $idsSelect = $this->_getClearSelect();
        $idsSelect->columns('e.' . $this->getEntity()->getIdFieldName());
        $idsSelect->limit($limit, $offset);
        return $this->getConnection()->fetchCol($idsSelect, $this->_bindParams);
    }
}