<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     Netresearch_OPS
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Netresearch\OPS\Helper;

class Quote extends \Magento\Framework\App\Helper\AbstractHelper
{
    const PAGE_SIZE = 100;

    const MINUTES_IN_PAST = 15;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $quoteQuoteFactory;

    /**
     * @var \Netresearch\OPS\Helper\Alias
     */
    protected $oPSAliasHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Netresearch\OPS\Model\ConfigFactory
     */
    protected $oPSConfigFactory;

    /**
     * @var \Magento\Backend\Model\Session\Quote
     */
    protected $backendSessionQuote;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote\Payment\CollectionFactory
     */
    protected $quotePaymentCollectionFactory;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $appState;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Quote\Model\QuoteFactory $quoteQuoteFactory,
        \Netresearch\OPS\Helper\Alias $oPSAliasHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Netresearch\OPS\Model\ConfigFactory $oPSConfigFactory,
        \Magento\Backend\Model\Session\Quote $backendSessionQuote,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Model\ResourceModel\Quote\Payment\CollectionFactory $quotePaymentCollectionFactory,
        \Magento\Framework\App\State $appState
    ) {
    
        parent::__construct($context);
        $this->quoteQuoteFactory = $quoteQuoteFactory;
        $this->oPSAliasHelper = $oPSAliasHelper;
        $this->storeManager = $storeManager;
        $this->oPSConfigFactory = $oPSConfigFactory;
        $this->backendSessionQuote = $backendSessionQuote;
        $this->checkoutSession = $checkoutSession;
        $this->quotePaymentCollectionFactory = $quotePaymentCollectionFactory;
        $this->appState = $appState;
    }

    /**
     * cleans up old payment information (deletes cvc etc. from additional data)
     */
    public function cleanUpOldPaymentInformation()
    {
        $allowedTimestamp = new \Zend_Db_Expr(sprintf(
            'NOW() - INTERVAL %d MINUTE',
            self::MINUTES_IN_PAST
        ));
        /*
         * fetching possible affected information from the sales_quote_payment table
         * criteria are:
         *  - ops_cc was used
         *  - the last update is more than 15 minutes ago
         *  - and CVC is included in the additional information
         */
        $paymentInformation = $this->quotePaymentCollectionFactory->create()
            ->addFieldToFilter('method', ['eq' => 'ops_cc'])
            ->addFieldToFilter('updated_at', ['lt' => $allowedTimestamp])
            ->addFieldToFilter('additional_information', ['like' => '%"cvc"%'])
            ->setOrder('created_at', 'DESC')
            ->setPageSize(self::PAGE_SIZE);
        foreach ($paymentInformation as $payment) {
            if (null !== $payment->getAdditionalInformation('cvc')) {
                // quote needs to be loaded, because saving the payment information would fail otherwise
                $payment->setQuote(
                    $this->quoteQuoteFactory->create()->load($payment->getQuoteId())
                );
                $this->oPSAliasHelper->cleanUpAdditionalInformation(
                    $payment,
                    true
                );
                $payment->save();
            }
        }
    }

    /**
     * returns the quote currency
     *
     * @param $quote
     *
     * @return string - the quotes currency
     */
    public function getQuoteCurrency(\Magento\Quote\Model\Quote $quote)
    {
        if ($quote->hasForcedCurrency()) {
            return $quote->getForcedCurrency()->getCode();
        } else {
            return $this->storeManager->getStore($quote->getStoreId())
                ->getBaseCurrencyCode();
        }
    }

    /**
     * get payment operation code
     *
     * @param \Magento\Sales\Model\Order $order
     *
     * @return string
     */
    public function getPaymentAction($order)
    {
        $operation
            = \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_AUTHORIZE_ACTION;

        // different capture operation name for direct debits
        if (\Netresearch\OPS\Model\Payment\DirectDebit::CODE == $order->getPayment()->getMethod()) {
            return \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_AUTHORIZE_CAPTURE_ACTION;
        }
        if ('authorize_capture' == $this->oPSConfigFactory->create()
                ->getPaymentAction($order->getStoreId())
        ) {
            $operation
                = \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_AUTHORIZE_CAPTURE_ACTION;
        }

        return $operation;
    }

    /**
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        if ($this->isBackendCurrentStore()) {
            return $this->backendSessionQuote->getQuote();
        }

        return $this->checkoutSession->getQuote();
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function isBackendCurrentStore()
    {
        return $this->appState->getAreaCode() === \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE;
    }
}
