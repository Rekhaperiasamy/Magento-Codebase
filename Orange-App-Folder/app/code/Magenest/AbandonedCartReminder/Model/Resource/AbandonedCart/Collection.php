<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 15/10/2015
 * Time: 10:18
 */
namespace Magenest\AbandonedCartReminder\Model\Resource\AbandonedCart;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected $_resource;


    /**
     * Initialize resource
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\AbandonedCartReminder\Model\AbandonedCart', 'Magenest\AbandonedCartReminder\Model\Resource\AbandonedCart');

    }//end _construct()
}//end class
