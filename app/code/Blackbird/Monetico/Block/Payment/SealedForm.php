<?php
/**
 * Blackbird Monetico Module
 *
 * NOTICE OF LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @category    Blackbird
 * @package     Blackbird_Monetico
 * @copyright   Copyright (c) 2018 Blackbird (https://black.bird.eu)
 * @author      Blackbird Team
 * @license     https://store.bird.eu/license/
 * @support     help@bird.eu
 */
namespace Blackbird\Monetico\Block\Payment;

/**
 * @method string getOrderId()
 * @method string getMethodCode()
 */
class SealedForm extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Sales\Model\OrderRepository
     */
    protected $_orderRepository;

    /**
     * @var \Blackbird\Monetico\Helper\DataSealedForm
     */
    protected $_dataSealedFormHelper;

    /**
     * @var string
     */
    protected $_template = 'Blackbird_Monetico::payment/sealed-form.phtml';

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Sales\Model\OrderRepository $orderRepository
     * @param \Blackbird\Monetico\Helper\DataSealedForm $dataSealedFormHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Blackbird\Monetico\Helper\DataSealedForm $dataSealedFormHelper,
        array $data = []
    ) {
        $this->_orderRepository = $orderRepository;
        $this->_dataSealedFormHelper = $dataSealedFormHelper;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function _construct()
    {
        parent::_construct();

        $order = $this->_orderRepository->get($this->getOrderId());
        $this->_dataSealedFormHelper->init($this->getMethodCode(), $order);
    }

    /**
     * @return array
     */
    public function getSealedFormData()
    {
        return $this->_dataSealedFormHelper->getData();
    }

    /**
     * @return bool
     */
    public function isPaymentMethodIntegrated()
    {
        return $this->_dataSealedFormHelper->isPaymentMethodIntegrated();
    }

    /**
     * @return string
     */
    public function getPaymentFormAction()
    {
        return $this->_dataSealedFormHelper->getPaymentFormAction();
    }

    /**
     * @return string
     */
    public function getPaymentFormActionUrlEncoded()
    {
        $url = '?';

        foreach ($this->_dataSealedFormHelper->getData() as $key => $value) {
            $url .= urlencode($key) . '=' . urlencode($value) . '&';
        }

        $url .= 'mode_affichage=iframe';

        return $this->getPaymentFormAction() . $url;
    }

    /**
     * @return string
     */
    public function getServiceName()
    {
        return $this->_dataSealedFormHelper->getServiceName();
    }
}
