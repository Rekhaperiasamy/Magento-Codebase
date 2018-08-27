<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 14/06/2016
 * Time: 13:25
 */

namespace Magenest\AbandonedCartReminder\Model\Resource;

class Message extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{


    /**
     * Bind this class to table in database
     */
    protected function _construct()
    {
        $this->_init('magenest_abandonedcartreminder_mail_sms', 'sms_id');

    }//end _construct()
}//end class
