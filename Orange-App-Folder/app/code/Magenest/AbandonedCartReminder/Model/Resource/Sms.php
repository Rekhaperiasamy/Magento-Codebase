<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 15/06/2016
 * Time: 10:17
 */

namespace Magenest\AbandonedCartReminder\Model\Resource;

/*
    * represent class
 */
class Sms extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{


    /**
     * bind class to table in database
     */
    protected function _construct()
    {
        $this->_init('magenest_abandonedcartreminder_sms_log', 'id');

    }//end _construct()
}//end class
