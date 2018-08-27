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
 *
 * RetryPayment.php
 *
 * @category  Payment
 * @package   Netresearch_OPS
 * @author    Paul Siedler <paul.siedler@netresearch.de>
 */

namespace Netresearch\OPS\Model\Rest;

use \Netresearch\OPS\Helper\Order as OrderHelper;

class RetryPayment implements \Netresearch\OPS\Api\RetryPaymentInterface
{
    /** @var  OrderHelper */
    private $orderHelper;

    /** @var  \Magento\Checkout\Model\Session */
    private $checkoutSession;

    /** @var \Magento\Quote\Api\CartRepositoryInterface */
    private $quoteRepository;

    public function __construct(
        OrderHelper $orderHelper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository
    ) {
        $this->orderHelper = $orderHelper;
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $cartRepository;
    }

    public function updatePaymentInformation(
        $cartId,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {

        $order = $this->getOrder();

        if (!$order) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Order could not be found.'));
        }

        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $order->getPayment();
        $quoteId = $order->getQuoteId();
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->get($quoteId);

        $quote->getPayment()->importData($paymentMethod->getData());
        $payment->setMethod($paymentMethod['method'])
            ->getMethodInstance()
            ->assignData($paymentMethod);

        $quote->save();
        $payment->save();
        $order->save();


        $redirectUrl = $payment->getMethodInstance()->getOrderPlaceRedirectUrl($payment);

        // Place order or rather in this case, send the inline payment method to Ingenico ePayments
        if (empty($redirectUrl)) {
            $this->checkoutSession->setRedirectUrl($redirectUrl);
            $order->place();
            $order->save();
        }

        // Set Session Data for further process
        $this->checkoutSession->setLastOrderId($order->getId());
        $this->checkoutSession->setLastRealOrderId($order->getIncrementId());
        $this->checkoutSession->setLastQuoteId($quote->getId());
        $this->checkoutSession->setLastSuccessQuoteId($quote->getId());
        $this->checkoutSession->setPaymentRetryFlow(false);
    }

    /**
     * @return bool|\Magento\Sales\Model\Order
     */
    private function getOrder()
    {
        $order = false;
        if ($this->checkoutSession->getOrderOnRetry()) {
            $order = $this->orderHelper->getOrder(OrderHelper::DELIMITER . $this->checkoutSession->getOrderOnRetry());
        }

        return $order;
    }
}
