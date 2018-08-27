<?php

namespace Netresearch\OPS\Block\Adminhtml\Sales\Order\Plugin;

class AddResendPaymentInfoButtonToOrderView
{
    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $authorization;

    /**
     * @param \Magento\Framework\AuthorizationInterface $authorization
     */
    public function __construct(\Magento\Framework\AuthorizationInterface $authorization)
    {
        $this->authorization = $authorization;
    }

    /**
     * @param \Magento\Sales\Block\Adminhtml\Order\View $block
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @return null
     */
    public function beforeSetLayout(
        \Magento\Sales\Block\Adminhtml\Order\View $block,
        \Magento\Framework\View\LayoutInterface $layout
    ) {
        $payment = $block->getOrder()->getPayment();
        $paymentMethod = $payment->getMethodInstance();
        if ($paymentMethod instanceof \Netresearch\OPS\Model\Payment\PaymentAbstract
            && $this->authorization->isAllowed('Magento_Sales::invoice')
            && \Netresearch\OPS\Model\Status::canResendPaymentInfo($payment->getAdditionalInformation('status'))
            && !in_array(
                $block->getOrder()->getState(),
                [
                    \Magento\Sales\Model\Order::STATE_CANCELED,
                    \Magento\Sales\Model\Order::STATE_CLOSED,
                    \Magento\Sales\Model\Order::STATE_COMPLETE
                ]
            )
        ) {
            $block->addButton(
                'ops_resend_info',
                [
                    'label'     => __('Resend payment information'),
                    'onclick'   => 'setLocation(\'' . $block->getUrl('adminhtml/admin/resendInfo') . '\')'
                ]
            );
        }

        return null;
    }
}
