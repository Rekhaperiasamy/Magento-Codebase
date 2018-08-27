<?php
namespace Orange\Checkout\Controller\Newstep;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;
	protected $_checkoutSession;
	public function __construct(
       \Magento\Framework\App\Action\Context $context,
	   \Magento\Framework\View\Result\PageFactory $resultPageFactory,
	   \Magento\Checkout\Model\Session $checkoutSession
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
		$this->_checkoutSession 	 = $checkoutSession;
    }
	
    /**
     * Flush cache storage
     *
     */
    public function execute()
    {
		$quote = $this->_checkoutSession->getQuote();
		$totalItemsInCart = $quote->getItemsCount();
		if ($totalItemsInCart) {
			foreach ($quote->getAllItems() as $item) {
				$productAttributeSet = $item->getProduct()->getAttributeSetId();
				
			}
			$this->resultPage = $this->resultPageFactory->create();  
			return $this->resultPage;
		} else {
			return $this->_redirect('checkout/cart', ['_secure' => true]);
		}
    }
}