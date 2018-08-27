<?php
namespace Netresearch\OPS\Model\Backend\Operation\Capture;

/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @copyright   Copyright (c) 2014 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Parameter extends \Netresearch\OPS\Model\Backend\Operation\Parameter\ParameterAbstract
{
    /**
     * @var \Netresearch\OPS\Model\Backend\Operation\Capture\Additional\OpenInvoiceNlFactory
     */
    protected $oPSOpenInvoiceNlFactory;

    /**
     * @var \Netresearch\OPS\Helper\Order\Capture
     */
    protected $oPSOrderCaptureHelper;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Netresearch\OPS\Model\ConfigFactory $oPSConfigFactory,
        \Netresearch\OPS\Helper\Data $oPSHelper,
        \Netresearch\OPS\Model\Backend\Operation\Capture\Additional\OpenInvoiceNlFactory $oPSOpenInvoiceNlFactory,
        \Netresearch\OPS\Helper\Order\Capture $oPSOrderCaptureHelper
    ) {
        parent::__construct($storeManager, $oPSConfigFactory, $oPSHelper);
        $this->oPSOpenInvoiceNlFactory = $oPSOpenInvoiceNlFactory;
        $this->oPSOrderCaptureHelper = $oPSOrderCaptureHelper;
    }
    /**
     * checks whether we need to retrieve additional parameter for the capture request or not
     *
     * @param \Netresearch\OPS\Model\Payment\PaymentAbstract $opsPaymentMethod
     *
     * @return bool - true if we need to retrieve any additional parameters, false otherwise
     */
    protected function isPmRequiringAdditionalParams(\Netresearch\OPS\Model\Payment\PaymentAbstract $opsPaymentMethod)
    {
        $opsPaymentMethodClass = get_class($opsPaymentMethod);
        $opsPmsRequiringSpecialParams = $this->getOpsConfig()
            ->getMethodsRequiringAdditionalParametersFor(
                \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_CAPTURE_TRANSACTION_TYPE
            );

        return (in_array($opsPaymentMethodClass, array_values($opsPmsRequiringSpecialParams)));
    }

    /**
     * sets the model which retrieves the additional params
     *
     * @param \Netresearch\OPS\Model\Payment\PaymentAbstract $opsPaymentMethod
     */
    protected function setAdditionalParamsModelFor(\Netresearch\OPS\Model\Payment\PaymentAbstract $opsPaymentMethod)
    {
        if ($opsPaymentMethod instanceof \Netresearch\OPS\Model\Payment\OpenInvoiceNl) {
            $this->additionalParamsModel = $this->oPSOpenInvoiceNlFactory->create();
        }
    }

    /**
     * Returns the order helper for the corresponding transaction type
     *
     * @return \Netresearch\OPS\Helper\Order\AbstractHelper
     */
    public function getOrderHelper()
    {
        return $this->oPSOrderCaptureHelper;
    }
}
