<?php
/**
 * @category   OPS
 * @package    Netresearch_OPS
 * @author     Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright  Copyright (c) 2017 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Netresearch\OPS\Plugin;

use Magento\Checkout\Model\PaymentInformationManagement;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\PaymentException;

class PaymentInformationManagementPlugin
{

    /**
     * Will prevent the default exception masking to enable the exception handling of onepage checkouts saveOrder
     *
     * @see \Magento\Checkout\Controller\Onepage\SaveOrder
     * @see PaymentInformationManagement::savePaymentInformationAndPlaceOrder
     *
     * @param PaymentInformationManagement $subject
     * @param \Closure                     $proceed
     * @param mixed[]                      ...$args
     *
     * @return int Order ID
     *
     * @throws CouldNotSaveException
     * @throws PaymentException
     */
    public function aroundSavePaymentInformationAndPlaceOrder(
        PaymentInformationManagement $subject,
        \Closure $proceed,
        ...$args
    ) {
        try {
            $orderId = $proceed(...$args);
        } catch (CouldNotSaveException $e) {
            /** @var \Magento\Quote\Api\Data\PaymentInterface $payment */
            $payment = $args[1];
            $originalException = $e->getPrevious();
            if ($originalException instanceof PaymentException
                && false !== strpos($payment->getMethod(), 'ops_')
            ) {
                throw $originalException;
            }
            throw $e;
        }

        return $orderId;
    }
}
