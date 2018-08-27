<?php
/**
 * Netresearch_OPS
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * @copyright Copyright (c) 2015 Netresearch GmbH & Co. KG (http://www.netresearch.de/)
 * @license   Open Software License (OSL 3.0)
 * @link      http://opensource.org/licenses/osl-3.0.php
 */

namespace Netresearch\OPS\Model\Response\Type;

/**
 * Void.php
 *
 * @category Payment
 * @package  Netresearch_OPS
 * @author   Paul Siedler <paul.siedler@netresearch.de>
 */
?>
<?php

use Magento\Framework\Exception\PaymentException;

class Void extends \Netresearch\OPS\Model\Response\Type\TypeAbstract
{
    /**
     * Handles the specific actions for the concrete payment status
     */
    protected function _handleResponse()
    {
        if (!\Netresearch\OPS\Model\Status::isVoid($this->getStatus())) {
            throw new PaymentException(__('%1 is not a void status!', $this->getStatus()));
        }

        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $this->getMethodInstance()->getInfoInstance();
        /** @var \Magento\Sales\Model\Order $order */
        $order = $payment->getOrder();
        if (\Netresearch\OPS\Model\Status::isFinal($this->getStatus())) {
            if ($this->getShouldRegisterFeedback()) {
                $payment->setMessage(
                    __('Received Ingenico ePayments status %1. Order cancelled.', $this->getStatus())
                );
                $payment->registerVoidNotification($this->getAmount());

                // payment void does not cancel the order, but sets it to processing
                // we therefore need to cancel the order ourselves
                $order->registerCancellation($this->getFinalStatusComment(), true);
            } else {
                $this->addFinalStatusComment();
            }
        } else {
            $payment->setMessage($this->getIntermediateStatusComment());
        }

        $order->save();
    }
}
