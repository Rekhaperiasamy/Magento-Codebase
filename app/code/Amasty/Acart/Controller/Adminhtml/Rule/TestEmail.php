<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Acart
 */

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Amasty\Acart\Controller\Adminhtml\Rule;

class TestEmail extends \Amasty\Acart\Controller\Adminhtml\Rule
{
    public function execute()
    {
        $quoteId = $this->getRequest()->getParam('quote_id');
        $ruleId = $this->getRequest()->getParam('rule_id');

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->_objectManager->create('Magento\Quote\Model\Quote')->load($quoteId);
        /** @var \Amasty\Acart\Model\Rule $rule */
        $rule = $this->_objectManager->create('Amasty\Acart\Model\Rule')->load($ruleId);
        /** @var \Amasty\Acart\Model\QuoteEmail $quoteEmail */
        $quoteEmail = $this->_objectManager->create('Amasty\Acart\Model\QuoteEmail')->load($quoteId, 'quote_id');

        try {
            if ($quote->getId() && $rule->getId()) {
                if ($quoteEmail->getId()) {
                    $quote->setAcartQuoteEmail($quoteEmail->getCustomerEmail());
                }

                $ruleQuote = $this->_objectManager
                    ->create('Amasty\Acart\Model\RuleQuote')
                    ->createRuleQuote($rule, $quote, true);

                if ($ruleQuote->getId()) {
                    $historyResource = $this->_objectManager->create('Amasty\Acart\Model\ResourceModel\History\Collection')
                    ->addRuleQuoteData()
                    ->addRuleData()
                    ->addFieldToFilter('main_table.rule_quote_id', $ruleQuote->getId());

                    foreach ($historyResource as $history) {
                        $history->execute(true);
                    }

                    $ruleQuote->complete();
                } else {
                    throw new \Magento\Framework\Exception\LocalizedException(__("Email didn't send."));
                }
            }
        } catch (\Magento\Framework\Exception\InputException $e) {
            $this->getMessageManager()->addError($e->getMessage());
            $this->logger->critical($e);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->getMessageManager()->addError($e->getMessage());
            $this->logger->critical($e);
        } catch (\Exception $e) {
            $this->getMessageManager()->addError($e->getMessage());
            $this->logger->critical($e);
        }

        $messages = $this->getMessageManager()->getMessages(true);

        return $this->resultJsonFactory->create()->setData([
            'error' => $messages->getCount() > 0,
            'errorMsg' => $messages->getCount() > 0 ?$messages->getLastAddedMessage()->getText() : null
        ]);
    }
}
