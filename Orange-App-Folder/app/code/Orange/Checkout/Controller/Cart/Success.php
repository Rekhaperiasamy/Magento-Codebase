<?php
namespace Orange\Checkout\Controller\Cart;
use Orange\Selligent\Helper\Selligent;
use Magento\Framework\App\ObjectManager;
class Success extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;
	protected $checkoutSession;
	protected $coreRegistry;
	public function __construct(
       \Magento\Framework\App\Action\Context $context,
	   \Magento\Checkout\Model\Session $checkoutSession,
	   \Magento\Framework\Registry $coreRegistry,
	   \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
		$this->_checkoutSession = $checkoutSession;
		$this->_coreRegistry = $coreRegistry;
        $this->resultPageFactory = $resultPageFactory;
    }
	
    /**
     * Flush cache storage
     *
     */
    public function execute()
    {
		$incrementId = $this->_checkoutSession->getLastRealOrderId();		
		if($incrementId) {
			$objectManager = ObjectManager::getInstance();			
			$orderD = $objectManager->create('Magento\Sales\Model\Order')->load($incrementId, 'increment_id');		
			$shippingAddress = $orderD->getBillingAddress(); 
			$objectManager->get('Orange\Selligent\Helper\Selligent')->doSelligentCall($orderD);
			$this->_coreRegistry->register('OrderId', $incrementId);
			$this->_checkoutSession->clearHelperData();
			unset($incrementId);
			$this->resultPage = $this->resultPageFactory->create();
			return $this->resultPage;
		} else {
			return $this->_redirect('checkout/cart', ['_secure' => true]);
		}
    }
}