<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 10/06/2016
 * Time: 09:13
 */

namespace Magenest\AbandonedCartReminder\Model\Resource;

class Notification extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{


    protected function _construct()
    {
        $this->_init('magenest_abandonedcartreminder_mail_notification', 'notication_id');

    }//end _construct()


    public function getContent($id)
    {
        $mainTable = $this->getMainTable();

        $mailTable = $this->getTable('magenest_abandonedcartreminder_mail_log');

        $adapter = $this->_getConnection('read');

        // select with the condition is sent
        $select = $adapter->select()->from(
            ['m' => $mainTable],
            '*'
        )->join(['mail' => $mailTable], 'm.mail_id = mail.id', '*')->where('m.notication_id='.$id);

        $row = $adapter->fetchRow($select);

        return $row;

    }//end getContent()
}//end class
