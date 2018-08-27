<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 17/11/2015
 * Time: 15:40
 */

namespace Magenest\AbandonedCartReminder\Model\Observer\Customer;

use Magento\Customer\Model\Logger;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class Register extends \Magenest\AbandonedCartReminder\Model\Processor\AbandonedCartReminder implements ObserverInterface
{

    protected $type = 'customer_registration';


    /**
     * Handler for 'customer_logout' event.
     *
     * @param  Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /*
            * @var  $customer \Magento\Customer\Model\Data\Customer
        */
        $customer = $observer->getEvent()->getCustomer();

        $firstName     = $customer->getFirstname();
        $lastName      = $customer->getLastname();
        $customerEmail = $customer->getEmail();

        $customer->getGender();

        $customerWebsiteId = $customer->getWebsiteId();
        $customerGroupId   = $customer->getGroupId();

        /** @var  $mobile \Magento\Framework\Api\AttributeValue */
        $mobile = $customer->getCustomAttribute('mobile_number');

        $mobileValue = $mobile->getValue();
        $logger =  $this->_context->getLogger();
        $logger->debug($mobileValue);

        $this->_emailTarget = $customer;

        // Set data used to send email
        $this->_emailTarget->setData('customer_email', $customerEmail);
        $this->_emailTarget->setData('customer_firstname', $firstName);
        $this->_emailTarget->setData('customer_lastname', $lastName);
        $this->run();

    }//end execute()


    /**
     * build email target and
     *
     * @return mixed
     */
    public function run()
    {
        $this->createFollowUpEmail();

    }//end run()


    public function isValidate($rule)
    {
        $isValidate = false;

        // validate the customer group and website
        $isValidate = parent::validateCustomerGroupAndWebsite();

        if ($isValidate) {
            $gender    = $this->_emailTarget->getGender();
            $condition = unserialize($this->_activeRule->getConditionsSerialized());

            if ($condition['gender']) {
                if ($gender == $condition['gender']) {
                    $isValidate = true;
                } else {
                    $isValidate = false;
                }
            }
        }

        return $isValidate;

    }//end isValidate()


    public function prepareMail()
    {
        if (!$this->_activeMail) {
            return;
        }

        $this->_vars = [
                        'customerFistName' => $this->_emailTarget->getFirstname(),
                        'customerLastName' => $this->_emailTarget->getLastname(),
                        'customerName'     => $this->_emailTarget->getFirstname().' '.$this->_emailTarget->getLastname(),
                       ];

    }//end prepareMail()


    public function postCreateMail()
    {

    }//end postCreateMail()
}//end class
