<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 15/10/2015
 * Time: 10:17
 */

namespace Magenest\AbandonedCartReminder\Model\Resource;

class AbandonedCart extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{


    protected function _construct()
    {
        $this->_init('magenest_abandonedcartreminder_guest_abandoned_cart', 'id');

    }//end _construct()


    public function getTimePeriodCondition($minute)
    {
        $now = new \DateTime();

        $modifyDelta = (60 * $minute);
        $modify      = '-'.$modifyDelta.' seconds';

        $now->modify($modify);

        $downLimit = $now->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);

        return $downLimit;

    }//end getTimePeriodCondition()


    public function getAbandonedCartForInsertOperation($downLimit)
    {
        $downLimit = $this->getTimePeriodCondition($downLimit);
        $adapter   = $this->_getConnection('read');

        $mainTable = $this->getTable('quote');

        $followUpAbandonedCartTable = $this->getTable('magenest_abandonedcartreminder_guest_abandoned_cart');

        $compareCondition = "STR_TO_DATE($downLimit,'%Y-%m-%d %H:%i:%s')";

        $select = $adapter->select()->from(
            ['m' => $mainTable],
            'm.*'
        )->joinLeft(['a' => $followUpAbandonedCartTable], 'm.entity_id = a.quote_id')->where(
            'a.quote_id is null AND m.is_active = 1 AND m.customer_email  is not null  AND m.items_count != 0'
        )->where('m.updated_at < ? or (m.created_at < "'.$compareCondition.'"  and m.updated_at ="0000-00-00 00:00:00")', $downLimit);
		
	//	echo $select; exit;

        $row = $adapter->fetchAssoc($select);

        return $row;

    }//end getAbandonedCartForInsertOperation()


    public function getCurrentTime()
    {
        $rs = $this->_getConnection('read')->fetchAll('select now()');

        return $rs;

    }//end getCurrentTime()


    public function getQuoteTime()
    {
        $adapter = $this->_getConnection('read');

        $mainTable = $this->getTable('quote');

        $select = $adapter->select()->from(
            ['m' => $mainTable],
            'm.created_at'
        );

        $row = $adapter->fetchAssoc($select);

        return $row;

    }//end getQuoteTime()
}//end class
