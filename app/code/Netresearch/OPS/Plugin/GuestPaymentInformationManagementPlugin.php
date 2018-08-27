<?php

/**
 * @category   OPS
 * @package    Netresearch_OPS
 * @author     Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright  Copyright (c) 2017 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Netresearch\OPS\Plugin;

use Magento\Checkout\Model\Session as CheckoutSession;

class GuestPaymentInformationManagementPlugin
{

    /** @var CheckoutSession */
    private $checkoutSession;

    /**
     * @var \Magento\Checkout\Api\PaymentInformationManagementInterface
     */
    protected $paymentInformationManagement;

    /**
     * @var \Magento\Quote\Model\QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;



    public function __construct(
        CheckoutSession $checkoutSession,
        \Magento\Checkout\Api\PaymentInformationManagementInterface $paymentInformationManagement,
        \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->paymentInformationManagement = $paymentInformationManagement;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
    }

    public function aroundGetPaymentInformation(
        \Magento\Checkout\Model\GuestPaymentInformationManagement $subject,
        \Closure $proceed,
        $cartId
    ) {

        if ($this->checkoutSession->getPaymentRetryFlow() === true) {
            $quoteIdMask    = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
            $quoteId        = $quoteIdMask->getQuoteId() ? : $cartId;
            $paymentDetails = $this->paymentInformationManagement->getPaymentInformation($quoteId);
        } else {
            $paymentDetails = $proceed($cartId);
        }

        return $paymentDetails;
    }
}
