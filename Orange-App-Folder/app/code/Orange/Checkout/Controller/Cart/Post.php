<?php
namespace Orange\Checkout\Controller\Cart;

class Post extends \Magento\Framework\App\Action\Action
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
         try {
			$data = $this->getRequest()->getPost();
			$store= $this->_storeManager->getStore();
			$websiteId = $this->_storeManager->getStore()->getWebsiteId();
			$quote = $this->_checkoutSession->getQuote();
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
				foreach ($quote->getAllItems() as $item) {
					$productAttributeSet = $item->getProduct()->getAttributeSetId();
					if($productAttributeSet != 14 || $item->getPrice() > 0) {
						return $this->_redirect('checkout/cart', ['_secure' => true]);
					}
				}
				/*$customer=$this->_customerFactory->create();
				$customer->setWebsiteId($websiteId);*/
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
						'region' => $data->country,
						'postcode' => $data->postcode,
						'telephone' => $data->number,
						'birth_place' => $data->birth_place
						]
				];
				/*$customer->loadByEmail($orderData['email']);// load customet by email address
				if(!$customer->getEntityId()){
					//If not avilable then create this customer 
					$customer->setWebsiteId($websiteId)
							->setStore($store)
							->setPrefix($orderData['shipping_address']['prefix'])
							->setFirstname($orderData['shipping_address']['firstname'])
							->setLastname($orderData['shipping_address']['lastname'])
							->setDob($orderData['shipping_address']['dob'])
							->setEmail($orderData['email'])
							->setPassword($orderData['email']);
					$customer->save();
				}
				
				// if you have allready buyer id then you can load customer directly 
				$customer= $this->_customerRepository->getById($customer->getEntityId());
				$quote->assignCustomer($customer);*/
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