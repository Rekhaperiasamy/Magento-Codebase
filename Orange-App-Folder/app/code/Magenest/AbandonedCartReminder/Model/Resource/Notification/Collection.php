<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 10/06/2016
 * Time: 09:14
 */
namespace Magenest\AbandonedCartReminder\Model\Resource\Notification;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{


    protected function _construct()
    {
        $this->_init('Magenest\AbandonedCartReminder\Model\Notification', 'Magenest\AbandonedCartReminder\Model\Resource\Notification');

    }//end _construct()


    /**
     * @param $customerId
     */
    public function getNotificationByCustomer($customerId)
    {
        $customerTable = $this->getTable('customer_entity');
        $this->_select->distinct('main_table.notification_id')->joinLeft(
            ['customer_table' => $customerTable],
            'main_table.recipient_email=customer_table.email AND customer_table.entity_id ='.$customerId
        );

        return $this;

    }//end getNotificationByCustomer()
}//end class
