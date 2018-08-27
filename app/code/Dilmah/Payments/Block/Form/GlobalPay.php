<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Payments
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Payments\Block\Form;

use Magento\Checkout\Model\Session;
use Magento\Framework\Webapi\Exception;

/**
 * Block for Global Payment method form
 */
class GlobalPay extends \Dilmah\Payments\Block\Form\AbstractInstruction
{
    /**
     * VPC version
     */
    const VPC_VERSION = '1';

    /**
     * command pay
     */
    const COMMAND_PAY = 'pay';

    /**
     * Secure Hash Type
     */
    const TEXT_SHA256 = 'SHA256';

    /**
     * @var string
     */
    protected $gatewayUrl;

    /**
     * @var string
     */
    protected $merchantId;

    /**
     * @var string
     */
    protected $accessCode;

    /**
     * @var string
     */
    protected $secureSecret;

    /**
     * Onepage model
     *
     * @var \Magento\Checkout\Model\Type\Onepage
     */
    protected $onepage;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $paymentHelper;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Dilmah\Payments\Model\Item
     */
    protected $item;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * Globalpay template
     *
     * @var string
     */
    protected $_template = 'form/global_pay.phtml';

    /**
     * GlobalPay constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Checkout\Model\Type\Onepage             $onepage
     * @param \Magento\Payment\Helper\Data                     $paymentHelper
     * @param \Magento\Framework\Stdlib\DateTime\DateTime      $date
     * @param \Dilmah\Payments\Model\Item                      $item
     * @param Session                                          $checkoutSession
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Type\Onepage $onepage,
        \Magento\Payment\Helper\Data $paymentHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Dilmah\Payments\Model\Item $item,
        Session $checkoutSession,
        array $data
    ) {
        $this->onepage = $onepage;
        $this->_urlBuilder = $context->getUrlBuilder();
        $this->paymentHelper = $paymentHelper;
        $this->date = $date;
        $this->item = $item;
        $this->_checkoutSession = $checkoutSession;
        parent::__construct($context, $data);
    }

    /**
     * Get gateway url from config
     *
     * @return null|string
     */
    public function getGatewayUrl()
    {
        if ($this->gatewayUrl === null) {
            /** @var \Magento\Payment\Model\Method\AbstractMethod $method */
            $method = $this->getMethod();
            $this->gatewayUrl = $method->getConfigData('gateway_url');
        }
        return $this->gatewayUrl;
    }

    /**
     * Get merchant id from config
     *
     * @return null|string
     */
    public function getMerchantId()
    {
        if ($this->merchantId === null) {
            /** @var \Magento\Payment\Model\Method\AbstractMethod $method */
            $method = $this->getMethod();
            $this->merchantId = $method->getConfigData('merchant_id');
        }
        return $this->merchantId;
    }

    /**
     * Get access code from config
     *
     * @return null|string
     */
    public function getAccessCode()
    {
        if ($this->accessCode === null) {
            /** @var \Magento\Payment\Model\Method\AbstractMethod $method */
            $method = $this->getMethod();
            $this->accessCode = $method->getConfigData('access_code');
        }
        return $this->accessCode;
    }

    /**
     * Get secure secret from config
     *
     * @return null|string
     */
    public function getSecureSecret()
    {
        if ($this->secureSecret === null) {
            /** @var \Magento\Payment\Model\Method\AbstractMethod $method */
            $method = $this->getMethod();
            $this->secureSecret = $method->getConfigData('secure_secret');
        }
        return $this->secureSecret;
    }

    /**
     * get the payment return url
     *
     * @return string
     */
    public function getReturnUrl()
    {
        return $this->_urlBuilder->getUrl('dilmah_payments/globalPay/return');
    }

    /**
     * generate hash
     *
     * @param array $formFields
     * @return string
     */
    public function getHash($formFields)
    {
        ksort($formFields);
        $hashInput = '';
        //$md5HashData = $this->getSecureSecret();
        foreach ($formFields as $key => $value) {
            if (strlen($value) > 0) {
                //$md5HashData .= $value;
                $hashInput .= $key . '=' . $value . '&';
            }
        }
        //return strtoupper(md5($md5HashData));
        $hashInput = rtrim($hashInput, '&');
        return strtoupper(hash_hmac(self::TEXT_SHA256, $hashInput, pack("H*", $this->getSecureSecret())));
    }

    /**
     * Get the data for form building
     *
     * @return array
     */
    public function getFormData()
    {
        $this->setMethod(
            $this->paymentHelper->getMethodInstance(
                \Dilmah\Payments\Model\GlobalPay::PAYMENT_METHOD_GLOBAL_PAY_CODE
            )
        );
        $quote = $this->onepage->getQuote();
        $formFields = [];
        $formFields['vpc_Version'] = self::VPC_VERSION;
        $formFields['vpc_Command'] = self::COMMAND_PAY;
        $formFields['vpc_AccessCode'] = substr($this->getAccessCode(), 0, 8);
        $formFields['vpc_MerchTxnRef'] = $quote->getReservedOrderId();
        $formFields['vpc_Merchant'] = substr($this->getMerchantId(), 0, 16);
        $formFields['vpc_OrderInfo'] = sprintf(
            __('QuoteID:%s Order:%s'),
            $quote->getId(),
            $quote->getReservedOrderId()
        );

        // For Migs: always use BaseGrandTotal
        // Currency is set at Merchant account level
        $amount = round($this->onepage->getQuote()->getBaseGrandTotal() * 100);
        $formFields['vpc_Amount'] = $amount;
        $formFields['vpc_Locale'] = 'en';
        $formFields['vpc_ReturnURL'] = $this->getReturnUrl();

        // vpc_SecureHash must come last to exclude itself
        $formFields['vpc_SecureHash'] = $this->getHash($formFields);
        $formFields['vpc_SecureHashType'] = self::TEXT_SHA256;

        return $formFields;
    }

    /**
     * save payment history for global pay
     *
     * @param array $formData
     * @return void
     */
    public function savePaymentHistory($formData)
    {
        $quote = $this->onepage->getQuote();
        $data['order_num'] = $quote->getReservedOrderId();
        $data['payment_method'] = \Dilmah\Payments\Model\GlobalPay::PAYMENT_METHOD_GLOBAL_PAY_CODE;
        $data['amount'] = isset($formData['vpc_Amount'])?(float)$formData['vpc_Amount']/100:null;
        $data['time_started'] = $this->date->gmtDate();

        try {
            $this->item->setData($data);
            $history = $this->item->save();
            if ($history->getId()) {
                $this->_checkoutSession->setLastPaymentHistoryId($history->getId());
            }
        } catch (Exception $e) {
            $this->_logger->error('SAVE PAYMENT HISTORY - TO GLOBALPAY: ' . $e->getMessage());
        }

    }
}
