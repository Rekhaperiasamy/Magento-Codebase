<?php
namespace Orange\Checkout\Controller\Cart;

class Tempo extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;
	protected $_checkoutSession;
	protected $_productRepository;
	protected $_cart;
    protected $_productModel;
	protected $_pageConfig;
	protected $resultRedirectFactory;
	
	public function __construct(
       \Magento\Framework\App\Action\Context $context,
	   \Magento\Framework\View\Result\PageFactory $resultPageFactory,
	   \Magento\Checkout\Model\Cart $cart,
	   \Magento\Catalog\Model\ProductRepository $productRepository,
	   \Magento\Catalog\Model\Product $productModel,
	   \Magento\Checkout\Model\Session $checkoutSession
	    
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
		$this->_checkoutSession 	 = $checkoutSession;
		$this->_productRepository 	 = $productRepository;
		$this->_productModel         = $productModel;
		$this->_cart 				 = $cart;
		$this->resultRedirectFactory = $context->getResultRedirectFactory();
		//$this->_pageConfig=$pageConfig;
    }
	
    /**
     * Flush cache storage
     *
     */
    public function execute()
    {  
	    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$customerGroupId = $objectManager->create('Orange\Customer\Model\Session')->getCustomerTypeId();
		$customerGroup = $objectManager->create('Orange\Customer\Model\Session')->getCustomerTypeName();
		$urlInterface=$objectManager->get('Magento\Framework\UrlInterface');
        $currentUrl=$urlInterface->getCurrentUrl();
		$urlarray=explode('/',$currentUrl);
		//echo $customerGroup = $objectManager->create('Magento\Customer\Api\GroupRepositoryInterface')->getById($customerGroupId)->getCode();//Get customer group name
		$resultPage = $this->resultPageFactory->create();
		if($customerGroup == 'SOHO') {
		 $resultPage->getConfig()->setRobots('NOINDEX,NOFOLLOW');
		}
		if(in_array('zelfstandigen',$urlarray) || in_array('independants',$urlarray)){
			$RedirectUrl=$urlInterface->getUrl('checkout/cart/tempo');
			$resultRedirect=$this->resultRedirectFactory->create()->setUrl($RedirectUrl);
            return $resultRedirect;
	    }
		$quote = $this->_checkoutSession->getQuote();
		$totalItemsInCart = $quote->getItemsCount();
	    $quote = $this->_checkoutSession->getQuote();
		$totalItemsInCart = $quote->getItemsCount();
		$onepageSku      = $this->getOnepageSku();
		$pIds = array();
		$productId = $this->loadBysku($onepageSku);
		$pId       = $productId->getEntityId();
		foreach ($quote->getAllItems() as $item) {
			$pIds[] = $item->getProductId();
		}
		if(!$totalItemsInCart) {
			$params = array(
				'product' => $pId,
				'qty' => 1
			);
			$_product = $this->_productRepository->getById($pId);
			$this->_cart->addProduct($_product,$params);
			$this->_cart->save();
		} else {
			if (!in_array($pId, $pIds)) {
				$params = array(
					'product' => $pId,
					'qty' => 1
				);
				$_product = $this->_productRepository->getById($pId);
				$this->_cart->addProduct($_product,$params);
				$this->_cart->save();
			}
		/* 	foreach ($quote->getAllItems() as $item) {
					$productAttributeSet = $item->getProduct()->getAttributeSetId();
					if($productAttributeSet == 14 && $item->getProductId() != $pId && $item->getPrice() <= 0) {
						return $this->_redirect('checkout/cart/prepaid-simcard', ['_secure' => true]);
					}
					
					if($productAttributeSet != 14 || $item->getPrice() > 0) {
						return $this->_redirect('checkout/cart', ['_secure' => true]);
					}
				} */
		}
		$resultPage = $this->resultPageFactory->create();
		$resultPage->getConfig()->getTitle()->set(__('Tempo Giga'));
		return $resultPage;
    }
	/* Load Product id by Sku */
	public function loadBysku($sku)
	{
	return $this->_productRepository->get($sku);
	}
	/* Get Onepage Sku from Configuration */
		 public function getOnepageSku()
	{
	
		$scopeconfig = $this->objectManager()->create('\Magento\Framework\App\Config\ScopeConfigInterface');
		$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
		$onepageSKu = $scopeconfig->getValue('simcard_sku/simcardsku_configuration/simcardsku_url',$storeScope); 
		return $onepageSKu;
	}
		   public function objectManager() {
        return \Magento\Framework\App\ObjectManager::getInstance();
    }
}