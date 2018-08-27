<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Payments
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Payments\Controller\Globalpay;

use Magento\Checkout\Model\Type\Onepage;

class Start extends \Dilmah\Payments\Controller\Globalpay\AbstractGlobalPay
{
    /**
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    protected $_addressRepository;

    /** @var \Magento\Quote\Model\Quote\AddressFactory */
    protected $_quoteAddressFactory;

    /**
     * Start constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Dilmah\Payments\Model\GlobalPay\Checkout\Factory $checkoutFactory
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param \Dilmah\Payments\Logger\Logger $logger
     * @param \Dilmah\Payments\Model\Item $item
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
     * @param \Magento\Quote\Model\Quote\AddressFactory $quoteAddressFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Dilmah\Payments\Model\GlobalPay\Checkout\Factory $checkoutFactory,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Customer\Model\Url $customerUrl,
        \Dilmah\Payments\Logger\Logger $logger,
        \Dilmah\Payments\Model\Item $item,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Quote\Model\Quote\AddressFactory $quoteAddressFactory
    ) {
        $this->_addressRepository = $addressRepository;
        $this->_quoteAddressFactory = $quoteAddressFactory;

        parent::__construct(
            $context,
            $customerSession,
            $checkoutSession,
            $checkoutFactory,
            $urlHelper,
            $date,
            $item,
            $logger,
            $customerUrl
        );
    }


    /**
     * Start Express Checkout by requesting initial token and dispatching customer to PayPal
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        try {
            $this->_initCheckout();

            $customerData = $this->_customerSession->getCustomerDataObject();
            $quoteCheckoutMethod = $this->_getQuote()->getCheckoutMethod();

            if ($this->_getQuote()->isVirtual()) {
                if ($this->_getQuote()->getBillingAddress()->getCountryId() === null) {
                    $billingAddress = $this->_addressRepository->getById($customerData->getDefaultBilling());
                    $quoteBillingAddress = $this->_quoteAddressFactory->create()->importCustomerAddressData($billingAddress);
                    $this->_getQuote()->setBillingAddress($quoteBillingAddress);
                }
            }

            if ($customerData->getId()) {
                $this->_checkout->setCustomerWithAddressChange(
                    $customerData,
                    $this->_getQuote()->getBillingAddress(),
                    $this->_getQuote()->getShippingAddress()
                );
            } elseif ((!$quoteCheckoutMethod || $quoteCheckoutMethod != Onepage::METHOD_REGISTER)
                && !$this->_objectManager->get('Magento\Checkout\Helper\Data')->isAllowedGuestCheckout(
                    $this->_getQuote(),
                    $this->_getQuote()->getStoreId()
                )
            ) {
                $this->messageManager->addNoticeMessage(
                    __('To check out, please sign in with your email address.')
                );

                $this->_objectManager->get('Magento\Checkout\Helper\ExpressRedirect')->redirectLogin($this);
                $this->_customerSession->setBeforeAuthUrl($this->_url->getUrl('*/*/*', ['_current' => true]));

                return;
            }

            $this->_checkout->start();

            $this->_view->loadLayout();
            $this->_view->renderLayout();
            return;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addExceptionMessage($e, $e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('We can\'t start Global Payments Checkout.')
            );
        }

        $this->_redirect('checkout/cart');
    }
}
