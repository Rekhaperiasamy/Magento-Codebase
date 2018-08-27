<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 19/01/2016
 * Time: 11:28
 */

namespace Magenest\AbandonedCartReminder\Model\Observer\Order;

use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Email\Model\Template as EmailTemplateModel;

class StatusChange extends \Magenest\AbandonedCartReminder\Model\Processor\AbandonedCartReminder implements ObserverInterface
{

    protected $_quote;
    protected $_customerFactory;
    protected $_quoteId;

    public function __construct(
        \Magenest\AbandonedCartReminder\Model\Resource\Rule\CollectionFactory $rulesFactory,
        \Magento\Reports\Model\ResourceModel\Quote\CollectionFactory $quotesFactory,
        \Magenest\AbandonedCartReminder\Model\MessageFactory $messageFactory,
        \Magenest\AbandonedCartReminder\Model\SmsFactory $smsFactory,
        EmailTemplateModel $emailTemplateModel,
        \Magento\Quote\Model\Quote\TotalsCollector $totalsCollector,
        \Magenest\AbandonedCartReminder\Model\Aggregator\AbandonedCart $abandonedCart,
        \Magenest\AbandonedCartReminder\Model\MailFactory $mailFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface,
        \Magento\SalesRule\Model\RuleFactory $ruleFactory,
        \Magento\Framework\Url $urlInterface,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\SalesRule\Model\Coupon\Massgenerator $massGenerator,
        \Magento\Store\Model\App\Emulation $appEmulation,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {
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
        return true;

    }//end isValidate()


    public function prepareMail()
    {
        $this->_vars = [
                        'order'            => $this->_emailTarget,
                        'customerFistName' => $this->_emailTarget->getCustomerFirstname(),
                        'customerLastName' => $this->_emailTarget->getCustomerLastname(),
                        'customerName'     => $this->_emailTarget->getCustomerFirstname().' '.$this->_emailTarget->getCustomerLastname(),
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
            * @var  $orderItem  \Magento\Sales\Model\Order\Item
        */
        $orderItem = $observer->getEvent()->getItem();
        
        /** @var  $order \Magento\Sales\Model\Order */
        $order     = $orderItem->getOrder();
        $quoteId   = $order->getQuoteId();
        // run the case the order is place
        $this->setType('order_is_placed');

        $this->_emailTarget = $order;
        $customerEmail      = $order->getCustomerEmail();
        $firstName          = $order->getCustomerFirstname();
        $lastName           = $order->getCustomerLastname();


        $customerId = $order->getCustomerId();
        $customer = $this->_customerFactory->create()->load($customerId);
        $mobileNumber = $customer->getData('mobile_number');

        $this->storeId      = $order->getStoreId();
        $this->_emailTarget = $order;
        $this->_emailTarget->setData('customer_email', $customerEmail);
        $this->_emailTarget->setData('mobile_number', $mobileNumber);
        $this->_emailTarget->setData('customer_firstname', $firstName);
        $this->_emailTarget->setData('customer_lastname', $lastName);
        $this->_quoteId = $quoteId;
        $this->_quote   = $this->_quoteFactory->create();
        $this->_quote   = $this->quoteRepository->get($this->_quoteId);
        $this->run();

        $orderStatus = $order->getStatus();

        $orderState = $order->getState();

        $this->setType('order_status_'.$orderState);

        $this->_emailTarget = $order;
        $customerEmail      = $order->getCustomerEmail();
        $firstName          = $order->getCustomerFirstname();
        $lastName           = $order->getCustomerLastname();

        $this->_emailTarget = $order;
        $this->_emailTarget->setData('customer_email', $customerEmail);
        $this->_emailTarget->setData('customer_firstname', $firstName);
        $this->_emailTarget->setData('customer_lastname', $lastName);
        $this->_quoteId = $quoteId;
        $this->_quote   = $this->_quoteFactory->create();
        $this->_quote   = $this->quoteRepository->get($this->_quoteId);
        $this->run();

    }//end execute()
}//end class
