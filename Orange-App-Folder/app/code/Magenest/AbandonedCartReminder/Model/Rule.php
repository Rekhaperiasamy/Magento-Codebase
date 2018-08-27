<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 29/09/2015
 * Time: 20:06
 */
namespace Magenest\AbandonedCartReminder\Model;

use Magenest\AbandonedCartReminder\Model\Resource\Rule as Resource;
use Magenest\AbandonedCartReminder\Model\Resource\Rule\Collection;

class Rule extends \Magento\Framework\Model\AbstractModel
{

    protected $_eventPrefix = 'abandonedcartreminder_rule';


    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        Resource $resource,
        Collection $resourceCollection,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

    }//end __construct()


    /**
     * @return array
     */
    public function getMailChain()
    {
        $chain_text = $this->getData('email_chain');

        $emailChains = unserialize($chain_text);

        return $emailChains;

    }//end getMailChain()


    /**
     * get the id array of sms
     *
     * @return mixed
     */
    public function getMessageChain()
    {
        $ids = unserialize($this->getData('sms_chain'));
        return $ids;

    }//end getMessageChain()


    public function isValidateTarget($customerGroupId, $websiteId)
    {
        $isValidate       = false;
        $websiteIds       = unserialize($this->getWebsiteId());
        $customerGroupIds = unserialize($this->getCustomerGroupId());

        if (in_array($websiteId, $websiteIds) && in_array($customerGroupId, $customerGroupIds)) {
            $isValidate = true;
        }

        return $isValidate;

    }//end isValidateTarget()


    /**
     * get sms bind to the rule
     */
    public function getSMSData()
    {
        $smsData = unserialize($this->getData('sms_chain'));
        return $smsData;

    }//end getSMSData()
}//end class
