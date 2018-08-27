<?php
/**
 * Blackbird Monetico Module
 *
 * NOTICE OF LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @category    Blackbird
 * @package     Blackbird_Monetico
 * @copyright   Copyright (c) 2018 Blackbird (https://black.bird.eu)
 * @author      Blackbird Team
 * @license     https://store.bird.eu/license/
 * @support     help@bird.eu
 */
namespace Blackbird\Monetico\Helper;

use Magento\Sales\Api\Data\OrderInterface;
use Blackbird\Monetico\Model\Method\Onetime;
use Blackbird\Monetico\Model\Method\Multitime;
use Blackbird\Monetico\Model\Method\CofidisEuro;
use Blackbird\Monetico\Model\Method\CofidisTxcb;
use Blackbird\Monetico\Model\Method\CofidisFxcb;
use Blackbird\Monetico\Model\Method\PayPal;

/**
 * Generate the MAC for the sealed form
 */
class DataSealedForm
{
    /**
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Blackbird\Monetico\Model\ConfigProvider
     */
    protected $_configProvider;

    /**
     * @var \Blackbird\Monetico\Model\Config\Source\PaymentMethod
     */
    protected $_paymentMethodSource;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var null
     */
    protected $methodCode = null;

    /**
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Blackbird\Monetico\Model\ConfigProvider $configProvider
     * @param \Blackbird\Monetico\Model\Config\Source\PaymentMethod $paymentMethodSource
     */
    public function __construct(
        \Magento\Framework\Escaper $escaper,
        \Magento\Customer\Model\Session $customerSession,
        \Blackbird\Monetico\Model\ConfigProvider $configProvider,
        \Blackbird\Monetico\Model\Config\Source\PaymentMethod $paymentMethodSource
    ) {
        $this->_escaper = $escaper;
        $this->_customerSession = $customerSession;
        $this->_configProvider = $configProvider;
        $this->_paymentMethodSource = $paymentMethodSource;
    }

    /**
     * Init the data of the sealed form
     *
     * @param string $methodCode
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return $this
     */
    public function init($methodCode, OrderInterface $order)
    {
        $this->reset();
        $this->setMethod($methodCode);
        $this->fillData($order);

        return $this;
    }

    /**
     * Reset the data sealed form helper
     *
     * @return $this
     */
    public function reset()
    {
        $this->setMethod(null);
        $this->data = [];

        return $this;
    }

    /**
     * Set the method code
     *
     * @param string $methodCode
     * @return $this
     */
    public function setMethod($methodCode)
    {
        $this->methodCode = $methodCode;
        $this->_configProvider->setMethodCode($methodCode);

        return $this;
    }

    /**
     * Retrieve the Monetico Order Data
     * 
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Retrieve the service name for the current payment method
     *
     * @return string
     */
    public function getServiceName()
    {
        return $this->_paymentMethodSource->getServiceName($this->methodCode);
    }

    /**
     * Check if the Payment form should in IFRAME 
     * 
     * @return bool
     */
    public function isPaymentMethodIntegrated()
    {
        return (bool) $this->_configProvider->getSystemConfigValue('use_iframe');
    }

    /**
     * Retrieve the payment form action url
     * 
     * @return string
     */
    public function getPaymentFormAction()
    {
        return $this->_configProvider->getPaymentFormAction();
    }

    /**
     * Retrieve the formatted order amount
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return string
     */
    public function getOrderAmountFormatted(OrderInterface $order)
    {
        return ($this->_configProvider->isBasePriceCurrencyEnabled())
            ? sprintf('%.2f', $order->getBaseGrandTotal()) . $order->getBaseCurrencyCode()
            : sprintf('%.2f', $order->getGrandTotal()) . $order->getOrderCurrencyCode();
    }

    /**
     * Retrieve the payment transaction text
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return string
     */
    public function getTransactionText(OrderInterface $order)
    {
        $text = $this->_configProvider->getTransferDescription();

        $text = str_replace('%order_id%', $order->getIncrementId(), $text);
        $text = str_replace(
            '%payment_method%',
            $this->_paymentMethodSource->getPaymentMethodTranslation($this->methodCode),
            $text
        );
        $text = str_replace('%code_societe%', $this->_configProvider->getSystemConfigValue('site_code'), $text);

        return substr($this->_escaper->escapeHtml($text), 0, 3200);
    }

    /**
     * "<TPE>*<date>*<montant>*<reference>*<texte-libre>*
     * <version>*<lgue>*<societe>*<mail>*<nbrech>*<dateech1>*
     * <montantech1>*<dateech2>*<montantech2>*<dateech3>*
     * <montantech3>*<dateech4>*<montantech4>*<options>"
     *
     * @return string
     */
    public function generateMAC()
    {
        return $this->_calcHmac($this->_getRequiredKeysMAC(), $this->getData());
    }

    /**
     * "<TPE>*<date>*<montant>*<reference>*<texte-libre>*
     * <version>*<coderetour>*<cvx>*<vld>*<brand>*<status3ds>*
     * <numauto>*<motifrefus>*<originecb>*<bincb>*<hpancb>*
     * <ipclient>*<originetr>*<veres>*<pares>*"
     *
     * @param array $data
     * @return string
     */
    public function getResponseMAC(array $data = [])
    {
        return $this->_calcHmac($this->_getRequiredResponseKeysMAC(), array_merge($this->getData(), $data), 0);
    }

    /**
     * Calc then retrieve the HMAC for the given data and keys
     *
     * @param array $requiredKeys
     * @param array $data
     * @param int $subStr (optional)
     * @return string
     */
    private function _calcHmac(array $requiredKeys, array $data, $subStr = -1)
    {
        $mac = '';

        foreach ($requiredKeys as $key) {
            $value = isset($data[$key]) ? $data[$key] : '';
            $mac .= $value . '*';
        }

        $mac = empty($subStr) ? $mac : substr($mac, 0, $subStr);

        // (by Monetico)
        return strtoupper(hash_hmac('sha1', $mac, $this->_getUsableKey()));
    }

    /**
     * Fill the sealed form data
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return $this
     */
    private function fillData(OrderInterface $order)
    {
        $config = $this->_configProvider->getConfig();
        $config = $config['payment']['monetico'];
        $date = new \DateTime($order->getCreatedAt());
        $date->setTimezone(new \DateTimeZone('Europe/Paris'));

        // Init the common data
        $this->data = [
            'version' => $this->_configProvider->getVersion(),
            'TPE' => $config['TPE'],
            'date' => $date->format('d/m/Y:H:i:s'),
            'montant' => $this->getOrderAmountFormatted($order),
            'reference' => $order->getIncrementId(),
            'texte-libre' => $this->getTransactionText($order),
            'mail' => $order->getCustomerEmail(),
            'lgue' => $config['locale'],
            'societe' => $config['societe'],
            'url_retour' => $config['redirectUrl']['back'],
            'url_retour_ok' => $config['redirectUrl']['success'],
            'url_retour_err' => $config['redirectUrl']['failure'],
        ];

        // Add specific param by payment method
        $this->fillDataByMethod($order);

        // Generate the MAC
        $this->data['MAC'] = $this->generateMAC();

        return $this;
    }

    /**
     * Add data by payment method type
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return $this
     */
    private function fillDataByMethod(OrderInterface $order)
    {
        switch ($this->methodCode) {
            case Onetime::METHOD_CODE:
                $this->fillDataOptions($order);
                break;
            case Multitime::METHOD_CODE:
                $this->fillDataSeveralPayments($order);
                $this->fillDataOptions($order);
                break;
            case CofidisEuro::METHOD_CODE:
                $this->data['protocole'] = '1euro';
                break;
            case CofidisTxcb::METHOD_CODE:
                $this->fillDataCofidisOptions($order);
                $this->data['protocole'] = '3xcb';
                break;
            case CofidisFxcb::METHOD_CODE:
                $this->fillDataCofidisOptions($order);
                $this->data['protocole'] = '4xcb';
                break;
            case PayPal::METHOD_CODE:
                $this->data['protocole'] = 'paypal';
                break;
        }

        return $this;
    }

    /**
     * Add the pre-configured customer data
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return $this
     */
    private function fillDataCofidisOptions(OrderInterface $order)
    {
        // Init Data
        $options = '';
        $billingAddress = $order->getBillingAddress();
        $street = $billingAddress->getStreet();
        // Split the address in two: main and extra street
        if (isset($street[0])) {
            $firstStreet = $street[0];
            unset($street[0]);
        } else {
            $firstStreet = '';
        }
        $extraStreet = implode(' ', $street);

        // Build the Customer Data
        $customerData = [
            'nomclient' => $billingAddress->getLastname(),
            'prenomclient' => $billingAddress->getFirstname(),
            'adresseclient' => $firstStreet,
            'codepostalclient' => $billingAddress->getPostcode(),
            'villeclient' => strtoupper($billingAddress->getCity()),
            'paysclient' => $billingAddress->getCountryId(),
            //'prescore' => '', //todo need further info (doc)
        ];

        if (!empty($extraStreet)) {
            $customerData['complementadresseclient'] = $extraStreet;
        }

        if (!empty($billingAddress->getTelephone())) {
            if (preg_match('/^(\+33|0)(6|7)/', $billingAddress->getTelephone())) {
                $customerData['telephonemobileclient'] = $billingAddress->getTelephone();
            } else {
                $customerData['telephonefixeclient'] = $billingAddress->getTelephone();
            }
        }

        // If customer has extra useful data
        if ($this->_customerSession->isLoggedIn()) {
            $customer = $this->_customerSession->getCustomer();

            if (!empty($customer->getData('gender'))) {
                $customerData['civiliteclient'] = $customer->getData('gender');
            }

            if (!empty($customer->getData('dob'))) {
                $dob = new \DateTime($customer->getData('dob'));
                $customerData['datenaissanceclient'] = $dob->format('Ymd');
            }
        }

        // Convert all values to hex
        foreach ($customerData as $key => $value) {
            $valHex = unpack('H*', $this->_escaper->escapeHtml($value));
            $options .= $key . '=' . array_shift($valHex) . '&';
        }

        $this->addOptionData(rtrim($options, '&'));

        return $this;
    }

    /**
     * Add the param for the options
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return $this
     */
    private function fillDataOptions(OrderInterface $order)
    {
        // Enable the payment express option
        if ($this->_configProvider->isPaymentExpressEnabled() && $this->_customerSession->isLoggedIn()) {
            $this->addOptionData('aliascb=' . $this->_customerSession->getCustomer()->getId());
            $this->addOptionData('forcesaisiecb=' . $this->_configProvider->getSystemConfigValue('force_cb'));
        }

        // Disable the 3D secure
        if ($this->_configProvider->is3DSecureDisabled($order->getTotalDue())) {
            $this->addOptionData('3dsdebrayable=1');
        }

        // Disable payment methods
        if ($this->_configProvider->hasDisabledOptions()) {
            $this->addOptionData(
                'desactivemoyenpaiement=' . $this->_configProvider->getSystemConfigValue('disabled_options')
            );
        }

        return $this;
    }

    /**
     * Add an option to the options var
     *
     * @param string $optionData
     * @return $this
     */
    private function addOptionData($optionData)
    {
        if (empty($this->data['options'])) {
            $this->data['options'] = $optionData;
        } else {
            $this->data['options'] .= '&' . $optionData;
        }

        return $this;
    }

    /**
     * Add the param for the several payments method
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return $this
     */
    private function fillDataSeveralPayments(OrderInterface $order)
    {
        $nbTerms = $this->_configProvider->getNumberOfTerms();

        if ($nbTerms > 1 && $nbTerms < 5) {
            $this->data['nbrech'] = $nbTerms;
            $date = new \DateTime($order->getCreatedAt());
            $totalAmount = 0;
            $termAmount = 0;

            for ($i = 1; $i <= $nbTerms; $i++) {
                if ($this->_configProvider->isTermsRateValid()) {
                    $termAmount = sprintf('%.2f', $order->getGrandTotal() * $this->_configProvider->getMultiplierTermRate($i));
                } else {
                    $termAmount = sprintf('%.2f', $order->getGrandTotal() / $nbTerms);
                }

                $this->data['dateech' . $i] = $date->format('d/m/Y');
                $this->data['montantech' . $i] = $termAmount . $order->getOrderCurrencyCode();
                $totalAmount += $termAmount;

                $date = $date->add($this->_getIntervalNextMonth($date));
            }

            $due = $termAmount + ($order->getGrandTotal() - $totalAmount);
            $this->data['montantech' . --$i] = sprintf('%.2f', $due) . $order->getOrderCurrencyCode();
        }

        return $this;
    }

    /**
     * Retrieve the required MAC keys
     * 
     * @return array
     */
    private function _getRequiredKeysMAC()
    {
        return [
            'TPE',
            'date',
            'montant',
            'reference',
            'texte-libre',
            'version',
            'lgue',
            'societe',
            'mail',
            'nbrech',
            'dateech1',
            'montantech1',
            'dateech2',
            'montantech2',
            'dateech3',
            'montantech3',
            'dateech4',
            'montantech4',
            'options',
        ];
    }

    /**
     * Retrieve the required response MAC keys
     * 
     * @return array
     */
    private function _getRequiredResponseKeysMAC()
    {
        return [
            'TPE',
            'date',
            'montant',
            'reference',
            'texte-libre',
            'version',
            'code-retour',
            'cvx',
            'vld',
            'brand',
            'status3ds',
            'numauto',
            'motifrefus',
            'originecb',
            'bincb',
            'hpancb',
            'ipclient',
            'originetr',
            'veres',
            'pares',
        ];
    }

    /**
     * Return the key to be used in the hmac function (by Monetico)
     *
     * @return string
     */
    private function _getUsableKey()
    {
        $key = $this->_configProvider->getSystemConfigValue('private_key');
        $hexStrKey = substr($key, 0, 38);
        $hexFinal = "" . substr($key, 38, 2) . "00";

        $cca0 = ord($hexFinal);

        if ($cca0 > 70 && $cca0 < 97) {
            $hexStrKey .= chr($cca0 - 23) . substr($hexFinal, 1, 1);
        } else {
            if (substr($hexFinal, 1, 1) == "M") {
                $hexStrKey .= substr($hexFinal, 0, 1) . "0";
            } else {
                $hexStrKey .= substr($hexFinal, 0, 2);
            }
        }

        return pack("H*", $hexStrKey);
    }

    /**
     * Retrieve the interval for the next month datetime
     *
     * @param \DateTime $dateTime
     * @return \DateInterval
     */
    private function _getIntervalNextMonth(\DateTime $dateTime)
    {
        $diff = clone $dateTime;
        $diff->modify('last day of next month');

        if ($dateTime->format('d') > $diff->format('d')) {
            $interval = $dateTime->diff($diff);
        } else {
            $interval = new \DateInterval('P1M');
        }

        return $interval;
    }
}
