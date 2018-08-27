<?php

namespace Orange\Seo\Helper;
use phpseclib\Net\SFTP;
use phpseclib\Crypt\RSA;
define('NET_SFTP_LOCAL_FILE2', 1);
class FraudReportData extends \Magento\Framework\App\Helper\AbstractHelper {
    protected $_scopeConfig;
    protected $_storeManager;
    protected $_categoryFactory;
    protected $_productCollectionFactory;
    protected $productAttributeRepository;    
    protected $_messageManager;    
    protected $storeScope;
    protected $scopeConfig;
    protected $_orderCollectionFactory;
    protected $baseDir;
    protected $_productloader;  
    protected $_attributeSet;
	protected $_timezoneInterface;
	protected $_logHelper;
	protected $_cronFlag;
	
    const REPORTPATH = BP . DIRECTORY_SEPARATOR . 'pub' . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'fraudreport' . DIRECTORY_SEPARATOR;

    public function __construct(
    \Magento\Framework\App\Helper\Context $context,
    \Magento\Catalog\Model\ProductFactory $_productloader,
    \Magento\Framework\Message\ManagerInterface $messageManager,
    \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,   
    \Magento\Framework\App\Filesystem\DirectoryList $directory_list,
    \Magento\Catalog\Model\ProductFactory $productFactory,
    \Magento\Store\Model\StoreManagerInterface $storeManager,
    \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet,
    \Magento\Customer\Model\Session $session, 
	\Magento\Catalog\Model\Product\Attribute\Repository $productAttributeRepository, 
	\Magento\Framework\Translate\Inline\StateInterface $inlineTranslation, 
	\Magento\Framework\File\Csv $csvProcessor, 
	\Magento\Catalog\Model\CategoryFactory $categoryFactory, 
	\Magento\Catalog\Model\ProductRepository $productRepository, 
	\Magento\Catalog\Model\Category $categoryModel, 
	\Magento\Catalog\Model\CategoryRepository $categoryRepository, 
	\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
	\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
        \Orange\Upload\Helper\Data $logHelper,
		\Magento\Variable\Model\Variable $variable
    )
    {
        $this->directory_list = $directory_list;
        $this->_storeManager = $storeManager;
        $this->_categoryFactory = $categoryFactory;
        $this->_session = $session;
        $this->scopeConfig = $context->getScopeConfig();
        $this->_productloader = $_productloader;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->inlineTranslation = $inlineTranslation;
        $this->csvProcessor = $csvProcessor;
        $this->categoryModel = $categoryModel;
        $this->_messageManager = $messageManager;
        $this->categoryRepository = $categoryRepository;
        $this->_productAttributeRepository = $productAttributeRepository;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_productRepository = $productRepository;
        
        $this->productFactory = $productFactory;
        $this->_attributeSet = $attributeSet;
		
        $this->_timezoneInterface = $timezoneInterface;
		
		$this->_logHelper = $logHelper;
		
		$this->_cronFlag = $variable;
		
        parent::__construct($context);
    }        

    private function getOrderCollection()
    {
         $time = time();
		 $to = date('Y-m-d H:i:s', $time);
		/* magento custom variable for date flag */
		$cronDateFlag = $this->_cronFlag->loadByCode('cronscript_date')->getValue('plain');
		
		 return $this->_orderCollectionFactory->create()
		              ->addAttributeToSelect('*')
			->addAttributeToFilter('fraud_capture',0)
            ->addAttributeToFilter('created_at', array('from' => $cronDateFlag, 'to' => $to));
					  
    }

    public function generateFraudReport()
    {
       $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
       $baseDir = $this->directory_list->getRoot();
       $scopeConfig = $this->objectManagerInt()->create('Magento\Framework\App\Config\ScopeConfigInterface');
       $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;       
       $discountSohoPrice = $scopeConfig->getValue('soho/soho_configuration/soho_discount', $storeScope);       
	   $orderCollection = $this->getOrderCollection();  
        $orderCount = $orderCollection->getSize();
       $reportFolder = self::REPORTPATH;
	   
        if (is_dir($reportFolder) === false) {
            mkdir($reportFolder);
            chmod($reportFolder, 0777);
        }
       $filename = 'frms_weborders_' . date('YmdHis') . '.txt';
        $file = $reportFolder . $filename;
		$dataArray = array();
		$orderFlagArray = array();
       $log_mode = $this->scopeConfig->getValue('custom_modulelog/modulelog__configuration/setting_mode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		if ($orderCount > 0) {
		   $store_code = $this->_storeManager->getStore()->getCode();
            
		   $onePagecheckoutData = '';
            /* fraud report to be capture only for subsidy, postpaid and IEW products */
            $allowed_lobs = array('Postpaid', 'Default', 'IEW');
		   /* try catch block added */
            try {
                foreach ($orderCollection as $order) {
                    // Fix for #5893
                    if ($order->getStatus() == 'processing') {
                        $order_status = 'paid';
                    }
                    else {
                        $order_status = $order->getStatus();
                    }
				   $orderId = $order->getIncrementId();
					$customerType = $order->getCustomerGroupId();           
                    if ($order->getShippingAddress() != '') {
									$shippingAddress = $order->getBillingAddress();    
				   } else {
									$shippingAddress = $order->getShippingAddress();
				   }
							$quoteid = $order->getQuoteId();
							$items = $order->getAllVisibleItems(); 
							   $onePagecheckoutData = $this->opcData($quoteid);
							   //item level 
								foreach ($items as $item):
                        $productId = $item->getProductId();
									$attributeSetRepository = $this->_attributeSet->get($item->getProduct()->getAttributeSetId());
									$attribute_name = $attributeSetRepository->getAttributeSetName();
									$subsidized_prod = $this->getSubsidyProduct($productId);
									$check_lob = 0;
						/* flag value for update sales_order table */
						$orderFlagArray[$orderId][$productId]['fraudcapture_flag'] = '1';
									
									if (in_array($attribute_name, $allowed_lobs)) {									
                            if (($attribute_name == 'Default') && ($subsidized_prod == 'No')) {
											$check_lob = 0;
                            } else {
											$check_lob = 1;
										}
									}
                        if ($check_lob == 1) {
                            if (isset($log_mode) && $log_mode == 1) {
                                $this->_logHelper->logCreate('/var/log/FraudReport_track.log', 'fraud report for allowed LOB item preparation process started at:' . date('YmdHis'));
                            }
										$postpaidModel = $this->objectManagerInt()->create('Orange\Checkout\Model\Postpaid');
                            $postpaidModelCollections = $postpaidModel->getCollection()->addFieldToFilter('quote_id', $quoteid)->addFieldToFilter('item_id', $item->getQuoteItemId());
                            /* check b2c or soho based price */
                            if ($customerType == 4) {
								/* soho price calculation */
                                $originalPrice = $item->getOriginalPrice() / (1 + ($discountSohoPrice / 100));
                                $productType = $item->getProductType();
										
										if ($productType == 'bundle') {
                                    $bundleprice = $this->getsubsidyprice($item, $order->getStoreId());
                                    $subscriptionAmount = $bundleprice / (1 + ($discountSohoPrice / 100));
                                } else {
                                    $subscriptionAmount = $item->getSubscriptionAmount() / (1 + ($discountSohoPrice / 100));
										}
									} else {
								/* b2c price calculation */
										$originalPrice = $item->getOriginalPrice();
                                $productType = $item->getProductType();
										
										if ($productType == 'bundle') {
                                    $bundleprice = $this->getsubsidyprice($item, $order->getStoreId());
											$subscriptionAmount = $bundleprice;
                                } else {
											$subscriptionAmount = $item->getSubscriptionAmount();	
										}
									}								
									$brandName = '';
									$family = '';
									$itemQty = '';
                            $nid = '';
									$catalogue = '';
									$nintendo_device = '';
									$current_tarrif_plan = '';
									$nintendo_phone = '';
									$existing_number = '';
									$created_date = date('d/m/Y', strtotime($order->getCreatedAt()));
									$created_time = $this->_timezoneInterface
										->date(new \DateTime($order->getCreatedAt()))
										->format('H:i:s');
									$quoteItemId = $item->getQuoteItemId();
                            $sim_data = $this->getDesignSimNumber($quoteid, $quoteItemId);
									
                            /* check postpaid products with multiple quantities */
                            if ($postpaidModelCollections->count() > 0) {
										$incremntId = 0;
                                $prod_name = $this->productData($item, 'prod'); // product name
                                $prod_plan = $this->productPlan($item); // product plan
										$manufacturerName = $this->getProductBrand($productId); // manufacturer
										$ItemQty = round($item->getQtyOrdered());  // Qty 
										$nid = $item->getProductId(); // nid
                                $catalogue = $this->getProductLOB($item, $attribute_name); //product catalogue
                                $current_tarrif_plan = $this->getTariffPlan($item); // product tarrif plan
										$nintendo_device = $this->productData($item, 'nint'); // nintendo_device
                                $city = isset($onePagecheckoutData['b_city']) ? $onePagecheckoutData['b_city'] : ''; // city
                                $zip_code = isset($onePagecheckoutData['b_postcode_city']) ? $onePagecheckoutData['b_postcode_city'] : ''; // zip code
                                $street = isset($onePagecheckoutData['b_street']) ? $onePagecheckoutData['b_street'] : '';
                                $nation = isset($onePagecheckoutData['nationality']) ? $onePagecheckoutData['nationality'] : '';
                                $residence_number = isset($onePagecheckoutData['residence_number']) ? $onePagecheckoutData['residence_number'] : '';
                                $passport_number = isset($onePagecheckoutData['passport_number']) ? $onePagecheckoutData['passport_number'] : '';
                                $nationality = ''; //nationality is blank in 1802 NID
										$other_add = isset($onePagecheckoutData['c_delivery_address']) ? $onePagecheckoutData['c_delivery_address'] : '';
                                $belgian_registered = 'yes';

                                /* 1802 NID Project */
                                $id_card_number  = '';
                                $permit_number = '';
                                if (isset($onePagecheckoutData['id_number'])) {
                                    $id_num = $onePagecheckoutData['id_number'];
                                    $id_first_Char = substr($id_num, 0, 1);
                                    if (ctype_alpha($id_first_Char)) {
                                       $permit_number = $id_num;
                                    } else {
                                        $id_card_number = $id_num;
                                    }
                                }
                                /* 1802 NID Project */

										$productType = $this->getProductTypes($productId);
                                $postalLocation = isset($onePagecheckoutData['customerPostalLocation']) ? $onePagecheckoutData['customerPostalLocation'] : '';
                                $postalMethod = isset($onePagecheckoutData['customerPostalCode']) ? $onePagecheckoutData['customerPostalCode'] : '';
										$bpost_data = $this->getBpostData($shippingAddress, $postalLocation, $postalMethod);
                                /* set delivery method */
                                if (isset($onePagecheckoutData['c_delivery_address'])) {
											if ($onePagecheckoutData['c_delivery_address'] == 3) {
												$delivery_method = 'CLICK_COLLECT_BPACK247';													
											} else if ($onePagecheckoutData['c_delivery_address'] == 2) {
                                        $delivery_method = 'OTHER';
											} else {
                                        $delivery_method = 'CURRENT';
											}
                                } else {
											$delivery_method = '';
										}
										
                                $customer_scoring = isset($onePagecheckoutData['scoringResponse']) ? $onePagecheckoutData['scoringResponse'] : '';
										$nintendo_loyality = $this->getNintendoLoyality($item);
										$scoring_res = $this->scoringResponse($quoteid);
										$ogone_transaction_id = $this->ogoneData($order->getId());
                                $nintendo_phone_model = $this->getNintendoModel($item, 'nint');
								/* clean up telephone no format */
                                if (isset($onePagecheckoutData['cust_telephone'])) {
											$temp_number = str_replace(' ', '', $onePagecheckoutData['cust_telephone']);
											$telephone_no = str_replace('/', '', $temp_number);
                                } else {
											$telephone_no = '';
										}
								/* preparing data array for product quantity level */
                                foreach ($postpaidModelCollections as $postpaidCollection) {
                                    try {
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['created_date'] = $created_date;    //$orderCreatedDate
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['created_time'] = $created_time; //$orderCreatedTime
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['uc_order_id'] = $order->getIncrementId(); //uc_order_id
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['order_status'] = $order_status; //order_status
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['order_total'] = $order->getBaseGrandTotal(); //order_total
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['product_count'] = count($items); //product_count
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['primary_email'] = $order->getCustomerEmail(); //primary_email
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['delivery_first_name'] = isset($onePagecheckoutData['first_name']) ? $onePagecheckoutData['first_name'] : ''; //first_name
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['delivery_last_name'] = isset($onePagecheckoutData['last_name']) ? $onePagecheckoutData['last_name'] : ''; //last_name
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['payment_method'] = $order->getPayment()->getMethod(); //payment_method
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['host'] = $order->getRemoteIp(); //get IP address
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['sim_activated'] = '0'; //sim_activated
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['productName'] = $prod_name; //prod name
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['model'] = ucfirst($prod_plan); //	model
                                        // set manufacturer name
                                        if (isset($manufacturerName)) {
											$dataArray[$orderId][$productId . '_' . $incremntId]['manufacturer'] = $manufacturerName;                                            
                                        } else {
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['manufacturer'] = '';
											}
                                        // set ItemQty
                                        if (isset($ItemQty)) {
											$dataArray[$orderId][$productId . '_' . $incremntId]['itemQty'] = $ItemQty;
                                        } else {
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['itemQty'] = '';
											}
											
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['itemCost'] = round($subscriptionAmount, 2); // cost
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['itemPrice'] = round($originalPrice, 2); // price
                                        //set nid
                                        if (isset($nid)) {
											$dataArray[$orderId][$productId . '_' . $incremntId]['nid'] = $nid;
                                        } else {
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['nid'] = '';
											}
                                        //set catalogue
                                        if (isset($catalogue)) {
											$dataArray[$orderId][$productId . '_' . $incremntId]['$catalogue'] = $catalogue;
                                        } else {
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['catalogue'] = '';
											}
                                        // set nintendo device
                                        if (isset($nintendo_device)) {
											$dataArray[$orderId][$productId . '_' . $incremntId]['nintendo_device'] = $nintendo_device;                                            
											} else {
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['nintendo_device'] = '';
											}
                                        //set language
                                        if ($order->getStoreId() == 1) {
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['language'] = 'nl';
                                        } else {
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['language'] = 'fr';
											}
                                        //set gender
                                        if (isset($onePagecheckoutData['gender'])) {
                                            if ($onePagecheckoutData['gender'] == 1) {
                                                $dataArray[$orderId][$productId . '_' . $incremntId]['title'] = 'mr';  // title
                                            } else {
                                                $dataArray[$orderId][$productId . '_' . $incremntId]['title'] = 'mrs';  // title
												}
											}
                                        /* address info */
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['zipcity'] = $zip_code . ' ' . strtoupper($city); //zip + city
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['street'] = strtoupper($street); //street
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['house_number'] = isset($onePagecheckoutData['b_number']) ? $onePagecheckoutData['b_number'] : ''; //house_number
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['bus'] = isset($onePagecheckoutData['b_box']) ? $onePagecheckoutData['b_box'] : ''; //bus
											
                                        //set optin for invoice
                                        if (isset($onePagecheckoutData['ex_invoice'])) {
                                            if ($onePagecheckoutData['ex_invoice'] == 'no') {
                                                $dataArray[$orderId][$productId . '_' . $incremntId]['optin'] = '';
                                            } else if ($onePagecheckoutData['ex_invoice'] == 'yes') {
                                                $dataArray[$orderId][$productId . '_' . $incremntId]['optin'] = '1';
                                            } else {
                                                $dataArray[$orderId][$productId . '_' . $incremntId]['optin'] = '';
												}
                                        } else {
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['optin'] = '';
											}											
											//design_sim_number
											$gsm_number = $postpaidCollection->getDesignSimNumber();
										
                                        if ($gsm_number == 1) {
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['gsm_number'] = "KEEP";
                                        } else if ($gsm_number == 2) {
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['gsm_number'] = "NEW";
                                        } else {
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['gsm_number'] = "";
											}											
										
											//current_operator
                                        if ($postpaidCollection->getSubscriptionType() !== null) {
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['current_operator'] = $postpaidCollection->getSubscriptionType();
                                        } else {
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['current_operator'] = "";
											}
											//current_gsm_number
                                        if (null !== $postpaidCollection->getDesignTeExistingNumber()) {
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['current_gsm_number'] = $this->getgsm($postpaidCollection->getDesignTeExistingNumber());//
                                        } else {
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['current_gsm_number'] = '';
											}
											//simcard_number
                                        if (null !== $postpaidCollection->getSimcardNumber()) {
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['current_sim_number'] = $postpaidCollection->getSimcardNumber();//current_gsm_number-;
                                        } else {
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['current_sim_number'] = '';
											}											
											//current_tarrif_plan
                                        if (!isset($current_tarrif_plan)) {
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['current_tarrif_plan'] = '';
                                        } else {
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['current_tarrif_plan'] = $current_tarrif_plan;
											}											
											//network_customer_number											
                                        if (null !== $postpaidCollection->getNetworkCustomerNumber()) {
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['current_customer_number'] = $postpaidCollection->getNetworkCustomerNumber();
                                        } else {
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['current_customer_number'] = '';
											}
											//owner of the account
                                        if ((null !== $postpaidCollection->getSubscriptionType()) && (null !== $postpaidCollection->getHoldersName())) {
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['are_you_the_owner_of_the_account'] = $this->getcustomernumber($postpaidCollection->getSubscriptionType(), $postpaidCollection->getHoldersName());
                                        } else {
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['are_you_the_owner_of_the_account'] = "";
										    }
											//first_name_owner
                                        if (null !== $postpaidCollection->getHoldersName()) {
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['first_name_owner'] = $postpaidCollection->getHoldersName(); //last_name_owner
                                        } else {
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['first_name_owner'] = '';
										    }
										    //last_name_owner
                                        if (null !== $postpaidCollection->getHolderFirstname()) {
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['last_name_owner'] = $postpaidCollection->getHolderFirstname(); //first_name_owner
                                        } else {
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['last_name_owner'] = '';
										    }
										    
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['telephone_number'] = $telephone_no; //telephone_number
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['date_of_birth'] = isset($onePagecheckoutData['c_dob']) ? $onePagecheckoutData['c_dob'] : ''; //customer dob
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['profession'] = ''; //profession
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['nationality'] = $nationality; //nationality
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['Belgian_ID_document'] = $belgian_registered; //registered with belgian government
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['identity_card_number'] = $id_card_number; //identity_card_number
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['residence_permit_number'] = $permit_number; //register number
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['passport_number'] = $passport_number; //passport_number
											
                                        if (($other_add == 2) && ($subsidized_prod == 'Yes') && ($customerType != 4)) {
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['my_domicile_address_is_different'] = '1'; //my_domicile_address_is_different
                                            $zipCode = isset($onePagecheckoutData['s_postcode_city']) ? $onePagecheckoutData['s_postcode_city'] : ''; //postcode_city
                                            $cityCode = isset($onePagecheckoutData['s_city']) ? $onePagecheckoutData['s_city'] : ''; //city
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['domicile_zipcity'] = $zipCode . ' ' . strtoupper($cityCode); //delivery_zipcity
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['domicile_street'] = isset($onePagecheckoutData['s_street']) ? $onePagecheckoutData['s_street'] : ''; //delivery_street
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['domicile_house_number'] = isset($onePagecheckoutData['s_number']) ? $onePagecheckoutData['s_number'] : ''; //delivery_house_number
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['domicile_bus'] = isset($onePagecheckoutData['s_box']) ? $onePagecheckoutData['s_box'] : ''; //delivery_bus
                                        } else {
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['my_domicile_address_is_different'] = '0'; //my_domicile_address_is_different
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['domicile_zipcity'] = ''; //delivery_zipcity
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['domicile_street'] = ''; //delivery_street
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['domicile_house_number'] = ''; //delivery_house_number
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['domicile_bus'] = ''; //delivery_bus
										    }
											
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['identity_card_number_other'] = isset($onePagecheckoutData['residence_number']) ? $onePagecheckoutData['residence_number'] : ''; //residence_number
											
                                        if (isset($catalogue) && ($catalogue == 'IEW')) {
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['add_to_existing_plan'] = isset($onePagecheckoutData['is_teneuro_' . $quoteItemId]) ? ucfirst($onePagecheckoutData['is_teneuro_' . $quoteItemId]) : ''; //add to existing plan
                                        } else {
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['add_to_existing_plan'] = isset($onePagecheckoutData['ex_invoice']) ? ucfirst($onePagecheckoutData['ex_invoice']) : '';
										    }
											
                                        $plan = $dataArray[$orderId][$productId . '_' . $incremntId]['add_to_existing_plan'];
											//customer_number
                                        if ($plan == 'Yes') {
                                            if (isset($catalogue) && ($catalogue == 'IEW')) {
                                                $dataArray[$orderId][$productId . '_' . $incremntId]['customer_number'] = isset($onePagecheckoutData['iew_telephone_' . $quoteItemId]) ? $onePagecheckoutData['iew_telephone_' . $quoteItemId] : '';//customer_number
                                            } else {
                                                $dataArray[$orderId][$productId . '_' . $incremntId]['customer_number'] = isset($onePagecheckoutData['cu_ex_invoice_cust_number']) ? $onePagecheckoutData['cu_ex_invoice_cust_number'] : '';//customer_number
												}
                                        } else {
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['customer_number'] = '';
											}
										
											//method_of_payment
                                        if ($order->getAccountNumber()) {
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['method_of_payment'] = 'domiciliary';
											} else {
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['method_of_payment'] = 'transfer';
											}

                                        $dataArray[$orderId][$productId . '_' . $incremntId]['bank_account_number'] = $order->getAccountNumber(); //account_no//
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['receive_invoice_electronically'] = isset($onePagecheckoutData['bill_in_name-']) ? $onePagecheckoutData['bill_in_name-'] : ''; //invoice electronically
                                        $profile_dropdown = isset($onePagecheckoutData['tx_profile_dropdown']) ? $onePagecheckoutData['tx_profile_dropdown'] : '';
											
                                        if ($profile_dropdown != '') {
                                            $cmpType = isset($onePagecheckoutData['legal_status']) ? $onePagecheckoutData['legal_status'] : '';
                                        } else {
												$cmpType = '';   
										    }
										   
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['company_type'] = $cmpType; //company_type
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['company_name'] = isset($onePagecheckoutData['company_name']) ? $onePagecheckoutData['company_name'] : ''; //company_name
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['vat_number'] = isset($onePagecheckoutData['vat_number']) ? $onePagecheckoutData['vat_number'] : ''; //vat_number
                                        if (($other_add == 2) && ($subsidized_prod == 'Yes') && ($customerType == 4)) {
                                            $zipCode = isset($onePagecheckoutData['s_postcode_city']) ? $onePagecheckoutData['s_postcode_city'] : '';
                                            $cityCode = isset($onePagecheckoutData['s_city']) ? $onePagecheckoutData['s_city'] : '';
										   
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['company_zipcity'] = $zipCode . ' ' . strtoupper($cityCode);
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['company_street'] = isset($onePagecheckoutData['s_street']) ? $onePagecheckoutData['s_street'] : '';
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['company_number'] = isset($onePagecheckoutData['s_number']) ? $onePagecheckoutData['s_number'] : '';
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['company_bus'] = isset($onePagecheckoutData['s_box']) ? $onePagecheckoutData['s_box'] : '';
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['company_country'] = 'Belgium';
                                        } else {
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['company_zipcity'] = '';
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['company_street'] = '';
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['company_number'] = '';
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['company_bus'] = '';
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['company_country'] = '';
											}
										   
										    /* enter_vat_number */
                                        if (!empty($dataArray[$orderId][$productId . '_' . $incremntId]['vat_number'])) {
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['enter_vat_number'] = '1';
                                        } else {
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['enter_vat_number'] = '0';
										    }
										   
                                        if ($productType == 'tablet_connected') {
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['already_iew'] = isset($onePagecheckoutData['design_te_existing_number-']) ? $onePagecheckoutData['design_te_existing_number-'] : '';
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['iew_number'] = isset($dataArray[$orderId][$productId . '_' . $incremntId]['current_sim_number']) ? $dataArray[$orderId][$productId . '_' . $incremntId]['current_sim_number'] : '';//current_gsm_number-;
                                        } else {
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['already_iew'] = '';
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['iew_number'] = '';
											}											
										
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['already_microsim_card'] = ''; // as per existing live site it's not captured
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['microsim_card_number'] = '';  // as per existing live site it's not captured
										   /* delivery company */
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['deliver_somewhere_else'] = isset($onePagecheckoutData['c_delivery_address']) ? $onePagecheckoutData['c_delivery_address'] : ''; //delivery_zipcity;
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['delivery_address_type'] = '';
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['delivery_company_name'] = '';
                                        $deliver_attention_customer_name = $shippingAddress['firstname'];
										   
										   //delivery_attention_off
                                        if (isset($deliver_attention_customer_name)) {
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['delivery_attention_off'] = $deliver_attention_customer_name;
                                        } else {
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['delivery_attention_off'] = "";
										   }
										   
                                        $zipCode1 = isset($onePagecheckoutData['s_postcode_city']) ? $onePagecheckoutData['s_postcode_city'] : ''; //s_postcode_city
                                        $cityCode1 = isset($onePagecheckoutData['s_city']) ? $onePagecheckoutData['s_city'] : ''; //s_city
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['delivery_zipcity'] = $zipCode1 . ' ' . strtoupper($cityCode1); //delivery_zipcity
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['delivery_street'] = isset($onePagecheckoutData['s_street']) ? $onePagecheckoutData['s_street'] : ''; //delivery_street
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['delivery_house_number'] = isset($onePagecheckoutData['s_number']) ? $onePagecheckoutData['s_number'] : ''; //delivery_house_number
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['delivery_bus'] = isset($onePagecheckoutData['s_box']) ? $onePagecheckoutData['s_box'] : ''; //delivery_bus
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['internet_activation'] = ''; //internet_activation
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['shipping_total'] = $order->getBaseGrandTotal(); //shipping_total
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['opc_brand'] = ''; //opc_brand
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['opc_form_id'] = $quoteid; //session url
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['bpost_data'] = $bpost_data; //bpost_data
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['delivery_method'] = $delivery_method; //delivery_method
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['customer_scoring'] = $customer_scoring; //customer_scoring
										   
										    //check_nationality
                                        if ($subsidized_prod == 'Yes') {
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['check_nationality'] = "Yes";
                                        } else {
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['check_nationality'] = '';
										    }
										
										    //eid_or_rpid
                                        if ($subsidized_prod == 'Yes') {
                                            if (!isset($dataArray[$orderId][$productId . '_' . $incremntId]['identity_card_number'])) {
                                                if (!isset($dataArray[$orderId][$productId . '_' . $incremntId]['residence_permit_number'])) {
                                                    $dataArray[$orderId][$productId . '_' . $incremntId]['eid_or_rpid'] = '';
                                                } else {
                                                    $dataArray[$orderId][$productId . '_' . $incremntId]['eid_or_rpid'] = $dataArray[$orderId][$productId . '_' . $incremntId]['residence_permit_number'];
												   }
                                            } else {
                                                $dataArray[$orderId][$productId . '_' . $incremntId]['eid_or_rpid'] = $dataArray[$orderId][$productId . '_' . $incremntId]['identity_card_number'];
											   }  
                                        } else {
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['eid_or_rpid'] = '';
										    }
											
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['scoring_decline_reason'] = $scoring_res; //scoring_decline_reason
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['pdd_legeal_selection'] = ''; //pdd_legeal_selection
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['place_of_birth'] = isset($onePagecheckoutData['cust_birthplace']) ? $onePagecheckoutData['cust_birthplace'] : ''; //Place of birth
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['ogone_transaction_id'] = $ogone_transaction_id; //ogone_transaction_id
											
										    //nintendo_phone
                                        if (!isset($nintendo_phone_model)) {
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['nintendo_phone'] = '';
                                        } else {
                                            $dataArray[$orderId][$productId . '_' . $incremntId]['nintendo_phone'] = $nintendo_phone_model;
											}
											
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['nintendo_loyalty'] = $nintendo_loyality; //nintendo_loyalty
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['webservice_fraud'] = ''; //webservice_fraud
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['webservice_fraud_2'] = ''; //webservice_fraud_2
                                        $dataArray[$orderId][$productId . '_' . $incremntId]['order_id'] = $orderId; //orderId
										   
											$incremntId++;
                                    } catch (\Exception $e) {
                                        if (isset($log_mode) && $log_mode == 1) {
                                            $this->_logHelper->logCreate('/var/log/FraudReport_track.log', 'Error in postpaidCollection:' . $e->getMessage());
										}
                                    }
                                }//end foreach
                            } else { // set data for single products 
                                try {
                                    $dataArray[$orderId][$productId]['created_date'] = $created_date;    //$orderCreatedDate
										$dataArray[$orderId][$productId]['created_time'] = $created_time; //$orderCreatedTime                             
										$dataArray[$orderId][$productId]['uc_order_id'] = $order->getIncrementId();
										$dataArray[$orderId][$productId]['order_status'] = $order_status;
										$dataArray[$orderId][$productId]['order_total'] = $order->getBaseGrandTotal();
										$dataArray[$orderId][$productId]['product_count'] = count($items);
                                    $dataArray[$orderId][$productId]['primary_email'] = $order->getCustomerEmail();
                                    $dataArray[$orderId][$productId]['delivery_first_name'] = isset($onePagecheckoutData['first_name']) ? $onePagecheckoutData['first_name'] : '';
                                    $dataArray[$orderId][$productId]['delivery_last_name'] = isset($onePagecheckoutData['last_name']) ? $onePagecheckoutData['last_name'] : '';
										$dataArray[$orderId][$productId]['payment_method'] = $order->getPayment()->getMethod(); //payment_method
                                    $dataArray[$orderId][$productId]['host'] = $order->getRemoteIp();
                                    $dataArray[$orderId][$productId]['sim_activated'] = '0'; //sim_activated
										$prod_name = $this->productData($item, 'prod');
										$dataArray[$orderId][$productId]['productName'] = $prod_name; //prod name								
										$prod_plan = $this->productPlan($item);
										$dataArray[$orderId][$productId]['model'] = ucfirst($prod_plan); //prod plan to be used instead of prod model
                                    $manufacturerName = $this->getProductBrand($productId); // manufacturer
										
                                    if (!isset($manufacturerName)) {
											$dataArray[$orderId][$productId]['manufacturer'] = '';
                                    } else {
											$dataArray[$orderId][$productId]['manufacturer'] = $manufacturerName;
										}
										
										$ItemQty = round($item->getQtyOrdered());  // Qty                              
									
                                    if (!isset($ItemQty)) {
											$dataArray[$orderId][$productId]['itemQty'] = '';
                                    } else {
											$dataArray[$orderId][$productId]['ItemQty'] = round($item->getQtyOrdered());
										}
										
                                    $dataArray[$orderId][$productId]['itemCost'] = round($subscriptionAmount, 2); // cost
                                    $dataArray[$orderId][$productId]['itemPrice'] = round($originalPrice, 2); // price
										$nid = $item->getProductId(); // nid
										$dataArray[$orderId][$productId]['nid'] = $item->getProductId();
									
                                    if (!isset($nid)) {
											$dataArray[$orderId][$productId]['nid'] = '';
                                    } else {
											$dataArray[$orderId][$productId]['nid'] = $item->getProductId();
										}
										
                                    $catalogue = $this->getProductLOB($item, $attribute_name);        //catalogue
                                    if (!isset($catalogue)) {
											$dataArray[$orderId][$productId]['catalogue'] = '';
                                    } else {
											$dataArray[$orderId][$productId]['$catalogue'] = $catalogue;
										}
										
										$nintendo_device = $this->productData($item, 'nint'); // nintendo_device
                                    if (!isset($nintendo_device)) {
											$dataArray[$orderId][$productId]['nintendo_device'] = '';
										} else {
											$dataArray[$orderId][$productId]['nintendo_device'] = $nintendo_device;
										}                                
										
										/* language */
                                    if ($order->getStoreId() == 1) {
                                        $dataArray[$orderId][$productId]['language'] = 'nl';
                                    } else {
                                        $dataArray[$orderId][$productId]['language'] = 'fr';
										}
									
										/* gender */
                                    if (isset($onePagecheckoutData['gender'])) {
                                        if ($onePagecheckoutData['gender'] == 1) {
												$dataArray[$orderId][$productId]['title'] = 'mr';  // title
                                        } else {
												$dataArray[$orderId][$productId]['title'] = 'mrs';  // title
											}
										}
											
									   $city = isset($onePagecheckoutData['b_city']) ? $onePagecheckoutData['b_city'] : '';
                                    $zip_code = isset($onePagecheckoutData['b_postcode_city']) ? $onePagecheckoutData['b_postcode_city'] : '';
                                    $dataArray[$orderId][$productId]['zipcity'] = $zip_code . ' ' . strtoupper($city); //zip + city
                                    $street = isset($onePagecheckoutData['b_street']) ? $onePagecheckoutData['b_street'] : '';
                                    $dataArray[$orderId][$productId]['street'] = strtoupper($street); //street
                                    $dataArray[$orderId][$productId]['house_number'] = isset($onePagecheckoutData['b_number']) ? $onePagecheckoutData['b_number'] : ''; //house_number
                                    $dataArray[$orderId][$productId]['bus'] = isset($onePagecheckoutData['b_box']) ? $onePagecheckoutData['b_box'] : ''; //bus
									   /* optin */
											   $dataArray[$orderId][$productId]['optin'] = '';
									
                                    if (isset($onePagecheckoutData['ex_invoice'])) {
                                        if ($onePagecheckoutData['ex_invoice'] == 'no') {
                                            $dataArray[$orderId][$productId]['optin'] = '';
                                        } else if ($onePagecheckoutData['ex_invoice'] == 'yes') {
											   $dataArray[$orderId][$productId]['optin'] = '1';
                                        } else {
											   $dataArray[$orderId][$productId]['optin'] = '';
										   }
                                    } else if (!isset($onePagecheckoutData['ex_invoice'])) {
										  $dataArray[$orderId][$productId]['optin'] = ''; 
									   }
									/* set variable data for plan*/
                                    if (isset($onePagecheckoutData['totalvirtualproduct'])) {
										   $arr = explode('_', $onePagecheckoutData['totalvirtualproduct']);
										   $itemId = $arr[0];
                                        $design_te_existing_number = 'design_te_existing_number-' . $onePagecheckoutData['totalvirtualproduct'];
										
                                        if (isset($onePagecheckoutData[$design_te_existing_number])) {
												$temp_number = str_replace(' ', '', $onePagecheckoutData[$design_te_existing_number]);
												$existing_number = str_replace('/', '', $temp_number);
											}
											
                                        $current_gsm_number = 'design_sim_number-' . $onePagecheckoutData['totalvirtualproduct'];
                                        $current_simcard_number = 'simcard_number-' . $onePagecheckoutData['totalvirtualproduct'];
                                        $first_name_owner = 'holder_name-' . $onePagecheckoutData['totalvirtualproduct'];
                                        $last_name_owner = 'holders_name-' . $onePagecheckoutData['totalvirtualproduct'];
                                        $are_you_the_owner_of_the_account = 'bill_in_name-' . $onePagecheckoutData['totalvirtualproduct'];
                                        $current_customer_number = 'network_customer_number-' . $onePagecheckoutData['totalvirtualproduct'];
                                        $add_to_existing_plan = 'is_teneuro_' . $itemId;
                                        $network_customer_number = 'network_customer_number-' . $onePagecheckoutData['totalvirtualproduct'];
									   }
									//set gsm number 
                                    if (isset($sim_data['design_sim_number'])) {
												$gsm_number = $sim_data['design_sim_number'];
                                        if ($gsm_number == 1) {
												   $dataArray[$orderId][$productId]['gsm_number'] = "KEEP";
                                        } else if ($gsm_number == 2) {
												  $dataArray[$orderId][$productId]['gsm_number'] = "NEW"; 
                                        } else {
												   $dataArray[$orderId][$productId]['gsm_number'] = "";
											   }
                                    } else {
												$dataArray[$orderId][$productId]['gsm_number'] = "";
											}
									
									// set current operator
                                    if (isset($sim_data['subscription_type'])) {
												$dataArray[$orderId][$productId]['current_operator'] = $sim_data['subscription_type'];
                                    } else {
												$dataArray[$orderId][$productId]['current_operator'] = "";
											}
									
									// set current gsm number
                                    if (isset($sim_data['design_te_existing_number'])) {
												$dataArray[$orderId][$productId]['current_gsm_number'] = $this->getgsm($sim_data['design_te_existing_number']);//
                                    } else {
												$dataArray[$orderId][$productId]['current_gsm_number'] = '';
											}
									
									// set sim card number 
                                    if (isset($sim_data['simcard_number'])) {    //simcard_number
											   $dataArray[$orderId][$productId]['current_sim_number'] = $sim_data['simcard_number'];//current_gsm_number-;
                                    } else {
											   $dataArray[$orderId][$productId]['current_sim_number'] = '';
											}								   
											
									   /* current_tarrif_plan */
									   $current_tarrif_plan = $this->getTariffPlan($item);
                                    if (!isset($current_tarrif_plan)) {
											$dataArray[$orderId][$productId]['current_tarrif_plan'] = ''; 
                                    } else {
											$dataArray[$orderId][$productId]['current_tarrif_plan'] = $current_tarrif_plan;
										}
										
                                    if (isset($sim_data['network_customer_number'])) {
											$dataArray[$orderId][$productId]['current_customer_number'] = $sim_data['network_customer_number'];
                                    } else {
											$dataArray[$orderId][$productId]['current_customer_number'] = '';
										}
										
									   //are_you_the_owner_of_the_account
                                    if (isset($sim_data['subscription_type']) && isset($sim_data['holders_name'])) {
                                        $dataArray[$orderId][$productId]['are_you_the_owner_of_the_account'] = $this->getcustomernumber($sim_data['subscription_type'], $sim_data['holders_name']);
                                    } else {
										   $dataArray[$orderId][$productId]['are_you_the_owner_of_the_account'] = "";
									   }
									   
                                    if (isset($sim_data['holders_name'])) {
										   $dataArray[$orderId][$productId]['first_name_owner'] = $sim_data['holders_name']; //last_name_owner
                                    } else {
										   $dataArray[$orderId][$productId]['first_name_owner'] = '';
									   }
									   
                                    if (isset($sim_data['holder_firstname'])) {
										   $dataArray[$orderId][$productId]['last_name_owner'] = $sim_data['holder_firstname']; //first_name_owner
                                    } else {
										   $dataArray[$orderId][$productId]['last_name_owner'] = '';
									   }
									   
                                    if (isset($onePagecheckoutData['cust_telephone'])) {
											$temp_number = str_replace(' ', '', $onePagecheckoutData['cust_telephone']);
											$telephone_no = str_replace('/', '', $temp_number);
                                    } else {
										   $telephone_no = '';
									   }
									   
									   $dataArray[$orderId][$productId]['telephone_number'] = $telephone_no; //telephone_number
                                    $dataArray[$orderId][$productId]['date_of_birth'] = isset($onePagecheckoutData['c_dob']) ? $onePagecheckoutData['c_dob'] : ''; //
									   $dataArray[$orderId][$productId]['profession'] = ''; //profession
                                    $nation = isset($onePagecheckoutData['nationality']) ? $onePagecheckoutData['nationality'] : '';
                                    $residence_number = isset($onePagecheckoutData['residence_number']) ? $onePagecheckoutData['residence_number'] : '';
                                    $passport_number = isset($onePagecheckoutData['passport_number']) ? $onePagecheckoutData['passport_number'] : '';
                                    $dataArray[$orderId][$productId]['nationality'] = ''; //nationality is blank in 1802 NID
                                    /* 1802 NID Project */
                                    $id_card_number  = '';
                                    $permit_number = '';
                                    if (isset($onePagecheckoutData['id_number'])) {
                                        $id_num = $onePagecheckoutData['id_number'];
                                        $id_first_Char = substr($id_num, 0, 1);
                                        if (ctype_alpha($id_first_Char)) {
                                           $permit_number = $id_num;
                                        } else {
                                            $id_card_number = $id_num;
                                        }
                                    }
                                    /* 1802 NID Project */
									$dataArray[$orderId][$productId]['Belgian_ID_document'] = 'yes';
                                    $dataArray[$orderId][$productId]['identity_card_number'] = $id_card_number; //identity_card_number
									   $dataArray[$orderId][$productId]['residence_permit_number'] = $permit_number;
									   $dataArray[$orderId][$productId]['passport_number'] = $passport_number;
									   $other_add = isset($onePagecheckoutData['c_delivery_address']) ? $onePagecheckoutData['c_delivery_address'] : '';
										
                                    if (($other_add == 2) && ($subsidized_prod == 'Yes') && ($customerType != 4)) {
										   $dataArray[$orderId][$productId]['my_domicile_address_is_different'] = '1'; //my_domicile_address_is_different 
                                        $zipCode = isset($onePagecheckoutData['s_postcode_city']) ? $onePagecheckoutData['s_postcode_city'] : '';
                                        $cityCode = isset($onePagecheckoutData['s_city']) ? $onePagecheckoutData['s_city'] : '';
                                        $dataArray[$orderId][$productId]['domicile_zipcity'] = $zipCode . ' ' . strtoupper($cityCode); //delivery_zipcity
                                        $dataArray[$orderId][$productId]['domicile_street'] = isset($onePagecheckoutData['s_street']) ? $onePagecheckoutData['s_street'] : ''; //delivery_street
                                        $dataArray[$orderId][$productId]['domicile_house_number'] = isset($onePagecheckoutData['s_number']) ? $onePagecheckoutData['s_number'] : ''; //delivery_house_number
                                        $dataArray[$orderId][$productId]['domicile_bus'] = isset($onePagecheckoutData['s_box']) ? $onePagecheckoutData['s_box'] : ''; //delivery_bus
                                    } else {
										   $dataArray[$orderId][$productId]['my_domicile_address_is_different'] = '0'; //my_domicile_address_is_different
										   $dataArray[$orderId][$productId]['domicile_zipcity'] = ''; //delivery_zipcity
										   $dataArray[$orderId][$productId]['domicile_street'] = ''; //delivery_street 
                                        $dataArray[$orderId][$productId]['domicile_house_number'] = ''; //delivery_house_number
										   $dataArray[$orderId][$productId]['domicile_bus'] = ''; //delivery_bus
									   }
									   
                                    $dataArray[$orderId][$productId]['identity_card_number_other'] = isset($onePagecheckoutData['residence_number']) ? $onePagecheckoutData['residence_number'] : ''; //residence_number
                                    if (isset($catalogue) && ($catalogue == 'IEW')) {
                                        $dataArray[$orderId][$productId]['add_to_existing_plan'] = isset($onePagecheckoutData['is_teneuro_' . $quoteItemId]) ? ucfirst($onePagecheckoutData['is_teneuro_' . $quoteItemId]) : '';
                                    } else {
                                        $dataArray[$orderId][$productId]['add_to_existing_plan'] = isset($onePagecheckoutData['ex_invoice']) ? ucfirst($onePagecheckoutData['ex_invoice']) : '';
                                    }
									   
									   $plan = $dataArray[$orderId][$productId]['add_to_existing_plan'];
										
                                    if ($plan == 'Yes') {
                                        if (isset($catalogue) && ($catalogue == 'IEW')) {
                                            $dataArray[$orderId][$productId]['customer_number'] = isset($onePagecheckoutData['iew_telephone_' . $quoteItemId]) ? $onePagecheckoutData['iew_telephone_' . $quoteItemId] : '';//customer_number
                                        } else {
                                            $dataArray[$orderId][$productId]['customer_number'] = isset($onePagecheckoutData['cu_ex_invoice_cust_number']) ? $onePagecheckoutData['cu_ex_invoice_cust_number'] : '';//customer_number
											}
                                    } else {
											$dataArray[$orderId][$productId]['customer_number'] = '';
										}
										
                                    if ($order->getAccountNumber()) {
											$dataArray[$orderId][$productId]['method_of_payment'] = 'domiciliary';
										} else {
											$dataArray[$orderId][$productId]['method_of_payment'] = 'transfer';
										}

									   $dataArray[$orderId][$productId]['bank_account_number'] = $order->getAccountNumber(); //account_no//
                                    $dataArray[$orderId][$productId]['receive_invoice_electronically'] = isset($onePagecheckoutData['bill_in_name-']) ? $onePagecheckoutData['bill_in_name-'] : ''; //receive_invoice_electronically;
                                    $profile_dropdown = isset($onePagecheckoutData['tx_profile_dropdown']) ? $onePagecheckoutData['tx_profile_dropdown'] : '';
									   
                                    if ($profile_dropdown != '') {
                                        $cmpType = isset($onePagecheckoutData['legal_status']) ? $onePagecheckoutData['legal_status'] : '';
                                    } else {
											$cmpType = '';   
									   }
									   
									   $dataArray[$orderId][$productId]['company_type'] = $cmpType; //company_type
                                    $dataArray[$orderId][$productId]['company_name'] = isset($onePagecheckoutData['company_name']) ? $onePagecheckoutData['company_name'] : ''; //company_name
                                    $dataArray[$orderId][$productId]['vat_number'] = isset($onePagecheckoutData['vat_number']) ? $onePagecheckoutData['vat_number'] : '';
										$cmp_add = isset($onePagecheckoutData['cu_ex_invoice_bill_in_name']) ? $onePagecheckoutData['cu_ex_invoice_bill_in_name'] : '';
										
                                    if (($other_add == 2) && ($subsidized_prod == 'Yes') && ($customerType == 4)) {
                                        $zipCode = isset($onePagecheckoutData['s_postcode_city']) ? $onePagecheckoutData['s_postcode_city'] : '';
                                        $cityCode = isset($onePagecheckoutData['s_city']) ? $onePagecheckoutData['s_city'] : '';
										   
                                        $dataArray[$orderId][$productId]['company_zipcity'] = $zipCode . ' ' . strtoupper($cityCode);
                                        $dataArray[$orderId][$productId]['company_street'] = isset($onePagecheckoutData['s_street']) ? $onePagecheckoutData['s_street'] : '';
                                        $dataArray[$orderId][$productId]['company_number'] = isset($onePagecheckoutData['s_number']) ? $onePagecheckoutData['s_number'] : '';
                                        $dataArray[$orderId][$productId]['company_bus'] = isset($onePagecheckoutData['s_box']) ? $onePagecheckoutData['s_box'] : '';
										   $dataArray[$orderId][$productId]['company_country'] = 'Belgium';
                                    } else {
										   $dataArray[$orderId][$productId]['company_zipcity'] = '';
										   $dataArray[$orderId][$productId]['company_street'] = '';
										   $dataArray[$orderId][$productId]['company_number'] = '';
										   $dataArray[$orderId][$productId]['company_bus'] = '';
										   $dataArray[$orderId][$productId]['company_country'] = '';
										}
									   
									   /* enter_vat_number */
                                    if (!empty($dataArray[$orderId][$productId]['vat_number'])) {
										   $dataArray[$orderId][$productId]['enter_vat_number'] = '1';
                                    } else {
										   $dataArray[$orderId][$productId]['enter_vat_number'] = '0';
									   }
									
									   $productType = $this->getProductTypes($productId);
									   
                                    if ($productType == 'tablet_connected') {
                                        $dataArray[$orderId][$productId]['already_iew'] = isset($onePagecheckoutData['design_te_existing_number-']) ? $onePagecheckoutData['design_te_existing_number-'] : '';
                                        if (isset($design_te_existing_number)) {
                                            $dataArray[$orderId][$productId]['iew_number'] = isset($dataArray[$orderId][$productId]['current_sim_number']) ? $dataArray[$orderId][$productId]['current_sim_number'] : '';//current_gsm_number-;
                                        } else {
											   $dataArray[$orderId][$productId]['iew_number'] = '';
										   }
											
                                    } else {
											$dataArray[$orderId][$productId]['already_iew'] = '';
											$dataArray[$orderId][$productId]['iew_number'] = '';									
										}
									   
										$dataArray[$orderId][$productId]['already_microsim_card'] = ''; // as per existing live site it's not captured
										$dataArray[$orderId][$productId]['microsim_card_number'] = '';  // as per existing live site it's not captured
									   /* delivery company */
                                    $dataArray[$orderId][$productId]['deliver_somewhere_else'] = isset($onePagecheckoutData['c_delivery_address']) ? $onePagecheckoutData['c_delivery_address'] : ''; //delivery_zipcity;
									   $dataArray[$orderId][$productId]['delivery_address_type'] = '';
									   $dataArray[$orderId][$productId]['delivery_company_name'] = '';
                                    $deliver_attention_customer_name = $shippingAddress['firstname'];

                                    if (isset($deliver_attention_customer_name)) {
										   $dataArray[$orderId][$productId]['delivery_attention_off'] = $deliver_attention_customer_name;
                                    } else {
										   $dataArray[$orderId][$productId]['delivery_attention_off'] = "";
									   }
									   
                                    $zipCode1 = isset($onePagecheckoutData['s_postcode_city']) ? $onePagecheckoutData['s_postcode_city'] : '';
                                    $cityCode1 = isset($onePagecheckoutData['s_city']) ? $onePagecheckoutData['s_city'] : '';
                                    $dataArray[$orderId][$productId]['delivery_zipcity'] = $zipCode1 . ' ' . strtoupper($cityCode1); //delivery_zipcity
                                    $dataArray[$orderId][$productId]['delivery_street'] = isset($onePagecheckoutData['s_street']) ? $onePagecheckoutData['s_street'] : ''; //delivery_street
                                    $dataArray[$orderId][$productId]['delivery_house_number'] = isset($onePagecheckoutData['s_number']) ? $onePagecheckoutData['s_number'] : ''; //delivery_house_number
                                    $dataArray[$orderId][$productId]['delivery_bus'] = isset($onePagecheckoutData['s_box']) ? $onePagecheckoutData['s_box'] : ''; //delivery_bus
                                    $dataArray[$orderId][$productId]['internet_activation'] = ''; //internet_activation
									   $dataArray[$orderId][$productId]['shipping_total'] = $order->getBaseGrandTotal();
									   $dataArray[$orderId][$productId]['opc_brand'] = ''; //opc_brand
									   $dataArray[$orderId][$productId]['opc_form_id'] = $quoteid; //session url
									   $dataArray[$orderId][$productId]['bpost_data'] = $this->getBpostData($shippingAddress, $postalLocation, $postalMethod);

                                    if (isset($onePagecheckoutData['c_delivery_address'])) {
											if ($onePagecheckoutData['c_delivery_address'] == 3) {
                                            $dataArray[$orderId][$productId]['delivery_method'] = 'CLICK_COLLECT_BPACK247';
											} else if ($onePagecheckoutData['c_delivery_address'] == 2) {
                                            $dataArray[$orderId][$productId]['delivery_method'] = 'OTHER';
											} else {
                                            $dataArray[$orderId][$productId]['delivery_method'] = 'CURRENT';
											}
                                    } else {
										   $dataArray[$orderId][$productId]['delivery_method'] = '';
									   }
																		
                                    $dataArray[$orderId][$productId]['customer_scoring'] = isset($onePagecheckoutData['scoringResponse']) ? $onePagecheckoutData['scoringResponse'] : '';
									   $nintendo_loyality = $this->getNintendoLoyality($item);
									   
                                    if ($subsidized_prod == 'Yes') {
											$dataArray[$orderId][$productId]['check_nationality'] = "Yes";
                                    } else {
											$dataArray[$orderId][$productId]['check_nationality'] = '';
									   }
									
                                    if ($subsidized_prod == 'Yes') {
                                        if (!isset($dataArray[$orderId][$productId]['identity_card_number'])) {
                                            if (!isset($dataArray[$orderId][$productId]['residence_permit_number'])) {
												   $dataArray[$orderId][$productId]['eid_or_rpid'] = '';
                                            } else {
												   $dataArray[$orderId][$productId]['eid_or_rpid'] = $dataArray[$orderId][$productId]['residence_permit_number'];
											   }
                                        } else {
											   $dataArray[$orderId][$productId]['eid_or_rpid'] = $dataArray[$orderId][$productId]['identity_card_number'];
										   }  
                                    } else {
											$dataArray[$orderId][$productId]['eid_or_rpid'] = '';
									   }
									   
									   $scoring_res = $this->scoringResponse($quoteid);
									   $dataArray[$orderId][$productId]['scoring_decline_reason'] = $scoring_res;
									   $dataArray[$orderId][$productId]['pdd_legeal_selection'] = ''; //pdd_legeal_selection
                                    $dataArray[$orderId][$productId]['place_of_birth'] = isset($onePagecheckoutData['cust_birthplace']) ? $onePagecheckoutData['cust_birthplace'] : ''; //Place of birth
                                    $dataArray[$orderId][$productId]['ogone_transaction_id'] = $this->ogoneData($order->getId());
                                    $nintendo_phone_model = $this->getNintendoModel($item, 'nint');
									   
                                    if (!isset($nintendo_phone_model)) {
											$dataArray[$orderId][$productId]['nintendo_phone'] = '';
                                    } else {
											$dataArray[$orderId][$productId]['nintendo_phone'] = $nintendo_phone_model;
										}
										
                                    $dataArray[$orderId][$productId]['nintendo_loyalty'] = $nintendo_loyality;
                                    $dataArray[$orderId][$productId]['webservice_fraud'] = '';
									   $dataArray[$orderId][$productId]['webservice_fraud_2'] = '';
									   $dataArray[$orderId][$productId]['order_id'] = $order->getIncrementId();
                                } catch (\Exception $e) {
                                    if (isset($log_mode) && $log_mode == 1) {
                                        $this->_logHelper->logCreate('/var/log/FraudReport_track.log', 'There was a Exception for single qty product:' . $e->getMessage());
									}
								}  
                            }// end else 
							
                            if (isset($log_mode) && $log_mode == 1) {
                                $this->_logHelper->logCreate('/var/log/FraudReport_track.log', 'fraud report for allowed LOB item for order at :' . date('YmdHis'));
										 }                                                            
									}
                    endforeach;//end foreach loop of order level
					//open file to write array data
                    
                    if ($fp = fopen($file, 'w')) {
						foreach ($dataArray as $productArray) {
                            foreach ($productArray as $writer) {
                                fputs($fp, implode("~", $writer) . "\n");
								}	
                        }
						if (isset($log_mode) && $log_mode == 1) {
                            $this->_logHelper->logCreate('/var/log/FraudReport_track.log', 'array data written succesfully in file:'. $filename);
                        }
                    } else {
						if (isset($log_mode) && $log_mode == 1) {
                            $this->_logHelper->logCreate('/var/log/FraudReport_track.log', 'Could not open file for output'. $filename);
                        }
                        return FALSE;
                    }
					
								fclose($fp); 
								chmod($file, 0777);     
                }// Order creation foreach end		
                
            } catch (\Exception $e) {
                if (isset($log_mode) && $log_mode == 1) {
                    $this->_logHelper->logCreate('/var/log/FraudReport_track.log', 'Order creation exception:' . $e->getMessage());
			   }
				}
                        
        } else {
			if ($fp = fopen($file, 'w')) {
				$txt = "";// empty file for no order
				fwrite($fp, $txt);
				if (isset($log_mode) && $log_mode == 1) {
					$this->_logHelper->logCreate('/var/log/FraudReport_track.log', 'No orders in last hour');
       }
            } else {
				if (isset($log_mode) && $log_mode == 1) {
                    $this->_logHelper->logCreate('/var/log/FraudReport_track.log', 'Could not open file for no order');
                }
            }
			fclose($fp);
                        chmod($file, 0777);
		}
                        
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($file));
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($file));
		readfile($file);
            /* Push code to remote server */
            $key_path = $baseDir . '/pub/media/upload';
            $WPAfile = $file;
            $info = $this->ftpDetails();
            //Getting Server Details
            $username = $info['Froudreport_User'];
            $password = $info['Froudreport_Password'];
            $pwd = $info['Froudreport_ppk'];
            $host = $info['Froudreport_Host'];
            $reportPath = $info['Froudreport_Path'];
        //verify ftp access credentials

            if (($username != "") && ($pwd != "") && ($host != "")) {
                $sftp = new SFTP($host);
                $key = new RSA();
            $key->loadKey(file_get_contents($key_path . '/' . $pwd, true));
            try {
                if ($sftp->login($username, $key)) {
                    /* loging check */
                
					//filesize based file transfer
					if(filesize($WPAfile) > 0 ){
						$sftp->put($reportPath . '/' . $filename, $WPAfile, NET_SFTP_LOCAL_FILE2);
						if (isset($log_mode) && $log_mode == 1) {
							$objectManager->get('Orange\Upload\Helper\Data')->logCreate('/var/log/FraudReport_track.log', 'SFTP done successfully for file' . $filename);
                    }
						
						$increment_key = array_keys($orderFlagArray);
						/* update sales_order table with flag value 1 */
						foreach($orderCollection as $order){
							if(in_array($order->getIncrementId(),$increment_key)){
								$order->setFraudCapture(1);
								$order->save();
                }
						}						
						if (isset($log_mode) && $log_mode == 1) {
							$objectManager->get('Orange\Upload\Helper\Data')->logCreate('/var/log/FraudReport_track.log', 'update sales_order table with flag value 1');
						}

					}else{						
						$increment_key = array_keys($orderFlagArray);
						/* update sales_order table with flag value 1 */
						foreach($orderCollection as $order){
							if(in_array($order->getIncrementId(),$increment_key)){
								$order->setFraudCapture(1);
								$order->save();
                }
            }   
						if (isset($log_mode) && $log_mode == 1) {
							$objectManager->get('Orange\Upload\Helper\Data')->logCreate('/var/log/FraudReport_track.log', 'update sales_order table with flag value 1, for other than subsidy, IEW and postpaid products.');
						}
						
						if (isset($log_mode) && $log_mode == 1) {
							$objectManager->get('Orange\Upload\Helper\Data')->logCreate('/var/log/FraudReport_track.log', 'No order with allowed lob with empty file' . $filename);
						}
					}
                    
                }else{
					if (isset($log_mode) && $log_mode == 1) {
                        $objectManager->get('Orange\Upload\Helper\Data')->logCreate('/var/log/FraudReport_track.log', 'Error in SFTP login');
                    }
				}
            } catch (\Exception $e) {
                if (isset($log_mode) && $log_mode == 1) {
                    $this->_logHelper->logCreate('/var/log/FraudReport_track.log', 'SFTP Login Exception:' . $e->getMessage());
                }
            }
        }
		
        if (isset($log_mode) && $log_mode == 1) {
            $this->_logHelper->logCreate('/var/log/FraudReport_track.log', 'fraud report script done at:' . date('YmdHis'));
        }
		exit;
   }   

    private function getCategory($categoryId)
    {
        $category = $this->_categoryFactory->create();
        $category->load($categoryId);
        return $category->getName();
    }
    
    public function getChildrenProduct($product)
    {
        $pId = $product->getId();
        //$objectManager = ObjectManager::getInstance();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $typeInstance = $product->getTypeInstance();
        $requiredChildrenIds = $typeInstance->getChildrenIds($pId, true);
        $productIds = array();
        foreach ($requiredChildrenIds as $ids) {
            $productIds[] = $ids;
        }
        $productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
        $collection = $productCollection->create()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('entity_id', array('in' => $productIds))
            ->addAttributeToFilter('type_id', 'simple')
            ->load();
        return $collection;
    }

    public function getVirtualChildrenProduct(\Magento\Catalog\Model\Product $product)
    {
        $pId = $product->getId();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $typeInstance = $product->getTypeInstance();
        $requiredChildrenIds = $typeInstance->getChildrenIds($pId, true);
        $productIds = array();
        foreach ($requiredChildrenIds as $ids) {
            $productIds[] = $ids;
        }
        $productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
        $collection = $productCollection->create()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('entity_id', array('in' => $productIds))
            ->addAttributeToFilter('type_id', 'virtual')
            ->load();
        return $collection;
    }

    private function getProductCollection($sku)
    {
        /* $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*');        
        $collection->addAttributeToFilter('sku', $sku);        
        $collection->load();
        return $collection;*/
         $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addAttributeToFilter('sku', $sku);      
        $collection->load();
        return $collection;
    }

   
    public function ftpDetails()
    {
        $Credentials = array();
        $Credentials['Froudreport_Host'] = $this->scopeConfig->getValue('fraudreport_ftpdetails/fraudreport__configuration/server_detail', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $Credentials['Froudreport_User'] = $this->scopeConfig->getValue('fraudreport_ftpdetails/fraudreport__configuration/server_user_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $Credentials['Froudreport_Password'] = $this->scopeConfig->getValue('fraudreport_ftpdetails/fraudreport__configuration/server_pwd', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $Credentials['Froudreport_ppk'] = $this->scopeConfig->getValue('fraudreport_ftpdetails/fraudreport__configuration/server_ppkey', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $Credentials['Froudreport_Path'] = $this->scopeConfig->getValue('fraudreport_ftpdetails/fraudreport__configuration/server_directorypath', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $Credentials;
    }
    
    public function productData($orderItem, $dat)
    {
        $productType = $orderItem->getProductType();
        $productName = $orderItem->getName();
        if ($productType == 'bundle') {
            if (strpos($productName, '+') !== false) {
                if ($dat != 'nint') {
                    $name = explode("+", $productName);
                    return $name['1'];
                } else {
                    $name = explode("+", $productName);
                    return $name['0'];
                }
            }
        } else {
            if ($dat == 'prod') {
                return $productName;
            } else {
                return '';
            }
        }
    }
    
    public function getNintendoModel($orderItem, $dat)
    {
        $productType = $orderItem->getProductType();
		$productId = $orderItem->getProductId();
		$item = $this->_productRepository->getById($productId);
		$family = "";
        if ($productType == 'bundle' && $dat == 'nint') {
            if (!empty($item->getHandsetFamily())) {
				$family = $item->getHandsetFamily(); 
            } else {
				$family = "";
			}
			return $family;
        } else {
			return $family;
		}
    }
    
    public function getNintendoLoyality($orderItem)
    {
        $productType = $orderItem->getProductType();
        $productId = $orderItem->getProductId();
        
        if ($productType == 'bundle') {
            $nintendo_product = $this->_productRepository->getById($productId);
            $typeInstance = $nintendo_product->getTypeInstance(true);
            $requiredChildrenIds = $typeInstance->getChildrenIds($productId, false);
            $subs_duration = "";
            foreach ($requiredChildrenIds as $bundle_item_ids => $item_ids) {
                foreach ($item_ids as $item_id) {
                    $bundle_item = $this->_productRepository->getById($item_id);
                    if ($bundle_item->getTypeId() == 'virtual') {
                        $subs_duration = $bundle_item->getSubsidyDuration();
                        if (!empty($subs_duration)) {
                            $subs_duration = $subs_duration . ' months';
                        } else {
                            $subs_duration = '24 months';
                        }
                        return $subs_duration;
                    }
                }
            }
        
        } else {
            return "";
        }
        
    }
    
    public function getNationalityData($nation, $residence_number, $passport_number)
    {       
        if ($passport_number != '' || $residence_number != '') {
            return "other";           
        } else if ($nation == 'belgian') {
            return "belgian";
        } else {
			return "";
		}
    }
    
    public function boxData($val, $qid)
    {
        $collect1 = "SELECT * FROM orange_abandonexport_items where quote_id='" . $qid . "'";
        $rest1 = $this->connectionEst()->fetchAll($collect1);
	   
        if (count($rest1)) {
            $fstep = $rest1[0]['stepsecond'];
            $dat = unserialize($fstep);
            if (isset($dat[$val]) && $dat[$val] != '') {
                return $dat[$val];
            } else {
                return '';
            }
        }

        return '';
    }
    
    public function ogoneData($oid)
    {
        $collect = "SELECT * FROM customweb_ogonecw_transaction WHERE order_id = '" . $oid . "'";
          $rest = $this->connectionEst()->fetchAll($collect);
          if (isset($rest[0])) {
          return $rest[0]['entity_id'];
          } 
        return '';
    }
    
    public function connectionEst()
    {
        $resource = $this->objectManagerInt()->get('Magento\Framework\App\ResourceConnection');
        return $resource->getConnection();
    }
    
    public function objectManagerInt()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        return $objectManager;
    }
    
    public function billingAndShipping($billing, $shipping)
    {
        return strcmp($billing, $shipping);
    }
    
    public function getBpostData($shippingAddress, $postalLocation, $postalMethod)
    {
	    $bpostPostalLocation = $postalLocation;
		$bpostMethod = $postalMethod;
        if ($bpostPostalLocation && $bpostMethod) {
            $data = array();
            $street = ($shippingAddress['street']) ?: '';
			
            $st = preg_replace('/\s+/', '', $street);
            $data ['street'] = preg_replace('~\x{00a0}~', '', $st);
            $data ['city'] = ($shippingAddress['city']) ?: '';
            $data ['postcode'] = ($shippingAddress['postcode']) ?: '';
            $data ['country_id'] = ($shippingAddress['country_id']) ?: '';
            $data ['lastname'] = ($shippingAddress['lastname']) ?: '';
            $data ['bpost_postal_location'] = ($shippingAddress['bpost_postal_location']) ?: '';
            $data ['bpost_method'] = ($shippingAddress['bpost_method']) ?: '';
            $bpost_data = '{' . serialize($data) . '}';
            return $bpost_data;
			
        }
        return 0;
    }
    
    
    public function opcData($qid)
    {
        $collect1 = "SELECT * FROM orange_abandonexport_items where quote_id='" . $qid . "'";
        $rest1 = $this->connectionEst()->fetchAll($collect1);
	   
        if (count($rest1)) {
			
            $fstep = $rest1[0]['stepsecond'];
			$step = $rest1[0]['stepfirst'];
			
            if (!empty($fstep)) {
				$dat = unserialize($fstep);
				if (isset($dat) && $dat != '') {
					return $dat;
				} else {
					return '';
				}
            } else if (!empty($step)) {
				$sdat = unserialize($step);
				if (isset($sdat) && $sdat != '') {
					return $sdat;
				} else {
					return '';
				}
			}
            
        }

        return '';
    }
    
    public function getProductLOB($type, $attrib_set)
    {
        $sku = $type->getSku();
        $productType = $type->getProductType();
        $typedata = $attrib_set;
		//$typedata = $this->getSKUProduct($sku);
        if (($productType == 'virtual') && ($typedata == 'Postpaid')) {
            $lob = 'Postpaid';
        } else if (($productType == 'virtual') && ($typedata == 'Prepaid')) {
            $lob = 'Prepaid';
        } else if (($productType == 'simple') && ($typedata == 'handset')) {
            $lob = 'Hardware';
        } else if (($productType == 'simple') && ($typedata == 'Accessories')) {
            $lob = 'Accessories';
        } else if (($productType == 'virtual') && ($typedata == 'IEW')) {
            $lob = 'IEW';
        } else {
            $lob = 'Postpaid';
        }
        return $lob;
    }
    
    public function getSKUProduct($sku)
    {
        $col1 = "SELECT * FROM catalog_product_entity WHERE sku ='" . $sku . "'";
        $ret1 = $this->connectionEst()->fetchAll($col1);
        if (count($ret1)) {
            $attr_id = $ret1[0]['attribute_set_id'];
            $col2 = "SELECT * FROM eav_attribute_set WHERE attribute_set_id = '" . $attr_id . "'";
            $ret2 = $this->connectionEst()->fetchAll($col2);
            return $ret2[0]['attribute_set_name'];
        }
    }
    
    private function getTariffPlan($orderItem)
    {
        $tariff = '';
        $productId = $orderItem->getProductId();
        
        $item = $this->_productRepository->getById($productId);
        $attributeSetRepository = $this->_attributeSet->get($item->getAttributeSetId());
        $attribute_name = $attributeSetRepository->getAttributeSetName();
        
        $type = $orderItem->getProductType();
        
        if ($attribute_name == 'Postpaid') {
            $tariff = 'POSTPAID';
        } else if ($attribute_name == 'Prepaid' || $attribute_name == 'Simcard') {
                $tariff = 'PREPAID';
        } else if ($type == 'bundle') {
            
            $nintendo_product = $this->_productRepository->getById($productId);
            $typeInstance = $nintendo_product->getTypeInstance(true);
            $requiredChildrenIds = $typeInstance->getChildrenIds($productId, false);
            foreach ($requiredChildrenIds as $bundle_item_ids => $item_ids) {
                foreach ($item_ids as $item_id) {
                    $bundle_item = $this->_productRepository->getById($item_id);
                    $attributeSetRepo = $this->_attributeSet->get($bundle_item->getAttributeSetId());
                    $nin_attribute_name = $attributeSetRepo->getAttributeSetName();
                    if ($bundle_item->getTypeId() == 'virtual' && ($nin_attribute_name = 'Prepaid' || $nin_attribute_name = 'Simcard')) {
                        $tariff = 'PREPAID';
                    } else if ($bundle_item->getTypeId() == 'virtual' && ($nin_attribute_name = 'Postpaid')) {
                        $tariff = 'POSTPAID';
                    }
                }
            } 
        }
        return $tariff;
    }
	
    public function getProductBrand($productId)
    {
        $item = $this->_productRepository->getById($productId);
		
		if (!empty($item->getBrand())) {
            $brandName = $item->getResource()->getAttribute('brand')->getFrontend()->getValue($item);
        } else {
            $brandName = "";
        }
		return $brandName;
	}
	
    public function getProductManufacturer($productId)
    {
        $item = $this->_productRepository->getById($productId);
		
        if (!empty($item->getHandsetFamily())) {
            $family = $item->getHandsetFamily(); 
        } else {
            $family = "";
        }
		return $family;
	}
	
    public function getProductTypes($productId)
    {
        $item = $this->_productRepository->getById($productId);
		$productType = $item->getResource()->getAttribute('product_icon_image')->getFrontend()->getValue($item);
        if ($productType != "") {
			return $productType;
        } else {
			return '';
		}
	}
	
    public function getDesignSimNumber($qid, $quoteItemId)
    {
		
        $collect1 = "SELECT design_sim_number, subscription_type ,simcard_number , holders_name, holder_firstname ,network_customer_number, design_te_existing_number FROM orange_multiple_postpaid where quote_id='" . $qid . "'AND item_id ='" . $quoteItemId . "'";
       
		$sim_number_details = $this->connectionEst()->fetchAll($collect1);
		
        if (!empty($sim_number_details)) {
			return $sim_number_details[0];
        } else {
			return '';
		}
        
	}
	
    public function dropDownValue($drop = '')
    {
        if ($drop == 'Selecteer') {
            $drop = '';
        }
        return $drop;
    }
	
    public function scoringResponse($quote_id)
    {
		$collect1 = "SELECT score_data from quote where entity_id='" . $quote_id . "'";       
		$score_data = $this->connectionEst()->fetchAll($collect1);
		if (count($score_data)) {
            $score = $score_data[0]['score_data'];
            if (!empty($score)) {
				$dat = unserialize($score);				
                $error_code = isset($dat['errorCode']) ? $dat['errorCode'] : '';
                $error_reason = isset($dat['scoringReason']) ? $dat['scoringReason'] : '';
                $result = $error_code . '::' . $error_reason;
				if (isset($result) && $result != '') {
					return $result;
				} else {
					return '';
				}
			}           
        }
        return '';
		
	}
	
    public function getSubsidyProduct($productId)
    {
        $item = $this->_productRepository->getById($productId);
		$subsidyProd = $item->getResource()->getAttribute('custom_bundle')->getFrontend()->getValue($item);
        if ($subsidyProd != "") {
			return $subsidyProd;
        } else {
			return '';
		}
	}
        
        
    public function productPlan($orderItem)
    {
            $productType = $orderItem->getProductType();
            $productId = $orderItem->getProductId();

            if ($productType == 'bundle') {
                $nintendo_product = $this->_productRepository->getById($productId);
                $typeInstance = $nintendo_product->getTypeInstance(true);
                $requiredChildrenIds = $typeInstance->getChildrenIds($productId, false);
                $plan_name = "";
            foreach ($requiredChildrenIds as $bundle_item_ids => $item_ids) {
                foreach ($item_ids as $item_id) {
                        $bundle_item = $this->_productRepository->getById($item_id);
						$attributeSetRepository = $this->_attributeSet->get($bundle_item->getAttributeSetId());
						$attribute_name = $attributeSetRepository->getAttributeSetName();
                    if (($bundle_item->getTypeId() == 'virtual') && ($attribute_name == 'Postpaid' || $attribute_name == 'Prepaid')) {
							$prod_name = $bundle_item->getName();
							$tp = trim(strtolower($prod_name));
                        $tps = explode(' ', $tp);
                        if (is_array($tps) && in_array("smartphone", $tps)) {
                            if (isset($tps[0])) {
                                    $plan_name = ucfirst($tps[0]);
                                }                                    
                        } else {
                                $plan_name = $prod_name;
                            }							
                            //$plan_name = $bundle_item->getResource()->getAttribute('product_icon_image')->getFrontend()->getValue($bundle_item);
                            //$plan_name = $bundle_item->getName();                        
                            return $plan_name;
                    } else if ($bundle_item->getTypeId() == 'virtual') {
                        $plan_name = $bundle_item->getName();
							 return $plan_name;
						}
                    }
                }        
        } else {
                return $orderItem->getName();
            }

        }
		
    public function getsubsidyprice($orderItemm, $storeId)
    {
			$productChildprice = '';
			$pid = $orderItemm->getProductId();
			$sku = $orderItemm->getSku();
			//$rtData = $this->getSKUProduct($sku);
			$productType = $orderItemm->getProductType();
			if ($productType == 'bundle') {
				//$sku = $this->getmodel($orderItemm);
				//$virtualProductId =  $this->getSKUProductId($sku);
				//$val = $this->getNintendoAttrdValue($pid,$storeId,$virtualProductId);
				$childItems = $orderItemm->getChildrenItems();
				
				$productChildsku = '';
				foreach ($childItems as $childs) {
					$vproduct = $childs->getProduct();
					if ($vproduct->getTypeId() == "virtual") {
						$productChildprice = $vproduct->getSubscriptionAmount();
					}
				}
				return $productChildprice;
        } else {
				return '';
			}
		}
		
    public function getgsm($gsm)
    {
        $gsm = str_replace('/', '', $gsm);
			$gsm_value = '';
        if ($gsm != '') {
            $gsm_data1 = substr($gsm, 0, 4);
            $gsm_data2 = substr($gsm, 4);
            $gsm_value = $gsm_data1 . "/" . $gsm_data2;
			}
			return $gsm_value;
		}
	
    public function getcustomernumber($operator, $owner)
    {
			$value = '';
        if ($operator) {
            if ($owner) {
					$value = 'No';
            } else {
					$value = 'Yes';
				}
			}
			return $value;
		}
 
}
