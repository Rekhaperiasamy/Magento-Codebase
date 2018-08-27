<?php
/**
 * \Netresearch\OPS\Controller\AbstractController
 *
 * @package
 * @copyright 2011 Netresearch
 * @author    Thomas Kappel <thomas.kappel@netresearch.de>
 * @author    André Herrn <andre.herrn@netresearch.de>
 * @license   OSL 3.0
 */
namespace Netresearch\OPS\Controller;

abstract class AbstractController extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $_order;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Netresearch\OPS\Model\ConfigFactory
     */
    protected $oPSConfigFactory;

    /**
     * @var \Netresearch\OPS\Helper\Order
     */
    protected $oPSOrderHelper;

    /**
     * @var \Netresearch\OPS\Helper\Payment
     */
    protected $oPSPaymentHelper;

    /**
     * @var \Netresearch\OPS\Helper\Directlink
     */
    protected $oPSDirectlinkHelper;

    /**
     * @var \Netresearch\OPS\Helper\Data
     */
    protected $oPSHelper;

    /**
     * AbstractController constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Netresearch\OPS\Model\ConfigFactory $oPSConfigFactory
     * @param \Netresearch\OPS\Helper\Order $oPSOrderHelper
     * @param \Netresearch\OPS\Helper\Payment $oPSPaymentHelper
     * @param \Netresearch\OPS\Helper\Directlink $oPSDirectlinkHelper
     * @param \Netresearch\OPS\Helper\Data $oPSHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Netresearch\OPS\Model\ConfigFactory $oPSConfigFactory,
        \Netresearch\OPS\Helper\Order $oPSOrderHelper,
        \Netresearch\OPS\Helper\Payment $oPSPaymentHelper,
        \Netresearch\OPS\Helper\Directlink $oPSDirectlinkHelper,
        \Netresearch\OPS\Helper\Data $oPSHelper
    ) {
        parent::__construct($context);
        $this->checkoutSession = $checkoutSession;
        $this->oPSConfigFactory = $oPSConfigFactory;
        $this->oPSOrderHelper = $oPSOrderHelper;
        $this->oPSPaymentHelper = $oPSPaymentHelper;
        $this->oPSDirectlinkHelper = $oPSDirectlinkHelper;
        $this->oPSHelper = $oPSHelper;
    }

    protected function getQuote()
    {
        return $this->_getCheckout()->getQuote();
    }

    /**
     * Get checkout session namespace
     *
     * @return \Magento\Checkout\Model\Session
     */
    protected function _getCheckout()
    {
        return $this->checkoutSession;
    }

    protected function getConfig()
    {
        return $this->oPSConfigFactory->create();
    }

    /**
     * Return order instance loaded by increment id
     *
     * @return \Magento\Sales\Model\Order
     */

    protected function _getOrder($opsOrderId = null)
    {
        if (empty($this->_order)) {
            if (null === $opsOrderId) {
                $opsOrderId = $this->getRequest()->getParam('orderID');
            }
            $this->_order = $this->oPSOrderHelper->getOrder($opsOrderId);
        }
        return $this->_order;
    }

    /**
     * Get singleton with Checkout by OPS Api
     *
     * @return \Netresearch\OPS\Model\Payment\PaymentAbstract
     */
    protected function _getApi()
    {
        if (null !== $this->getRequest()->getParam('orderID')) {
            return $this->_getOrder()->getPayment()->getMethodInstance();
        } else {
            return $this->checkoutSession->getQuote()->getPayment()->getMethodInstance();
        }
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
     * get direct link helper
     *
     * @return \Netresearch\OPS\Helper\Directlink
     */
    protected function getDirectlinkHelper()
    {
        return $this->oPSDirectlinkHelper;
    }

    /**
     * Validation of incoming OPS data
     *
     * @return bool
     */
    protected function _validateOPSData($paramOverwrite = false)
    {
        $params = $paramOverwrite ? : $this->getRequest()->getParams();
        $order = $this->_getOrder();
        if (!$order->getId()) {
            $this->oPSHelper->log(
                __(
                    "Incoming Ingenico ePayments Feedback\n\nRequest Path: %1\nParams: %2\n\nOrder not valid\n",
                    $this->getRequest()->getPathInfo(),
                    serialize($this->getRequest()->getParams())
                )
            );
            $this->messageManager->addError(__('Order is not valid'));
            return false;
        }
        $storeId = $order->getStoreId();

        $template = $this->getConfig()->getConfigData('template');
        //remove custom responseparams, because they are not hashed by Ingenico ePayments
        if ($template == \Netresearch\OPS\Model\Payment\PaymentAbstract::TEMPLATE_OPS_IFRAME
            && array_key_exists('IFRAME', $params)
        ) {
            unset($params['IFRAME']);
        }

        $secureKey = $this->getConfig()->getShaInCode($storeId);
        $secureSet = $this->getPaymentHelper()->getSHAInSet($params, $secureKey);

        $this->oPSHelper->log(
            __(
                "Incoming Ingenico ePayments Feedback\n\nRequest Path: %1\nParams: %2\n",
                $this->getRequest()->getPathInfo(),
                serialize($this->getRequest()->getParams())
            )
        );

        if ($this->oPSPaymentHelper->shaCryptValidation($secureSet, $params['SHASIGN']) !== true) {
            $this->messageManager->addError(__('Hash is not valid'));
            return false;
        }

        return true;
    }

    public function isJsonRequested($params)
    {
        if (array_key_exists('RESPONSEFORMAT', $params) && $params['RESPONSEFORMAT'] == 'JSON') {
            return true;
        }
        return false;
    }
}
