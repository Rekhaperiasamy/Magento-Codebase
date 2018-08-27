<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 15/10/2015
 * Time: 10:25
 */

namespace Magenest\AbandonedCartReminder\Model\Resource;

class Mail extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{


    protected function _construct()
    {
        $this->_init('magenest_abandonedcartreminder_mail_log', 'id');

    }//end _construct()


    public function getIsRuleProcessed($rule_id, $key)
    {
        $isProcessed = false;
        $adapter     = $this->_getConnection('write');

        $mainTable = $this->getTable('magenest_abandonedcartreminder_mail_log');
        $select    = $adapter->select()->from(
            ['m' => $mainTable]
        )->where(
            'm.rule_id=:rule_id and m.duplicated_key =:duplicated_key'
        );
        $bind      = [
                      ':rule_id'        => $rule_id,
                      ':duplicated_key' => $key,
                     ];

        $row = $adapter->fetchRow($select, $bind);

        if (!empty($row)) {
            $isProcessed = true;
        }

        return $isProcessed;


        $transaction = $transactionModel->getCollection()
            ->addFieldToFilter('customer_id', [['eq'=>$customerId] ,['eq' => null]])
            ->addFieldToFilter('rule_id', [['eq'=>$ruleId] ,['eq' => null]])
            ->addFieldToFilter('product_id', $productId)
            ->addFieldToFilter('order_id', $orderId)
            ->getFirstItem();


    }//end getIsRuleProcessed()
}//end class
