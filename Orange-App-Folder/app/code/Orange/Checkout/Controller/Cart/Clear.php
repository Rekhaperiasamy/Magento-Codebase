<?php
namespace Orange\Checkout\Controller\Cart;

class Clear extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;

    /**
     * Constructor
     * 
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
	    $params=$this->getRequest()->getParams();       
		$group=$params['group'];
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cart = $objectManager->get('\Magento\Checkout\Model\Cart'); 
        $totalItems = $cart->getQuote()->getItemsCount();
	    $customerGroup = $objectManager->create('Orange\Customer\Model\Session')->getCustomerTypeName();     
		if($totalItems>0){
           $del = $this->deleteQuoteItems();                        
		}
    }

	public function deleteQuoteItems(){
		$checkoutSession = $this->getCheckoutSession();
		$allItems = $checkoutSession->getQuote()->getAllVisibleItems();//returns all teh items in session
		foreach ($allItems as $item) {
			$itemId = $item->getItemId();//item id of particular item
			$quoteItem=$this->getItemModel()->load($itemId);//load particular item which you want to delete by his item id
			$quoteItem->delete();//deletes the item
		}
    }

	public function getCheckoutSession(){
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();//instance of object manager 
		$checkoutSession = $objectManager->get('Magento\Checkout\Model\Session');//checkout session
		return $checkoutSession;
	}
 
    public function getItemModel(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();//instance of object manager
        $itemModel = $objectManager->create('Magento\Quote\Model\Quote\Item');//Quote item model to load quote item
        return $itemModel;
    }
}