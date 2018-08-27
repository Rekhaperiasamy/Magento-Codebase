<?php
/**
 * \Netresearch\OPS\ApiController
 *
 * @package
 * @copyright 2011 Netresearch
 * @author    Thomas Kappel <thomas.kappel@netresearch.de>
 * @author    André Herrn <andre.herrn@netresearch.de>
 * @license   OSL 3.0
 */
namespace Netresearch\OPS\Controller;

use Magento\Framework\App\RequestInterface;

abstract class Api extends \Netresearch\OPS\Controller\AbstractController
{
    /**
     * Order instance
     */
    protected $_order;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Netresearch\OPS\Helper\Api
     */
    protected $oPSApiHelper;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Netresearch\OPS\Model\ConfigFactory $oPSConfigFactory
     * @param \Netresearch\OPS\Helper\Order $oPSOrderHelper
     * @param \Netresearch\OPS\Helper\Payment $oPSPaymentHelper
     * @param \Netresearch\OPS\Helper\Directlink $oPSDirectlinkHelper
     * @param \Netresearch\OPS\Helper\Data $oPSHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Netresearch\OPS\Helper\Api $oPSApiHelper
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
        \Netresearch\OPS\Helper\Api $oPSApiHelper
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
        $this->storeManager = $storeManager;
        $this->oPSApiHelper = $oPSApiHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->_validateOPSData()) {
            $this->getResponse()->setHttpResponseCode(422);
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }
        return parent::dispatch($request);
    }
}
