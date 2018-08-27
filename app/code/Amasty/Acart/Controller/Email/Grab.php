<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Acart
 */

namespace Amasty\Acart\Controller\Email;

use Magento\Checkout\Model\Cart as CustomerCart;

class Grab extends \Amasty\Acart\Controller\Email
{
    /**
     * @var CustomerCart
     */
    protected $cart;

    /**
     * @var \Amasty\Acart\Model\QuoteEmail
     */
    private $quoteEmail;

    /**
     * Grab constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Amasty\Acart\Model\UrlManager $urlManager
     * @param \Amasty\Acart\Model\RuleQuote $ruleQuote
     * @param \Amasty\Acart\Model\ResourceModel\History\CollectionFactory $historyCollectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\SessionFactory $checkoutSessionFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param CustomerCart $cart
     * @param \Amasty\Acart\Model\QuoteEmailFactory $quoteEmailFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Amasty\Acart\Model\UrlManager $urlManager,
        \Amasty\Acart\Model\RuleQuote $ruleQuote,
        \Amasty\Acart\Model\ResourceModel\History\CollectionFactory $historyCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\SessionFactory $checkoutSessionFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        CustomerCart $cart,
        \Amasty\Acart\Model\QuoteEmailFactory $quoteEmailFactory
    ){
        $this->cart = $cart;
        $this->quoteEmail = $quoteEmailFactory->create();
        return parent::__construct(
            $context,
            $urlManager,
            $ruleQuote,
            $historyCollectionFactory,
            $customerSession,
            $checkoutSessionFactory,
            $customerFactory,
            $quoteFactory
        );
    }

    public function execute()
    {
        $email = $this->getRequest()->getParam('email');
        $quote = $this->cart->getQuote();

        if ($quote->getId()){
            $quote->setCustomerIsGuest(1);
            $quoteEmail = $this->quoteEmail->load($quote->getId(), 'quote_id')->addData([
                    'quote_id' => $quote->getId(),
                    'customer_email' => $email
            ])->save();

            $quote->save();
        }
    }


}