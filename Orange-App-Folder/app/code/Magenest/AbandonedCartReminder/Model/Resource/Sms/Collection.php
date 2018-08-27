<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 15/06/2016
 * Time: 10:17
 */
namespace Magenest\AbandonedCartReminder\Model\Resource\Sms;

/**
 * Class Collection
 *
 * @package Magenest\AbandonedCartReminder\Model\Resource\Mail
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{


    /**
     * Initialize resource
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\AbandonedCartReminder\Model\Sms', 'Magenest\AbandonedCartReminder\Model\Resource\Sms');

    }//end _construct()


    /**
     * @return $this
     */
    public function getSmsNeedToBeSent()
    {
        $current_date_time = new \DateTime();

        $currentTime = $current_date_time->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
        $cond        = 'scheduled_send_date < '."'$currentTime'";
        $select      = $this->getSelect()->where($cond);
        $this->addFieldToFilter('status', 0);
        $this->setOrder('created_at', self::SORT_ORDER_ASC);

        return $this;

    }//end getSmsNeedToBeSent()
}//end class
