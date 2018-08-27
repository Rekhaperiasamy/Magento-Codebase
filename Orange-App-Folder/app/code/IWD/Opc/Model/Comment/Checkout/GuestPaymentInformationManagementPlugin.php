<?php

namespace IWD\Opc\Model\Comment\Checkout;

class GuestPaymentInformationManagementPlugin
{

    /** @var \Magento\Sales\Model\Order\Status\HistoryFactory $historyFactory */
    protected $historyFactory;
    /** @var \Magento\Sales\Model\OrderFactory $orderFactory */
    protected $orderFactory;
	protected $orderCookie;

    /**
     * @param \Magento\Sales\Model\Order\Status\HistoryFactory $historyFactory
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     */
    public function __construct(
        \Magento\Sales\Model\Order\Status\HistoryFactory $historyFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
		\Orange\Customer\Model\CustomerCookie $customerCookie
    ) {
        $this->historyFactory = $historyFactory;
        $this->orderFactory = $orderFactory;
		$this->orderCookie = $customerCookie;
    }

    /**
     * @param \Magento\Checkout\Model\PaymentInformationManagement $subject
     * @param \Closure $proceed
     * @param int $cartId
     * @param \Magento\Quote\Api\Data\PaymentInterface $paymentMethod
     * @param \Magento\Quote\Api\Data\AddressInterface $billingAddress
     *
     * @return int $orderId
     */
    public function aroundSavePaymentInformationAndPlaceOrder(
        \Magento\Checkout\Model\GuestPaymentInformationManagement $subject,
        \Closure $proceed,
        $cartId,
        $email,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        /** @param string $comment */
        $comment = NULL;
        // get JSON post data
        $request_body = file_get_contents('php://input');
        // decode JSON post data into array
        $data = json_decode($request_body, true);
        // get order comments
        if (isset ($data['paymentMethod']['additional_data']['comments'])) {
            // make sure there is a comment to save
            $orderComments = $data['paymentMethod']['additional_data']['comments'];
            if ($orderComments) {
                // remove any HTML tags
                $comment = strip_tags ($comment);
                $comment = $orderComments;
            }
        }
        // run parent method and capture int $orderId
        $orderId = $proceed($cartId, $email, $paymentMethod, $billingAddress);
		/** @param \Magento\Sales\Model\OrderFactory $order */
        $order = $this->orderFactory->create()->load($orderId);
        // if $comments
        if ($comment) {
            
            // make sure $order exists
            if ($order->getData('entity_id')) {
                /** @param string $status */
                $status = $order->getData('status');
                /** @param \Magento\Sales\Model\Order\Status\HistoryFactory $history */
                $history = $this->historyFactory->create();
                // set comment history data
                $history->setData('comment', $comment);
                $history->setData('parent_id', $orderId);
                $history->setData('is_visible_on_front', 1);
                $history->setData('is_customer_notified', 0);
                $history->setData('entity_name', 'order');
                $history->setData('status', $status);
                $history->save();
            }
        }
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$objectManager->get('Orange\Customer\Model\CustomerCookie')
			->setLastOrderCookie($order->getIncrementId(), 3600);
		$objectManager->get('Psr\Log\LoggerInterface')->addDebug('lastpaymentOrderId:'.$order->getIncrementId());
		
        return $orderId;
    }
}