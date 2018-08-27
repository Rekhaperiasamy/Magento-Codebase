<?php
namespace Netresearch\OPS\Model\Payment\Features;

/**
 * @package
 * @copyright 2014 Netresearch
 * @author    Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license   OSL 3.0
 */
class ZeroAmountAuth
{
    /**
     * check if payment method is cc and zero amount authorization is enabled
     *
     * @param \Netresearch\OPS\Model\Payment\PaymentAbstract $opsPaymentMethod
     * @param \Magento\Quote\Model\Quote $quote
     * @return bool
     */
    public function isCCAndZeroAmountAuthAllowed(
        \Netresearch\OPS\Model\Payment\PaymentAbstract $opsPaymentMethod,
        \Magento\Quote\Model\Quote $quote
    ) {
        $result  = false;
        $storeId = $quote->getStoreId();
        if ($quote->getBaseGrandTotal() < 0.01
            && $opsPaymentMethod instanceof \Netresearch\OPS\Model\Payment\Cc
            && $opsPaymentMethod->isZeroAmountAuthorizationAllowed($storeId)
            && 0 < $quote->getItemsCount()
        ) {
            $result = true;
        }

        return $result;
    }
}
