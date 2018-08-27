<?php
/**
 * \Netresearch\OPS\Model\Payment\DirectEbanking
 *
 * @package
 * @copyright 2011 Netresearch
 * @author    Thomas Kappel <thomas.kappel@netresearch.de>
 * @license   OSL 3.0
 */
namespace Netresearch\OPS\Model\Payment;

class DirectEbanking extends \Netresearch\OPS\Model\Payment\PaymentAbstract
{
    /**
     * Check if we can capture directly from the backend
     *
     * @var bool
     */
    protected $_canBackendDirectCapture = true;

    /**
     * Payment Code
     *
     * @var string
     */
    protected $_code = 'ops_directEbanking';

    /**
     * Info block type
     *
     * @var string
     */
    protected $_infoBlockType = 'Netresearch\OPS\Block\Info\Redirect';

    /**
     * Form block type
     *
     * @var string
     */
    protected $_formBlockType = 'Netresearch\OPS\Block\Form\DirectEbanking';

    protected function getPayment()
    {
        $checkout = $this->checkoutSession;
        $payment  = $checkout->getQuote()->getPayment();
        if (!$payment->getId()) {
            $payment = $this->salesOrderFactory->create()
                ->loadByIncrementId($checkout->getLastRealOrderId())->getPayment();
        }

        return $payment;
    }

    /**
     * Assign data to info model instance
     *
     * @param   \Magento\Framework\DataObject $data
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function assignData(\Magento\Framework\DataObject $data)
    {
        $additionalData = $data->getAdditionalData();
        $brand          =
            isset($additionalData['directEbanking_brand']) ? $additionalData['directEbanking_brand'] : null;

        $brand = $this->fixSofortUberweisungBrand($brand);

        $payment = $this->getInfoInstance();
        // brand == pm for all DirectEbanking methods
        $payment->setAdditionalInformation('PM', $brand);
        $payment->setAdditionalInformation('BRAND', $brand);
        parent::assignData($data);

        return $this;
    }

    /**
     * Fixes legacy brand value of Sofort Uberweisung for DirectEbanking
     *
     * @param string $value
     *
     * @return string
     */
    protected function fixSofortUberweisungBrand($value)
    {
        if ($value === 'Sofort Uberweisung') {
            return 'DirectEbanking';
        }

        return $value;
    }
}
