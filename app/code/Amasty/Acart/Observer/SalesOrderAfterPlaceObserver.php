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
namespace Amasty\Acart\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class SalesOrderAfterPlaceObserver implements ObserverInterface
{
    protected $_ruleQuoteFactory;

    public function __construct(
        \Amasty\Acart\Model\RuleQuoteFactory $ruleQuoteFactory
    ) {
        $this->_ruleQuoteFactory = $ruleQuoteFactory;
    }

    public function execute(EventObserver $observer)
    {
        $order = $observer->getEvent()->getOrder();

        if (!$order) {
            return $this;
        }

        $ruleQuote = $this->_ruleQuoteFactory->create();
        $ruleQuote->buyQuote($order->getQuoteId());
    }
}