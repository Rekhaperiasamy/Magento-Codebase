<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 20/01/2016
 * Time: 15:15
 */
namespace Magenest\AbandonedCartReminder\Model\Observer\Wishlist;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Email\Model\Template as EmailTemplateModel;

class Shared extends \Magenest\AbandonedCartReminder\Model\Processor\AbandonedCartReminder implements ObserverInterface
{

    protected $type = 'wishlist_shared';

    protected $_customerFactory;

    /**
     * @var
     */
    protected $_customer;


    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magenest\AbandonedCartReminder\Model\Resource\Rule\CollectionFactory $rulesFactory,
        \Magenest\AbandonedCartReminder\Model\MailFactory $mailFactory,
        \Magenest\AbandonedCartReminder\Model\MessageFactory $messageFactory,
        \Magenest\AbandonedCartReminder\Model\SmsFactory $smsFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface,
        EmailTemplateModel $emailTemplateModel,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\SalesRule\Model\Coupon\Massgenerator $massGenerator,
        \Magento\Store\Model\App\Emulation $appEmulation,
        \Magento\Customer\Model\CustomerFactory $customerFactory

    )
   {
        $this->_customerFactory = $customerFactory;
        parent::__construct($context, $rulesFactory, $mailFactory,
            $messageFactory, $smsFactory, $quoteFactory,
            $cartRepositoryInterface, $emailTemplateModel,$scopeConfig,$urlInterface,
            $massGenerator, $appEmulation);

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
        return true;

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
        // TODO: Implement postCreateMail() method.

    }//end postCreateMail()


    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /*
            * @var  $wishlist \Magento\Wishlist\Model\Wishlist
        */
        $wishlist   = $observer->getEvent()->getWishlist();
        $customerId = $wishlist->getCustomerId();

        /*
            * @var $customer \Magento\Customer\Model\Customer
        */
        $customer           = $this->_customerFactory->create()->load($customerId);
        $firstName          = $customer->getFirstname();
        $lastName           = $customer->getLastname();
        $customerEmail      = $customer->getEmail();

        $mobileNumber       = $customer->getData('mobile_number');
        $this->_emailTarget = $wishlist;

        $this->_customer = $customer;

        // Set data used to send email
        $this->_emailTarget->setData('customer_email', $customerEmail);
        $this->_emailTarget->setData('mobile_number', $mobileNumber);

        $this->_emailTarget->setData('customer_firstname', $firstName);
        $this->_emailTarget->setData('customer_lastname', $lastName);
        $this->run();

    }//end execute()
}//end class
