<?php

namespace Orange\CheckoutRedirect\Observer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
class ProductRedirectCheckout implements ObserverInterface
{
   protected $_responseFactory;
    protected $_url;
	protected $_catalogHelper;

    public function __construct(

        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\UrlInterface $url,
		\Orange\Catalog\Helper\CatalogUrl $catalogHelper
    ) {
        $this->_responseFactory = $responseFactory;
        $this->_url = $url;
		$this->_catalogHelper = $catalogHelper;
    }

    public function execute(Observer $observer) {
		$event = $observer->getEvent();
		$RedirectUrl= $this->_url->getUrl('checkout/cart', ['_secure' => true]);
		$RedirectUrl = $this->_catalogHelper->getFormattedUrl($RedirectUrl);//Format URL to SOHO URL
		
		$this->_responseFactory->create()->setRedirect($RedirectUrl)->sendResponse(); //commented from perfomance
		die();
	}
	
}