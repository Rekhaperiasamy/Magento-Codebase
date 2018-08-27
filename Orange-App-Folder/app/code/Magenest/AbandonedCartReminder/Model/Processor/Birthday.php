<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 30/09/2015
 * Time: 15:35
 */

namespace Magenest\AbandonedCartReminder\Model\Processor;

use Symfony\Component\Config\Definition\Exception\Exception;

class Birthday extends AbandonedCartReminder
{

    protected $_customerFactory;


    public function __construct(
        \Magenest\AbandonedCartReminder\Model\Resource\Rule\CollectionFactory $rulesFactory,
        \Magenest\AbandonedCartReminder\Model\MessageFactory $messageFactory,
        \Magenest\AbandonedCartReminder\Model\SmsFactory $smsFactory,
        \Magento\Reports\Model\ResourceModel\Quote\CollectionFactory $quotesFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Email\Model\Template $emailTemplateModel,
        \Magenest\AbandonedCartReminder\Model\Aggregator\Birthday $birthdayAgg,
        \Magenest\AbandonedCartReminder\Model\MailFactory $mailFactory,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface,
        \Magento\SalesRule\Model\RuleFactory $ruleFactory,
        \Magento\Framework\Url $urlInterface,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\SalesRule\Model\Coupon\Massgenerator $massGenerator,
        \Magento\Store\Model\App\Emulation $appEmulation
    ) {
        $this->type = 'customer_birthday';

        $this->_aggegator = $birthdayAgg;

        $this->_quoteFactory = $quoteFactory;

        $this->_saleRuleFactory = $ruleFactory;

        $this->_customerFactory = $customerFactory;

        parent::__construct($context, $rulesFactory, $mailFactory, $messageFactory, $smsFactory, $quoteFactory, $cartRepositoryInterface, $emailTemplateModel, $scopeConfig, $urlInterface, $massGenerator, $appEmulation);

    }//end __construct()


    /**
     * build email target and
     *
     * @return mixed
     */
    public function run()
    {
        $rules = $this->getMatchingRule();
        if ($rules) {
            /*
                * @var  $rule
            */
            foreach ($rules as $rule) {
                $isFirstTime = true;

                $this->_activeRule = $rule;

                $emailInChains = $rule->getMailChain();

                foreach ($emailInChains as $emailInChain) {
                    $sendBeforeXdays = $emailInChain['day'];

                    // todo :
                    // get the customers will have birthday in next x days
                    $this->_aggegator->_xDay = $sendBeforeXdays;
                    $customeIds              = $this->_aggegator->collectCustomerHaveBirthdayInXdays();

                    if ($customeIds) {
                        foreach ($customeIds as $customerId) {
                            $customer           = $this->_customerFactory->create()->load($customerId);
                            $this->_emailTarget = $customer;
                            $firstName          = $customer->getFirstname();
                            $lastName           = $customer->getLastname();
                            $customerEmail      = $customer->getEmail();
                            $this->_emailTarget->setData('customer_email', $customerEmail);
                            $this->_emailTarget->setData('customer_firstname', $firstName);
                            $this->_emailTarget->setData('customer_lastname', $lastName);
                            $this->createFollowUpEmail();
                        }
                    }
                }//end foreach
            }//end foreach
        }//end if

    }//end run()


    public function isValidate($rule)
    {
        return true;

    }//end isValidate()


    public function prepareMail()
    {
        /*
            * @var  $customer \Magento\Customer\Model\Customer
        */
        $customer          = $this->_emailTarget;
        $customerFirstName = $customer->getFirstname();
        $customerLastName  = $customer->getLastname();
        $customerName      = $customerFirstName.' '.$customerLastName;
        $this->_vars       = [
                              'customer'          => $this->_emailTarget,
                              'customerFirstName' => $customerFirstName,
                              'customerLastName'  => $customerLastName,
                              'customerName'      => $customerName,
                             ];

    }//end prepareMail()


    public function postCreateMail()
    {

    }//end postCreateMail()
}//end class
