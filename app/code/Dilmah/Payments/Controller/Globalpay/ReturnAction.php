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

use Magento\Framework\Controller\ResultFactory;

/**
 * Class ReturnAction
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ReturnAction extends \Dilmah\Payments\Controller\Globalpay\AbstractGlobalPay
{
    /**
     * xml path for global pay secure secret
     */
    const XML_PATH_PAYMENT_GLOBAL_PAY_SECURE_SECRET = 'payment/global_pay/secure_secret';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * debug mode
     *
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    protected $_debugModeEnabled = 0;

    /**
     * @var \Dilmah\Payments\Logger\Logger
     */
    protected $logger;

    /**
     * ReturnAction constructor.
     *
     * @param \Magento\Framework\App\Action\Context              $context
     * @param \Magento\Customer\Model\Session                    $customerSession
     * @param \Magento\Checkout\Model\Session                    $checkoutSession
     * @param \Dilmah\Payments\Model\GlobalPay\Checkout\Factory  $checkoutFactory
     * @param \Magento\Framework\Url\Helper\Data                 $urlHelper
     * @param \Magento\Customer\Model\Url                        $customerUrl
     * @param \Dilmah\Payments\Logger\Logger                     $logger
     * @param \Dilmah\Payments\Model\Item                        $item
     * @param \Magento\Framework\Stdlib\DateTime\DateTime        $date
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
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
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $this->_debugModeEnabled = $this->scopeConfig->getValue('payment/global_pay/debug_mode', $storeScope);
        $this->logger = $logger;
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
     * Return from GlobalPay
     *
     * @return void|\Magento\Framework\Controller\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $params = $this->getRequest()->getParams();
        if (isset($params['vpc_SecureHash']) && $this->validateReceipt($params)) {
            $txnResponseCode = isset($params['vpc_TxnResponseCode']) ? $params['vpc_TxnResponseCode'] : 0;
            $this->_txnResponseCode = $txnResponseCode;
            $this->_transactionId = $params['vpc_TransactionNo'];
            $this->saveHistoryReturnInfo();
            switch ($txnResponseCode) {
                case '0':
                case '00':
                    $this->_forward('placeOrder');
                    return;
                default:
                    $params['txnResponseCode'] = $txnResponseCode;
                    $this->recordError($params);
            }
        } else {
            $params['txnResponseCode'] = __('Secure Hash Not set or Secure Hash Validation failed');
            $this->recordError($params);
        }

        return $resultRedirect->setPath('checkout/cart');
    }

    /**
     * Validate receipt
     *
     * @param array $params
     * @return bool
     */
    public function validateReceipt($params)
    {
        $secureSecret = $this->scopeConfig->getValue(
            self::XML_PATH_PAYMENT_GLOBAL_PAY_SECURE_SECRET,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $hashInput = '';
        ksort($params);
        foreach ($params as $key => $value) {
            if (
                $key != 'vpc_SecureHash'
                && $key != 'vpc_SecureHashType'
                && ((substr($key, 0, 4) == "vpc_") || (substr($key, 0, 5) == "user_"))
                && strlen($value) > 0) {
                //$md5HashData .= $value;
                $hashInput .= $key . '=' . $value . '&';
            }
        }
        $hashInput = rtrim($hashInput, '&');

        return strtoupper($params['vpc_SecureHash']) == strtoupper(
            hash_hmac(
                \Dilmah\Payments\Block\Form\GlobalPay::TEXT_SHA256,
                $hashInput,
                pack("H*", $secureSecret)
            )
        );
    }

    /**
     * record errors
     *
     * @param array $params
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function recordError($params)
    {
        if ($this->_debugModeEnabled) {
            $this->_getQuote()->getPayment()->setAdditionalInformation($params);
            $this->_getQuote()->getPayment()->save();
        }
        $this->logger->error('We can\'t process the payment approval. TxnResponseCode: ' . $params['txnResponseCode']);
        $this->messageManager->addError(
            __('We can\'t process the payment approval.')
        );
    }
}
