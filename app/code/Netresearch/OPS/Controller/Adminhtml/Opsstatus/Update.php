<?php

namespace Netresearch\OPS\Controller\Adminhtml\Opsstatus;

class Update extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $salesOrderFactory;

    /**
     * @var \Netresearch\OPS\Model\Status\UpdateFactory
     */
    protected $oPSStatusUpdateFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Sales\Model\OrderFactory $salesOrderFactory
     * @param \Netresearch\OPS\Model\Status\UpdateFactory $oPSStatusUpdateFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Sales\Model\OrderFactory $salesOrderFactory,
        \Netresearch\OPS\Model\Status\UpdateFactory $oPSStatusUpdateFactory
    ) {
        parent::__construct($context);
        $this->salesOrderFactory = $salesOrderFactory;
        $this->oPSStatusUpdateFactory = $oPSStatusUpdateFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Sales::invoice');
    }

    /**
     * performs the status update call to Ingenico ePayments
     */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        if (0 < $orderId) {
            $order = $this->salesOrderFactory->create()->load($orderId);
            $this->oPSStatusUpdateFactory->create()->updateStatusFor($order);
        }
        $this->_redirect('sales/order/view', ['order_id' => $orderId]);
    }
}
