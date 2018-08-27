<?php

namespace Netresearch\OPS\Controller\Alias;

class GenerateHash extends \Netresearch\OPS\Controller\Alias
{
    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * GenerateHash constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Netresearch\OPS\Model\ConfigFactory $oPSConfigFactory
     * @param \Netresearch\OPS\Helper\Order $oPSOrderHelper
     * @param \Netresearch\OPS\Helper\Payment $oPSPaymentHelper
     * @param \Netresearch\OPS\Helper\Directlink $oPSDirectlinkHelper
     * @param \Netresearch\OPS\Helper\Data $oPSHelper
     * @param \Magento\Quote\Model\QuoteFactory $quoteQuoteFactory
     * @param \Netresearch\OPS\Helper\Alias $oPSAliasHelper
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Netresearch\OPS\Model\ConfigFactory $oPSConfigFactory,
        \Netresearch\OPS\Helper\Order $oPSOrderHelper,
        \Netresearch\OPS\Helper\Payment $oPSPaymentHelper,
        \Netresearch\OPS\Helper\Directlink $oPSDirectlinkHelper,
        \Netresearch\OPS\Helper\Data $oPSHelper,
        \Magento\Quote\Model\QuoteFactory $quoteQuoteFactory,
        \Netresearch\OPS\Helper\Alias $oPSAliasHelper,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder
    ) {
        parent::__construct(
            $context,
            $checkoutSession,
            $oPSConfigFactory,
            $oPSOrderHelper,
            $oPSPaymentHelper,
            $oPSDirectlinkHelper,
            $oPSHelper,
            $quoteQuoteFactory,
            $oPSAliasHelper
        );
        $this->jsonEncoder = $jsonEncoder;
    }

    /**
     * Generates the hash for the hosted tokenization page request
     *
     * @throws Exception
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
