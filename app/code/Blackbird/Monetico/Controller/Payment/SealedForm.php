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
namespace Blackbird\Monetico\Controller\Payment;

use Magento\Framework\Exception\LocalizedException;

class SealedForm extends \Blackbird\Monetico\Controller\Payment
{
    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    private $_resultRawFactory;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    private $_layoutFactory;

    /**
     * Core form key validator
     *
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    private $_formKeyValidator;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Model\OrderRepository $orderRepository
     * @param \Blackbird\Monetico\Model\ConfigProvider $configProvider
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Blackbird\Monetico\Model\ConfigProvider $configProvider
    ) {
        $this->_resultRawFactory = $resultRawFactory;
        $this->_layoutFactory = $layoutFactory;
        $this->_formKeyValidator = $formKeyValidator;
        parent::__construct($context, $checkoutSession, $orderRepository, $configProvider);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        // Silence is golden
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->_resultRawFactory->create()->setContents('');

        if ($this->_isAllowed() && $this->getRequest()->getParam('method_code')) {
            $resultRaw->setContents(
                $this->_getSealedFormHtml(
                    $this->getRequest()->getParam('method_code'),
                    $this->_checkoutSession->getLastRealOrder()->getId()
                )
            );
        } else {
            throw new LocalizedException(__('You are not allowed to access.'));
        };

        return $resultRaw;
    }

    /**
     * Retrieve the sealed form html for the given order/payment method
     *
     * @param string $methodCode
     * @param int $orderId
     * @return string
     */
    private function _getSealedFormHtml($methodCode, $orderId)
    {
        $block = $this->_layoutFactory->create()->createBlock(
            \Blackbird\Monetico\Block\Payment\SealedForm::class,
            'sealed_form',
            [
                'data' => [
                    'order_id' => $orderId,
                    'method_code' => $methodCode,
                ],
            ]
        );

        return $block->toHtml();
    }

    /**
     * Check if the current custom can access to the action
     *
     * @return bool
     */
    private function _isAllowed()
    {
        return ($this->getRequest()->isPost()
            && $this->getRequest()->isAjax()
            && $this->_formKeyValidator->validate($this->getRequest())
            && $this->_checkoutSession->getLastRealOrder()->getId());
    }
}
