<?php

namespace Netresearch\OPS\Controller\Api;

class DirectLinkPostBack extends \Netresearch\OPS\Controller\Api
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $opsIncomingLogger;

    /**
     * DirectLinkPostBack constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Netresearch\OPS\Model\ConfigFactory $oPSConfigFactory
     * @param \Netresearch\OPS\Helper\Order $oPSOrderHelper
     * @param \Netresearch\OPS\Helper\Payment $oPSPaymentHelper
     * @param \Netresearch\OPS\Helper\Directlink $oPSDirectlinkHelper
     * @param \Netresearch\OPS\Helper\Data $oPSHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Netresearch\OPS\Helper\Api $oPSApiHelper
     * @param \Psr\Log\LoggerInterface $opsIncomingLogger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Netresearch\OPS\Model\ConfigFactory $oPSConfigFactory,
        \Netresearch\OPS\Helper\Order $oPSOrderHelper,
        \Netresearch\OPS\Helper\Payment $oPSPaymentHelper,
        \Netresearch\OPS\Helper\Directlink $oPSDirectlinkHelper,
        \Netresearch\OPS\Helper\Data $oPSHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Netresearch\OPS\Helper\Api $oPSApiHelper,
        \Psr\Log\LoggerInterface $opsIncomingLogger
    ) {
        parent::__construct(
            $context,
            $checkoutSession,
            $oPSConfigFactory,
            $oPSOrderHelper,
            $oPSPaymentHelper,
            $oPSDirectlinkHelper,
            $oPSHelper,
            $storeManager,
            $oPSApiHelper
        );
        $this->opsIncomingLogger = $opsIncomingLogger;
    }

    /**
     * Action to control postback data from ops
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $this->opsIncomingLogger->info("Incoming Request on Directlink: " . serialize($params));
        try {
            $this->getDirectlinkHelper()->processFeedback(
                $this->_getOrder(),
                $params
            );
        } catch (\Exception $e) {
            $this->oPSHelper->log(sprintf('Run into exception %s in directLinkPostBackAction', $e->getMessage()));
            $this->getResponse()->setHttpResponseCode(500);
        }
    }
}
