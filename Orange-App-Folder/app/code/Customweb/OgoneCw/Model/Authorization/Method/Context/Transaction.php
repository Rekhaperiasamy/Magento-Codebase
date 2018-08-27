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

namespace Customweb\OgoneCw\Model\Authorization\Method\Context;

class Transaction extends AbstractContext
{
	/**
	 * @var \Customweb\OgoneCw\Model\Authorization\TransactionFactory
	 */
	protected $_transactionFactory;

	/**
	 * @var \Customweb\OgoneCw\Model\Authorization\Transaction
	 */
	protected $transaction;

	/**
	 * @param \Magento\Framework\Registry $coreRegistry
	 * @param \Magento\Checkout\Model\Session $checkoutSession
	 * @param \Magento\Framework\App\RequestInterface $request
	 * @param \Customweb\OgoneCw\Model\Authorization\OrderContextFactory $orderContextFactory
	 * @param \Customweb\OgoneCw\Model\Authorization\CustomerContextFactory $customerContextFactory
	 */
	public function __construct(
			\Magento\Framework\Registry $coreRegistry,
			\Magento\Checkout\Model\Session $checkoutSession,
			\Magento\Framework\App\RequestInterface $request,
			\Customweb\OgoneCw\Model\Authorization\OrderContextFactory $orderContextFactory,
			\Customweb\OgoneCw\Model\Authorization\CustomerContextFactory $customerContextFactory,
			\Customweb\OgoneCw\Model\Authorization\TransactionFactory $transactionFactory,
			\Customweb\OgoneCw\Model\Authorization\Transaction $transaction = null
	) {
		parent::__construct($coreRegistry, $checkoutSession, $request, $orderContextFactory, $customerContextFactory);
		$this->_transactionFactory = $transactionFactory;

		if (!($transaction instanceof \Customweb\OgoneCw\Model\Authorization\Transaction)) {
			$transaction = $this->_transactionFactory->create()->loadByOrderId($this->_checkoutSession->getLastRealOrder()->getId());
		}
		$this->transaction = $transaction;
	}

	public function getPaymentMethod()
	{
		return $this->getTransaction()->getTransactionObject()->getPaymentMethod();
	}

	public function getOrderContext()
	{
            if($this->getTransaction()->getTransactionObject() != null){
                return $this->getTransaction()->getTransactionObject()->getTransactionContext()->getOrderContext();
            }
	}

	public function getTransaction()
	{
		return $this->transaction;
	}

	public function getOrder()
	{
		return $this->getTransaction()->getOrder();
	}

	public function getQuote()
	{
		return $this->getTransaction()->getQuote();
	}

	public function isMoto()
	{
		return $this->getTransaction()->getTransactionObject() != null
			&& $this->getTransaction()->getTransactionObject()->getAuthorizationMethod() == \Customweb_Payment_Authorization_Moto_IAdapter::AUTHORIZATION_METHOD_NAME;
	}
}