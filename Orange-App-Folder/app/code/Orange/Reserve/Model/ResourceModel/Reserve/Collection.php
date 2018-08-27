<?php

namespace Orange\Reserve\Model\ResourceModel\Reserve;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    /**
     * Initialize resource collection
     *
     * @return void
     */
    public function _construct() {
        $this->_init('Orange\Reserve\Model\Reserve', 'Orange\Reserve\Model\ResourceModel\Reserve');
    }

}
