<?php

namespace Netresearch\OPS\Controller\Adminhtml\Admin;

class ResendInfo extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $salesOrderFactory;

    /**
     * @var \Netresearch\OPS\Model\Payment\Features\PaymentEmailFactory
     */
    protected $oPSPaymentFeaturesPaymentEmailFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Sales\Model\OrderFactory $salesOrderFactory
     * @param \Netresearch\OPS\Model\Payment\Features\PaymentEmailFactory $oPSPaymentFeaturesPaymentEmailFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Sales\Model\OrderFactory $salesOrderFactory,
        \Netresearch\OPS\Model\Payment\Features\PaymentEmailFactory $oPSPaymentFeaturesPaymentEmailFactory
    ) {
        parent::__construct($context);
        $this->salesOrderFactory = $salesOrderFactory;
        $this->oPSPaymentFeaturesPaymentEmailFactory = $oPSPaymentFeaturesPaymentEmailFactory;
    }

    /**
     * Resends the payment information for the given order
     */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $order = $this->salesOrderFactory->create()->load($orderId);
        /** @var \Netresearch\OPS\Model\Payment\Features\PaymentEmail $paymentEmail */
        $paymentEmail = $this->oPSPaymentFeaturesPaymentEmailFactory->create();

        try {
            if ($paymentEmail->isAvailableForOrder($order)) {
                $paymentEmail->resendPaymentInfo($order);
                $this->messageManager->addSuccess(__('Payment information has been resend to customer.'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(__('Payment information could not be sent.'));
        }

        return $this->getResponse()->setRedirect($this->_redirect->getRefererUrl());
    }
}
