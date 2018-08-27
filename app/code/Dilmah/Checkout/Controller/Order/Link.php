<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Checkout
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
namespace Dilmah\Checkout\Controller\Order;

use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NoSuchEntityException;

class Link extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Sales\Api\OrderCustomerManagementInterface
     */
    protected $orderCustomerService;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * @var \Magento\Framework\Json\Helper\Data $helper
     */
    protected $helper;

    /**
     * Link constructor.
     *
     * @param \Magento\Framework\App\Action\Context               $context
     * @param \Magento\Checkout\Model\Session                     $checkoutSession
     * @param \Magento\Customer\Model\Session                     $customerSession
     * @param \Magento\Sales\Api\OrderCustomerManagementInterface $orderCustomerService
     * @param \Magento\Sales\Model\OrderFactory                   $orderFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory    $resultJsonFactory
     * @param \Magento\Framework\Json\Helper\Data                 $helper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Api\OrderCustomerManagementInterface $orderCustomerService,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Json\Helper\Data $helper
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->orderCustomerService = $orderCustomerService;
        $this->_orderFactory = $orderFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->helper = $helper;
        parent::__construct($context);
    }

    /**
     * Executes request
     *
     * @return void
     * @throws \Exception
     */
    public function execute()
    {
        $response = [
            'errors' => false,
            'message' => __('Account linked successfully')
        ];
        /** @var \Magento\Framework\Controller\Result\Json $_resultJson */
        $resultJson = $this->_resultJsonFactory->create();

        if ($this->customerSession->isLoggedIn()) {
            $this->messageManager->addError(__("Customer is already registered"));
            $response = [
                'errors' => true,
                'message' => __("Customer is already registered")
            ];
            return $resultJson->setData($response);
        }
        $orderId = $this->checkoutSession->getLastOrderId();
        if (!$orderId) {
            $response = [
                'errors' => true,
                'message' => __("Your session has expired")
            ];
            $this->messageManager->addError(__("Your session has expired"));
            return $resultJson->setData($response);
        }
        try {
            $order = $this->_orderFactory->create()->load($orderId);
            if ($this->checkoutSession->getCustomerId()) {
                $order->setCustomerId($this->checkoutSession->getCustomerId());
                $order->save();
            }
        } catch (\Exception $e) {
            $response = [
                'errors' => true,
                'message' => $e->getMessage()
            ];
        }

        return $resultJson->setData($response);
    }
}
