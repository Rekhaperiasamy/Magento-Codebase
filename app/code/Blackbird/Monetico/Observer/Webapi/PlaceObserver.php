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
namespace Blackbird\Monetico\Observer\Webapi;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class PlaceObserver implements ObserverInterface
{
    /**
     * @var \Blackbird\Monetico\Model\Config\Source\PaymentMethod
     */
    protected $_methodSource;

    /**
     * @param \Blackbird\Monetico\Model\Config\Source\PaymentMethod $paymentMethod
     */
    public function __construct(
        \Blackbird\Monetico\Model\Config\Source\PaymentMethod $paymentMethod
    ) {
        $this->_methodSource = $paymentMethod;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var  \Magento\Sales\Model\Order\Payment $payment */
        $payment = $observer->getEvent()->getPayment();

        if (in_array($payment->getMethod(), $this->_methodSource->getAvailablePaymentMethods())) {
            $payment->getOrder()->setCanSendNewEmailFlag(false);
        }
    }
}
