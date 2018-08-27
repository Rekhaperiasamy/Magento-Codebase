<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Checkout
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Checkout\Block;

use Magento\Framework\View\Element\Template;

/**
 * Class Registration
 *
 * @package Dilmah\Checkout\Block
 */
class Registration extends \Magento\Checkout\Block\Registration
{

    /**
     * Registration constructor.
     *
     * @param Template\Context                                 $context
     * @param \Magento\Checkout\Model\Session                  $checkoutSession
     * @param \Magento\Customer\Model\Session                  $customerSession
     * @param \Magento\Customer\Model\Registration             $registration
     * @param \Magento\Customer\Api\AccountManagementInterface $accountManagement
     * @param \Magento\Sales\Api\OrderRepositoryInterface      $orderRepository
     * @param \Magento\Sales\Model\Order\Address\Validator     $addressValidator
     */
    public function __construct(
        Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Registration $registration,
        \Magento\Customer\Api\AccountManagementInterface $accountManagement,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Model\Order\Address\Validator $addressValidator
    ) {
        parent::__construct(
            $context,
            $checkoutSession,
            $customerSession,
            $registration,
            $accountManagement,
            $orderRepository,
            $addressValidator
        );
    }

    /**
     * {@inheritdoc}
     */
    public function toHtml()
    {
        if (
            $this->customerSession->isLoggedIn()
            || !$this->registration->isAllowed()
            || !$this->accountManagement->isEmailAvailable($this->getEmailAddress())
        ) {
            return '';
        }
        return \Magento\Framework\View\Element\Template::toHtml();
    }
}
