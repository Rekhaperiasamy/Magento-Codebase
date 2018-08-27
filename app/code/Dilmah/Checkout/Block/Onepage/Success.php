<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Checkout
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
namespace Dilmah\Checkout\Block\Onepage;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Sales\Helper\Guest;

/**
 * Class Success
 *
 * @package Dilmah\Checkout\Block\Onepage
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Success extends \Magento\Checkout\Block\Onepage\Success
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $_customerRepository;

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $cookieManager;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    protected $cookieMetadataFactory;

    /**
     * @var \Dilmah\Checkout\Helper\Data
     */
    protected $helper;

    /**
     * Success constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
     * @param \Dilmah\Checkout\Helper\Data $helper
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Dilmah\Checkout\Helper\Data $helper,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->_customerRepository = $customerRepository;
        $this->_storeManager = $context->getStoreManager();
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->helper = $helper;
        parent::__construct($context, $checkoutSession, $orderConfig, $httpContext);
    }

    /**
     * Initialize data and prepare it for output
     *
     * @return string
     */
    protected function _beforeToHtml()
    {
        /* below actions were taken to display the rewards points
         that the logged-in customer will get once the order is placed */
        $quote = $this->_checkoutSession->getQuote();
        $lastOrder = $this->_checkoutSession->getLastRealOrder();
        $address = $quote->getIsVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress();
        $isVirtualProductMessageAvailable = $this->isVirtualProductAvailable($lastOrder);
        if ($isVirtualProductMessageAvailable) {
            $lastOrder->setIsVirtualProductMessageAvailable($isVirtualProductMessageAvailable);
        }

        $quote->setBaseGrandTotal($lastOrder->getBaseGrandTotal());
        $address->setBaseShippingAmount($lastOrder->getBaseShippingAmount());
        $address->setBaseShippingTaxAmount($lastOrder->getBaseShippingTaxAmount());

        return parent::_beforeToHtml();
    }

    /**
     * check if virtual products are available in cart
     *
     * @param \Magento\Sales\Model\Order $order
     * @return bool|null
     */
    public function isVirtualProductAvailable($order)
    {
        return $this->helper->isVirtualProductAvailable($order);
    }

    /**
     * get virtual product message
     * 
     * @param \Magento\Sales\Model\Order $order
     * @return \Magento\Framework\Phrase|null
     */
    public function getVirtualProductMessage(\Magento\Sales\Model\Order $order)
    {
        return $this->helper->getVirtualProductMessage($order);
    }

    /**
     * Retrieve checkout session
     *
     * @return \Magento\Checkout\Model\Session
     */
    public function getCheckoutSession()
    {
        return $this->_checkoutSession;
    }

    /**
     * check is the customer logged in
     *
     * @return bool
     */
    public function isCustomerLoggedIn()
    {
        return (bool)$this->_checkoutSession->getQuote()->getCustomer()->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function isEmailAvailable($customerEmail, $websiteId = null)
    {
        try {
            if ($websiteId === null) {
                $websiteId = $this->_storeManager->getStore()->getWebsiteId();
            }
            $customer = $this->_customerRepository->get($customerEmail, $websiteId);
            $this->_checkoutSession->setCustomerId($customer->getId());
            return true;
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }

    /**
     * Checks if the email is registered
     *
     * @return bool
     */
    public function isEmailRegistered()
    {
        return $this->isEmailAvailable($this->getEmailAddress());

    }

    /**
     * Retrieve order linking url
     *
     * @return string
     */
    public function getLinkOrderUrl()
    {
        return $this->getUrl('dil_checkout/order/link');
    }

    /**
     * Retrieve current email address
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getEmailAddress()
    {
        return $this->_checkoutSession->getLastRealOrder()->getCustomerEmail();
    }

    /**
     * set guest print cookie
     *
     * @param \Magento\Sales\Model\Order $order
     * @return void
     */
    public function setGuestPrintCookie($order)
    {
        if ($order instanceof \Magento\Sales\Model\Order) {
            $toCookie = base64_encode($order->getProtectCode() . ':' . $order->getIncrementId());
            $metadata = $this->cookieMetadataFactory->createPublicCookieMetadata()
                ->setPath(Guest::COOKIE_PATH)
                ->setHttpOnly(false);
            $this->cookieManager->setPublicCookie(Guest::COOKIE_NAME, $toCookie, $metadata);
        }
    }
}
