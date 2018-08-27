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
 * @copyright Copyright (c) 2017 Netresearch GmbH & Co. KG (http://www.netresearch.de/)
 * @license   Open Software License (OSL 3.0)
 * @link      http://opensource.org/licenses/osl-3.0.php
 */

/**
 * TransactionalMailPlugin.php
 *
 * @category ops
 * @package  Netresearch_OPS
 * @author   Paul Siedler <paul.siedler@netresearch.de>
 */

namespace Netresearch\OPS\Plugin;

use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Netresearch\OPS\Model\Response\Type\TypeAbstract;
use Netresearch\OPS\Model\Response\Type\Authorize;
use Netresearch\OPS\Model\Response\Type\Capture;
use Netresearch\OPS\Model\Response\TypeInterface;
use \Netresearch\OPS\Model\Response\Handler;

class TransactionalMailPlugin
{
    /**
     * @var InvoiceSender
     */
    private $invoiceSender;
    /**
     * @var OrderSender
     */
    private $orderSender;

    /**
     * TransactionalMailPlugin constructor.
     *
     * @param OrderSender   $orderSender
     * @param InvoiceSender $invoiceSender
     */
    public function __construct(
        OrderSender $orderSender,
        InvoiceSender $invoiceSender
    ) {
        $this->invoiceSender = $invoiceSender;
        $this->orderSender = $orderSender;
    }

    /**
     * Send Order email if we have received feedback
     *
     * @param TypeInterface $subject
     * @param TypeAbstract  $result
     *
     * @return TypeInterface
     */
    public function afterProcessResponse(Handler $subject, $result)
    {
        if ($result->getShouldRegisterFeedback()
            && ($result instanceof Authorize || $result instanceof Capture)
        ) {
            /** @var \Magento\Sales\Model\Order\Payment $payment */
            $payment = $result->getMethodInstance()->getInfoInstance();
            $order = $payment->getOrder();

            if ($this->shouldSendOrderMail($order, $result->getStatus())) {
                $this->orderSender->send($order);
            }
            if ($result->getConfig()->getSendInvoice()
                && $payment->getCreatedInvoice() instanceof \Magento\Sales\Model\Order\Invoice
            ) {
                $this->invoiceSender->send($payment->getCreatedInvoice());
            }
        }

        return $result;
    }

    /**
     * Check if order email should be send.
     *
     * @param \Magento\Sales\Model\Order $order
     * @param                            $paymentState
     *
     * @return bool
     */
    protected function shouldSendOrderMail(\Magento\Sales\Model\Order $order, $paymentState)
    {
        $result = false;
        if ($order
            && !$order->getEmailSent()
            && $order->getState() != \Magento\Sales\Model\Order::STATE_CANCELED
            && $paymentState != \Netresearch\OPS\Model\Status::AUTHORISATION_DECLINED
        ) {
            $result = true;
        }

        return $result;
    }
}
