<?php
namespace Orange\Intermediate\Controller\Listing;

use Magento\Framework\App\Action\Context;
 
class Item extends \Magento\Framework\App\Action\Action
{
    protected $_resultPageFactory;
 
    public function __construct(Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\View\Page\Config $pageConfig)
    {
        $this->_resultPageFactory = $resultPageFactory;
		$this->_pageConfig = $pageConfig;  
        parent::__construct($context);
    }
 
    public function execute()
    {
		$intermediateId = $this->getRequest()->getParam('id');
		$intermediateSort = $this->getRequest()->getParam('sort');		
        $resultPage = $this->_resultPageFactory->create();
		$resultPage->getLayout()->getBlock('intermediate')->setIntermediateId($intermediateId);
		$resultPage->getLayout()->getBlock('intermediate')->setSort($intermediateSort);
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		if ($intermediateId) {
		    $registry = $objectManager->get('Magento\Framework\Registry');
			$registry->register('inetrmidiateid',$intermediateId);
			$product = $objectManager->create('Magento\Catalog\Model\Product')->load($intermediateId);
			if ($product->getMetaTitle()) {
				$this->_pageConfig->getTitle()->set($product->getMetaTitle());
			}
			if ($product->getMetaKeyword()) {
				$this->_pageConfig->setKeywords($product->getMetaKeyword());
			}
			if ($product->getMetaDescription()) {
			    $this->_pageConfig->setDescription($product->getMetaDescription());
			}
			$urlInterface = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\UrlInterface');
			$this->_pageConfig->addRemotePageAsset(
                    $urlInterface->getCurrentUrl(),
                    'canonical',
                    ['attributes' => ['rel' => 'canonical']]
                );
			
		}
        return $resultPage;
    }
}