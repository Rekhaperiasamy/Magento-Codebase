<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Acart
 */

namespace Amasty\Acart\Controller\Email;

use Magento\Framework\App\Action\Context;

class Url extends \Amasty\Acart\Controller\Email
{
    /**
     * @var \Amasty\Acart\Model\App\Response\Redirect
     */
    private $redirect;

    /**
     * Url constructor.
     * @param Context $context
     * @param \Amasty\Acart\Model\UrlManager $urlManager
     * @param \Amasty\Acart\Model\RuleQuote $ruleQuote
     * @param \Amasty\Acart\Model\ResourceModel\History\CollectionFactory $historyCollectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\SessionFactory $checkoutSessionFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \Amasty\Acart\Model\App\Response\Redirect $redirect
     */
    public function __construct(
        Context $context,
        \Amasty\Acart\Model\UrlManager $urlManager,
        \Amasty\Acart\Model\RuleQuote $ruleQuote,
        \Amasty\Acart\Model\ResourceModel\History\CollectionFactory $historyCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\SessionFactory $checkoutSessionFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Amasty\Acart\Model\App\Response\Redirect $redirect
    ) {
        parent::__construct(
            $context,
            $urlManager,
            $ruleQuote,
            $historyCollectionFactory,
            $customerSession,
            $checkoutSessionFactory,
            $customerFactory,
            $quoteFactory
        );
        $this->redirect = $redirect;
    }

    /**
     * @return mixed|null
     */
    protected function _getHistory()
    {
        $ret = null;

        $id = $this->getRequest()->getParam('id');
        $key = $this->getRequest()->getParam('key');

        $historyResource = $this->historyCollectionFactory->create()
            ->addRuleQuoteData()
            ->addFieldToFilter('main_table.history_id', $id);


        if ($historyResource->getSize() > 0) {
            $items = $historyResource->getItems();
            $history = end($items);

            if ($history->getId() && $history->getPublicKey() == $key) {
                $ret = $history;
            }
        }

        return $ret;
    }

    /**
     * execute
     */
    public function execute()
    {
        $url = $this->getRequest()->getParam('url');
        $mageUrl = $this->getRequest()->getParam('mageUrl');

        $history = $this->_getHistory();
        $this->urlManager->init($history->getRule(), $history);

        if ($history && ($url || $mageUrl)) {
            $target = null;

            if ($url){
                $target = $this->urlManager->getCleanUrl(base64_decode($url));
            } else if ($mageUrl){
                $target = $this->_url->getUrl($this->urlManager->getCleanUrl(
                    base64_decode($mageUrl)), $this->urlManager->getUtmParams()
                );
            }

            $this->loginCustomer($history);
            $ruleQuote = $this->ruleQuote->load($history->getRuleQuoteId());
            $ruleQuote->clickByLink($history);
            $this->getResponse()->setRedirect($this->redirect->validateRedirectUrl($target));
        } else {
            $this->_forward('defaultNoRoute');
        }
    }

    /**
     * @param $history
     */
    protected function loginCustomer($history)
    {
        $checkoutSession = $this->checkoutSessionFactory->create();

        if ($this->customerSession->isLoggedIn()) {
            if ($history->getCustomerId() != $this->customerSession->getCustomerId()) {
                $this->customerSession->logout();
            }
        }

        // customer. login
        if ($history->getCustomerId()){
            $customer = $this->customerFactory->create()->load($history->getCustomerId());

            if ($customer->getId()) {
                $this->customerSession->setCustomerAsLoggedIn($customer);
            }
        }
        elseif ($history->getQuoteId()){
            //visitor. restore quote in the session
            $quote = $this->quoteFactory->create()->load($history->getQuoteId());

            if ($quote){
                $checkoutSession->replaceQuote($quote);
                $quote->getBillingAddress()->setEmail($history->getEmail());
            }
        }

        if ($history->getSalesRuleCoupon()) {
            $code = $history->getSalesRuleCoupon();
            $quote = $checkoutSession->getQuote();

            if ($code && $quote){
                $quote->setCouponCode($code)
                    ->collectTotals()
                    ->save();
            }
        }
    }
}