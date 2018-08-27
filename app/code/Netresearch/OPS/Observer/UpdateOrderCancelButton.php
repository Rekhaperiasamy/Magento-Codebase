<?php

namespace Netresearch\OPS\Observer;

class UpdateOrderCancelButton implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * Replace order cancel comfirm message of Magento by a custom message from Ingenico Payment Services
     *
     * @event adminhtml_block_html_before
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var $block \Magento\Backend\Block\Template|\Magento\Sales\Block\Adminhtml\Order\View */
        $block = $observer->getBlock();

        //Stop if block is not sales order view
        if (!$block instanceof \Magento\Sales\Block\Adminhtml\Order\View) {
            return;
        }

        $methodInstance = $block->getOrder()->getPayment()->getMethodInstance();

        //If payment method is one of the Ingenico ePayments-ones and order can be cancelled manually
        if ($methodInstance instanceof \Netresearch\OPS\Model\Payment\PaymentAbstract
            && true === $block->getOrder()->getPayment()->getMethodInstance()->canCancelManually($block->getOrder())
        ) {
            //Build message and update cancel button
            $message = __(
                "Are you sure you want to cancel this order? Warning: " .
                "Please check the payment status in the back-office of Ingenico ePayments before. " .
                "By cancelling this order you won\\'t be able to update the status in Magento anymore."
            );
            $block->updateButton(
                'order_cancel',
                'onclick',
                'deleteConfirm(\'' . $message . '\', \'' . $block->getCancelUrl() . '\')'
            );
        }
    }
}
