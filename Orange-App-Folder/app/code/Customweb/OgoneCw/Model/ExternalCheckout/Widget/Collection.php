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

namespace Customweb\OgoneCw\Model\ExternalCheckout\Widget;

class Collection
{
	/**
	 * @var \Magento\Checkout\Model\Session
	 */
	protected $_checkoutSession;

	/**
	 * @var \Customweb\OgoneCw\Model\ExternalCheckout\ContextFactory
	 */
	protected $_contextFactory;

	/**
	 * @var \Customweb\OgoneCw\Model\DependencyContainer
	 */
	protected $_container;

	/**
	 * @param \Magento\Checkout\Model\Session $checkoutSession
	 * @param \Customweb\OgoneCw\Model\ExternalCheckout\ContextFactory $contextFactory
	 * @param \Customweb\OgoneCw\Model\DependencyContainer $container
	 */
	public function __construct(
			\Magento\Checkout\Model\Session $checkoutSession,
			\Customweb\OgoneCw\Model\ExternalCheckout\ContextFactory $contextFactory,
			\Customweb\OgoneCw\Model\DependencyContainer $container
	) {
		$this->_checkoutSession = $checkoutSession;
		$this->_contextFactory = $contextFactory;
		$this->_container = $container;
	}

	/**
	 * @return \Customweb\Base\Model\ExternalCheckout\IWidget[]
	 */
	public function getWidgets()
	{
		$widgets = [];

		

		$quote = $this->_checkoutSession->getQuote();
		if ($quote instanceof \Magento\Quote\Model\Quote && $quote->getId() && $quote->hasItems()) {
			/* @var $context \Customweb\OgoneCw\Model\ExternalCheckout\Context */
			$context = $this->_contextFactory->create()->loadReusableByQuoteId($this->_checkoutSession->getQuoteId());
			if (!$context->getId()) {
				$context->setQuoteId($quote->getId());
				$context->save();
			}

			$providerService = $this->_container->getBean('Customweb_Payment_ExternalCheckout_IProviderService');
			if (!($providerService instanceof \Customweb_Payment_ExternalCheckout_IProviderService)) {
				throw new \Customweb_Core_Exception_CastException('Customweb_Payment_ExternalCheckout_IProviderService');
			}

			$context->updateFromQuote($quote);
			$context->setState(\Customweb_Payment_ExternalCheckout_IContext::STATE_PENDING);

			$checkouts = $providerService->getCheckouts($context);

			foreach ($checkouts as $checkout) {
				$widgets[] = new \Customweb\OgoneCw\Model\ExternalCheckout\Widget($checkout, $providerService->getWidgetHtml($checkout, $context));
			}

			$context->save();
		}

		

		return $widgets;
	}
}