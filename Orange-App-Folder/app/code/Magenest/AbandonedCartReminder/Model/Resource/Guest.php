<?php
/**
 * Created by PhpStorm.
 * User: magenest
 * Date: 13/05/2016
 * Time: 10:42
 */

namespace Magenest\AbandonedCartReminder\Model\Resource;


class Guest extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    protected function _construct()
    {
        $this->_init('magenest_abandonedcartreminder_guest_capture', 'id');
    }
}