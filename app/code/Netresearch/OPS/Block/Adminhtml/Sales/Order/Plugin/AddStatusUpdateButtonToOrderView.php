<?php

namespace Netresearch\OPS\Block\Adminhtml\Sales\Order\Plugin;

class AddStatusUpdateButtonToOrderView
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
        $paymentMethod = $block->getOrder()->getPayment()->getMethodInstance();
        if ($paymentMethod instanceof \Netresearch\OPS\Model\Payment\PaymentAbstract
            && $this->authorization->isAllowed('Magento_Sales::invoice')) {
            $block->addButton('ops_refresh', [
                'label'     => __('Refresh payment status'),
                'onclick'   => 'setLocation(\'' . $block->getUrl('adminhtml/opsstatus/update') . '\')']);
        }
        return null;
    }
}
