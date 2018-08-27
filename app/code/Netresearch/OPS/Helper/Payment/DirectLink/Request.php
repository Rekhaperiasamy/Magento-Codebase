<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     Netresearch_OPS
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Netresearch\OPS\Helper\Payment\DirectLink;

abstract class Request implements \Netresearch\OPS\Helper\Payment\DirectLink\RequestInterface
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    protected $dataHelper = null;

    protected $quoteHelper = null;

    protected $orderHelper = null;

    protected $requestHelper = null;

    protected $config = null;

    /**
     * @var \Netresearch\OPS\Model\ConfigFactory
     */
    protected $oPSConfigFactory;

    /**
     * @var \Netresearch\OPS\Helper\Payment\Request
     */
    protected $oPSPaymentRequestHelper;

    /**
     * @var \Netresearch\OPS\Helper\Data
     */
    protected $oPSHelper;

    /**
     * @var \Netresearch\OPS\Helper\Quote
     */
    protected $oPSQuoteHelper;

    /**
     * @var \Netresearch\OPS\Helper\Order
     */
    protected $oPSOrderHelper;

    /**
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Netresearch\OPS\Model\ConfigFactory $oPSConfigFactory
     * @param \Netresearch\OPS\Helper\Payment\Request $oPSPaymentRequestHelper
     * @param \Netresearch\OPS\Helper\Data $oPSHelper
     * @param \Netresearch\OPS\Helper\Quote $oPSQuoteHelper
     * @param \Netresearch\OPS\Helper\Order $oPSOrderHelper
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Netresearch\OPS\Model\ConfigFactory $oPSConfigFactory,
        \Netresearch\OPS\Helper\Payment\Request $oPSPaymentRequestHelper,
        \Netresearch\OPS\Helper\Data $oPSHelper,
        \Netresearch\OPS\Helper\Quote $oPSQuoteHelper,
        \Netresearch\OPS\Helper\Order $oPSOrderHelper
    ) {
    
        $this->customerSession = $customerSession;
        $this->oPSConfigFactory = $oPSConfigFactory;
        $this->oPSPaymentRequestHelper = $oPSPaymentRequestHelper;
        $this->oPSHelper = $oPSHelper;
        $this->oPSQuoteHelper = $oPSQuoteHelper;
        $this->oPSOrderHelper = $oPSOrderHelper;
    }

    /**
     * @param null $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return \Netresearch\OPS\Model\Config
     */
    public function getConfig()
    {
        if (null === $this->config) {
            $this->config = $this->oPSConfigFactory->create();
        }
        return $this->config;
    }

    public function setRequestHelper(\Netresearch\OPS\Helper\Payment\Request $requestHelper)
    {
        $this->requestHelper = $requestHelper;
    }

    /**
     * @return \Netresearch\OPS\Helper\Payment\Request
     */
    public function getRequestHelper()
    {
        if (null === $this->requestHelper) {
            $this->requestHelper = $this->oPSPaymentRequestHelper;
            $this->requestHelper->setConfig($this->getConfig());
        }

        return $this->requestHelper;
    }

    /**
     * sets the data helper
     *
     * @param \Netresearch\OPS\Helper\Data $dataHelper
     */
    public function setDataHelper(\Netresearch\OPS\Helper\Data $dataHelper)
    {
        $this->dataHelper = $dataHelper;
    }

    /**
     * gets the data helper
     *
     * @return \Netresearch\OPS\Helper\Data
     */
    public function getDataHelper()
    {
        if (null === $this->dataHelper) {
            $this->dataHelper = $this->oPSHelper;
        }

        return $this->dataHelper;
    }

    /**
     * sets the quote helper
     *
     * @param \Netresearch\OPS\Helper\Quote $quoteHelper
     */
    public function setQuoteHelper(\Netresearch\OPS\Helper\Quote $quoteHelper)
    {
        $this->quoteHelper = $quoteHelper;
    }

    /**
     * gets the quote helper
     *
     * @return \Netresearch\OPS\Helper\Quote
     */
    public function getQuoteHelper()
    {
        if (null === $this->quoteHelper) {
            $this->quoteHelper = $this->oPSQuoteHelper;
        }

        return $this->quoteHelper;
    }

    /**
     * sets the order helper
     *
     * @param \Netresearch\OPS\Helper\Order $orderHelper
     */
    public function setOrderHelper(\Netresearch\OPS\Helper\Order $orderHelper)
    {
        $this->orderHelper = $orderHelper;
    }

    /**
     * gets the order helper
     *
     * @return \Netresearch\OPS\Helper\Order
     */
    public function getOrderHelper()
    {
        if (null === $this->orderHelper) {
            $this->orderHelper = $this->oPSOrderHelper;
        }

        return $this->orderHelper;
    }

    /**
     * extracts the parameter for the direct link request from the quote,
     * order and, optionally from existing request params
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Sales\Model\Order $order
     * @param array $requestParams
     *
     * @return array - the parameters for the direct link request
     */
    public function getDirectLinkRequestParams(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Sales\Model\Order $order,
        $requestParams = []
    ) {
    
        $billingAddress = $order->getBillingAddress();
        $shippingAddress = $this->getShippingAddress($order, $billingAddress);
        $requestParams = $this->getBaseRequestParams($quote, $order, $billingAddress);
        $requestParams = array_merge($requestParams, $this->getPaymentSpecificParams($order));

        if ($this->getConfig()->canSubmitExtraParameter($quote->getStoreId())) {
            $shipToParams = $this->getRequestHelper()->extractShipToParameters($shippingAddress, $quote);
            $shipToParams = $this->decodeParamsForDirectLinkCall($shipToParams);
            $requestParams = array_merge($requestParams, $shipToParams);
        }

        $requestParams = $this->addCustomerSpecificParams($requestParams);
        $requestParams = $this->addParamsForAdminPayments($requestParams);

        return $requestParams;
    }

    /**
     * specail handling like validation and so on for admin payments
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param array $requestParams
     *
     * @return mixed
     */
    abstract public function handleAdminPayment(\Magento\Quote\Model\Quote $quote, $requestParams);

    /**
     * extracts payment specific payment parameters
     *
     * @param \Magento\Quote\Model\Quote $order
     *
     * @return array
     */
    abstract protected function getPaymentSpecificParams(\Magento\Sales\Model\Order $order);

    /**
     * gets the shipping address if there is one, otherwise the billing address is used as shipping address
     *
     * @param $order
     * @param $billingAddress
     *
     * @return mixed
     */
    protected function getShippingAddress(\Magento\Sales\Model\Order $order, $billingAddress)
    {
        $shippingAddress = $order->getShippingAddress();
        if (null === $shippingAddress || false === $shippingAddress) {
            $shippingAddress = $billingAddress;
        }
        return $shippingAddress;
    }

    /**
     * utf8 decode for direct link calls
     *
     * @param array $requestParams
     *
     * @return array - the decoded array
     */
    protected function decodeParamsForDirectLinkCall(array $requestParams)
    {
        foreach ($requestParams as $key => $value) {
            $requestParams[$key] = utf8_decode($value);
        }
        return $requestParams;
    }

    /**
     * @param $requestParams
     *
     * @return mixed
     */
    protected function addCustomerSpecificParams($requestParams)
    {
        if ($this->customerSession->isLoggedIn()) {
            $requestParams['CUID'] = $this->customerSession->getCustomerId();
        }
        return $requestParams;
    }

    /**
     * @param $requestParams
     *
     * @return mixed
     */
    protected function addParamsForAdminPayments($requestParams)
    {
        if ($this->getDataHelper()->isAdminSession()) {
            $requestParams['ECI'] = \Netresearch\OPS\Model\Eci\Values::MANUALLY_KEYED_FROM_MOTO;
            $requestParams['REMOTE_ADDR'] = 'NONE';
        }
        return $requestParams;
    }

    /**
     * @param $quote
     * @param $order
     * @param $billingAddress
     *
     * @return array
     */
    protected function getBaseRequestParams($quote, $order, $billingAddress)
    {
        $merchantRef = $this->getOrderHelper()->getOpsOrderId($order, $this->canUseOrderId($order->getPayment()));
        $requestParams = [
            'AMOUNT' => $this->getDataHelper()->getAmount($order->getBaseGrandTotal()),
            'CURRENCY' => $this->getQuoteHelper()->getQuoteCurrency($quote),
            'OPERATION' => $this->getQuoteHelper()->getPaymentAction($order),
            'ORDERID' => $merchantRef,
            'ORIG' => $this->getDataHelper()->getModuleVersionString(),
            'EMAIL' => $order->getCustomerEmail(),
            'REMOTE_ADDR' => $quote->getRemoteIp(),
            'RTIMEOUT' => $this->getConfig()->getTransActionTimeout()
        ];

        $ownerParams = $this->getOwnerParams($quote, $billingAddress, $requestParams);
        $requestParams = array_merge($requestParams, $ownerParams);
        $requestParams['ADDMATCH'] = $this->getOrderHelper()->checkIfAddressesAreSame($order);

        return $requestParams;
    }

    /**
     * @param $quote
     * @param $billingAddress
     *
     * @return array
     */
    protected function getOwnerParams($quote, $billingAddress)
    {
        $ownerParams = $this->getRequestHelper()->getOwnerParams($billingAddress, $quote);
        if (array_key_exists('OWNERADDRESS', $ownerParams) && array_key_exists('OWNERTOWN', $ownerParams)) {
            $ownerAddrParams = $this->decodeParamsForDirectLinkCall(
                ['OWNERADDRESS' => $ownerParams['OWNERADDRESS'], 'OWNERTOWN' => $ownerParams['OWNERTOWN']]
            );
            $ownerParams = array_merge($ownerParams, $ownerAddrParams);
        }

        return $ownerParams;
    }

    /**
     * @param \Magento\Framework\DataObject $payment
     * @return bool
     */
    public function canUseOrderId(\Magento\Framework\DataObject $payment)
    {
        $methodInstance = $payment->getMethodInstance();
        $inlineOrderReference = $this->getConfig()->getInlineOrderReference();
        return
            ($inlineOrderReference == \Netresearch\OPS\Model\Payment\PaymentAbstract::REFERENCE_ORDER_ID
                || $inlineOrderReference == null)
            && $methodInstance instanceof \Netresearch\OPS\Model\Payment\DirectLink;
    }
}
