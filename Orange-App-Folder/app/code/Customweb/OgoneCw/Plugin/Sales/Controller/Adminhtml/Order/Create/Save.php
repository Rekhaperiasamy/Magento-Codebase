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

namespace Customweb\OgoneCw\Plugin\Sales\Controller\Adminhtml\Order\Create;

class Save
{
	/**
	 * Core registry
	 *
	 * @var \Magento\Framework\Registry
	 */
	protected $_coreRegistry = null;

	/**
	 * @var \Magento\Framework\Controller\Result\RedirectFactory
	 */
	protected $_redirectResultFactory;

	/**
	 * @var \Magento\Framework\Message\ManagerInterface
	 */
	protected $_messageManager;

	public function __construct(
			\Magento\Framework\Registry $coreRegistry,
			\Magento\Framework\Controller\Result\RedirectFactory $redirectResultFactory,
			\Magento\Framework\Message\ManagerInterface $messageManager
	) {
		$this->_coreRegistry = $coreRegistry;
		$this->_redirectResultFactory = $redirectResultFactory;
		$this->_messageManager = $messageManager;
	}

	/**
	 * @param Magento\Sales\Controller\Adminhtml\Order\Create\Save $subject
	 * @param mixed $result
	 */
	public function aroundExecute(\Magento\Sales\Controller\Adminhtml\Order\Create\Save $subject, \Closure $proceed)
	{
		$this->_coreRegistry->register('ogonecwcheckout_moto', true);
		$result = $proceed();
		if ($this->_messageManager->getMessages()->getLastAddedMessage()->getType() == 'success') {
			$this->_messageManager->getMessages()->clear();
			$order = $this->_coreRegistry->registry('ogonecw_checkout_last_order');
			if ($order instanceof \Magento\Sales\Model\Order) {
				if ($order->getPayment()->getMethodInstance() instanceof \Customweb\OgoneCw\Model\Payment\Method\AbstractMethod) {
					return $this->_redirectResultFactory->create()->setPath('ogonecw/checkout/payment', ['order_id' => $order->getId()]);
				}
			}
		}
		return $result;
	}
}