<?php
/**
 * \Netresearch\OPS\Model\Payment\BankTransfer
 *
 * @package
 * @copyright 2011 Netresearch
 * @author    Thomas Kappel <thomas.kappel@netresearch.de>
 * @license   OSL 3.0
 */
namespace Netresearch\OPS\Model\Payment;

class BankTransfer extends \Netresearch\OPS\Model\Payment\PaymentAbstract
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
    protected $_code = 'ops_bankTransfer';

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
    protected $_formBlockType = 'Netresearch\OPS\Block\Form\BankTransfer';


    /**
     * Assign data to info model instance
     *
     * @param \Magento\Framework\DataObject $data
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function assignData(\Magento\Framework\DataObject $data)
    {
        $countryId = '';

        $additionalData = $data->getAdditionalData();
        if (isset($additionalData['country_id'])) {
            $countryId = $additionalData['country_id'];
        }

        $pm = $brand = trim('Bank transfer' . (('*' == $countryId) ? '' : ' ' . $countryId));

        $payment = $this->getInfoInstance();
        $payment->setAdditionalInformation('PM', $pm);
        $payment->setAdditionalInformation('BRAND', $brand);

        parent::assignData($data);

        return $this;
    }
}
