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

namespace Customweb\OgoneCw\Controller;

abstract class Checkout extends \Magento\Framework\App\Action\Action
{
	/**
	 * @var \Magento\Framework\View\Result\PageFactory
	 */
	protected $_resultPageFactory;

	/**
	 * @var \Magento\Checkout\Model\Session
	 */
	protected $_checkoutSession;

	/**
	 * @var \Magento\Quote\Api\CartRepositoryInterface
	 */
	protected $_quoteRepository;

	/**
	 * @var \Customweb\OgoneCw\Model\Authorization\TransactionFactory
	 */
	protected $_transactionFactory;

	/**
	 * @var \Customweb\OgoneCw\Model\Authorization\Method\Factory
	 */
	protected $_authorizationMethodFactory;	
	const CHECKOUT_SESSION_LOG = '/var/log/checkout_session.log';

	/**
	 * @param \Magento\Framework\App\Action\Context $context
	 * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
	 * @param \Magento\Checkout\Model\Session $checkoutSession
	 * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
	 * @param \Customweb\OgoneCw\Model\Authorization\TransactionFactory $transactionFactory
	 * @param \Customweb\OgoneCw\Model\Authorization\Method\Factory $authorizationMethodFactory
	 */
	public function __construct(
			\Magento\Framework\App\Action\Context $context,
			\Magento\Framework\View\Result\PageFactory $resultPageFactory,
			\Magento\Checkout\Model\Session $checkoutSession,
			\Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
			\Customweb\OgoneCw\Model\Authorization\TransactionFactory $transactionFactory,
			\Customweb\OgoneCw\Model\Authorization\Method\Factory $authorizationMethodFactory			
	) {
		parent::__construct($context);
		$this->_resultPageFactory = $resultPageFactory;
		$this->_checkoutSession = $checkoutSession;
		$this->_quoteRepository = $quoteRepository;
		$this->_transactionFactory = $transactionFactory;
		$this->_authorizationMethodFactory = $authorizationMethodFactory;		
	}

	/**
	 * @param \Customweb\OgoneCw\Model\Authorization\Transaction $transaction
	 */
	protected function handleSuccess(\Customweb\OgoneCw\Model\Authorization\Transaction $transaction)
	{
		$transaction->getQuote()->setIsActive(false)->save();
	}

	/**
	 * @param \Customweb\OgoneCw\Model\Authorization\Transaction $transaction
	 * @param string $errorMessage
	 * @return \Magento\Framework\Controller\Result\Redirect
	 */
	protected function handleFailure(\Customweb\OgoneCw\Model\Authorization\Transaction $transaction, $errorMessage)
	{

	    /*Checking Status for Failure Order if is 92 Redirecting to Notification Page */
			    //$params = $transaction->getQueryParams();
		$params   =  $this->getRequest()->getParams();
        $statusparams =  array_key_exists('STATUS', $params) ? $params['STATUS'] : '';
		$om = \Magento\Framework\App\ObjectManager::getInstance();
		$orderId = $this->getRequest()->getParam('cw_transaction_id');
		$order = $om->create('\Magento\Sales\Model\Order')->loadByIncrementId ($orderId);
		$store = $order->getStoreId();
		$customerGroupId = $order->getCustomerGroupId();
		$customerGroup = $om->create('Magento\Customer\Api\GroupRepositoryInterface')->getById($customerGroupId)->getCode();//Get customer group name
		if($statusparams == 92)
		{ 
		    /*Redirectin to SOHO Page Notification*/
		  	if($customerGroup == 'SOHO') {
				if($store == '2') {
					$urlPath = '/fr/independants/checkout/index/notification'; //SOHO URL PATH for FR
				}
				else {
					$urlPath = '/nl/zelfstandigen/checkout/index/notification'; //SOHO URL PATH for NL
				}
				//$RedirectUrl = $this->_catalogHelper->getFormattedURL($urlPath);//Format URL path for redirection	
				$om->get('Psr\Log\LoggerInterface')->addDebug('Redirect URL: '.$urlPath);
				return $this->resultRedirectFactory->create()->setUrl($urlPath, ['_secure' => true]);
			}
		return $this->resultRedirectFactory->create()->setPath('checkout/index/notification', ['_secure' => true]);
		}
		$this->_checkoutSession->setLastRealOrderId($transaction->getOrder()->getRealOrderId());
		$this->restoreQuote();
		
		
		$registychecksession = $om->get('Magento\Checkout\Model\Session');
	    $backurl = $registychecksession->getBackurl();
		$registychecksession->setBackurl('2');
		
        if ($errorMessage !=__("The transaction is cancelled.") || $backurl != 1) {			
			$this->messageManager->addError($errorMessage);
		}		
		$this->reactivateCoupon($transaction->getOrder()->getRealOrderId()); //Added for incident 39120247 coupon reactivation
// 		$this->_checkoutSession->setOgoneCwFailureMessage($errorMessage);
        if ($backurl == 1) {
			if($customerGroup == 'SOHO') {
				if($store == '2') {
					return $this->resultRedirectFactory->create()->setPath('independants/checkout');
				}
				else {
					return $this->resultRedirectFactory->create()->setPath('zelfstandigen/checkout');
				}
			}else{
				return $this->resultRedirectFactory->create()->setPath('checkout');
			}
		} else {
			return $this->resultRedirectFactory->create()->setPath('checkout/cart');
		}
	}

	/**
	 * @param int $transactionId
	 * @return \Customweb\OgoneCw\Model\Authorization\Transaction
	 * @throws \Exception
	 */
	protected function getTransaction($transactionId)
	{
		$transaction = $this->_transactionFactory->create()->load($transactionId);
		if (!$transaction->getId()) {
			throw new \Exception('The transaction has not been found.');
		}
		return $transaction;
	}

	/**
	 * Restore last active quote
	 *
	 * @return bool True if quote restored successfully, false otherwise
	 */
	private function restoreQuote()
	{
		/** @var \Magento\Sales\Model\Order $order */
		$order = $this->_checkoutSession->getLastRealOrder();
		if ($order->getId()) {
			try {
				
				$this->_orangeHelper =$this->_objectManager->create('Orange\Upload\Helper\Data');
				$this->_oldSession = $this->_objectManager->create('Orange\Abandonexport\Model\Items');
                                $scopeConfig = $this->_objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');
                                $log_mode = $scopeConfig->getValue('custom_modulelog/modulelog__configuration/setting_mode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                                if (isset($log_mode) && $log_mode == 1) {
				$this->_orangeHelper->logCreate(self::CHECKOUT_SESSION_LOG,'AFTER OGONE FAILURE:'.$this->_checkoutSession->getSessionId());		
				$this->_orangeHelper->logCreate(self::CHECKOUT_SESSION_LOG,$this->_checkoutSession->getNewcheckout());//LOG Session data
				$this->_orangeHelper->logCreate(self::CHECKOUT_SESSION_LOG,'SESSION DB DATA');
                                }
				$oldSessionModel = $this->_oldSession;
				$oldSessionCollection = $oldSessionModel->getCollection()->addFieldToFilter('quote_id',$order->getQuoteId());
				$oldSessionStepData = array();
				foreach($oldSessionCollection as $oldSessionData) {
                                    if (isset($log_mode) && $log_mode == 1) {
					$this->_orangeHelper->logCreate(self::CHECKOUT_SESSION_LOG,'---------------------------------');
					$this->_orangeHelper->logCreate(self::CHECKOUT_SESSION_LOG,$oldSessionData->getData('quote_id'));//LOG Session data
					$this->_orangeHelper->logCreate(self::CHECKOUT_SESSION_LOG,$oldSessionData->getData('stepfirst'));//LOG Session data
					$this->_orangeHelper->logCreate(self::CHECKOUT_SESSION_LOG,$oldSessionData->getData('stepsecond'));//LOG Session data
                                    }
                                        $oldSessionStepData = unserialize($oldSessionData->getStepsecond());
                                        if (isset($log_mode) && $log_mode == 1) {
					$this->_orangeHelper->logCreate(self::CHECKOUT_SESSION_LOG,unserialize($oldSessionData->getStepsecond()));//LOG Session data					
					$this->_orangeHelper->logCreate(self::CHECKOUT_SESSION_LOG,'---------------------------------');
				
                                        }
                                        
                                        }
				
				$quote = $this->_quoteRepository->get($order->getQuoteId());
				$quote->setIsActive(1)->setReservedOrderId(null);
				$this->_quoteRepository->save($quote);
				$this->_checkoutSession->replaceQuote($quote)->unsLastRealOrderId();
				$this->_checkoutSession->setNewcheckout($oldSessionStepData);
// 				$this->_eventManager->dispatch('restore_quote', ['order' => $order, 'quote' => $quote]);
				return true;
			} catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
			}
		}
		return false;
	}
	
	
	/**
	 * Reactivate Coupon 
	 * Added for incident 39120247 coupon reactivation
	 */
	protected function reactivateCoupon($orderIncrementId) {
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$this->_couponHelper = $objectManager->create('Orange\Coupon\Helper\Data');
		$this->_couponHelper->reactivateCouponByOrder($orderIncrementId);
	}
}
