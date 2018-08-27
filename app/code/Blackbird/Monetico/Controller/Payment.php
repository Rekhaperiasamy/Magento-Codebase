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
namespace Blackbird\Monetico\Controller;

abstract class Payment extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Sales\Model\OrderRepository
     */
    protected $_orderRepository;

    /**
     * @var \Blackbird\Monetico\Model\ConfigProvider
     */
    protected $_config;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Blackbird\Monetico\Model\ConfigProvider $configProvider
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Blackbird\Monetico\Model\ConfigProvider $configProvider
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_orderRepository = $orderRepository;
        $this->_config = $configProvider;
        parent::__construct($context);
        $this->_construct();
    }

    /**
     * Sub construct
     *
     * @return void
     */
    abstract protected function _construct();
}
