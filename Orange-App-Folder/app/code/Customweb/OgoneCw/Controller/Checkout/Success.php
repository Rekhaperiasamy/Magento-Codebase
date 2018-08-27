<?php
/**
 * You are allowed to use this API in your web application.
 *
 * Copyright (C) 2016 by customweb GmbH
 *
 * This program is licenced under the customweb software licence. With the
 * purchase or the installation of the software in your application you
 * accept the licence agreement. The allowed usage is outlined in the
 * customweb software licence which can be found under
 * http://www.sellxed.com/en/software-license-agreement
 *
 * Any modification or distribution is strictly forbidden. The license
 * grants you the installation in one application. For multiuse you will need
 * to purchase further licences at http://www.sellxed.com/shop.
 *
 * See the customweb software licence agreement for more details.
 *
 *
 * @category	Customweb
 * @package		Customweb_OgoneCw
 *
 */

namespace Customweb\OgoneCw\Controller\Checkout;
use Magento\Sales\Model\Order;
class Success extends \Customweb\OgoneCw\Controller\Checkout
{
	/**
	 * @var \Customweb\OgoneCw\Model\Authorization\Notification
	 */
	protected $_notification;
	protected $_catalogHelper;
    protected $_container;
	/**
	 * @param \Magento\Framework\App\Action\Context $context
	 * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
	 * @param \Magento\Checkout\Model\Session $checkoutSession
	 * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
	 * @param \Customweb\OgoneCw\Model\Authorization\TransactionFactory $transactionFactory
	 * @param \Customweb\OgoneCw\Model\Authorization\Method\Factory $authorizationMethodFactory
	 * @param \Customweb\OgoneCw\Model\Authorization\Notification $notification
	 */
	public function __construct(
			\Magento\Framework\App\Action\Context $context,
			\Magento\Framework\View\Result\PageFactory $resultPageFactory,
			\Magento\Checkout\Model\Session $checkoutSession,
			\Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
			\Customweb\OgoneCw\Model\Authorization\TransactionFactory $transactionFactory,
			\Customweb\OgoneCw\Model\Authorization\Method\Factory $authorizationMethodFactory,
			\Customweb\OgoneCw\Model\Authorization\Notification $notification,
			\Customweb\OgoneCw\Model\DependencyContainer $container,
			\Orange\Catalog\Helper\CatalogUrl $catalogHelper
	) {
		parent::__construct($context, $resultPageFactory, $checkoutSession, $quoteRepository, $transactionFactory, $authorizationMethodFactory);
		$this->_notification = $notification;
		$this->_catalogHelper = $catalogHelper;	
		$this->_container=$container;
		$this->_checkoutSession = $checkoutSession;
	}

	public function execute()
	{
		$transactionId = $this->getRequest()->getParam('cstrxid');	
		$params = $this->getRequest()->getParams();
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();		
        $scopeConfig = $objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');
		try {
			$orderId = $this->getRequest()->getParam('cw_transaction_id');
			$order = $objectManager->create('\Magento\Sales\Model\Order')->loadByIncrementId($orderId);
			$statusparams =  array_key_exists('STATUS', $params) ? $params['STATUS'] : '';
			$this->_notification->waitForNotification($transactionId);
			$store = $order->getStoreId();
			$customerGroupId = $order->getCustomerGroupId();
			$customerGroup = $objectManager->create('Magento\Customer\Api\GroupRepositoryInterface')->getById($customerGroupId)->getCode();//Get customer group name
			if($order->getStatus()=="processing") {
			        $payid = $this->getRequest()->getParam('PAYID');
			        $this->getCheckoutSession()->setOgoneOrderid($orderId);
                    $this->getCheckoutSession()->setOgoneTranscationid($payid);//5689 changed PAYID
			}
   		    $this->handleSuccess($this->getTransaction($transactionId));	
			if($statusparams == 92) {
                if($customerGroup == 'SOHO') {
                    if($store == '2') {
                        $urlPath = '/fr/independants/checkout/index/notification'; //SOHO URL PATH for FR
                    } else {
                        $urlPath = '/nl/zelfstandigen/checkout/index/notification'; //SOHO URL PATH for NL
                    }
                    return $this->resultRedirectFactory->create()->setUrl($urlPath, ['_secure' => true]);
                }
                return $this->resultRedirectFactory->create()->setPath('checkout/index/notification',['_secure' => true]);
			} else {
                if($customerGroup == 'SOHO') {
                    if($store == '2') {
                        $urlPath = '/fr/independants/checkout/onepage/success'; //SOHO URL PATH for FR
                    } else {
                        $urlPath = '/nl/zelfstandigen/checkout/onepage/success'; //SOHO URL PATH for NL
                    }
                    return $this->resultRedirectFactory->create()->setUrl($urlPath, ['_secure' => true]);
                }
                return $this->resultRedirectFactory->create()->setPath('checkout/onepage/success', ['_secure' => true]);
			}
		} catch (\Exception $e) {
		    $objectManager->get('Orange\Upload\Helper\Data')->logCreate('/var/log/order_ogone_responce.log', $params);
			return $this->handleFailure($this->getTransaction($transactionId), $e->getMessage());
		}
	}
	
	public function getCheckoutSession() 
    {
        return $this->_checkoutSession;
    }
}