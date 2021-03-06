<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Dilmah\Payments\Model;

use Magento\Framework\Exception\State\InvalidTransitionException;

/**
 * Class PaymentMethodManagement
 */
class PaymentMethodManagement implements \Dilmah\Payments\Api\PaymentMethodManagementInterface
{
    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Payment\Model\Checks\ZeroTotal
     */
    protected $zeroTotalValidator;

    /**
     * @var \Magento\Payment\Model\MethodList
     */
    protected $methodList;

    /**
     * @var \Magento\Quote\Api\GuestBillingAddressManagementInterface
     */
    protected $billingAddressManagement;

    /**
     * @var \Magento\Quote\Model\QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;

    /**
     * PaymentMethodManagement constructor.
     *
     * @param \Magento\Quote\Api\CartRepositoryInterface                 $quoteRepository
     * @param \Magento\Payment\Model\Checks\ZeroTotal                    $zeroTotalValidator
     * @param \Magento\Payment\Model\MethodList                          $methodList
     * @param \Magento\Quote\Api\GuestBillingAddressManagementInterface  $billingAddressManagement
     * @param \Magento\Quote\Model\QuoteIdMaskFactory                    $quoteIdMaskFactory
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Payment\Model\Checks\ZeroTotal $zeroTotalValidator,
        \Magento\Payment\Model\MethodList $methodList,
        \Magento\Quote\Api\GuestBillingAddressManagementInterface $billingAddressManagement,
        \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->zeroTotalValidator = $zeroTotalValidator;
        $this->methodList = $methodList;
        $this->billingAddressManagement = $billingAddressManagement;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
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
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->get($cartId);

        $method->setChecks([
            \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_CHECKOUT,
            \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_FOR_COUNTRY,
            \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_FOR_CURRENCY,
            \Magento\Payment\Model\Method\AbstractMethod::CHECK_ORDER_TOTAL_MIN_MAX,
        ]);
        $payment = $quote->getPayment();

        $data = $method->getData();
        if (isset($data['additional_data'])) {
            $data = array_merge($data, (array)$data['additional_data']);
            unset($data['additional_data']);
        }
        $payment->importData($data);
        $quote->setCustomerEmail($email);
        $quote->getBillingAddress()->setEmail($email);
        if ($quote->isVirtual()) {
            $quote->getBillingAddress()->setPaymentMethod($payment->getMethod());
        } else {
            // check if shipping address is set
            if ($quote->getShippingAddress()->getCountryId() === null) {
                throw new InvalidTransitionException(__('Shipping address is not set'));
            }
            $quote->getShippingAddress()->setPaymentMethod($payment->getMethod());
            $quote->getShippingAddress()->setEmail($email);
        }
        if (!$quote->isVirtual() && $quote->getShippingAddress()) {
            $quote->getShippingAddress()->setCollectShippingRates(true);
        }

        if (!$this->zeroTotalValidator->isApplicable($payment->getMethodInstance(), $quote)) {
            throw new InvalidTransitionException(__('The requested Payment Method is not available.'));
        }

        $quote->setTotalsCollectedFlag(false)->collectTotals()->save();
        return $quote->getPayment()->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function get($cartId)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->get($cartId);
        $payment = $quote->getPayment();
        if (!$payment->getId()) {
            return null;
        }
        return $payment;
    }

    /**
     * {@inheritdoc}
     */
    public function getList($cartId)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->get($cartId);
        return $this->methodList->getAvailableMethods($quote);
    }
}
