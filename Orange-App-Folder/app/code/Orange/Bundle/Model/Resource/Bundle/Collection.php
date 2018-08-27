<?php

/**
 * Copyright © 2015 Wilsonart. All rights reserved.
 */

namespace Orange\Bundle\Model\Resource\Bundle;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct() {
        $this->_init('Orange\Bundle\Model\Bundle', 'Orange\Bundle\Model\Resource\Bundle');
    }

}
