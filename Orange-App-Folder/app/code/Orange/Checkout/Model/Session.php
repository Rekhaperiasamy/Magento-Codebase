<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Orange\Checkout\Model;
/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Session extends \Magento\Checkout\Model\Session
{
	/**
     * Get checkout quote instance by current session
     *
     * @return Quote
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getQuote()
    {
        $this->_eventManager->dispatch('custom_quote_process', ['checkout_session' => $this]);

        if ($this->_quote === null) {
            $quote = $this->quoteFactory->create();
            if ($this->getQuoteId()) {
                try {
                    if ($this->_loadInactive) {
                        $quote = $this->quoteRepository->get($this->getQuoteId());
                    } else {
                        $quote = $this->quoteRepository->getActive($this->getQuoteId());
                    }

                    /**
                     * If current currency code of quote is not equal current currency code of store,
                     * need recalculate totals of quote. It is possible if customer use currency switcher or
                     * store switcher.
                     */
                    if ($quote->getQuoteCurrencyCode() != $this->_storeManager->getStore()->getCurrentCurrencyCode()) {
                        $quote->setStore($this->_storeManager->getStore());
                        $this->quoteRepository->save($quote->collectTotals());
                        /*
                         * We mast to create new quote object, because collectTotals()
                         * can to create links with other objects.
                         */
                        $quote = $this->quoteRepository->get($this->getQuoteId());
                    }
                } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                    $this->setQuoteId(null);
                }
            }

            if (!$this->getQuoteId()) {
                if ($this->_customerSession->isLoggedIn() || $this->_customer) {
                    $customerId = $this->_customer
                        ? $this->_customer->getId()
                        : $this->_customerSession->getCustomerId();
                    try {
                        $quote = $this->quoteRepository->getActiveForCustomer($customerId);
                        $this->setQuoteId($quote->getId());
                    } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                    }
                } else {
                    $quote->setIsCheckoutCart(true);
                    $this->_eventManager->dispatch('checkout_quote_init', ['quote' => $quote]);
                }
            }

            if ($this->_customer) {
                $quote->setCustomer($this->_customer);
            } elseif ($this->_customerSession->isLoggedIn()) {
                $quote->setCustomer($this->customerRepository->getById($this->_customerSession->getCustomerId()));
            }

            $quote->setStore($this->_storeManager->getStore());
            $this->_quote = $quote;
        }

        if (!$this->isQuoteMasked() && !$this->_customerSession->isLoggedIn() && $this->getQuoteId()) {
            $quoteId = $this->getQuoteId();
            /** @var $quoteIdMask \Magento\Quote\Model\QuoteIdMask */
            $quoteIdMask = $this->quoteIdMaskFactory->create()->load($quoteId, 'quote_id');
            if ($quoteIdMask->getMaskedId() === null) {
                $quoteIdMask->setQuoteId($quoteId)->save();
            }
            $this->setIsQuoteMasked(true);
        }

        $remoteAddress = $this->_remoteAddress->getRemoteAddress();
        if ($remoteAddress) {
            $this->_quote->setRemoteIp($remoteAddress);
            $xForwardIp = $this->request->getServer('HTTP_X_FORWARDED_FOR');
            $this->_quote->setXForwardedFor($xForwardIp);
        }

        /*if($this->_quote->getCustomerGroupId() != $this->getCustomerTypeId()) {
            $this->_quote->removeAllItems(); // Remove all items on context switch
        }*/

		$this->_quote->setCustomerGroupId($this->getCustomerTypeId()); // Set customer Group to Quote
        return $this->_quote;
    }
	
	private function getCustomerTypeId()
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$customerGroupId = $objectManager->create('Orange\Customer\Model\Session')->getCustomerTypeId();		
		return $customerGroupId;		
	}
}
