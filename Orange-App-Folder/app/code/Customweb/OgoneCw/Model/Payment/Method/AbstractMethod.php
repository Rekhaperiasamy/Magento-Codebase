<?php

/**
 *  * You are allowed to use this API in your web application.
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
 * @category Customweb
 * @package Customweb_OgoneCw
 *
 */
namespace Customweb\OgoneCw\Model\Payment\Method;

class AbstractMethod extends \Magento\Payment\Model\Method\AbstractMethod implements \Customweb_Payment_Authorization_IPaymentMethod {
	/**
	 *
	 * @var \Magento\Checkout\Model\Session
	 */
	protected $_checkoutSession;

	/**
	 *
	 * @var \Magento\Framework\App\RequestInterface
	 */
	protected $_request;

	/**
	 *
	 * @var \Magento\Framework\DB\TransactionFactory
	 */
	protected $_dbTransactionFactory;

	/**
	 *
	 * @var \Customweb\OgoneCw\Model\Authorization\Method\Factory
	 */
	protected $_authorizationMethodFactory;

	/**
	 *
	 * @var \Customweb\OgoneCw\Model\Configuration
	 */
	protected $_configuration;

	/**
	 *
	 * @var \Customweb\OgoneCw\Model\DependencyContainer
	 */
	protected $_container;

	/**
	 *
	 * @var \Customweb\OgoneCw\Model\Authorization\TransactionFactory
	 */
	protected $_transactionFactory;

	/**
	 *
	 * @var \Customweb\OgoneCw\Helper\InvoiceItem
	 */
	protected $_invoiceItemHelper;

	/**
	 * Payment method code
	 *
	 * @var string
	 */
	protected $_code;

	/**
	 * Payment method name
	 *
	 * @var string
	 */
	protected $_name;

	/**
	 * Form block paths
	 *
	 * @var string
	 */
	protected $_formBlockType = 'Customweb\OgoneCw\Block\Payment\Method\Form';

	/**
	 * Info block path
	 *
	 * @var string
	 */
	protected $_infoBlockType = 'Customweb\OgoneCw\Block\Payment\Method\Info';

	/**
	 *
	 * @param \Magento\Framework\Model\Context $context
	 * @param \Magento\Framework\Registry $registry
	 * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
	 * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
	 * @param \Magento\Payment\Helper\Data $paymentData
	 * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
	 * @param \Magento\Payment\Model\Method\Logger $logger
	 * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
	 * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
	 * @param \Magento\Checkout\Model\Session $checkoutSession
	 * @param \Magento\Framework\App\RequestInterface $request
	 * @param \Magento\Framework\DB\TransactionFactory $dbTransactionFactory
	 * @param \Customweb\OgoneCw\Model\Authorization\Method\Factory $authorizationMethodFactory
	 * @param \Customweb\OgoneCw\Model\Configuration $configuration
	 * @param \Customweb\OgoneCw\Model\DependencyContainer $container
	 * @param \Customweb\OgoneCw\Model\Authorization\TransactionFactory $transactionFactory
	 * @param \Customweb\OgoneCw\Helper\InvoiceItem $invoiceItemHelper
	 * @param array $data
	 */
	public function __construct(\Magento\Framework\Model\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory, \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory, \Magento\Payment\Helper\Data $paymentData, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Payment\Model\Method\Logger $logger, \Magento\Checkout\Model\Session $checkoutSession, \Magento\Framework\App\RequestInterface $request, \Magento\Framework\DB\TransactionFactory $dbTransactionFactory, \Customweb\OgoneCw\Model\Authorization\Method\Factory $authorizationMethodFactory, \Customweb\OgoneCw\Model\Configuration $configuration, \Customweb\OgoneCw\Model\DependencyContainer $container, \Customweb\OgoneCw\Model\Authorization\TransactionFactory $transactionFactory, \Customweb\OgoneCw\Helper\InvoiceItem $invoiceItemHelper, \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null, \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null, array $data = []){
		parent::__construct($context, $registry, $extensionFactory, $customAttributeFactory, $paymentData, $scopeConfig, $logger, $resource,
				$resourceCollection, $data);
		$this->_checkoutSession = $checkoutSession;
		$this->_request = $request;
		$this->_dbTransactionFactory = $dbTransactionFactory;
		$this->_authorizationMethodFactory = $authorizationMethodFactory;
		$this->_configuration = $configuration;
		$this->_container = $container;
		$this->_transactionFactory = $transactionFactory;
		$this->_invoiceItemHelper = $invoiceItemHelper;
	}

	public function getPaymentMethodName(){
		return $this->_name;
	}

	public function getPaymentMethodDisplayName(){
		return $this->getPaymentMethodConfigurationValue('title');
	}

	public function getPaymentMethodConfigurationValue($key, $languageCode = null){
		return $this->_configuration->getConfigurationValue('payment', $this->getCode() . '/' . $key);
	}

	public function existsPaymentMethodConfigurationValue($key, $languageCode = null){
		return $this->_configuration->existsConfiguration('payment', $this->getCode() . '/' . $key);
	}

	/**
	 * Get description text
	 *
	 * @return string
	 */
	public function getDescription(){
		return trim($this->getPaymentMethodConfigurationValue('description'));
	}

	/**
	 * Should show image
	 *
	 * @return boolean
	 */
	public function isShowImage(){
		return (boolean) $this->getPaymentMethodConfigurationValue('show_image');
	}

	/**
	 * Should use base currency
	 *
	 * @return boolean
	 */
	public function isUseBaseCurrency(){
		return (boolean) $this->getPaymentMethodConfigurationValue('base_currency');
	}

	/**
	 *
	 * @return string
	 */
	public function getOrderPlaceRedirectUrl(){
		$quote = $this->_checkoutSession->getQuote();
		$quote->setIsActive(true);
		$quote->setReservedOrderId(null);
		$quote->save();

		$transactionId = null;
		$transaction = $this->_registry->registry('ogonecw_transaction');
		if ($transaction instanceof \Customweb\OgoneCw\Model\Authorization\Transaction) {
			$transactionId = $transaction->getId();
		}
		return $quote->getStore()->getUrl('ogonecw/checkout/error', [
			'_secure' => true,
			'transaction_id' => $transactionId
		]);
	}

	public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null){
		$isAvailable = parent::isAvailable($quote);

		if ($isAvailable) {
			$allowedCurrencies = $this->getPaymentMethodConfigurationValue('currency');
			if ($quote !== null && !empty($allowedCurrencies)) {
				$isAvailable = (in_array($quote->getCurrency()->getQuoteCurrencyCode(), $allowedCurrencies));
			}
		}

		if ($isAvailable) {
			try {
				$context = $this->getAuthorizationMethodFactory()->getContextFactory()->createQuote($this, $quote);
				$adapter = $this->getAuthorizationMethodFactory()->create($context);
				$adapter->preValidate();
			}
			catch (\Exception $e) {
				$isAvailable = false;
			}
		}

		return $isAvailable;
	}

	public function validate(){
		
		$arguments = null;
		return \Customweb_Licensing_OgoneCw_License::run('7m6jklil8gvggu9e', $this, $arguments);
	}

	final public function call_he5kquf89ijmgs9h() {
		$arguments = func_get_args();
		$method = $arguments[0];
		$call = $arguments[1];
		$parameters = array_slice($arguments, 2);
		if ($call == 's') {
			return call_user_func_array(array(get_class($this), $method), $parameters);
		}
		else {
			return call_user_func_array(array($this, $method), $parameters);
		}
		
		
	}
	private function parentValidate(){
		parent::validate();
	}

	/**
	 * Set initial order status to pending payment.
	 *
	 * @param string $paymentAction
	 * @param \Magento\Framework\Object $stateObject
	 * @return \Customweb\OgoneCw\Model\Payment\Method\AbstractMethod
	 */
	public function initialize($paymentAction, $stateObject){
		$state = \Magento\Sales\Model\Order::STATE_PENDING_PAYMENT;
		$stateObject->setState($state);
		$stateObject->setStatus('pending_payment');
		$stateObject->setIsNotified(false);
		return $this;
	}

	/**
	 * Set transaction id and set transaction as pending if authorization is uncertain.
	 *
	 * @param \Magento\Payment\Model\InfoInterface $payment
	 * @param float $amount
	 * @return \Customweb\OgoneCw\Model\Payment\Method\AbstractMethod
	 */
	public function authorize(\Magento\Payment\Model\InfoInterface $payment, $amount){
		parent::authorize($payment, $amount);

		$transaction = $this->_registry->registry('ogonecw_authorization_transaction');
		if ($transaction instanceof \Customweb\OgoneCw\Model\Authorization\Transaction) {
			$payment->setIsTransactionClosed(false);
			if ($transaction->getTransactionObject()->isAuthorizationUncertain()) {
				$payment->setIsTransactionPending(true);
			}
		}
		return $this;
	}

	/**
	 * Capture amount online.
	 *
	 * @param \Magento\Payment\Model\InfoInterface $payment
	 * @param float $amount
	 * @return \Customweb\OgoneCw\Model\Payment\Method\AbstractMethod
	 */
	public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount){
		parent::capture($payment, $amount);

		
		$transaction = $this->_transactionFactory->create()->loadByOrderPaymentId($payment->getId());
		if ($transaction->getId()) {
			$invoice = $this->_registry->registry('ogonecw_invoice');
			$isNoClose = $this->isCaptureNoClose();
			$items = [];
			if ($invoice instanceof \Magento\Sales\Model\Order\Invoice) {
				$items = $this->_invoiceItemHelper->getInvoiceItems(
					$invoice->getAllItems(),
					$invoice->getBillingAddress(),
					$invoice->getShippingAddress(),
					$invoice->getStore(),
					$this->isUseBaseCurrency() ? $invoice->getBaseDiscountAmount() : $invoice->getDiscountAmount(),
					$this->isUseBaseCurrency() ? $invoice->getBaseDiscountTaxCompensationAmount() : $invoice->getDiscountTaxCompensationAmount(),
					$invoice->getDiscountDescription(),
					$this->isUseBaseCurrency() ? $invoice->getBaseShippingAmount() : $invoice->getShippingAmount(),
					$this->isUseBaseCurrency() ? $invoice->getBaseShippingTaxAmount() : $invoice->getShippingTaxAmount(),
					$invoice->getOrder()->getShippingDescription(), $invoice->getOrder()->getCustomerId(),
					$this->isUseBaseCurrency() ? $invoice->getBaseGrandTotal() : $invoice->getGrandTotal(),
					$this->isUseBaseCurrency(),
					false
				);
			}
			if (count($items) <= 0) {
				$items = $transaction->getTransactionObject()->getUncapturedLineItems();
			}
			$items = \Customweb_Util_Invoice::getItemsByReductionAmount($items, $amount, $transaction->getCurrency());
			$this->captureItems($transaction, $items);
			$payment->setShouldCloseParentTransaction(!$isNoClose);
			$payment->setIsTransactionPending(false);
		}
		


		return $this;
	}

	/**
	 * Refund amount online.
	 *
	 * @param \Magento\Payment\Model\InfoInterface $payment
	 * @param float $amount
	 * @return \Customweb\OgoneCw\Model\Payment\Method\AbstractMethod
	 */
	public function refund(\Magento\Payment\Model\InfoInterface $payment, $amount){
		parent::refund($payment, $amount);

		
		$transaction = $this->_transactionFactory->create()->loadByOrderPaymentId($payment->getId());
		if ($transaction->getId()) {
			if ($transaction->getTransactionObject()->isRefundPossible()) {
				try {
					$refundAdapter = $this->_container->getBean('Customweb_Payment_BackendOperation_Adapter_Service_IRefund');
					$compareAmount = \Customweb_Util_Currency::compareAmount($amount, $transaction->getAuthorizationAmount(),
							$transaction->getCurrency());
					if ($compareAmount !== 0) {
						if ($transaction->getTransactionObject()->isPartialRefundPossible()) {
							$creditmemo = $payment->getCreditmemo();
							$items = [];
							if ($creditmemo instanceof \Magento\Sales\Model\Order\Creditmemo) {
								$items = $this->_invoiceItemHelper->getInvoiceItems(
									$creditmemo->getAllItems(),
									$creditmemo->getBillingAddress(),
									$creditmemo->getShippingAddress(),
									$creditmemo->getStore(),
									$this->isUseBaseCurrency() ? $creditmemo->getBaseDiscountAmount() : $creditmemo->getDiscountAmount(),
									$this->isUseBaseCurrency() ? $creditmemo->getBaseDiscountTaxCompensationAmount() : $creditmemo->getDiscountTaxCompensationAmount(),
									$creditmemo->getDiscountDescription(),
									$this->isUseBaseCurrency() ? $creditmemo->getBaseShippingAmount() : $creditmemo->getShippingAmount(),
									$this->isUseBaseCurrency() ? $creditmemo->getBaseShippingTaxAmount() : $creditmemo->getShippingTaxAmount(),
									$creditmemo->getOrder()->getShippingDescription(),
									$creditmemo->getOrder()->getCustomerId(),
									$this->isUseBaseCurrency() ? $creditmemo->getBaseGrandTotal() : $creditmemo->getGrandTotal(),
									$this->isUseBaseCurrency(),
									false
								);
							}
							if (count($items) <= 0) {
								$items = $transaction->getTransactionObject()->getNonRefundedLineItems();
							}
							$items = \Customweb_Util_Invoice::getItemsByReductionAmount($items, $amount, $transaction->getCurrency());
							$refundAdapter->partialRefund($transaction->getTransactionObject(), $items, false);
						}
						else {
							throw new \Exception(__('Partial refund not possible. You may retry with the total transaction amount.'));
						}
					}
					else {
						$refundAdapter->refund($transaction->getTransactionObject());
					}
					$transaction->save();
				}
				catch (\Exception $e) {
					$transaction->save();
					throw $e;
				}
			}
			else {
				throw new \Exception(__('The transaction cannot be refunded online.'));
			}
		}
		


		return $this;
	}

	/**
	 * Void amount online.
	 *
	 * @param \Magento\Payment\Model\InfoInterface $payment
	 * @return \Customweb\OgoneCw\Model\Payment\Method\AbstractMethod
	 */
	public function void(\Magento\Payment\Model\InfoInterface $payment){
		parent::void($payment);

		
		$transaction = $this->_transactionFactory->create()->loadByOrderPaymentId($payment->getId());
		if ($transaction->getId()) {
			if ($transaction->getTransactionObject()->isCancelPossible()) {
				try {
					$cancelAdapter = $this->_container->getBean('Customweb_Payment_BackendOperation_Adapter_Service_ICancel');
					$cancelAdapter->cancel($transaction->getTransactionObject());
					$transaction->save();
				}
				catch (\Exception $e) {
					$transaction->save();
					throw $e;
				}
			}
			else {
				throw new \Exception(__('The transaction cannot be cancelled online.'));
			}
		}
		


		return $this;
	}

	public function acceptPayment(\Magento\Payment\Model\InfoInterface $payment){
		$transaction = $this->_transactionFactory->create()->loadByOrderPaymentId($payment->getId());
		if ($transaction->getId()) {
			if ($transaction->getTransactionObject()->isCapturePossible()) {
				$this->captureItems($transaction, $transaction->getTransactionObject()->getUncapturedLineItems());
			}
		}
		return true;
	}

	public function denyPayment(\Magento\Payment\Model\InfoInterface $payment){
		$transaction = $this->_transactionFactory->create()->loadByOrderPaymentId($payment->getId());
		if ($transaction->getId()) {
			if ($transaction->getTransactionObject()->isCancelPossible()) {
				$this->void($payment);
			}
			elseif ($transaction->getTransactionObject()->isCaptured()) {
				// TODO: If transaction is captured, we need to issue a refund.
			}
		}
		return true;
	}

	public function assignData(\Magento\Framework\DataObject $data){
		parent::assignData($data);
		$infoInstance = $this->getInfoInstance();
		//Since 2.1 the alias and form values are stored in the additional_data array
		if ($data->getData('additional_data') !== null) {
			$infoInstance->setAdditionalInformation('alias', $data->getData('additional_data/alias'));
			foreach ($data->getData('additional_data') as $key => $value) {
				if (strpos($key, 'form[') === 0) {
					$infoInstance->setAdditionalInformation(substr($key, 5, -1), $value);
				}
			}
		}
		else {
			$infoInstance->setAdditionalInformation('alias', $data->getData('alias'));

			foreach ($data->getData() as $key => $value) {
				if (strpos($key, 'form[') === 0) {
					$infoInstance->setAdditionalInformation(substr($key, 5, -1), $value);
				}
			}
		}
		return $this;
	}

	private function captureItems(\Customweb\OgoneCw\Model\Authorization\Transaction $transaction, $items = []){
		
		if ($transaction->getTransactionObject()->isCapturePossible()) {
			try {
				$captureAdapter = $this->_container->getBean('Customweb_Payment_BackendOperation_Adapter_Service_ICapture');
				if ($transaction->getTransactionObject()->isPartialCapturePossible()) {
					$isNoClose = $this->isCaptureNoClose();
					$captureAdapter->partialCapture($transaction->getTransactionObject(), $items, !$isNoClose);
				}
				else {
					$isNoClose = false;
					$captureAdapter->capture($transaction->getTransactionObject());
				}
				$transaction->save();
			}
			catch (\Exception $e) {
				$transaction->save();
				throw $e;
			}
		}
		elseif ($transaction->getTransactionObject()->isCaptured()) {
			return;
		}
		else {
			throw new \Exception(__('The transaction cannot be captured online.'));
		}
		
	}

	/**
	 *
	 * @return boolean
	 */
	private function isCaptureNoClose(){
		if ($this->_request->getParam('capture_no_close')) {
			return true;
		}
		$invoice = $this->_request->getParam('invoice');
		if (is_array($invoice) && isset($invoice['capture_no_close']) && $invoice['capture_no_close']) {
			return true;
		}
		return false;
	}

	private function getAuthorizationMethodFactory(){
		return $this->_authorizationMethodFactory;
	}
}
