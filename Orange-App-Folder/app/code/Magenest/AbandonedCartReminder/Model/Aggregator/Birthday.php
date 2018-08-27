<?php

namespace Magenest\AbandonedCartReminder\Model\Aggregator;

class Birthday
{

    public $_xDay = 1;

    protected $_eavConfig;

    protected $_customerResource;


    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Customer\Model\ResourceModel\Customer $customerResource
    ) {
        $this->_eavConfig        = $eavConfig;
        $this->_customerResource = $customerResource;

    }//end __construct()


    public function collectCustomerHaveBirthdayInXdays()
    {
        $readConnection = $this->_customerResource->getConnection('write');

        $today = new \DateTime();
        if ($this->_xDay) {
            $today->add(new \DateInterval('P'.$this->_xDay.'D'));
        }

        $birthDay = $today->format('m-d');
        $select   = $readConnection->select()->from(
            $this->_customerResource->getEntityTable(),
            [$this->_customerResource->getEntityIdField()]
        )->where('DATE_FORMAT(dob, "%m-%d")=?', $birthDay);

        $customers = $readConnection->fetchAssoc($select);

        return $customers;

    }//end collectCustomerHaveBirthdayInXdays()
}//end class
