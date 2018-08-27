<?php
/**
 * @category   OPS
 * @package    Netresearch_OPS
 * @author     André Herrn <andre.herrn@netresearch.de>
 * @copyright  Copyright (c) 2013 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Netresearch\OPS\Model;

/**
 *
 * @author     André Herrn <andre.herrn@netresearch.de>
 * @copyright  Copyright (c) 2013 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Observer
{
    /**
     * @var \Netresearch\OPS\Model\ConfigFactory
     */
    protected $oPSConfigFactory;

    public function __construct(\Netresearch\OPS\Model\ConfigFactory $oPSConfigFactory)
    {
        $this->oPSConfigFactory = $oPSConfigFactory;
    }

    /**
     * @return \Netresearch\OPS\Model\Config
     */
    public function getConfig()
    {
        return $this->oPSConfigFactory->create();
    }

    /**
     * Since there is no other way for inline payments to change the order state, we enforce the pending_payment state
     * for only authorized, not yet payed orders
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @event sales_order_payment_place_end
     * @return $this
     */
    public function enforcePaymentPendingForAuthorizedOrders(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $observer->getData('payment');
        /** @var \Magento\Sales\Model\Order $order */
        $order = $payment->getOrder();

        $status = $payment->getAdditionalInformation('status');
        if ($this->getConfig()->getPaymentAction($order->getStoreId())
            == \Netresearch\OPS\Model\Payment\PaymentAbstract::ACTION_AUTHORIZE
            && \Netresearch\OPS\Model\Status::isAuthorize($status)
            && $order->getState() != \Magento\Sales\Model\Order::STATE_PENDING_PAYMENT
        ) {
            $message = __('Order has been authorized, but not captured/paid yet.');
            $order->setState(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);
            $order->setStatus(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);
            $order->addStatusHistoryComment($message);
        }

        return $this;
    }
}
