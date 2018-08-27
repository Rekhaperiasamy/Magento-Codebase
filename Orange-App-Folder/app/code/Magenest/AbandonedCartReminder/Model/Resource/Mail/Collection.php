<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 15/10/2015
 * Time: 10:25
 */
namespace Magenest\AbandonedCartReminder\Model\Resource\Mail;

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
        $this->_init('Magenest\AbandonedCartReminder\Model\Mail', 'Magenest\AbandonedCartReminder\Model\Resource\Mail');

    }//end _construct()


    /**
     * Get the emails which have sent time greater than current time
     *
     * @return $this
     */
    public function getMailsNeedToBeSent()
    {
        $current_date_time = new \DateTime();

        $currentTime = $current_date_time->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
        $cond        = 'send_date < '."'$currentTime'";
        $select      = $this->getSelect()->where($cond);
        $this->addFieldToFilter('status', 0);
        $this->setOrder('created_at', self::SORT_ORDER_ASC);

        return $this;

    }//end getMailsNeedToBeSent()
}//end class
