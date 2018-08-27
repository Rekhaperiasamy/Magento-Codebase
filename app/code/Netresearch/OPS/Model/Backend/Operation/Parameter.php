<?php
namespace Netresearch\OPS\Model\Backend\Operation;

/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Parameter
{
    protected $parameterModel = null;

    protected $dataHelper = null;

    /**
     * @var \Netresearch\OPS\Model\Backend\Operation\Capture\ParameterFactory
     */
    protected $oPSBackendOperationCaptureParameterFactory;

    /**
     * @var \Netresearch\OPS\Model\Backend\Operation\Refund\ParameterFactory
     */
    protected $oPSBackendOperationRefundParameterFactory;

    /**
     * @var \Netresearch\OPS\Helper\Data
     */
    protected $oPSHelper;

    public function __construct(
        \Netresearch\OPS\Model\Backend\Operation\Capture\ParameterFactory $oPSBackendOperationCaptureParameterFactory,
        \Netresearch\OPS\Model\Backend\Operation\Refund\ParameterFactory $oPSBackendOperationRefundParameterFactory,
        \Netresearch\OPS\Helper\Data $oPSHelper
    ) {
        $this->oPSBackendOperationCaptureParameterFactory = $oPSBackendOperationCaptureParameterFactory;
        $this->oPSBackendOperationRefundParameterFactory = $oPSBackendOperationRefundParameterFactory;
        $this->oPSHelper = $oPSHelper;
    }
    /**
     * retrieves the neccessary parameter for the given operation
     *
     * @param                                                $operation
     * @param \Netresearch\OPS\Model\Payment\PaymentAbstract $opsPaymentMethod
     * @param \Magento\Framework\DataObject                  $payment
     * @param                                                $amount
     *
     * @return array
     */
    public function getParameterFor(
        $operation,
        \Netresearch\OPS\Model\Payment\PaymentAbstract $opsPaymentMethod,
        \Magento\Framework\DataObject $payment,
        $amount
    ) {
        return $this->getParameterModel($operation)->getRequestParams($opsPaymentMethod, $payment, $amount);
    }

    /**
     * retrieves the parameter model for the given operation
     *
     * @param $operation - the operation we need the parameters for
     *
     * @throws \Magento\Framework\Exception\LocalizedException - in case the operation is not supported
     * @return \Netresearch\OPS\Model\Backend\Operation\Parameter\ParameterInterface - the model for the parameters
     *                                                                                 of the operation
     */
    protected function getParameterModel($operation)
    {
        if ($operation === \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_CAPTURE_TRANSACTION_TYPE) {
            return $this->oPSBackendOperationCaptureParameterFactory->create();
        }
        if ($operation === \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_REFUND_TRANSACTION_TYPE) {
            return $this->oPSBackendOperationRefundParameterFactory->create();
        }

        throw new \Magento\Framework\Exception\LocalizedException(__('operation %1 not supported', $operation));
    }

    /**
     * retrieves the data helper
     *
     * @return \Netresearch\OPS\Helper\Data|null
     */
    protected function getDataHelper()
    {
        if (null == $this->dataHelper) {
            $this->dataHelper = $this->oPSHelper;
        }

        return $this->dataHelper;
    }
}
