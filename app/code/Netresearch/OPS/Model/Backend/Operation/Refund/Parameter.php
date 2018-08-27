<?php
namespace Netresearch\OPS\Model\Backend\Operation\Refund;

/**
 * @author      Paul Siedler <paul.siedler@netresearch.de>
 * @category    Netresearch
 * @copyright   Copyright (c) 2014 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

use Netresearch\OPS\Model\Backend\Operation\Parameter\Additional\AdditionalInterface;

class Parameter extends \Netresearch\OPS\Model\Backend\Operation\Parameter\ParameterAbstract
{
    /**
     * @var \Netresearch\OPS\Model\Backend\Operation\Refund\Additional\OpenInvoiceNlFactory
     */
    protected $oPSOpenInvoiceNlFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Netresearch\OPS\Helper\Order\Refund
     */
    protected $oPSOrderRefundHelper;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Netresearch\OPS\Model\ConfigFactory $oPSConfigFactory,
        \Netresearch\OPS\Helper\Data $oPSHelper,
        \Netresearch\OPS\Model\Backend\Operation\Refund\Additional\OpenInvoiceNlFactory $oPSOpenInvoiceNlFactory,
        \Magento\Framework\Registry $registry,
        \Netresearch\OPS\Helper\Order\Refund $oPSOrderRefundHelper
    ) {
        parent::__construct($storeManager, $oPSConfigFactory, $oPSHelper);
        $this->oPSOpenInvoiceNlFactory = $oPSOpenInvoiceNlFactory;
        $this->registry = $registry;
        $this->oPSOrderRefundHelper = $oPSOrderRefundHelper;
    }
    /**
     * checks whether we need to retrieve additional parameter for the refund request or not
     *
     * @param \Netresearch\OPS\Model\Payment\PaymentAbstract $opsPaymentMethod
     *
     * @return bool - true if we need to retrieve any additional parameters, false otherwise
     */
    protected function isPmRequiringAdditionalParams(\Netresearch\OPS\Model\Payment\PaymentAbstract $opsPaymentMethod)
    {
        $opsPaymentMethodClass = get_class($opsPaymentMethod);
        $opsPmsRequiringSpecialParams = $this->getOpsConfig()->getMethodsRequiringAdditionalParametersFor(
            \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_REFUND_TRANSACTION_TYPE
        );

        return (in_array($opsPaymentMethodClass, array_values($opsPmsRequiringSpecialParams)));
    }
    /**
     * sets the model which retrieves the additional params for the refund request
     *
     * @param \Netresearch\OPS\Model\Payment\PaymentAbstract $opsPaymentMethod
     */
    protected function setAdditionalParamsModelFor(\Netresearch\OPS\Model\Payment\PaymentAbstract $opsPaymentMethod)
    {
        if ($opsPaymentMethod instanceof \Netresearch\OPS\Model\Payment\OpenInvoiceNl) {
            $this->additionalParamsModel = $this->oPSOpenInvoiceNlFactory->create();
        }
    }

    protected function addPmSpecificParams(
        \Netresearch\OPS\Model\Payment\PaymentAbstract $opsPaymentMethod,
        \Magento\Framework\DataObject $payment,
        $amount
    ) {
        if ($this->isPmRequiringAdditionalParams($opsPaymentMethod)) {
            $this->setAdditionalParamsModelFor($opsPaymentMethod);
            if ($this->additionalParamsModel instanceof AdditionalInterface) {
                $params = $this->additionalParamsModel
                    ->extractAdditionalParams($this->registry->registry('current_creditmemo'));
                $this->requestParams = array_merge($this->requestParams, $params);
            }
        }

        return $this;
    }

    /**
     * Returns the order helper for the corresponding transaction type
     *
     * @return \Netresearch\OPS\Helper\Order\AbstractHelper
     */
    public function getOrderHelper()
    {
        return $this->oPSOrderRefundHelper;
    }
}
