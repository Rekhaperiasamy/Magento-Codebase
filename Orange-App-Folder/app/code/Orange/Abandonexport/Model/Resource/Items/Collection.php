<?php
/**
 * Copyright © 2015 Orange. All rights reserved.
 */

namespace Orange\Abandonexport\Model\Resource\Items;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Orange\Abandonexport\Model\Items', 'Orange\Abandonexport\Model\Resource\Items');
    }
}
