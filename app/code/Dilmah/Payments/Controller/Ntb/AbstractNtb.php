<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Dilmah\Payments\Controller\Ntb;

use Magento\Checkout\Controller\Express\RedirectLoginInterface;
use Magento\Framework\App\Action\Action as AppAction;
use Magento\Framework\Webapi\Exception;

/**
 * Abstract Express Checkout Controller
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class AbstractNtb extends AppAction implements RedirectLoginInterface
{
    /**
     * @var \Dilmah\Payments\Model\Ntb\Checkout
     */
    protected $_checkout;

    /**
     * Internal cache of checkout models
     *
     * @var array
     */
    protected $_checkoutTypes = [];

    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $_quote = false;

    /**
     * Checkout mode type
     *
     * @var string
     */
    protected $_checkoutType = 'Dilmah\Payments\Model\Ntb\Checkout';

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Paypal\Model\Express\Checkout\Factory
     */
    protected $_checkoutFactory;

    /**
     * @var \Magento\Framework\Url\Helper
     */
    protected $_urlHelper;

    /**
     * @var \Magento\Customer\Model\Url
     */
    protected $_customerUrl;

    /**
     * @var \Dilmah\Payments\Model\Item
     */
    protected $item;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Dilmah\Payments\Logger\Logger
     */
    protected $logger;

    /**
     * AbstractNtb constructor.
     *
     * @param \Magento\Framework\App\Action\Context       $context
     * @param \Magento\Customer\Model\Session             $customerSession
     * @param \Magento\Checkout\Model\Session             $checkoutSession
     * @param \Dilmah\Payments\Model\Ntb\Checkout\Factory $checkoutFactory
     * @param \Magento\Framework\Url\Helper\Data          $urlHelper
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Dilmah\Payments\Model\Item                 $item
     * @param \Dilmah\Payments\Logger\Logger              $logger
     * @param \Magento\Customer\Model\Url                 $customerUrl
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Dilmah\Payments\Model\Ntb\Checkout\Factory $checkoutFactory,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Dilmah\Payments\Model\Item $item,
        \Dilmah\Payments\Logger\Logger $logger,
        \Magento\Customer\Model\Url $customerUrl
    ) {
        $this->_customerSession = $customerSession;
        $this->_checkoutSession = $checkoutSession;
        $this->_checkoutFactory = $checkoutFactory;
        $this->_urlHelper = $urlHelper;
        $this->date = $date;
        $this->item = $item;
        $this->logger = $logger;
        $this->_customerUrl = $customerUrl;
        parent::__construct($context);
    }

    /**
     * Instantiate quote and checkout
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _initCheckout()
    {
        $quote = $this->_getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
            $this->getResponse()->setStatusHeader(403, '1.1', 'Forbidden');
            throw new \Magento\Framework\Exception\LocalizedException(
                __('We can\'t initialize American Express Checkout.')
            );
        }
        if (!isset($this->_checkoutTypes[$this->_checkoutType])) {
            $parameters = [
                'params' => [
                    'quote' => $quote,
                ],
            ];
            $this->_checkoutTypes[$this->_checkoutType] = $this->_checkoutFactory
                ->create($this->_checkoutType, $parameters);
        }
        $this->_checkout = $this->_checkoutTypes[$this->_checkoutType];
    }

    /**
     * Return checkout session object
     *
     * @return \Magento\Checkout\Model\Session
     */
    protected function _getCheckoutSession()
    {
        return $this->_checkoutSession;
    }

    /**
     * Return checkout quote object
     *
     * @return \Magento\Quote\Model\Quote
     */
    protected function _getQuote()
    {
        if (!$this->_quote) {
            $this->_quote = $this->_getCheckoutSession()->getQuote();
        }
        return $this->_quote;
    }

    /**
     * Returns before_auth_url redirect parameter for customer session
     *
     * @return null
     */
    public function getCustomerBeforeAuthUrl()
    {
        return;
    }

    /**
     * Returns a list of action flags [flag_key] => boolean
     *
     * @return array
     */
    public function getActionFlagList()
    {
        return [];
    }

    /**
     * Returns login url parameter for redirect
     *
     * @return string
     */
    public function getLoginUrl()
    {
        return $this->_customerUrl->getLoginUrl();
    }

    /**
     * Returns action name which requires redirect
     *
     * @return string
     */
    public function getRedirectActionName()
    {
        return 'start';
    }

    /**
     * Redirect to login page
     *
     * @return void
     */
    public function redirectLogin()
    {
        $this->_actionFlag->set('', 'no-dispatch', true);
        $this->_customerSession->setBeforeAuthUrl($this->_redirect->getRefererUrl());
        $this->getResponse()->setRedirect(
            $this->_urlHelper->addRequestParam($this->_customerUrl->getLoginUrl(), ['context' => 'checkout'])
        );
    }

    /**
     * save payment history for ntb
     *
     * @return void
     */
    public function savePaymentHistory()
    {
        $quote = $this->_getQuote();
        $data['order_num'] = $quote->getReservedOrderId();
        $data['payment_method'] = \Dilmah\Payments\Model\Ntb::PAYMENT_METHOD_NTB_CODE;
        $grandTotal = $quote->getGrandTotal();
        $grandTotal = number_format($grandTotal, 2);
        $data['amount'] = $grandTotal;
        $data['time_started'] = $this->date->gmtDate();

        try {
            $this->item->setData($data);
            $history = $this->item->save();
            if ($history->getId()) {
                $this->_checkoutSession->setLastPaymentHistoryId($history->getId());
            }
        } catch (Exception $e) {
            $this->logger->error('SAVE PAYMENT HISTORY - TO NTB: ' . $e->getMessage());
        }
    }

    /**
     * save payment info when returning from ntb
     *
     * @return void
     */
    public function saveHistoryReturnInfo()
    {
        $quote = $this->_getQuote();
        $data['status'] = $this->_checkout->getTransactionStatus();
        $data['transaction_id'] = $this->_checkout->getIpgTxnId();
        $data['time_completed'] = $this->date->gmtDate();
        $lastPaymentHistoryId = !empty($this->_getCheckoutSession()->getLastPaymentHistoryId()) ?
            $this->_getCheckoutSession()->getLastPaymentHistoryId():
            null;
        if (!$lastPaymentHistoryId) {
            /*
             * if for some reason the last payment history id is not available, we need to fill the other fields
             *  as well to recognize the order since it will be saved as a new record.
             */
            $data['order_num'] = $quote->getReservedOrderId();
            $data['payment_method'] = \Dilmah\Payments\Model\Ntb::PAYMENT_METHOD_NTB_CODE;
            $data['amount'] = $quote->getBaseGrandTotal();
            $data['time_started'] = $this->date->gmtDate();
        }
        $data['id'] = $lastPaymentHistoryId;

        try {
            $this->item->setData($data);
            $this->item->save();
        } catch (Exception $e) {
            $this->logger->error('SAVE PAYMENT HISTORY - FROM NTB: ' . $e->getMessage());
            $this->_getCheckoutSession()->unsLastPaymentHistoryId();
        }
    }
}
