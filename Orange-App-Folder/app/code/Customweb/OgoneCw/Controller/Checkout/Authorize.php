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

class Authorize extends \Customweb\OgoneCw\Controller\Checkout
{
	/**
	 * @var \Magento\Framework\Controller\Result\JsonFactory
	 */
	protected $_resultJsonFactory;
	
	

	/**
	 * @param \Magento\Framework\App\Action\Context $context
	 * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
	 * @param \Magento\Checkout\Model\Session $checkoutSession
	 * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
	 * @param \Customweb\OgoneCw\Model\Authorization\TransactionFactory $transactionFactory
	 * @param \Customweb\OgoneCw\Model\Authorization\Method\Factory $authorizationMethodFactory
	 * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
	 */
	public function __construct(
			\Magento\Framework\App\Action\Context $context,
			\Magento\Framework\View\Result\PageFactory $resultPageFactory,
			\Magento\Checkout\Model\Session $checkoutSession,
			\Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
			\Customweb\OgoneCw\Model\Authorization\TransactionFactory $transactionFactory,
			\Customweb\OgoneCw\Model\Authorization\Method\Factory $authorizationMethodFactory,
			\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
	) {
		parent::__construct($context, $resultPageFactory, $checkoutSession, $quoteRepository, $transactionFactory, $authorizationMethodFactory);
		$this->_resultJsonFactory = $resultJsonFactory;
	}

	public function execute()
	{
		$context = $this->_authorizationMethodFactory->getContextFactory()->createTransaction();
		$authorizationMethodAdapter = $this->_authorizationMethodFactory->create($context);
		$result = $authorizationMethodAdapter->startAuthorization();
		/* $ofrequest = $this->createlogarray($result);
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $objectManager->get('Orange\Upload\Helper\Data')->logCreate('/var/log/ogonerequest.log',$ofrequest); */		
		return $this->_resultJsonFactory->create()->setData($result);
	}
	public function createlogarray($ofrequest)
	{
	$newvalue = explode('?',$ofrequest['redirect']);
	$finallog = explode('&',$newvalue[1]);
	$final_log_array = array();
	foreach(array_filter($finallog) as $jsonlog)
		{
		$sperate_value = explode('=',$jsonlog);
		$final_log_array[$sperate_value[0]]  = rawurldecode($sperate_value[1]);
		}
    return $final_log_array;
	}

}