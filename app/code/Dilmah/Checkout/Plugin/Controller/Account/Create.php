<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Checkout
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Checkout\Plugin\Controller\Account;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\Exception\EmailNotConfirmedException;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;

/**
 * Class Create
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Create extends \Magento\Checkout\Controller\Account\Create
{
    const PASSWORD_LENGTH = 6;
    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var AccountManagementInterface
     */
    protected $customerAccountManagement;

    /**
     * @var \Magento\Framework\Json\Helper\Data $helper
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * @var \Dilmah\Checkout\Logger\Logger
     */
    protected $logger;

    /**
     * Create constructor.
     *
     * @param \Magento\Framework\App\Action\Context               $context
     * @param \Magento\Checkout\Model\Session                     $checkoutSession
     * @param \Magento\Customer\Model\Session                     $customerSession
     * @param \Magento\Framework\Json\Helper\Data                 $helper
     * @param \Magento\Framework\Controller\Result\JsonFactory    $resultJsonFactory
     * @param AccountManagementInterface                          $customerAccountManagement
     * @param \Magento\Sales\Api\OrderCustomerManagementInterface $orderCustomerService
     * @param \Magento\Framework\Controller\Result\RawFactory     $resultRawFactory
     * @param \Dilmah\Checkout\Logger\Logger                      $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Json\Helper\Data $helper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        AccountManagementInterface $customerAccountManagement,
        \Magento\Sales\Api\OrderCustomerManagementInterface $orderCustomerService,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Dilmah\Checkout\Logger\Logger $logger
    ) {
        $this->helper = $helper;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->resultRawFactory = $resultRawFactory;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->logger = $logger;
        parent::__construct($context, $checkoutSession, $customerSession, $orderCustomerService);
    }

    /**
     * @param \Magento\Checkout\Controller\Account\Create $subject
     * @param \Closure $proceed
     * @return $this|void
     * @throws \Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings("unused")
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function aroundExecute(
        \Magento\Checkout\Controller\Account\Create $subject,
        \Closure $proceed
    ) {
        $formData = null;
        $httpBadRequestCode = 400;
        $response = [
            'errors' => false,
            'message' => __('Account linked successfully')
        ];
        /** @var \Magento\Framework\Controller\Result\Json $_resultJson */
        $resultJson = $this->_resultJsonFactory->create();

        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        try {
            $formData = $this->helper->jsonDecode($this->getRequest()->getContent());
        } catch (\Exception $e) {
            $response = [
                'errors' => true,
                'message' => __("Bad request")
            ];
            return $resultJson->setData($response);
        }
        if (!$formData || $this->getRequest()->getMethod() !== 'POST' || !$this->getRequest()->isXmlHttpRequest()) {
            $response = [
                'errors' => true,
                'message' => __("Bad request")
            ];
            return $resultJson->setData($response);
        }
        $password = isset($formData['password']) ? $formData['password'] : '';
        $subscribe = isset($formData['is_subscribed']) ? $formData['is_subscribed'] : 0;
        if (empty($password) || strlen($password) < self::PASSWORD_LENGTH) {
            $response = [
                'errors' => true,
                'message' => __("Incorrect password, password must be atleast 6 characters long")
            ];
            return $resultJson->setData($response);
        } else {
            $this->customerSession->setPassword($password);
        }
        if (!empty($subscribe)) {
            $this->customerSession->setIsSubscribed($subscribe);
        }

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
            $this->messageManager->addError(__("Your session has expired"));
            $response = [
                'errors' => true,
                'message' => __("Your session has expired")
            ];
            return $resultJson->setData($response);
        }
        try {
            $this->orderCustomerService->create($orderId);

            $credentials['username'] = $this->checkoutSession->getLastRealOrder()->getCustomerEmail();
            $credentials['password'] = $this->customerSession->getPassword();
            $customer = $this->customerAccountManagement->authenticate(
                $credentials['username'],
                $credentials['password']
            );
            $this->customerSession->setCustomerDataAsLoggedIn($customer);
            $this->customerSession->regenerateId();

        } catch (EmailNotConfirmedException $e) {
            $response = [
                'errors' => true,
                'message' => $e->getMessage()
            ];
        } catch (InvalidEmailOrPasswordException $e) {
            $response = [
                'errors' => true,
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $response = [
                'errors' => true,
                'message' => __('Invalid login or password.') . ' - ' . $e->getMessage()
            ];
        } catch (\Exception $e) {
            $this->messageManager->addException($e, $e->getMessage());
            $response = [
                'errors' => true,
                'message' => $e->getMessage()
            ];
        }
        if ($response['errors']) {
            $this->logger->error(__('Dilmah order success account creation error "%1".', $response['message']));
        }
        return $resultJson->setData($response);
    }
}
