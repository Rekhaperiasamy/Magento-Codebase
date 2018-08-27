<?php
/**
 * \Netresearch\OPS\Model\Payment\InterSolve
 *
 * @package
 * @copyright 2011 Netresearch
 * @author    Thomas Kappel <thomas.kappel@netresearch.de>
 * @license   OSL 3.0
 */
namespace Netresearch\OPS\Model\Payment;

class InterSolve extends \Netresearch\OPS\Model\Payment\PaymentAbstract
{
    protected $pm = 'InterSolve';
    protected $brand = 'InterSolve';

    /** Check if we can capture directly from the backend */
    protected $_canBackendDirectCapture = true;

    /** payment code */
    protected $_code = 'ops_interSolve';

    protected $_formBlockType = 'Netresearch\OPS\Block\Form\InterSolve';

    /**
     * @var string
     */
    protected $_infoBlockType = 'Netresearch\OPS\Block\Info\Redirect';

    /**
     * {@inheritDoc}
     */
    public function assignData(\Magento\Framework\DataObject $data)
    {
        $additionalData = $data->getAdditionalData();
        $brand = isset($additionalData['intersolve_brand']) ? $additionalData['intersolve_brand'] : null;
        if (strlen(trim($brand)) === 0) {
            $brand = 'InterSolve';
        }
        $payment = $this->checkoutSession->getQuote()->getPayment();
        $payment->setAdditionalInformation('BRAND', $brand);

        parent::assignData($data);
        return $this;
    }
}
