<?php

namespace Netresearch\OPS\Controller;

/**
 * AliasController.php
 *
 * @author    paul.siedler@netresearch.de
 * @copyright Copyright (c) 2015 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License
 */
abstract class Alias extends \Netresearch\OPS\Controller\AbstractController
{
    /**
     * @var \Netresearch\OPS\Helper\Alias
     */
    protected $oPSAliasHelper;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $quoteQuoteFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Netresearch\OPS\Model\ConfigFactory $oPSConfigFactory,
        \Netresearch\OPS\Helper\Order $oPSOrderHelper,
        \Netresearch\OPS\Helper\Payment $oPSPaymentHelper,
        \Netresearch\OPS\Helper\Directlink $oPSDirectlinkHelper,
        \Netresearch\OPS\Helper\Data $oPSHelper,
        \Magento\Quote\Model\QuoteFactory $quoteQuoteFactory,
        \Netresearch\OPS\Helper\Alias $oPSAliasHelper
    ) {
        parent::__construct(
            $context,
            $checkoutSession,
            $oPSConfigFactory,
            $oPSOrderHelper,
            $oPSPaymentHelper,
            $oPSDirectlinkHelper,
            $oPSHelper
        );
        $this->quoteQuoteFactory = $quoteQuoteFactory;
        $this->oPSAliasHelper = $oPSAliasHelper;
    }

    /**
     * Get store id from quote or request
     *
     * @return int
     */
    protected function getStoreId()
    {
        $storeId = null;
        $quoteId = $this->getRequest()->getParam('orderid');

        $quote = $this->quoteQuoteFactory->create()->load($quoteId);

        if (null === $quote->getId()) {
            $quote = $this->quoteQuoteFactory->create()->loadByIdWithoutStore($quoteId);
        }

        if (null !== $quote->getId()) {
            $storeId = $quote->getStoreId();
        }
        if (null !== $this->getRequest()->getParam('storeId')) {
            $storeId = $this->getRequest()->getParam('storeId');
        }

        return $storeId;
    }

    /**
     * updates the additional information from payment, thats needed for backend reOrders
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param array $params
     */
    public function updateAdditionalInformation(\Magento\Quote\Model\Quote $quote, $params)
    {
        if (null !== $quote->getId() && $quote->getPayment() && null !== $quote->getPayment()->getId()) {
            $payment = $quote->getPayment();
            if (array_key_exists('Alias_AliasId', $params)) {
                $payment->setAdditionalInformation('alias', $params['Alias_AliasId']);
            }
            if (array_key_exists('Card_Brand', $params)) {
                $payment->setAdditionalInformation('CC_BRAND', $params['Card_Brand']);
            }
            if (array_key_exists('Card_CardHolderName', $params)) {
                $payment->setAdditionalInformation('CC_CN', $params['Card_CardHolderName']);
            }
            if ($this->userIsRegistering()) {
                $payment->setAdditionalInformation('userIsRegistering', true);
            }
            $quote->setPayment($payment)->setDataChanges(true)->save();
            $quote->setDataChanges(true)->save();
        }
    }

    /**
     * Checks if checkout method is registering
     *
     * @return bool
     */
    protected function userIsRegistering()
    {
        return $this->getQuote()->getCheckoutMethod() === \Magento\Checkout\Model\Type\Onepage::METHOD_REGISTER;
    }

    /**
     * Cleans param array from magentos admin params, fixes underscored keys
     *
     * @return array
     */
    protected function cleanParamKeys()
    {
        $data = [];
        foreach ($this->getRequest()->getParams() as $key => $value) {
            if ($key == 'form_key' || $key == 'isAjax' || $key == 'key') {
                continue;
            }
            $data[str_replace('_', '.', $key)] = $value;
        }

        return $data;
    }
}
