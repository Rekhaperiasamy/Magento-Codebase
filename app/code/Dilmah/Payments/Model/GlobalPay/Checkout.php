<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Payments
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Payments\Model\GlobalPay;

use Magento\Customer\Api\Data\CustomerInterface as CustomerDataObject;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Magento\Quote\Model\Quote\Address;

/**
 * Wrapper that performs Global payments and Checkout communication
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Checkout
{

    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $_quote;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * Customer ID
     *
     * @var int
     */
    protected $_customerId;

    /**
     * Order
     *
     * @var \Magento\Sales\Model\Order
     */
    protected $_order;

    /**
     * Checkout data
     *
     * @var \Magento\Checkout\Helper\Data
     */
    protected $_checkoutData;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var OrderSender
     */
    protected $orderSender;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Quote\Api\CartManagementInterface
     */
    protected $quoteManagement;

    /**
     * @var \Magento\Framework\DB\TransactionFactory
     */
    protected $transactionFactory;

    /**
     * @var \Magento\Sales\Model\Order\Email\Sender\InvoiceSender
     */
    protected $invoiceSender;

    /**
     * debug mode
     *
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    protected $_debugModeEnabled = 0;

    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $paymentHelper;

    /**
     * Checkout constructor
     *
     * @param \Magento\Checkout\Helper\Data $checkoutData
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Quote\Api\CartManagementInterface $quoteManagement
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param OrderSender $orderSender
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Framework\DB\TransactionFactory $transactionFactory
     * @param \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender
     * @param \Magento\Payment\Helper\Data $paymentHelper
     * @param array $params
     * @throws \Exception
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Checkout\Helper\Data $checkoutData,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Quote\Api\CartManagementInterface $quoteManagement,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        OrderSender $orderSender,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender,
        \Magento\Payment\Helper\Data $paymentHelper,
        $params = []
    ) {
        $this->quoteManagement = $quoteManagement;
        $this->_checkoutData = $checkoutData;
        $this->_checkoutSession = $checkoutSession;
        $this->_messageManager = $messageManager;
        $this->orderSender = $orderSender;
        $this->quoteRepository = $quoteRepository;
        $this->transactionFactory = $transactionFactory;
        $this->invoiceSender = $invoiceSender;

        $this->paymentHelper = $paymentHelper;
        $method = $this->paymentHelper->getMethodInstance(
            \Dilmah\Payments\Model\GlobalPay::PAYMENT_METHOD_GLOBAL_PAY_CODE
        );

        $this->_debugModeEnabled = $method->getConfigData('debug_mode');
        $this->_customerSession = isset($params['session'])
        && $params['session'] instanceof \Magento\Customer\Model\Session ? $params['session'] : $customerSession;

        if (isset($params['quote']) && $params['quote'] instanceof \Magento\Quote\Model\Quote) {
            $this->_quote = $params['quote'];
        } else {
            throw new \Exception('Quote instance is required.');
        }
    }

    /**
     * Setter for customer with billing and shipping address changing ability
     *
     * @param CustomerDataObject $customerData
     * @param Address|null $billingAddress
     * @param Address|null $shippingAddress
     * @return $this
     */
    public function setCustomerWithAddressChange(
        CustomerDataObject $customerData,
        $billingAddress = null,
        $shippingAddress = null
    ) {
        $this->_quote->assignCustomerWithAddressChange($customerData, $billingAddress, $shippingAddress);
        $this->_customerId = $customerData->getId();
        return $this;
    }


    /**
     * Reserve order ID for specified quote
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function start()
    {
        $this->_quote->collectTotals();

        if (!$this->_quote->getGrandTotal()) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __(
                    'PayPal can\'t process orders with a zero balance due. '
                    . 'To finish your purchase, please go through the standard checkout process.'
                )
            );
        }

        $this->_quote->reserveOrderId();
        $this->quoteRepository->save($this->_quote);
    }


    /**
     * Place the order when customer returned from PayPal until this moment all quote data must be valid.
     *
     * @return void
     * @throws \Exception
     */
    public function place()
    {
        if ($this->getCheckoutMethod() == \Magento\Checkout\Model\Type\Onepage::METHOD_GUEST) {
            $this->prepareGuestQuote();
        }

        $this->ignoreAddressValidation();
        $this->_quote->collectTotals();
        $order = $this->quoteManagement->submit($this->_quote);

        if (!$order) {
            return;
        }
        $this->_order = $order;
        $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING); // set order state to 'processing'
        $order->setStatus('paid');
        $this->orderSender->send($order); // send order email
        $invoice = $this->invoice(); // create the invoice
        if ($invoice instanceof \Magento\Sales\Model\Order\Invoice) {
            $this->invoiceSender->send($invoice); // send invoice email
        }
    }

    /**
     * Make sure addresses will be saved without validation errors
     *
     * @return void
     */
    private function ignoreAddressValidation()
    {
        $this->_quote->getBillingAddress()->setShouldIgnoreValidation(true);
        if (!$this->_quote->getIsVirtual()) {
            $this->_quote->getShippingAddress()->setShouldIgnoreValidation(true);
        }
    }

    /**
     * Return order
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->_order;
    }

    /**
     * Get customer session object
     *
     * @return \Magento\Customer\Model\Session
     */
    public function getCustomerSession()
    {
        return $this->_customerSession;
    }

    /**
     * Get checkout method
     *
     * @return string
     */
    public function getCheckoutMethod()
    {
        if ($this->getCustomerSession()->isLoggedIn()) {
            return \Magento\Checkout\Model\Type\Onepage::METHOD_CUSTOMER;
        }
        if (!$this->_quote->getCheckoutMethod()) {
            if ($this->_checkoutData->isAllowedGuestCheckout($this->_quote)) {
                $this->_quote->setCheckoutMethod(\Magento\Checkout\Model\Type\Onepage::METHOD_GUEST);
            } else {
                $this->_quote->setCheckoutMethod(\Magento\Checkout\Model\Type\Onepage::METHOD_REGISTER);
            }
        }
        return $this->_quote->getCheckoutMethod();
    }


    /**
     * Prepare quote for guest checkout order submit
     *
     * @return $this
     */
    protected function prepareGuestQuote()
    {
        $quote = $this->_quote;
        $quote->setCustomerId(null)
            ->setCustomerEmail($quote->getBillingAddress()->getEmail())
            ->setCustomerIsGuest(true)
            ->setCustomerGroupId(\Magento\Customer\Model\Group::NOT_LOGGED_IN_ID);
        return $this;
    }

    /**
     * create invoice to the current order
     *
     * @return \Magento\Sales\Model\Order\Invoice
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws bool
     */
    public function invoice()
    {
        $invoice = $this->getOrder()->prepareInvoice();
        $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE);
        $invoice->register();
        /** @var \Magento\Framework\DB\Transaction $transaction */
        $transaction = $this->transactionFactory->create();
        $transaction->addObject($invoice)
            ->addObject($invoice->getOrder())
            ->save();

        return $invoice;
    }
}
