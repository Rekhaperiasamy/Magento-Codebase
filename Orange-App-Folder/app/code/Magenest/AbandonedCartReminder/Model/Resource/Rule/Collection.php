<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 29/09/2015
 * Time: 20:07
 */
namespace Magenest\AbandonedCartReminder\Model\Resource\Rule;

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
        $this->_init('Magenest\AbandonedCartReminder\Model\Rule', 'Magenest\AbandonedCartReminder\Model\Resource\Rule');

    }//end _construct()


    public function getRulesByType($type)
    {
        $this->addFieldToFilter('type', $type)->addFieldToFilter(
            'main_table.status',
            '1'
        );

        $now = (new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);

        $this->getSelect()->where('from_date <=? or from_date is null', $now)->where('to_date>=? or to_date is null', $now);
        return $this;

    }//end getRulesByType()
}//end class
