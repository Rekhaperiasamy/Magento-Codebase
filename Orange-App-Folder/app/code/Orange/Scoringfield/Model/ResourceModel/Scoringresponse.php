<?php
/**
 * Copyright © 2015 Orange. All rights reserved.
 */
namespace Orange\Scoringfield\Model\ResourceModel;

/**
 * Priority resource
 */
class Scoringresponse extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('scoringresponse', 'id');
    }

  
}
