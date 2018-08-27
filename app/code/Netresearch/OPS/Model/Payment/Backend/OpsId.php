<?php
/**
 * \Netresearch\OPS\Model\Payment\Backend\OpsId
 *
 * @package   OPS
 * @copyright 2012 Netresearch App Factory AG <http://www.netresearch.de>
 * @author    Thomas Birke <thomas.birke@netresearch.de>
 * @license   OSL 3.0
 */
namespace Netresearch\OPS\Model\Payment\Backend;

class OpsId extends \Magento\Payment\Model\Method\AbstractMethod
{
    /* allow usage in Magento backend */
    protected $_canUseInternal = true;

    /* deny usage in Magento frontend */
    protected $_canUseCheckout = false;

    protected $_canBackendDirectCapture = true;

    protected $_isGateway               = false;
    protected $_canAuthorize            = false;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = true;
    protected $_canRefund               = true;
    protected $_canRefundInvoicePartial = true;
    protected $_canVoid                 = true;

    /** payment code */
    protected $_code = 'ops_opsid';

    protected $_formBlockType = 'Netresearch\OPS\Block\Form\OpsId';

    protected $_infoBlockType = 'Netresearch\OPS\Block\Info\OpsId';

    /**
     * {@inheritDoc}
     */
    public function assignData(\Magento\Framework\DataObject $data)
    {
        parent::assignData($data);

        $paymentInfo = $this->getInfoInstance();
        $additionalInfo = $data->getData('additional_data');
        if (isset($additionalInfo['ops_pay_id'])) {
            $paymentInfo->setAdditionalInformation('paymentId', $additionalInfo['ops_pay_id']);
        }

        return $this;
    }
}
