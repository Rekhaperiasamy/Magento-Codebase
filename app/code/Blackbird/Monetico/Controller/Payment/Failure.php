<?php
/**
 * Blackbird Monetico Module
 *
 * NOTICE OF LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @category    Blackbird
 * @package     Blackbird_Monetico
 * @copyright   Copyright (c) 2018 Blackbird (https://black.bird.eu)
 * @author      Blackbird Team
 * @license     https://store.bird.eu/license/
 * @support     help@bird.eu
 */
namespace Blackbird\Monetico\Controller\Payment;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order;

class Failure extends \Blackbird\Monetico\Controller\Payment
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_config->setMethodCode('monetico');
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create()->setPath('checkout/onepage/failure');
        $order = $this->_checkoutSession->getLastRealOrder();

        if ($order && $order->getPayment()) {
            $status = $this->getConfigOrderCancelStatus($order);
            $isProcessed = true;

            try {
                if ($status === Order::STATE_CANCELED && $order->canCancel()) {
                    $order->cancel();
                } elseif ($status === Order::STATE_HOLDED && $order->canHold()) {
                    $order->hold();
                } else {
                    $isProcessed = false;
                }

                if ($isProcessed) {
                    $order->addStatusToHistory($status, __('The order has been canceled by the customer.'));
                    $this->_orderRepository->save($order);

                    if ($this->_config->getSystemConfigValue('canceled_empty_cart')) {
                        $this->emptyCart();
                    } else {
                        $this->fillCart();
                    }

                    if ($this->_config->getSystemConfigValue('canceled_redirect_cart')) {
                        $resultRedirect->setPath('checkout/cart/');
                    } else {
                        // todo update the cart section
                        $message = $this->_config->getSystemConfigValue('canceled_error_message');

                        if (!empty($message)) {
                            $this->_checkoutSession->setErrorMessage($message);
                        } else {
                            $this->_checkoutSession->setErrorMessage(__('Your order has been canceled.'));
                        }
                    }
                }
            } catch (LocalizedException $e) {
                $this->messageManager->addExceptionMessage($e, __('An error has occurred during the cancellation.'));
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, $e->getMessage());
            }
        }

        return $resultRedirect;
    }

    /**
     * Retrieve the cancel status configured for the method of the order
     *
     * @param Order $order
     * @return string
     */
    protected function getConfigOrderCancelStatus(Order $order)
    {
        $commonCode = $this->_config->getMethodCode();
        $this->_config->setMethodCode($order->getPayment()->getMethod());
        $status = $this->_config->getSystemConfigValue('order_status_payment_canceled');
        $this->_config->setMethodCode($commonCode);

        return $status;
    }

    /**
     * Reset et restore the cart
     *
     * @return void
     */
    protected function fillCart()
    {
        $this->_checkoutSession->restoreQuote();
    }

    /**
     * Empty and reset the cart
     *
     * @return void
     */
    protected function emptyCart()
    {
        $this->_checkoutSession->clearQuote();
        $this->_checkoutSession->clearStorage();
        $this->_checkoutSession->clearHelperData();
        $this->_checkoutSession->resetCheckout();
    }
}
