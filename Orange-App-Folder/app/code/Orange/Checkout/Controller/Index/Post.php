<?php
namespace Orange\Checkout\Controller\Index;
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
		//////First Step Calculation///////////////////////////	
			if(isset($data->te_existing_number) && $data->te_existing_number!="") {
				$te_existing_number = $data->te_existing_number;
			} else {
				$te_existing_number = 0;
			}
			if(isset($data->te_existing_number)&& $data->te_existing_number!="" ) {
				$telephone = $data->te_existing_number;
			} else {
				$telephone = 9999999999;
			}
			if(isset($data->sim_number)) {
			  if($data->sim_number == 1)
			  {
				$sim_number = 0;
				$te_existing_number = $data->te_existing_number;
				
			  }
              else
              {
                	$sim_number = 1;
				   $te_existing_number = "";
              } 			  
			} else {
				$sim_number = 1;
				   $te_existing_number = "";
			}
			
			if(isset($data->offer_subscription)) {
				$newsletter = 1;
			} else {
				$newsletter = 0;
			}
			if(isset($data->ex_invoice)) {
			$ex_invoice = 1;
			} else {
				$ex_invoice = 0;
			}
			if(isset($data->discount_f)) {
			$discount_f = 1;
			} else {
				$discount_f = 0;
			}
				if(isset($data->gender)&&($data->gender == 1)) {
			$gender = 'Mr';
			} else {
				$gender = 'Mme';
			}
			/*************Code to Check Profile Related Fields*******************************/		
			if(isset($data->tx_profile_dropdown)) {
			 
			 $tx_profile_dropdown = $data->tx_profile_dropdown;
			 if($tx_profile_dropdown == 1)
			 {
			   $tx_profile_dropdown  = 'Profession libérale sans numéro de TVA';
			   if(isset($data->vat_number))
			   {
			   $vat_number    =   $data->vat_number;
			   }
			   else
			   {
			   $vat_number    =   '';
			   }
			   $company_name  =   'None';
			   $legal_status  =   'None';
			 }
			 else if($tx_profile_dropdown == 2)
			 {
			   $tx_profile_dropdown  = 'Profession libérale avec numéro de TVA';
		       $vat_number    =   'None';
			   $company_name  =   'None';
			   $legal_status  =   'None';
			 
			 }
			  else if($tx_profile_dropdown == 3)
			 {
			    $tx_profile_dropdown  = 'Entreprise';
			    	   if(isset($data->vat_number))
			   {
			   $vat_number    =   $data->vat_number;
			   }
			   else
			   {
			   $vat_number    =   '';
			   }
			    $company_name  =   $data->company_name;
			    $legal_status  =   $data->legal_status;
			 }
			  else if($tx_profile_dropdown == 4)
			 {
			    $tx_profile_dropdown  = 'Indépendant';
			    $vat_number    =   $data->vat_number;
			    $company_name  =   'None';
			    $legal_status  =   'None';
			 }
			 
			}
			else
			{
			$tx_profile_dropdown = "None";
			$vat_number    =   'None';
			$company_name  =   'None';
			$legal_status  =   'None';
			}

			/////////////////////////////////////////////////////
	/*************Code to Check nationality*******************************/		
			if(isset($data->nationality))
			{
			    if($data->nationality == 'belgium')
				{
				  $passport_number  = $data->passport_number;
				  $registered       =  0;
				  $residence_number = '';
				  $client_code      = $data->client_code;
				  $client_name      = $data->client_name;
				}
				else
				{
				  $passport_number  = $data->passport_number;
				  $residence_number = $data->residence_number;
				  if(isset($data->registered))
				  {
				    $registered  =  1;
				  }
				  else
				  {
				  $registered  =  0;
				  }
				   $client_code      = '';
				   $client_name      = '';
				
				}
			}	
//////////////////////////////////////////////////////////////////////////////////////		
////////////////////Existing Number////////////////////////////////////////////////////
if(isset($data->nationality))
			{
			    if($data->nationality == 'belgium')
				{
				  $passport_number  = $data->passport_number;
				  $registered       =  0;
				  $residence_number = '';
				  $client_code      = $data->client_code;
				  $client_name      = $data->client_name;
				}
				else
				{
				  $passport_number  = $data->passport_number;
				  $residence_number = $data->residence_number;
				  if(isset($data->registered))
				  {
				    $registered  =  1;
				  }
				  else
				  {
				  $registered  =  0;
				  }
				   $client_code      = '';
				   $client_name      = '';
				
				}
			}	
//////////////////////////////////////////////////////////////////////////////////////	
if(isset($data->subscription_type))
{
  $currentOperator   =  $data->subscription_type; 
}
else
{
 $currentOperator   =  "";
}
if(isset($data->current_operator))
{
  $currentOperatorType   =  $data->current_operator; 
}
else
{
 $currentOperatorType   =  "";
}
if(isset($data->current_operator))
{
  $currentOperatorType   =  $data->current_operator; 
}
else
{
 $currentOperatorType   =  "";
}
if(isset($data->bill_in_name))
{
  $bill_in_name   =  1; 
}
else
{
 $bill_in_name   =  0;
}
if(isset($data->holders_name))
{
  $holders_name   =  $data->holders_name; 
}
else
{
 $holders_name   =  "";
}
if(isset($data->holder_name))
{
  $holder_name   =  $data->holder_name; 
}
else
{
 $holder_name   =  "";
}
if(isset($data->network_customer_number))
{
  $network_customer_number   =  $data->network_customer_number; 
}
else
{
 $network_customer_number   =  "";
}
if(isset($data->simcard_number))
{
  $simcard_number   =  $data->simcard_number; 
}
else
{
 $simcard_number   =  "";
}
		if ($totalItemsInCart) {
				$dob = $data->date_year.'-'.$data->date_month.'-'.$data->date_day;
				$bpostcode_city = explode(' ',$data->b_postcode_city);
				$bpostcode = $bpostcode_city[0];
				$bcity     = $bpostcode_city[1]; 
				$spostcode_city = explode(' ',$data->s_postcode_city);
				$spostcode = $spostcode_city[0];
				$scity     = $spostcode_city[1]; 
				$orderData=[
					'currency_id'  => 'EUR',
					'email'        => $data->email, //buyer email id
                   	'billing_address' =>[
					    'sim_number'        => $sim_number,
						'prefix'    => $gender, 
                        'te_existing_number'=>$te_existing_number,
						'ex_invoice'        =>$ex_invoice,
						'discount_f'        =>$discount_f,
						'tx_drop_down'      =>$tx_profile_dropdown,
						'newsletter_subscription' => $newsletter,
						'vat_id'            => $vat_number,
						'legal_status'            =>$legal_status,
						'company_name'            =>$company_name,
						'firstname'         => $data->first_name, 
						'lastname'          => $data->last_name,
						'dob'		   => $dob,
						'street'  => array (
							'0' => $data->b_street,
							'1' => $data->b_box,
						),
						'client_name' => $client_name,
						'client_code' => $client_code,
						'registered' => $registered,
						'passport_number' => $passport_number,
						'residence_number' => $residence_number,
						'nationality'      =>$data->nationality,
						'current_operator'      =>$currentOperator,
						'current_operator_type'      =>$currentOperatorType,
						'bill_in_name'      =>$bill_in_name,
						'holders_name'      =>$holders_name,
						'holder_name'      =>$holder_name,
						'network_customer_number'=>$network_customer_number,
						'simcard_number'=>$simcard_number,
						'country_id' => 'BE',
						'region' => $data->nationality,
						'postcode' => $bpostcode,
						'city' => $bcity,
						'telephone' => $telephone,
						'street_number' => $data->b_number
					],
					'shipping_address' =>[
					    'sim_number'        => $sim_number,
						'prefix'    => $gender, 
                        'te_existing_number'=>$te_existing_number,
						'ex_invoice'        =>$ex_invoice,
						'discount_f'        =>$discount_f,
						'tx_drop_down'      =>$tx_profile_dropdown,
						'newsletter_subscription' => $newsletter,
						'vat_id'            =>$vat_number,
						'legal_status'            =>$legal_status,
						'company_name'            =>$company_name,
						'firstname'    => $data->s_name, 
						'lastname'     => $data->last_name,
						'dob'		   => $dob,
						'street'  => array (
							'0' => $data->s_street,
							'1' => $data->s_box,
						),
						'client_name' => $client_name,
						'client_code' => $client_code,
						'registered' => $registered,
						'passport_number' => $passport_number,
						'residence_number' => $residence_number,
						'nationality'      =>$data->nationality,
						'current_operator'      =>$currentOperator,
						'current_operator_type'      =>$currentOperatorType,
						'bill_in_name'      =>$bill_in_name,
						'holders_name'      =>$holders_name,
						'holder_name'      =>$holder_name,
						'network_customer_number'=>$network_customer_number,
						'simcard_number'=>$simcard_number,
						'city' => $scity,
						'country_id' => 'BE',
						'region' => $data->country,
						'postcode' => $spostcode,
						'telephone' => $telephone,
						'street_number' => $data->s_number
						]
				];
       
				$quote->setCustomerEmail($orderData['email']);
				$quote->setCustomerId(null);
				$quote->setCustomerIsGuest(true);
				$quote->setCurrency();
				$quote->setStore($store);

				$billingAddress  = $quote->getBillingAddress()->addData($orderData['billing_address']);
				$shippingAddress = $quote->getShippingAddress()->addData($orderData['shipping_address']);

				$shippingAddress->setCollectShippingRates(true)->collectShippingRates()
								->setShippingMethod('freeshipping_freeshipping')
								->setPaymentMethod('cashondelivery');

				$quote->getShippingAddress()->setShippingMethod('freeshipping_freeshipping');
				$quote->setPaymentMethod('cashondelivery');
				$quote->getPayment()->setQuote($quote);
				$quote->getPayment()->importData(['method' => 'cashondelivery']);
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
					$this->_checkoutSession->unsNewcheckout();
					$this->_redirect('checkout/index/success', ['_secure' => true]);
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