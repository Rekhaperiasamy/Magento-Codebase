<?php

namespace Orange\Checkout\Controller\Index;


class Notification extends \Magento\Framework\App\Action\Action
{
	protected $_pageFactory;
	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $_pageFactory)
	{
		$this->_pageFactory = $_pageFactory;
		return parent::__construct($context);
	}

	public function execute()
	{  

		return $this->_pageFactory->create();
	}
}