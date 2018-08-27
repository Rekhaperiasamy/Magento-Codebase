<?php
namespace Netresearch\OPS\Model\Backend\Operation\Parameter;

/**
 * @author      Paul Siedler <paul.siedler@netresearch.de>
 * @category    Netresearch
 * @copyright   Copyright (c) 2014 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

use Netresearch\OPS\Model\Backend\Operation\Parameter\Additional\AdditionalInterface;

abstract class ParameterAbstract implements \Netresearch\OPS\Model\Backend\Operation\Parameter\ParameterInterface
{
    protected $requestParams = [];

    protected $opsConfig = null;
    protected $dataHelper = null;

    protected $additionalParamsModel = null;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Netresearch\OPS\Model\ConfigFactory
     */
    protected $oPSConfigFactory;

    /**
     * @var \Netresearch\OPS\Helper\Data
     */
    protected $oPSHelper;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Netresearch\OPS\Model\ConfigFactory $oPSConfigFactory,
        \Netresearch\OPS\Helper\Data $oPSHelper
    ) {
        $this->storeManager = $storeManager;
        $this->oPSConfigFactory = $oPSConfigFactory;
        $this->oPSHelper = $oPSHelper;
    }
    /**
     * @param \Netresearch\OPS\Model\Payment\PaymentAbstract $opsPaymentMethod
     * @param                                        $payment
     * @param                                        $amount
     *
     * @return array
     */
    public function getRequestParams(
        \Netresearch\OPS\Model\Payment\PaymentAbstract $opsPaymentMethod,
        \Magento\Framework\DataObject $payment,
        $amount
    ) {
    
        $this->getBaseParams($opsPaymentMethod, $payment, $amount);
        $this->addPmSpecificParams($opsPaymentMethod, $payment, $amount);

        return $this->requestParams;
    }

    /**
     * retrieves the basic parameters for a capture call
     *
     * @param \Netresearch\OPS\Model\Payment\PaymentAbstract $opsPaymentMethod
     * @param \Magento\Framework\DataObject                  $payment
     * @param                                                $amount
     *
     * @return $this
     */
    protected function getBaseParams(
        \Netresearch\OPS\Model\Payment\PaymentAbstract $opsPaymentMethod,
        \Magento\Framework\DataObject $payment,
        $amount
    ) {
    
        $this->requestParams['AMOUNT']    = $this->getDataHelper()->getAmount($amount);
        $this->requestParams['PAYID']     = $payment->getAdditionalInformation('paymentId');
        $this->requestParams['OPERATION'] = $this->getOrderHelper()->determineOperationCode($payment, $amount);
        $this->requestParams['CURRENCY']  = $this->storeManager->getStore($payment->getOrder()->getStoreId())
                                                ->getBaseCurrencyCode();

        return $this;
    }

    /**
     * retrieves ops config model
     *
     * @return \Netresearch\OPS\Model\Config
     */
    protected function getOpsConfig()
    {
        if (null === $this->opsConfig) {
            $this->opsConfig = $this->oPSConfigFactory->create();
        }

        return $this->opsConfig;
    }

    /**
     * if we have to add payment specific paramters to our request, we'll set them here
     *
     * @param $opsPaymentMethod
     * @param $payment
     * @param $amount
     *
     * @return $this
     */
    protected function addPmSpecificParams(
        \Netresearch\OPS\Model\Payment\PaymentAbstract $opsPaymentMethod,
        \Magento\Framework\DataObject $payment,
        $amount
    ) {
        if ($this->isPmRequiringAdditionalParams($opsPaymentMethod)) {
            $this->setAdditionalParamsModelFor($opsPaymentMethod);
            if ($this->additionalParamsModel instanceof AdditionalInterface) {
                $params = $this->additionalParamsModel->extractAdditionalParams($payment->getInvoice());
                $this->requestParams = array_merge($this->requestParams, $params);
            }
        }

        return $this;
    }

    protected function isPmRequiringAdditionalParams(\Netresearch\OPS\Model\Payment\PaymentAbstract $opsPaymentMethod)
    {
        return false;
    }

    protected function setAdditionalParamsModelFor(\Netresearch\OPS\Model\Payment\PaymentAbstract $opsPaymentMethod)
    {
        $this->additionalParamsModel = null;
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

    /**
     * Returns the order helper for the corresponding transaction type
     *
     * @return \Netresearch\OPS\Helper\Order\AbstractHelper
     */
    abstract public function getOrderHelper();
}
