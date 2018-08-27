<?php
namespace Netresearch\OPS\Block;

class Placeform extends \Magento\Framework\View\Element\Template
{
    protected $hasMissingParams;
    protected $missingFormFields;
    protected $formFields;
    protected $question;

    /**
     * @var \Netresearch\OPS\Model\ConfigFactory
     */
    protected $oPSConfigFactory;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $salesOrderFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Netresearch\OPS\Model\ConfigFactory $oPSConfigFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\OrderFactory $salesOrderFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->oPSConfigFactory = $oPSConfigFactory;
        $this->checkoutSession = $checkoutSession;
        $this->salesOrderFactory = $salesOrderFactory;
    }

    public function getConfig()
    {
        return $this->oPSConfigFactory->create();
    }

    /**
     * Get checkout session namespace
     *
     * @return \Magento\Checkout\Model\Session
     */
    public function getCheckout()
    {
        return $this->checkoutSession;
    }

    /**
     * OPS payment APi instance
     *
     * @return \Netresearch\OPS\Model\Payment\PaymentAbstract
     */
    protected function _getApi()
    {
        $order = $this->salesOrderFactory->create()->loadByIncrementId($this->getCheckout()->getLastRealOrderId());
        if ($order && null !== $order->getId()) {
            return $order->getPayment()->getMethodInstance();
        }
    }

    /**
     * Return order instance with loaded information by increment id
     *
     * @return \Magento\Sales\Model\Order
     */
    protected function _getOrder()
    {
        if ($this->getOrder()) {
            $order = $this->getOrder();
        } elseif ($this->getCheckout()->getLastRealOrderId()) {
            $order = $this->salesOrderFactory->create()->loadByIncrementId($this->getCheckout()->getLastRealOrderId());
        } else {
            return null;
        }
        return $order;
    }

    /**
     * check if payment method is q kwixo one
     *
     * @return boolean
     */
    public function isKwixoPaymentMethod()
    {
        $isKwixoPayment = false;
        $methodInstance = $this->_getOrder()->getPayment()->getMethodInstance();
        if ($methodInstance instanceof \Netresearch\OPS\Model\Payment\Kwixo\KwixoAbstract) {
            $isKwixoPayment= true;
        }
        return $isKwixoPayment;
    }
    /**
     * Get Form data by using ops payment api
     *
     * @return array
     */
    public function getFormData()
    {
        if (null === $this->formFields && $this->_getOrder() && null !== $this->_getOrder()->getId()) {
            $this->formFields = $this->_getApi()->getFormFields($this->_getOrder(), $this->getRequest()->getParams());
        }
        return $this->formFields;
    }

    /**
     * Getting gateway url
     *
     * @return string
     */
    public function getFormAction()
    {
        $formAction = '';

        // extract variable to ensure php 5.4 compatibility
        $question = $this->getQuestion();

        if ($this->getRequest()->isPost() || empty($question)) {
            $formAction = $this->getConfig()->getFrontendGatewayPath();
        } else {
            $formAction = $this->getUrl(
                '*/*/*',
                ['_secure' => $this->getRequest()->isSecure()]
            );
        }

        return $formAction;
    }

    public function hasMissingParams()
    {
        if (null === $this->_getOrder()) {
            return null;
        }
        if (null === $this->hasMissingParams) {
            $this->hasMissingParams = $this->_getApi()
                ->hasFormMissingParams($this->_getOrder(), $this->getRequest()->getParams(), $this->getFormData());
        }
        return $this->hasMissingParams;
    }

    public function getQuestion()
    {
        if (null === $this->question && $this->_getOrder() && null !== $this->_getOrder()->getId()) {
            $this->question = $this->_getApi()->getQuestion($this->_getOrder(), $this->getRequest()->getParams());
        }
        return $this->question;
    }

    public function getQuestionedFormFields()
    {
        if (null === $this->missingFormFields && $this->_getOrder() && null !== $this->_getOrder()->getId()) {
            $this->missingFormFields = $this->_getApi()
                ->getQuestionedFormFields($this->_getOrder(), $this->getRequest()->getParams());
        }
        return $this->missingFormFields;
    }

    public function isIframeTarget()
    {
        $template = $this->getConfig()->getConfigData('template');
        return $template === \Netresearch\OPS\Model\Payment\PaymentAbstract::TEMPLATE_OPS_IFRAME;
    }
}
