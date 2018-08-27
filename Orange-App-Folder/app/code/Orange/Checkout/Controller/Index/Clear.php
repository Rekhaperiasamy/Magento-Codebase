<?php
namespace Orange\Checkout\Controller\Index;
class Clear extends \Magento\Framework\App\Action\Action
{
    protected $_quoteFactory;
    protected $_orderModel;
    protected $_productModel;
    protected $_customerRepository;
    protected $_quoteManagementModel;
	protected $_checkoutSession;
	protected $_customerFactory;
	protected $_storeManager;

    public function __construct(
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
		\Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Sales\Model\Order $orderModel,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Product $productModel,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Quote\Model\QuoteManagement $quoteManagementModel,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
		\Magento\Checkout\Model\Session $checkoutSession
    ) {
		$this->_storeManager		 = $storeManager;
        $this->_quoteFactory         = $quoteFactory;
        $this->_orderModel           = $orderModel;
        $this->_productModel         = $productModel;
		$this->_customerFactory 	 = $customerFactory;
        $this->_customerRepository   = $customerRepository;
        $this->_quoteManagementModel = $quoteManagementModel;
		$this->_checkoutSession 	 = $checkoutSession;

        parent::__construct($context);
    }

    public function execute()
    {
	               echo "hai";
			
                    print_r($this->_checkoutSession->getNewcheckout());

					$this->_checkoutSession->unsNewcheckout();
						   exit;
					$this->_redirect('checkout/newstep/index', ['_secure' => true]);
	}

}