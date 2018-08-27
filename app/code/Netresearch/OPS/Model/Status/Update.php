<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @copyright   Copyright (c) 2014 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Netresearch\OPS\Model\Status;

class Update
{
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /** @var \Netresearch\OPS\Model\Api\DirectLink $directLinkApi */
    protected $directLinkApi = null;

    protected $order = null;

    protected $requestParams = [];

    /** @var \Netresearch\OPS\Model\Config $opsConfig */
    protected $opsConfig = null;

    protected $opsResponse = [];

    protected $paymentHelper = null;

    protected $directLinkHelper = null;

    protected $messageContainer = null;

    protected $dataHelper = null;

    /**
     * @var \Netresearch\OPS\Helper\Data
     */
    protected $oPSHelper;

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

    /** @var \Netresearch\OPS\Helper\Order $orderHelper */
    protected $orderHelper = null;

    /**
     * @var \Netresearch\OPS\Helper\Directlink
     */
    protected $oPSDirectlinkHelper;

    /**
     * @var \Netresearch\OPS\Model\Response\Handler
     */
    protected $oPSResponseHandler;

    /**
     * Update constructor.
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Netresearch\OPS\Helper\Data $oPSHelper
     * @param \Netresearch\OPS\Model\ConfigFactory $oPSConfigFactory
     * @param \Netresearch\OPS\Helper\Order $oPSOrderHelper
     * @param \Netresearch\OPS\Model\Api\DirectLink $oPSApiDirectlink
     * @param \Netresearch\OPS\Helper\Payment $oPSPaymentHelper
     * @param \Netresearch\OPS\Helper\Directlink $oPSDirectlinkHelper
     * @param \Netresearch\OPS\Model\Response\Handler $oPSResponseHandler
     */
    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Netresearch\OPS\Helper\Data $oPSHelper,
        \Netresearch\OPS\Model\ConfigFactory $oPSConfigFactory,
        \Netresearch\OPS\Helper\Order $oPSOrderHelper,
        \Netresearch\OPS\Model\Api\DirectLink $oPSApiDirectlink,
        \Netresearch\OPS\Helper\Payment $oPSPaymentHelper,
        \Netresearch\OPS\Helper\Directlink $oPSDirectlinkHelper,
        \Netresearch\OPS\Model\Response\Handler $oPSResponseHandler
    ) {
        $this->messageManager = $messageManager;
        $this->oPSHelper = $oPSHelper;
        $this->oPSConfigFactory = $oPSConfigFactory;
        $this->oPSOrderHelper = $oPSOrderHelper;
        $this->directLinkApi = $oPSApiDirectlink;
        $this->oPSPaymentHelper = $oPSPaymentHelper;
        $this->oPSDirectlinkHelper = $oPSDirectlinkHelper;
        $this->oPSResponseHandler = $oPSResponseHandler;
    }

    /**
     * @param \Netresearch\OPS\Helper\Data $dataHelper
     */
    public function setDataHelper($dataHelper)
    {
        $this->dataHelper = $dataHelper;
    }

    /**
     * @return \Netresearch\OPS\Helper\Data
     */
    public function getDataHelper()
    {
        if (null == $this->dataHelper) {
            $this->dataHelper = $this->oPSHelper;
        }

        return $this->dataHelper;
    }

    /**
     * @param \Netresearch\OPS\Model\Config $opsConfig
     */
    public function setOpsConfig(\Netresearch\OPS\Model\Config $opsConfig)
    {
        $this->opsConfig = $opsConfig;
    }

    /**
     * @return \Netresearch\OPS\Model\Config
     */
    public function getOpsConfig()
    {
        if (null === $this->opsConfig) {
            $this->opsConfig = $this->oPSConfigFactory->create();
        }
        return $this->opsConfig;
    }

    /**
     * @param array $requestParams
     */
    public function setRequestParams($requestParams)
    {
        $this->requestParams = $requestParams;
    }

    /**
     * @return array
     */
    public function getRequestParams()
    {
        return $this->requestParams;
    }

    /**
     * @param \Netresearch\OPS\Helper\Order $orderHelper
     */
    public function setOrderHelper(\Netresearch\OPS\Helper\Order $orderHelper)
    {
        $this->orderHelper = $orderHelper;
    }

    /**
     * @return \Netresearch\OPS\Helper\Order
     */
    public function getOrderHelper()
    {
        if (null == $this->orderHelper) {
            $this->orderHelper = $this->oPSOrderHelper;
        }

        return $this->orderHelper;
    }

    /**
     * @param array $opsResponse
     */
    public function setOpsResponse($opsResponse)
    {
        $this->opsResponse = $opsResponse;
    }

    /**
     * @return array
     */
    public function getOpsResponse()
    {
        return $this->opsResponse;
    }

    /**
     * @param \Netresearch\OPS\Model\Api\DirectLink $directLinkApi
     */
    public function setDirectLinkApi(\Netresearch\OPS\Model\Api\DirectLink $directLinkApi)
    {
        $this->directLinkApi = $directLinkApi;
    }

    /**
     * @return \Netresearch\OPS\Model\Api\DirectLink
     */
    public function getDirectLinkApi()
    {
        return $this->directLinkApi;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     */
    public function setOrder(\Magento\Sales\Model\Order $order)
    {
        $this->order = $order;
    }

    /**
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    public function updateStatusFor(\Magento\Sales\Model\Order $order)
    {
        if (!($order->getPayment()->getMethodInstance() instanceof \Netresearch\OPS\Model\Payment\PaymentAbstract)) {
            return $this;
        }
        $this->setOrder($order);
        $this->buildParams($order->getPayment());

        try {
            $this->performRequest();
            $this->updatePaymentStatus();
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        return $this;
    }

    protected function buildParams(\Magento\Sales\Model\Order\Payment $payment)
    {
        // use PAYID if possible
        if (0 < strlen(trim($payment->getAdditionalInformation('paymentId')))) {
            $this->requestParams['PAYID'] = $payment->getAdditionalInformation('paymentId');
        } else {
            $useOrderId = true;
            if ($this->canNotUseOrderId($payment)) {
                $useOrderId = false;
            }

            $this->requestParams['ORDERID'] = $this->getOrderHelper()->getOpsOrderId($this->getOrder(), $useOrderId);
        }
        $this->addPayIdSub($payment);

        return $this;
    }

    protected function performRequest()
    {
        $storeId = $this->getOrder()->getStoreId();
        $url = $this->getOpsConfig()->getDirectLinkMaintenanceApiPath($storeId);
        try {
            $this->opsResponse = $this->getDirectLinkApi()->performRequest($this->getRequestParams(), $url, $storeId);
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
            return $this;
        }
        $this->opsResponse = array_change_key_case($this->opsResponse, CASE_UPPER);
        // in further processing the amount is sometimes in upper and sometimes in lower case :(
        if (array_key_exists('AMOUNT', $this->opsResponse)) {
            $this->opsResponse['amount'] = $this->opsResponse['AMOUNT'];
        }
        return $this;
    }

    protected function updatePaymentStatus()
    {
        if (!array_key_exists('STATUS', $this->getOpsResponse())
            || $this->opsResponse['STATUS'] == $this->getOrder()->getPayment()->getAdditionalInformation('status')
        ) {
            $this->messageManager->addNotice(__('No update available from Ingenico ePayments.'));
            return $this;
        }

        if (false != strlen(trim($this->getOrder()->getPayment()->getAdditionalInformation('paymentId')))) {
            $this->oPSResponseHandler->processResponse(
                $this->getOpsResponse(),
                $this->getOrder()->getPayment()->getMethodInstance()
            );
        } else {
            // simulate initial request
            $this->getPaymentHelper()->applyStateForOrder($this->getOrder(), $this->getOpsResponse());
        }

        $this->getPaymentHelper()->saveOpsStatusToPayment($this->getOrder()->getPayment(), $this->getOpsResponse());
        $this->messageManager->addSuccess(__('Ingenico ePayments status successfully updated'));

        return $this;
    }

    public function getPaymentHelper()
    {
        if (null == $this->paymentHelper) {
            $this->paymentHelper = $this->oPSPaymentHelper;
        }

        return $this->paymentHelper;
    }

    public function setPaymentHelper(\Netresearch\OPS\Helper\Payment $paymentHelper)
    {
        $this->paymentHelper = $paymentHelper;
    }

    public function getDirectLinkHelper()
    {
        if (null == $this->directLinkHelper) {
            $this->directLinkHelper = $this->oPSDirectlinkHelper;
        }

        return $this->directLinkHelper;
    }

    public function setDirectLinkHelper(\Netresearch\OPS\Helper\Directlink $directLinkHelper)
    {
        $this->directLinkHelper = $directLinkHelper;
    }

    /**
     * @param \Magento\Sales\Model\Order\Payment $payment
     */
    protected function addPayIdSub(\Magento\Sales\Model\Order\Payment $payment)
    {
        $lastTransaction = $payment->getLastTransId();
        $lastTransactionParts = explode('/', $lastTransaction);
        if ($lastTransaction && count($lastTransactionParts) > 1) {
            $this->requestParams['PAYIDSUB'] = $lastTransactionParts[1];
        }
        return $this;
    }

    protected function canNotUseOrderId(\Magento\Sales\Model\Order\Payment $payment)
    {
        $methodInstance = $payment->getMethodInstance();

        return $methodInstance instanceof \Netresearch\OPS\Model\Payment\Kwixo\KwixoAbstract;
    }
}
