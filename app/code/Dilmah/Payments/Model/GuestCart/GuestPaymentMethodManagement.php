<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Dilmah\Payments\Model\GuestCart;

use Dilmah\Payments\Api\PaymentMethodManagementInterface;
use Dilmah\Payments\Api\GuestPaymentMethodManagementInterface;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;

/**
 * Payment method management class for guest carts.
 */
class GuestPaymentMethodManagement implements GuestPaymentMethodManagementInterface
{
    /**
     * @var QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;

    /**
     * @var PaymentMethodManagementInterface
     */
    protected $paymentMethodManagement;

    /**
     * Initialize dependencies.
     *
     * @param PaymentMethodManagementInterface $paymentMethodManagement
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     */
    public function __construct(
        PaymentMethodManagementInterface $paymentMethodManagement,
        QuoteIdMaskFactory $quoteIdMaskFactory
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->paymentMethodManagement = $paymentMethodManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function set(
        $cartId,
        \Magento\Quote\Api\Data\PaymentInterface $method,
        $email,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        /** @var $quoteIdMask QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        return $this->paymentMethodManagement->set($quoteIdMask->getQuoteId(), $method, $email, $billingAddress);
    }

    /**
     * {@inheritdoc}
     */
    public function get($cartId)
    {
        /** @var $quoteIdMask QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        return $this->paymentMethodManagement->get($quoteIdMask->getQuoteId());
    }

    /**
     * {@inheritdoc}
     */
    public function getList($cartId)
    {
        /** @var $quoteIdMask QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        return $this->paymentMethodManagement->getList($quoteIdMask->getQuoteId());
    }
}
