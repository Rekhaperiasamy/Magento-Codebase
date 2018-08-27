<?php
namespace Netresearch\OPS\Model\Payment;

/**
 * \Netresearch\OPS\Model\Payment\IDeal
 *
 * @package
 * @copyright 2011 Netresearch
 * @author    Thomas Kappel <thomas.kappel@netresearch.de>
 * @license   OSL 3.0
 */
class IDeal extends \Netresearch\OPS\Model\Payment\PaymentAbstract
{
    protected $pm = 'iDEAL';
    protected $brand = 'iDEAL';

    /** Check if we can capture directly from the backend */
    protected $_canBackendDirectCapture = true;

    /**
     * @var string
     */
    protected $_formBlockType = 'Netresearch\OPS\Block\Form\Ideal';

    /**
     * @var string
     */
    protected $_infoBlockType = 'Netresearch\OPS\Block\Info\Redirect';

    /** payment code */
    protected $_code = 'ops_iDeal';

    /**
     * adds payment specific information to the payment
     *
     * @param mixed $data - data containing the issuer id which should be used
     *
     * @return \Netresearch\OPS\Model\Payment\IDeal
     */
    public function assignData(\Magento\Framework\DataObject $data)
    {
        $additionalData = $data->getAdditionalData();
        $issuerId = isset($additionalData['iDeal_issuer_id']) ? $additionalData['iDeal_issuer_id'] : null;
        if ($issuerId) {
            $payment = $this->checkoutSession->getQuote()->getPayment();
            $payment->setAdditionalInformation('iDeal_issuer_id', $issuerId);
        }
        parent::assignData($data);

        return $this;
    }

    /**
     * getter for the iDeal issuers
     *
     * @return array
     */
    public function getIDealIssuers()
    {
        return $this->_scopeConfig
            ->getValue('payment/ops_iDeal/issuer', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * add iDeal issuer id to form fields
     *
     * @override \Netresearch\OPS\Model\Payment\PaymentAbstract
     *
     * @param      $order
     * @param null $requestParams
     *
     * @return array
     */
    public function getMethodDependendFormFields($order, $requestParams = null)
    {
        $formFields = parent::getMethodDependendFormFields($order, $requestParams);
        if ($order->getPayment()->getAdditionalInformation('iDeal_issuer_id')) {
            $formFields['ISSUERID'] = $order->getPayment()->getAdditionalInformation('iDeal_issuer_id');
        }

        return $formFields;
    }
}
