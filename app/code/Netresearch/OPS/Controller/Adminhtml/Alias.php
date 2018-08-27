<?php

namespace Netresearch\OPS\Controller\Adminhtml;

abstract class Alias extends \Magento\Backend\App\Action
{
    /**
     * @var array
     */
    protected $_publicActions = ['accept', 'exception'];

    /**
     * @var \Netresearch\OPS\Helper\Data
     */
    protected $oPSHelper;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $quoteQuoteFactory;

    /**
     * @var \Netresearch\OPS\Helper\Alias
     */
    protected $oPSAliasHelper;

    /**
     * @var \Netresearch\OPS\Model\Config
     */
    protected $oPSConfig;

    /**
     * @var \Netresearch\OPS\Helper\Payment
     */
    protected $oPSPaymentHelper;

    /**
     * @var \Magento\Backend\Model\Session\Quote
     */
    protected $backendSessionQuote;

    /**
     * Alias constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Netresearch\OPS\Helper\Data $oPSHelper
     * @param \Magento\Quote\Model\QuoteFactory $quoteQuoteFactory
     * @param \Netresearch\OPS\Helper\Alias $oPSAliasHelper
     * @param \Netresearch\OPS\Model\Config $oPSConfig
     * @param \Netresearch\OPS\Helper\Payment $oPSPaymentHelper
     * @param \Magento\Backend\Model\Session\Quote $backendSessionQuote
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Netresearch\OPS\Helper\Data $oPSHelper,
        \Magento\Quote\Model\QuoteFactory $quoteQuoteFactory,
        \Netresearch\OPS\Helper\Alias $oPSAliasHelper,
        \Netresearch\OPS\Model\Config $oPSConfig,
        \Netresearch\OPS\Helper\Payment $oPSPaymentHelper,
        \Magento\Backend\Model\Session\Quote $backendSessionQuote
    ) {
        parent::__construct($context);
        $this->oPSHelper = $oPSHelper;
        $this->quoteQuoteFactory = $quoteQuoteFactory;
        $this->oPSAliasHelper = $oPSAliasHelper;
        $this->oPSConfig = $oPSConfig;
        $this->oPSPaymentHelper = $oPSPaymentHelper;
        $this->backendSessionQuote = $backendSessionQuote;
    }

    /**
     * @return \Magento\Quote\Model\Quote
     */
    protected function getQuote()
    {
        return $this->backendSessionQuote->getQuote();
    }

    /**
     * @return \Netresearch\OPS\Model\Config
     */
    protected function getConfig()
    {
        return $this->oPSConfig;
    }

    /**
     * get payment helper
     *
     * @return \Netresearch\OPS\Helper\Payment
     */
    protected function getPaymentHelper()
    {
        return $this->oPSPaymentHelper;
    }

    /**
     * Get store id from quote or request
     *
     * @return int
     */
    protected function getStoreId()
    {
        $quote = $this->getQuote();
        return $quote->getStoreId();
    }

    /**
     * updates the additional information from payment, thats needed for backend reOrders
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param array                  $params
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
            $quote->setPayment($payment)->setDataChanges(true);
            $quote->setDataChanges(true)->save();
            $quote->getPayment()->save();
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
