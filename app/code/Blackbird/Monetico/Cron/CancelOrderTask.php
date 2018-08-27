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
namespace Blackbird\Monetico\Cron;

use Magento\Sales\Model\Order;

class CancelOrderTask
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $_orderCollectionFactory;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $_orderRepository;

    /**
     * @var \Blackbird\Monetico\Model\ConfigProvider
     */
    protected $_config;

    /**
     * @var \Blackbird\Monetico\Model\Config\Source\PaymentMethod
     */
    protected $_moneticoPaymentSource;

    /**
     * @var array
     */
    protected $newOrderStatusByMethod;

    /**
     * @var array
     */
    protected $cancelOrderStatusByMethod;

    /**
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Blackbird\Monetico\Model\ConfigProvider $configProvider
     * @param \Blackbird\Monetico\Model\Config\Source\PaymentMethod $paymentMethodSource
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Blackbird\Monetico\Model\ConfigProvider $configProvider,
        \Blackbird\Monetico\Model\Config\Source\PaymentMethod $paymentMethodSource
    ) {
        $this->_localeDate = $localeDate;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_orderRepository = $orderRepository;
        $this->_config = $configProvider;
        $this->_moneticoPaymentSource = $paymentMethodSource;
    }

    /**
     * Execute the Cancel Order Task
     *
     * @return $this
     */
    public function execute()
    {
        $this->_config->setMethodCode('monetico');

        if ($this->_config->getSystemConfigValue('use_cron_cancel')) {
            $this->processCancelOrders();
        }

        return $this;
    }

    /**
     * Retrieve orders older than the configured delay
     *
     * @return $this
     */
    protected function processCancelOrders()
    {
        // Init Data
        $this->_config->setMethodCode('monetico');
        $availablePaymentMethods = $this->_moneticoPaymentSource->getAvailablePaymentMethods();
        $orderNewStatus = $this->getNewStatusByMethod();

        // Build the Order Collection

        $orderCollection = $this->_orderCollectionFactory->create()->join(
            ['pay' => 'sales_order_payment'],
            'pay.parent_id = main_table.entity_id',
            ['method_code' => 'pay.method']
        );

        $connection = $orderCollection->getSelect()->getConnection();

        foreach ($availablePaymentMethods as $paymentMethod) {
            if (isset($orderNewStatus[$paymentMethod])) {
                $orderCollection->getSelect()->orWhere(
                    '(' . $connection->quoteInto('pay.method = ?', $paymentMethod) . ' AND ' .
                    $connection->quoteInto('main_table.status = ?', $orderNewStatus[$paymentMethod]) . ')'
                );
            }
        }

        $this->cancelOldOrders($orderCollection);

        return $this;
    }

    /**
     * Cancel orders older than the configured delay
     *
     * @param \Magento\Sales\Model\ResourceModel\Order\Collection $orderCollection
     * @return $this
     */
    protected function cancelOldOrders(\Magento\Sales\Model\ResourceModel\Order\Collection $orderCollection)
    {
        // Init Data
        $this->_config->setMethodCode('monetico');
        $cancelDelay = $this->_config->getSystemConfigValue('cancel_delay');
        $orderCancelStatus = $this->getCancelStatusByMethod();

        /** @var \Magento\Sales\Model\Order $order */
        foreach ($orderCollection as $order) {
            $orderDate = new \DateTime($order->getCreatedAt());
            $status = $orderCancelStatus[$order->getData('method_code')];

            if (($orderDate->getTimestamp() + $cancelDelay) < $this->_localeDate->date()->getTimestamp()) {

                if ($status === Order::STATE_HOLDED && $order->canHold()) {
                    $order->hold();
                } elseif ($status === Order::STATE_CANCELED && $order->canCancel()) {
                    $order->cancel();
                }

                $this->_orderRepository->save($order);
            }
        }

        return $this;
    }

    /**
     * Retrieve the cancel status for orders by method
     *
     * @return array
     */
    protected function getCancelStatusByMethod()
    {
        if (!$this->cancelOrderStatusByMethod) {
            $cancelStatus = [];
            $previousMethod = $this->_config->getMethodCode();

            foreach ($this->_moneticoPaymentSource->getAvailablePaymentMethods() as $paymentMethod) {
                $this->_config->setMethodCode($paymentMethod);
                $cancelStatus[$paymentMethod] = $this->_config->getSystemConfigValue('order_status_payment_canceled');
            }

            $this->_config->setMethodCode($previousMethod);
            $this->cancelOrderStatusByMethod = $cancelStatus;
        }

        return $this->cancelOrderStatusByMethod;
    }

    /**
     * Retrieve the new order status by method
     *
     * @return array
     */
    protected function getNewStatusByMethod()
    {
        if (!$this->newOrderStatusByMethod) {
            $newStatus = [];
            $previousMethod = $this->_config->getMethodCode();

            foreach ($this->_moneticoPaymentSource->getAvailablePaymentMethods() as $paymentMethod) {
                $this->_config->setMethodCode($paymentMethod);
                $newStatus[$paymentMethod] = $this->_config->getSystemConfigValue('order_status');
            }

            $this->_config->setMethodCode($previousMethod);
            $this->newOrderStatusByMethod = $newStatus;
        }

        return $this->newOrderStatusByMethod;
    }
}
