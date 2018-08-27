<?php
namespace Orange\Checkout\Controller\Cart;
use Magento\Framework\App\ObjectManager;

class PostTempo extends \Magento\Framework\App\Action\Action
{
    protected $_quoteFactory;
    protected $_orderModel;
	protected $_productRepository;
	protected $_cart;
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
		\Magento\Catalog\Model\ProductRepository $productRepository,
		\Magento\Checkout\Model\Cart $cart,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Product $productModel,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Quote\Model\QuoteManagement $quoteManagementModel,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
		\Magento\Checkout\Model\Session $checkoutSession
    ) {
		$this->_storeManager		 = $storeManager;
		$this->_productRepository 	 = $productRepository;
		$this->_cart 				 = $cart;
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
         try {
			$data = $this->getRequest()->getPost();
			$store= $this->_storeManager->getStore();
			$websiteId = $this->_storeManager->getStore()->getWebsiteId();
			$quote = $this->_checkoutSession->getQuote();
			//$quote = $this->_cart->getQuote();
			$totalItemsInCart = $quote->getItemsCount();
			if(isset($data->existing_number)) {
				$existing_number = 1;
			} else {
				$existing_number = 0;
			}
			if(isset($data->newsletter)) {
				$newsletter = 1;
			} else {
				$newsletter = 0;
			}
			if ($totalItemsInCart) {
				//$dob = $data->date_year.'-'.$data->date_month.'-'.$data->date_day;
				$orderData=[
					'currency_id'  => 'EUR',
					'email'        => $data->email, //buyer email id
					'shipping_address' =>[
						'prefix'    => $data->gender, //address Details
						'is_existing_number' => $existing_number,
						'newsletter_subscription' => $newsletter,
						'dob'		   => $data->c_dob, 
						'firstname'    => $data->first_name, 
						'lastname'     => $data->name,
						'street'  => array (
							'0' => $data->street,
							'1' => $data->box,
						),
						'city' => $data->city,
						'country_id' => 'BE',
						'region' => 'Belgium',
						'postcode' => $data->postcode,
						'telephone' => $data->number,
						'birth_place' => $data->birth_place
						]
				];
				
				$quote->setCustomerEmail($orderData['email']);
				$quote->setCustomerDob($data->c_dob);
				$quote->setCustomerId(null);
				$quote->setCustomerIsGuest(true);
				$quote->setCurrency();
				$quote->setStore($store);

				$billingAddress  = $quote->getBillingAddress()->addData($orderData['shipping_address']);
				$shippingAddress = $quote->getShippingAddress()->addData($orderData['shipping_address']);

				$shippingAddress->setCollectShippingRates(true)->collectShippingRates()
								->setShippingMethod('freeshipping_freeshipping')
								->setPaymentMethod('free');

				$quote->getShippingAddress()->setShippingMethod('freeshipping_freeshipping');
				$quote->setPaymentMethod('free');
				$quote->getPayment()->setQuote($quote);
				$quote->getPayment()->importData(['method' => 'free']);
				$quote->setInventoryProcessed(false);
				$quote->save();		
			
				$order = $this->_quoteManagementModel->submit($quote);
				$order->setEmailSent(1);
				$increment_id = $order->getRealOrderId();
				$order_id = $order->getId();
				if($order->getEntityId()){
					$result['order_id']= $order->getRealOrderId();					
					$this->_checkoutSession->clearHelperData();
					$this->_checkoutSession->setLastRealOrderId($increment_id);
					$this->_redirect('checkout/cart/success', ['_secure' => true]);
				} else {
					$result=['error'=>1,'msg'=>'Error on creating order'];
					echo $result['msg']; die;
				}
			} else {
				return $this->_redirect('checkout/cart', ['_secure' => true]);
			}
			
        } catch (\Exception $ex) {
            echo $ex->getMessage();
            exit;
        }
        
    }
}