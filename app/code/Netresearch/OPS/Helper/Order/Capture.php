<?php
namespace Netresearch\OPS\Helper\Order;

/**
 * @package
 * @copyright 2011 Netresearch
 * @author    André Herrn <andre.herrn@netresearch.de>
 * @license   OSL 3.0
 */
class Capture extends \Netresearch\OPS\Helper\Order\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Netresearch\OPS\Helper\Payment $oPSPaymentHelper,
        \Magento\Framework\App\Request\Http $request
    ) {
        parent::__construct($context, $oPSPaymentHelper);
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFullOperationCode()
    {
        return \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_CAPTURE_FULL;
    }

    /**
     * {@inheritdoc}
     */
    protected function getPartialOperationCode()
    {
        return \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_CAPTURE_PARTIAL;
    }

    /**
     * {@inheritdoc}
     */
    protected function getPreviouslyProcessedAmount($payment)
    {
        return $payment->getBaseAmountPaidOnline();
    }

    /**
     * Prepare capture informations
     *
     * @param \Magento\Sales\Model\Order\Payment $payment
     * @param float $amount
     * @return array
     */
    public function prepareOperation($payment, $amount)
    {
        $params = $this->request->getParams();
        if (array_key_exists('invoice', $params)) {
            $arrInfo           = $params['invoice'];
            $arrInfo['amount'] = $amount;
        }
        $arrInfo['type']      = $this->determineType($payment, $amount);
        $arrInfo['operation'] = $this->determineOperationCode($payment, $amount);

        return $arrInfo;
    }
}
