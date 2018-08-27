<?php

namespace Orange\Bundle\Model\Resource;

class Bundle extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct() {
        $this->_init('bundle_color_swatch_mapping', 'id');
    }

}
