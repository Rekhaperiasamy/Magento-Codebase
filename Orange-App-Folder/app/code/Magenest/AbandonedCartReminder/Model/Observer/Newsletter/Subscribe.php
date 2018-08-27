<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 20/01/2016
 * Time: 15:33
 */
namespace Magenest\AbandonedCartReminder\Model\Observer\Newsletter;

use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Email\Model\Template as EmailTemplateModel;

class Subscribe extends \Magenest\AbandonedCartReminder\Model\Processor\AbandonedCartReminder implements ObserverInterface
{

    protected $_customerFactory;

    /**
     * @var   \Magento\Customer\Model\Customer
     */
    protected $_customer;

    protected $_subscribe;

    protected $heper;


    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magenest\AbandonedCartReminder\Model\Resource\Rule\CollectionFactory $rulesFactory,
        \Magenest\AbandonedCartReminder\Model\MailFactory $mailFactory,
        \Magenest\AbandonedCartReminder\Model\MessageFactory $messageFactory,
        \Magenest\AbandonedCartReminder\Model\SmsFactory $smsFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface,
        \Magento\Email\Model\Template $emailTemplateModel,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\SalesRule\Model\Coupon\Massgenerator $massGenerator,
        \Magento\Store\Model\App\Emulation $appEmulation,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magenest\AbandonedCartReminder\Helper\Data $data
    ) {
        $this->heper            = $data;
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
        $this->createFollowUpEmail();

    }//end run()


    public function isValidate($rule)
    {
        $isValidate = false;

        // validate the customer group and website
        $isValidate = parent::validateCustomerGroupAndWebsite($this->_customer->getGroupId(), $this->_customer->getWebsiteId());

        if ($isValidate) {
            $gender    = $this->_customer->getGender();
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
        $this->_vars = [
                        'customer'         => $this->_customer,
                        'customerFistName' => $this->_customer->getFirstname(),
                        'customerLastName' => $this->_customer->getLastname(),
                        'customerName'     => $this->_customer->getName(),
                       ];

    }//end prepareMail()


    public function postCreateMail()
    {

    }//end postCreateMail()


    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /*
            * @var $request \Magento\Framework\App\Request\Http\Proxy
        */
        $request       = $this->heper->getRequest();
        $pathInfo      = $request->getPathInfo();
        $params        = $request->getParams();
        $is_subscribed = 0;
        if (isset($params['is_subscribed'])) {
            $is_subscribed = $params['is_subscribed'];
        }

        /*
            * @var $subscriber \Magento\Newsletter\Model\Subscriber
        */
        $subscriber = $observer->getEvent()->getSubscriber();

        $this->_subscribe = $subscriber;

        $statusChange = $subscriber->getIsStatusChanged();
        $customerId   = $subscriber->getCustomerId();

        /*
            * @var $customer \Magento\Customer\Model\Customer *
        */
        $customer        = $this->_customerFactory->create()->load($customerId);
        $this->_customer = $customer;
        $firstName       = $customer->getFirstname();
        $lastName        = $customer->getLastname();
        $customerEmail   = $customer->getEmail();
        $mobileNumber    = $customer->getData('mobile_number');

        $this->_emailTarget = $subscriber;
        $this->_emailTarget->setData('customer_email', $customerEmail);
        $this->_emailTarget->setData('mobile_number', $mobileNumber);

        $this->_emailTarget->setData('customer_firstname', $firstName);
        $this->_emailTarget->setData('customer_lastname', $lastName);

        if ($is_subscribed == 1) {
            $this->type = 'newsletter_subscribe';
        } else {
            $this->type = 'newsletter_unsubscribe';
        }

            $this->run();

    }//end execute()
}//end class
