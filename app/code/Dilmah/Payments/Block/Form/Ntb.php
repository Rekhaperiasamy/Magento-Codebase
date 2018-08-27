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

use Magento\Framework\View\Element\Template\Context;
use Magento\Checkout\Model\Type\Onepage;
use Magento\Checkout\Model\Session;
use Magento\Framework\Webapi\Exception;

/**
 * Block for NTB payment method form
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Ntb extends \Dilmah\Payments\Block\Form\AbstractInstruction
{
    /**
     * Value for SaleTxn
     */
    const SALETXN = 'SaleTxn';

    /**
     * Value for SaleMerchUpdated
     */
    const SALEMERCHUPDATED = 'SaleMerchUpdated';

    /**
     * Value for SaleTxnVerify
     */
    const SALETXNVERIFY = 'SaleTxnVerify';

    /**
     * Merchant ID
     */
    const XML_PATH_PAYMENT_NTB_MERCHANT_ID = 'payment/ntb/merchant_id';

    /**
     * Client IP
     */
    const XML_PATH_PAYMENT_NTB_CLIENT_IP = 'payment/ntb/client_ip';

    /**
     * Client Port
     */
    const XML_PATH_PAYMENT_NTB_CLIENT_PORT = 'payment/ntb/client_port';

    /**
     * Socket Timeout
     */
    const XML_PATH_PAYMENT_NTB_SOCKET_TIMEOUT = 'payment/ntb/socket_timeout';

    /**
     *Server Url
     */
    const XML_PATH_PAYMENT_NTB_SERVER_URL = 'payment/ntb/server_url';

    /**
     * Return Url
     */
    const XML_PATH_PAYMENT_NTB_RETURN_URL = 'payment/ntb/return_url';

    /**
     * Merchant var1
     */
    const XML_PATH_PAYMENT_NTB_MERCHANT_VAR1 = 'payment/ntb/merchant_var1';

    /**
     * Merchant var2
     */
    const XML_PATH_PAYMENT_NTB_MERCHANT_VAR2 = 'payment/ntb/merchant_var2';

    /**
     * Merchant var3
     */
    const XML_PATH_PAYMENT_NTB_MERCHANT_VAR3 = 'payment/ntb/merchant_var3';

    /**
     * Merchant var4
     */
    const XML_PATH_PAYMENT_NTB_MERCHANT_VAR4 = 'payment/ntb/merchant_var4';

    /**
     * NTB template
     *
     * @var string
     */
    protected $_template = 'form/ntb.phtml';

    /**
     * Merchant Id
     *
     * @var string
     */
    protected $_merchantId = null;

    /**
     * IPG Client Ip address
     *
     * @var string
     */
    protected $_ipgClientIp = null;

    /**
     * Socket Timeout
     *
     * @var string
     */
    protected $_socketTimeout = null;

    /**
     * IPG Client Port
     *
     * @var string
     */
    protected $_ipgClientPort = null;

    /**
     * Error No
     *
     * @var string
     */
    protected $_errNo = null;

    /**
     * Error String
     *
     * @var string
     */
    protected $_errString = '';

    /**
     * Socket Timeout
     *
     * @var string
     */
    protected $_ipgSocket = '';

    /**
     * Error message
     *
     * @var string
     */
    protected $_errorMessage = '';

    /**
     * Invoice Sent Error
     *
     * @var string
     */
    protected $_invoiceSentError = '';

    /**
     * Encryption error
     *
     * @var string
     */
    protected $_encryptionErr = '';

    /**
     * Invoice
     *
     * @var string
     */
    protected $_invoice = '';

    /**
     * Encrypted invoice
     *
     * @var string
     */
    protected $_encryptedInvoice = '';

    /**
     * IPG Server Url
     *
     * @var string
     */
    protected $_ipgServerUrl = '';

    /**
     * Return Url
     *
     * @var string
     */
    protected $_returnUrl = '';

    /**
     * Return Url
     *
     * @var string
     */
    protected $_merchantVar1 = '';

    /**
     * Return Url
     *
     * @var string
     */
    protected $_merchantVar2 = '';

    /**
     * Return Url
     *
     * @var string
     */
    protected $_merchantVar3 = '';

    /**
     * Return Url
     *
     * @var string
     */
    protected $_merchantVar4 = '';

    /**
     * Onepage model
     *
     * @var \Magento\Checkout\Model\Type\Onepage
     */
    protected $_onepage;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $paymentHelper;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var \Dilmah\Checkout\Logger\Logger
     */
    protected $logger;

    /**
     * debug mode
     *
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    protected $_debugModeEnabled = 0;

    /**
     * Ntb constructor.
     *
     * @param Context                        $context
     * @param Onepage                        $onepage
     * @param \Magento\Payment\Helper\Data   $paymentHelper
     * @param \Dilmah\Payments\Logger\Logger $logger
     * @param array                          $data
     */
    public function __construct(
        Context $context,
        Onepage $onepage,
        \Magento\Payment\Helper\Data $paymentHelper,
        \Dilmah\Payments\Logger\Logger $logger,
        array $data
    ) {
        $this->_onepage = $onepage;
        $this->scopeConfig = $context->getScopeConfig();
        $this->paymentHelper = $paymentHelper;
        $this->_urlBuilder = $context->getUrlBuilder();
        $this->logger = $logger;

        $method = $this->paymentHelper->getMethodInstance(\Dilmah\Payments\Model\Ntb::PAYMENT_METHOD_NTB_CODE);

        $this->_merchantId = $method->getConfigData('merchant_id');
        $this->_ipgClientIp = $method->getConfigData('client_ip');
        $this->_ipgClientPort = $method->getConfigData('client_port');
        $this->_socketTimeout = $method->getConfigData('socket_timeout');
        $this->_ipgServerUrl = $method->getConfigData('server_url');
        $this->_returnUrl = $this->getReturnUrl();
        $this->_debugModeEnabled = $method->getConfigData('debug_mode');
        $this->_merchantVar1 = $method->getConfigData('merchant_var1');
        $this->_merchantVar2 = $method->getConfigData('merchant_var2');
        $this->_merchantVar3 = $method->getConfigData('merchant_var3');
        $this->_merchantVar4 = $method->getConfigData('merchant_var4');

        parent::__construct($context, $data);
    }

    /**
     * generate request xml
     *
     * @param string $action
     * @return string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function generateRequestXml($action)
    {
        $invoiceXml = '';
        switch ($action) {
            case self::SALETXN:
                $invoiceXml = "<req>" .
                    "<mer_id>" . $this->_merchantId . "</mer_id>" .
                    "<mer_txn_id>" . $this->_onepage->getQuote()->getReservedOrderId() . "</mer_txn_id>" .
                    "<action>" . $action . "</action>";

                $grandTotal = $this->_onepage->getQuote()->getGrandTotal();
                if (!empty($grandTotal)) {
                    $grandTotal = number_format($grandTotal, 2);
                }
                $invoiceXml .= "<txn_amt>" . $grandTotal . "</txn_amt>" .
                    "<cur>" . $this->_onepage->getQuote()->getStoreCurrencyCode() . "</cur>" .
                    "<lang>en</lang>";

                if ($this->_ipgServerUrl != "") {
                    $invoiceXml .= "<ipg_server_url>" . $this->_ipgServerUrl . "</ipg_server_url>";
                }

                if ($this->_returnUrl != "") {
                    $invoiceXml .= "<ret_url>" . $this->_returnUrl . "</ret_url>";
                }

                if ($this->_merchantVar1 != "") {
                    $invoiceXml .= "<mer_var1>" . $this->_merchantVar1 . "</mer_var1>";
                }

                if ($this->_merchantVar2 != "") {
                    $invoiceXml .= "<mer_var2>" . $this->_merchantVar2 . "</mer_var2>";
                }

                if ($this->_merchantVar3 != "") {
                    $invoiceXml .= "<mer_var3>" . $this->_merchantVar3 . "</mer_var3>";
                }

                if ($this->_merchantVar4 != "") {
                    $invoiceXml .= "<mer_var4>" . $this->_merchantVar4 . "</mer_var4>";
                }
                $invoiceXml .= "</req>";
                break;

            case self::SALEMERCHUPDATED:
                break;

            case self::SALETXNVERIFY:
                break;
        }
        return $invoiceXml;
    }

    /**
     * Is debug mode enabled
     *
     * @return \Magento\Config\Model\Config\Source\Yesno|mixed
     */
    public function isDebugEnabled()
    {
        return $this->_debugModeEnabled;
    }

    /**
     * create socket connection
     *
     * @return bool|resource
     */
    public function createSocketConnection()
    {
        if ($this->_ipgClientIp != "" && $this->_ipgClientPort != "") {
            return fsockopen(
                $this->_ipgClientIp,
                $this->_ipgClientPort,
                $this->_errNo,
                $this->_errString,
                $this->_socketTimeout
            );
        } else {
            $this->setSocketCreationErr(true);
            return false;
        }
    } 


     /* public function createSocketConnection()
    {

          
       echo 'sss'; die('sdfsd');
       /* if ($this->_ipgClientIp != "" && $this->_ipgClientPort != "") {
            $fp = @fsockopen(
                $this->_ipgClientIp,
                $this->_ipgClientPort,
                $this->_errNo,
                $this->_errString,
                $this->_socketTimeout
            );

                if($fp){ echo 'online'; else echo 'offline'; } die('test');
        } else {
            $this->setSocketCreationErr(true);
            return false;
        } 
    }*/



    /**
     * send invoice to client
     *
     * @param resource $ipgSocket
     * @param string $invoiceXml
     * @return bool
     */
    public function sendInvoiceToClient($ipgSocket, $invoiceXml)
    {
        socket_set_timeout($ipgSocket, $this->_socketTimeout);

        // Write the invoice to socket connection
        if (fwrite($ipgSocket, $invoiceXml) === false) {
            $this->setInvoiceSentError(true);
            return false;
        }
    }

    /**
     * generate encrypted invoice
     *
     * @param resource $ipgSocket
     * @return string
     */
    public function generateEncryptedInvoice($ipgSocket)
    {
        $encryptedInvoice = '';
        while (!feof($ipgSocket)) {
            $encryptedInvoice .= fread($ipgSocket, 8192);
        }
        return $encryptedInvoice;
    }

    /**
     * close socket connection
     *
     * @param resource $ipgSocket
     * @return void
     */
    public function closeSocketConnection($ipgSocket)
    {
        fclose($ipgSocket);
    }

    /**
     * has encryption errors
     *
     * @param string $encryptedInvoice
     * @return array
     */
    public function hasEncryptionErrors($encryptedInvoice)
    {
        $_errors = [];
        if (!(strpos($encryptedInvoice, '<error_code>') === false &&
            strpos($encryptedInvoice, '</error_code>') === false &&
            strpos($encryptedInvoice, '<error_msg>') === false &&
            strpos($encryptedInvoice, '</error_msg>') === false)
        ) {
            $this->setEncErrorNo(
                substr(
                    $encryptedInvoice,
                    (strpos($encryptedInvoice, '<error_code>') + 12),
                    (strpos($encryptedInvoice, '</error_code>') - (strpos($encryptedInvoice, '<error_code>') + 12))
                )
            );
            $this->setEncErrorMsg(
                substr(
                    $encryptedInvoice,
                    (strpos($encryptedInvoice, '<error_msg>') + 11),
                    (strpos($encryptedInvoice, '</error_msg>') - (strpos($encryptedInvoice, '<error_msg>') + 11))
                )
            );
            $this->setEncryptionErr(true);
        }
        return $_errors;
    }

    /**
     * get ipg server url
     *
     * @return string
     */
    public function getIpgServerUrl()
    {
        return $this->_ipgServerUrl;
    }

    /**
     * get error number
     *
     * @return string
     */
    public function getErrNo()
    {
        return $this->_errNo;
    }

    /**
     * get Error string
     *
     * @return string
     */
    public function getErrString()
    {
        return $this->_errString;
    }

    /**
     * returns the encrypted invoice
     *
     * @return string
     * @throws \Exception
     */
    public function getEncryptedInvoice()
    {
        try {
            $invoiceXml = $this->generateRequestXml(\Dilmah\Payments\Model\Ntb\Checkout::SALETXN);

            /*Create the socket connection with IPG client*/
            $ipgSocket = $this->createSocketConnection();
            if ($ipgSocket === false) {
                throw new \Exception('Could not establish a socket connection for given configurations');
            }

            /*Send Invoice to IPG client*/
            $invoiceCreated = $this->sendInvoiceToClient($ipgSocket, $invoiceXml);
            if ($invoiceCreated === false) {
                throw new \Exception('Invoice could not be written to socket connection');
            }

            /*Receive the encrypted Invoice from IPG client*/
            $encryptedInvoice = $this->generateEncryptedInvoice($ipgSocket);
            if (empty($encryptedInvoice)) {
                throw new \Exception('Invoice encryption failed');
            }

            /*Close the socket connection*/
            $this->closeSocketConnection($ipgSocket);

            /*Check for Encryption errors*/
            $_errors = $this->hasEncryptionErrors($encryptedInvoice);
            if (!empty($_errors)) {
                throw new \Exception($_errors['err_no'] . ' - ' . $_errors['err_msg']);
            }

            return $encryptedInvoice;
        } catch (Exception $e) {
            $this->logger->error(__('Dilmah Payments NTB(Amex) error - "%1".', $e->getMessage()));
        }
    }

    /**
     * get the payment return url
     *
     * @return string
     */
    public function getReturnUrl()
    {
        return $this->_urlBuilder->getUrl('dilmah_payments/ntb/return');
    }

    /**
     * get logger object
     *
     * @param string $err
     * @return \Psr\Log\LoggerInterface
     */
    public function logErrors($err)
    {
        return $this->_logger->error(__('Dilmah Payments NTB(Amex) connection error - "%1".', $err));
    }
}
