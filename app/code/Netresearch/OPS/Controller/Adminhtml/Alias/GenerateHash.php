<?php

namespace Netresearch\OPS\Controller\Adminhtml\Alias;

class GenerateHash extends \Netresearch\OPS\Controller\Adminhtml\Alias
{
    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * GenerateHash constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Netresearch\OPS\Helper\Data $oPSHelper
     * @param \Magento\Quote\Model\QuoteFactory $quoteQuoteFactory
     * @param \Netresearch\OPS\Helper\Alias $oPSAliasHelper
     * @param \Netresearch\OPS\Model\Config $oPSConfig
     * @param \Netresearch\OPS\Helper\Payment $oPSPaymentHelper
     * @param \Magento\Backend\Model\Session\Quote $backendSessionQuote
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Netresearch\OPS\Helper\Data $oPSHelper,
        \Magento\Quote\Model\QuoteFactory $quoteQuoteFactory,
        \Netresearch\OPS\Helper\Alias $oPSAliasHelper,
        \Netresearch\OPS\Model\Config $oPSConfig,
        \Netresearch\OPS\Helper\Payment $oPSPaymentHelper,
        \Magento\Backend\Model\Session\Quote $backendSessionQuote,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder
    ) {
        parent::__construct(
            $context,
            $oPSHelper,
            $quoteQuoteFactory,
            $oPSAliasHelper,
            $oPSConfig,
            $oPSPaymentHelper,
            $backendSessionQuote
        );
        $this->jsonEncoder = $jsonEncoder;
    }

    /**
     * Generates the hash for the hosted tokenization page request
     */
    public function execute()
    {
        $storeId = $this->getStoreId();

        $result = $this->generateHash($storeId);

        $this->getResponse()->setBody($this->jsonEncoder->encode($result));
    }

    /**
     * Generate hash from request parameters
     *
     * @param $storeId
     *
     * @return array
     */
    protected function generateHash($storeId)
    {
        $data = $this->cleanParamKeys();

        $secret = $this->getConfig()->getShaOutCode($storeId);
        $raw = $this->getPaymentHelper()->getSHAInSet($data, $secret);
        $result = ['hash' => $this->getPaymentHelper()->shaCrypt($raw)];

        return $result;
    }
}
