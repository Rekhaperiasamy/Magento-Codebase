<?php
namespace Orange\Seo\Controller\Bireport;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Action\Context;
//use Magento\Framework\View\Result\PageFactory;
use phpseclib\Net\SFTP;
use phpseclib\Crypt\RSA;
define('NET_SFTP_LOCAL_FILEBIR', 1);
ini_set('memory_limit','1536M');

class Report extends \Magento\Framework\App\Action\Action {

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;
    protected $_orderCollectionFactory;
	protected  $baseDir;
	protected  $directory_list;
	protected $_timezoneInterface;

    public function __construct(
    Context $context,
	\Magento\Framework\App\Filesystem\DirectoryList $directory_list,
	\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
	\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
	\Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
    ) {

        $this->directory_list = $directory_list;
		$this->_productCollectionFactory = $productCollectionFactory;
        $this->_orderCollectionFactory = $orderCollectionFactory;
		$this->_timezoneInterface 	= $timezoneInterface;
		parent::__construct($context);
    }

    public function execute() {
		
		$scopeConfig = $this->objectManagerInt()->create('Magento\Framework\App\Config\ScopeConfigInterface');
		$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
		$discountSohoPrice = $scopeConfig->getValue('soho/soho_configuration/soho_discount', $storeScope);
		$bundleB2CPrice = $scopeConfig->getValue('soho/soho_configuration/amount_subsidy', $storeScope);
		$bundleSohoPrice = $scopeConfig->getValue('soho/soho_configuration/amount_subsidy_soho', $storeScope);
		$attributeSet = $this->objectManagerInt()->create('Magento\Eav\Api\AttributeSetRepositoryInterface');
        $reserve = array();
        $from = new \DateTime($this->getRequest()->getParam('date_from'));
        $to = new \DateTime($this->getRequest()->getParam('date_to'));
        $status = $this->getRequest()->getParam('status1');
		$owner = '';
		$scoring_decline = '';
        $current_sim_number = '';
		$product_model = '';
		$postPaidprice  = '';
		$sohosegment    = '';
		$ogoneTid ='';
		$add_existing_plan = '';
		$orderIds = array();
		if ($status == '') {
            $status = 'all';
        }
		if ($from == '') {
			$time = time();
			$to = date('Y-m-d H:i:s', $time);
			$lastTime = $time - 3600; // 60*60
			$from = date('Y-m-d H:i:s', $lastTime);
		}

        $diff = $to->diff($from)->format("%R%a days");
        $toDate = $to->format("Y-m-d 23:59:59");

		$fromDate = $from->format("Y-m-d 00:00:00");
        if ($diff == "+0" || $diff == "+0 days") {
            $fromDate = $from->format("Y-m-d 00:00:00");
        }
		
        $orderCollection = $this->_orderCollectionFactory->create()
		     //->addAttributeToFilter('increment_id', '100008194');
             //->addAttributeToFilter('created_at', array('from' => $fromDate, 'to' => $toDate));
			 ->addAttributeToFilter('bi_tracking_flag','0');
        if ($status != 'all') {
            $orderCollection->addAttributeToFilter('status', $status);
        }    
        $heading = $this->getHeaders();
		$filename = "C_WebOrders_" . date('Ymd') . ".csv";
        $outputFile = BP . "/common-header/" .$filename;
        $handle = fopen($outputFile, 'w+');
        fputcsv($handle, $heading);
		
		

        foreach ($orderCollection as $order) {
            $orderItems = $order->getAllVisibleItems();
			$orderIds[] = $order->getId();
			$allItems = $order->getAllItems();
			// #5893 - Change status from 'prossing' to 'paid' 
			if ($order->getStatus() == 'processing') {
			  $order_status = 'paid';
			}
			else {
			  $order_status = $order->getStatus();
			}

			$biTrackingFlag = $this->getBiTrackingFlag($order->getId());
			if ($biTrackingFlag == "1") {
				continue;
			}
			/*foreach($allItems as $allitem){
			$productall = $allitem->getProduct();		
			$attributeSetRepository = $attributeSet->get($productall->getAttributeSetId());
			$attributeSetName = $attributeSetRepository->getAttributeSetName();
			//Dont show shipping info for Prepaid,Postpaid,IEW and Simcard
			if($allitem->getProductType() == 'simple' && $attributeSetName!='Simcard') {
				$hasShippingInfo = true;
			} 
			}*/
            $k = 1;
			$customerType = $order->getCustomerGroupId();
            $billingAddress = $order->getBillingAddress();
            $shippingAddress = $order->getShippingAddress();
            $quoteid = $order->getQuoteId();
            foreach ($orderItems as $orderItem):
			// Checking Order Status is processing or complete or payment_review.
			if(strtolower($order->getStatus())=="processing" || strtolower($order->getStatus())=="complete" || strtolower($order->getStatus())=="payment_review" || strtolower($order->getStatus())=="pending_payment" || strtolower($order->getStatus())=="processing" || strtolower($order->getStatus())=="canceled"){
			$postpaidModel = $this->objectManagerInt()->create('Orange\Checkout\Model\Postpaid');
			$postpaidModelCollections = $postpaidModel->getCollection()->addFieldToFilter('quote_id',$quoteid)->addFieldToFilter('item_id',$orderItem->getQuoteItemId());
			$line_item = $k;
			$methodOfPaymentName = '';
			if($customerType == 4) {
				$originalPrice = $orderItem->getOriginalPrice() / (1+($discountSohoPrice/100));
				$productType = $orderItem->getProductType();
				if ($productType == 'bundle') {
					$bundleprice = $this->getsubsidyprice($orderItem,$order->getStoreId());
					$subscriptionAmount = $bundleprice / (1+($discountSohoPrice/100));
				}else{
					$subscriptionAmount = $orderItem->getSubscriptionAmount() / (1+($discountSohoPrice/100));	
				}
				
			} else {
				$originalPrice = $orderItem->getOriginalPrice();
				
				$productType = $orderItem->getProductType();
				if ($productType == 'bundle') {
					$bundleprice = $this->getsubsidyprice($orderItem,$order->getStoreId());
					$subscriptionAmount = $bundleprice;
				}else{
					$subscriptionAmount = $orderItem->getSubscriptionAmount();	
				}
			}
	 $bbox = $this->boxData('b_box',$quoteid);
	if ($bbox) {
		$bbox = "\t".$bbox."\t";
		}
			if($quoteid != ''){
			if ($postpaidModelCollections->count() > 0 ) {
			
			$transfertType = $this->boxData('transfer_type', $quoteid);
			if($order->getAccountNumber()) {
				$methodOfPaymentName = 'Domiciliation';
			} else {
				$methodOfPaymentName = 'Virement';
			}
		
			foreach($postpaidModelCollections as $postpaidCollection) {
				$line_item = $k;
				$created_time = $this->_timezoneInterface
									->date(new \DateTime($order->getCreatedAt()))
									->format('H:i:s');
				$created_date = $this->_timezoneInterface
									->date(new \DateTime($order->getCreatedAt()))
									->format('d/m/y');
                $updateTime = $this->_timezoneInterface
									->date(new \DateTime($order->getUpdatedAt()))
									->format('H:i:s');
				$updateDate = $this->_timezoneInterface
									->date(new \DateTime($order->getUpdatedAt()))
									->format('d/m/y');									
									
			$this->boxData('is_teneuro_'.$postpaidCollection->getItemId(), $quoteid);
if($billingAddress->getTelephone() != '9999999999'){ $tele = $billingAddress->getTelephone();}else{ $tele = '';}
if($this->boxData('scoringResponse', $quoteid) == 'ACC'){ $scoring_decline = '';}else{ $scoring_decline = $this->boxData('scoringResponse', $quoteid);}

     if($postpaidCollection->getSimcardNumber()){
		$current_sim_number = $this->getcurrentsim($postpaidCollection->getSimcardNumber(),$postpaidCollection->getDesignTeExistingNumberFinalValidation());
	 }else{
		 $current_sim_number = '';
	 }
     $product_model  =   $this->getmodel($orderItem);
	 $postPaidprice  = round($originalPrice,2);
	 $sohosegment     = $this->checkSOHO($order->getIncrementId());
	 $ogoneTid = $this->ogoneData($order->getId());
	 $add_existing_plan = $this->getExistingPlan($postpaidCollection,$quoteid);

	 $row = array();
                $row = [
                    $created_date, //created_date
                    $created_time, //created_time
					$updateDate, //update_date
					$updateTime, //update_time
                    $order->getIncrementId(), //uc_order_id
                    $order_status, //order_status
                    $order->getGrandTotal(), //order_total
                    $order->getData('total_qty_ordered'), //product_count
                    $order->getCustomerEmail(), //primary_email
                    $billingAddress->getFirstname(), //delivery_first_name
                    $billingAddress->getLastname(), //delivery_last_name
                    $order->getPayment()->getMethod(), //payment_method
                    $order->getRemoteIp(), //host
                    $order->getData('Active'), //sim_activated
                    ltrim($this->productData($orderItem, 'prod'), ' '), //Products
                    $product_model, //model
                    '1',//count($orderItems), //qty
                    round($subscriptionAmount,2), //cost
                    round($originalPrice,2), //price
                    $orderItem->getProductId(), //nid
                    $this->getProductLOB($orderItem), //catalogue					
                    $this->productData($orderItem, 'nint'), //current_common
                    $this->getStore($order->getStoreId()), //language
                    $this->getTitleDataValue($billingAddress->getPrefix()), //title
                    $billingAddress->getPostcode() . ' ' . strtoupper($billingAddress->getCity()), //zipcity
                    strtoupper($this->boxData('b_street', $quoteid)), //street
                    $this->boxData('b_number', $quoteid), //house_number
                    $bbox, //bus
                    ($this->boxData('offer_subscription', $quoteid)) ? 1 : 0,//optin
                    $this->getGsmNumber($postpaidCollection->getDesignSimNumber()), //gsm_number
                    $this->CurrentOperator($postpaidCollection->getSubscriptionType(),$postpaidCollection->getDesignTeExistingNumberFinalValidation()), //current_operator
                    $this->getgsm($postpaidCollection->getDesignTeExistingNumber()), //current_gsm_number
                    $current_sim_number, //current_sim_number
                    $this->getTariffPlan($postpaidCollection->getCurrentOperator(),$postpaidCollection->getDesignSimNumber(),$postpaidCollection->getDesignTeExistingNumberFinalValidation()), //current_tarrif_plan
                    $this->CurrentCustomerNumber($postpaidCollection->getNetworkCustomerNumber(),$postpaidCollection->getDesignTeExistingNumberFinalValidation()), //current_customer_number $billingAddress->getExInvoiceCustNumber()
                    $this->getcustomernumber($postpaidCollection->getCurrentOperator(),$postpaidCollection->getDesignTeExistingNumberFinalValidation(),$postpaidCollection->getBillInName(),$postpaidCollection->getDesignSimNumber()), // are_you_the_owner_of_the_account                   
                    $this->OwnerName($postpaidCollection->getHoldersName(),$postpaidCollection->getDesignTeExistingNumberFinalValidation()), //la st_name_owner
                    $this->FirstOwnerName($postpaidCollection->getHolderFirstname(),$postpaidCollection->getDesignTeExistingNumberFinalValidation()), //first_name_owner (Need to Verify from here)
                    $tele, //telephone_number                  
                    '', //nationality is blank 1802 NID
                    $this->boxData('cust_birthplace', $quoteid), //Place of birth
                    $this->getdateofbirth($quoteid), //date_of_birth
                    $this->registered_with_belgian($quoteid),//Belgian_ID_document
                    $this->get_identity_card_value($quoteid), //identity_card_number
                    $this->get_residence_value($quoteid), //residence_permit_number
                    $this->getpassport($quoteid), //passport_number  $shippingAddress->getPassportNumber()
                    $add_existing_plan, //add_to_existing_plan
                    $this->getcustomer_number($quoteid,$this->getExistingPlan($postpaidCollection,$quoteid)), //customer_number
					$methodOfPaymentName, //method_of_payment
                    $order->getAccountNumber(), //bank_account_number                    
                    $this->getvatnumber($quoteid,$orderItem), //enter_vat_number
                    $this->billingAndShipping($billingAddress, $shippingAddress,$this->getProductLOB($orderItem),$quoteid), //delivery_attention_off                   
                    $this->getdeliveryzipcity($shippingAddress,$quoteid,$this->getProductLOB($orderItem)), //delivery_zipcity
                    $this->getdeliverystreet($quoteid,$this->getProductLOB($orderItem)), //delivery_street
                    $this->getdeliveryhouse($quoteid,$this->getProductLOB($orderItem)),//delivery_house_number
                    $this->getdeliverybus($quoteid,$this->getProductLOB($orderItem)), //delivery_bus
                    '0', //shipping_total
                    '', //bpost_data
                    $this->deliverMethod($order->getShippingMethod(), $quoteid,$this->getProductLOB($orderItem)), //delivery_method
					$this->getinvoiceownerfname($orderItem,$quoteid), //invoice_owner_first_name
                    $this->getinvoiceownerlname($orderItem,$quoteid), //invoice_owner_last_name
                    $this->getinvoiceownerdob($orderItem,$quoteid), //invoice_owner_date_of_birth
                    $this->getinvoiceowner($orderItem,$quoteid), //invoice_owner
                    $this->dropDownValue($this->boxData('tx_profile_dropdown', $quoteid),$order->getStoreId(),$quoteid,$this->getProductLOB($orderItem)), //business_profile
                    $this->getCompanyName($quoteid), //business_company_name                    
                    $this->getCompanyVat($quoteid), //business_company_vat
                    $this->getCompanyLegal($quoteid), //business_company_legal
                    $this->customerscoring($quoteid,$order), //customer_scoring                    
                    $this->geteid_or_rpid($quoteid), //eid_or_rpid // empty beacuse of client command as "need to check internally"
                    '', //scoring_decline_reason //$this->customerdecline($orderItem,$quoteid)                  
                    $this->mobileclienttype($orderItem->getSku(),$orderItem->getProductType(),$product_model), //Mobile Internet Client Type
                    $this->jupiterLoyaltybonus($orderItem,$order->getStoreId()), //Subsidy advantage  
                    $this->jupiterClientType($postpaidCollection->getDesignTeExistingNumberFinalValidation(),$postpaidCollection->getDesignSimNumber()), //client type
                    $this->iewAdvantage($orderItem), //IEW Advantage
                    $this->getreduction($orderItem,$add_existing_plan), //Reduction on IEW
                    $this->getNintendoAttrValueFetch($orderItem), //Promo postpaid
                    $this->getpropack($postpaidCollection,$order->getIncrementId()), //Pro pack 
                    $this->proPackDetails($postpaidCollection->getProPacks(), 'Smartphone ProPack'), //Smartphone ProPack
                    $this->proPackDetails($postpaidCollection->getProPacks(), 'Reduction ProPack'), //Reduction ProPack
                    $this->proPackDetails($postpaidCollection->getProPacks(), 'Surf ProPack'), //Surf ProPack
                    $this->checkSOHO($order->getIncrementId()), //SOHO segment                   
                    $this->ogoneDataPostpaid($postPaidprice,$sohosegment,$bundleB2CPrice,$bundleSohoPrice,$ogoneTid,$order->getGrandTotal()), //Ogone transaction ID
                    '1', //General conditions.
                     $this->getCoupon($order), //coupon
					 $line_item,
					//$order->getData('StatusFlag'), //status_flag
					//date('d/m/Y', strtotime($order->getCreatedAt())), //event_dt
                    
                ];
				$k++;
           fputcsv($handle, $row);
			}
			} else {

				$created_time = $this->_timezoneInterface
									->date(new \DateTime($order->getCreatedAt()))
									->format('H:i:s');
				$created_date = $this->_timezoneInterface
									->date(new \DateTime($order->getCreatedAt()))
									->format('d/m/y');
				$updateTime = $this->_timezoneInterface
									->date(new \DateTime($order->getUpdatedAt()))
									->format('H:i:s');
				$updateDate = $this->_timezoneInterface
									->date(new \DateTime($order->getUpdatedAt()))
									->format('d/m/y');
				if($billingAddress->getTelephone() != '9999999999'){ $tele = $billingAddress->getTelephone();}else{ $tele = '';}
	  
			 	$row = [
                    $created_date, //created_date
                    $created_time, //created_time
					$updateDate, //update_date
					$updateTime, //update_time
                    $order->getIncrementId(), //uc_order_id
                    $order_status, //order_status
                    $order->getGrandTotal(), //order_total
                    $order->getData('total_qty_ordered'), //product_count
                    $order->getCustomerEmail(), //primary_email
                    $billingAddress->getFirstname(), //delivery_first_name
                    $billingAddress->getLastname(), //delivery_last_name
                    $order->getPayment()->getMethod(), //payment_method
                    $order->getRemoteIp(), //host
                    $order->getData('Active'), //sim_activated
                    ltrim($this->productData($orderItem, 'prod'),' '), //Products
                    $this->getmodel($orderItem), //model
                    $orderItem->getQtyOrdered(), //count($orderItems), //qty 
                    round($subscriptionAmount,2), //cost
                    round($originalPrice,2), //price
                    $orderItem->getProductId(), //nid
                    $this->getProductLOB($orderItem), //catalogue					
                    $this->productData($orderItem, 'nint'), //current_common
                    $this->getStore($order->getStoreId()), //language
                    $this->getTitleDataValue($billingAddress->getPrefix()), //title
                    $billingAddress->getPostcode() . ' ' . strtoupper($billingAddress->getCity()), //zipcity
                    strtoupper($this->boxData('b_street', $quoteid)), //street
                    $this->boxData('b_number', $quoteid), //house_number
                    $bbox, //bus
                    ($this->boxData('offer_subscription', $quoteid)) ? 1 : 0,//optin
                    '', //gsm_number
                    '', //current_operator
                    '', //current_gsm_number
                    '', //current_sim_number
                    '', //current_tarrif_plan
                    '', //current_customer_number 
                    '', // are_you_the_owner_of_the_account                   
                    '', //la st_name_owner
                    '', //first_name_owner (Need to Verify from here)
                    $tele, //telephone_number                  
                    '', //nationality // hard code beligium
                    $this->boxData('cust_birthplace', $quoteid), //Place of birth
                    $this->getdateofbirth($quoteid), //date_of_birth
                    $this->registered_with_belgian($quoteid),//Belgian_ID_document
                    '', //identity_card_number
                    '', //residence_permit_number
                    '', //passport_number  $shippingAddress->getPassportNumber()
                    '', //add_to_existing_plan
                    '',//customer_number
					'', //method_of_payment
                    '', //bank_account_number                    
                    $this->getvatnumber($quoteid,$orderItem), //enter_vat_number  
                    $this->billingAndShipping($billingAddress, $shippingAddress,$this->getProductLOB($orderItem),$quoteid), //delivery_attention_off                   
                    $this->getdeliveryzipcity($shippingAddress,$quoteid,$this->getProductLOB($orderItem)), //delivery_zipcity
                    $this->getdeliverystreet($quoteid,$this->getProductLOB($orderItem)), //delivery_street
                    $this->getdeliveryhouse($quoteid,$this->getProductLOB($orderItem)),//delivery_house_number
                    $this->getdeliverybus($quoteid,$this->getProductLOB($orderItem)), //delivery_bus
                    '0', //shipping_total
                    $this->getBpostData($shippingAddress, $quoteid), //bpost_data
                    $this->deliverMethod($order->getShippingMethod(), $quoteid,$this->getProductLOB($orderItem)), //delivery_method
					'', //invoice_owner_first_name
                    '', //invoice_owner_last_name
                    '', //invoice_owner_date_of_birth
                    '', //invoice_owner
                    $this->dropDownValue($this->boxData('tx_profile_dropdown', $quoteid),$order->getStoreId(),$quoteid,$this->getProductLOB($orderItem)), //business_profile
                    $this->getCompanyName($quoteid), //business_company_name                    
                    $this->getCompanyVat($quoteid), //business_company_vat
                    $this->getCompanyLegal($quoteid), //business_company_legal
                    '', //customer_scoring                    
                    '', //eid_or_rpid // empty beacuse of client command as "need to check internally"
                    '', //scoring_decline_reason // //We are not addding details in table                  
                    '', //Mobile Internet Client Type
                    '', //Subsidy advantage 
                    '', //client type
                    '', //IEW Advantage
                    '', //Reduction on IEW
                    '', //Promo postpaid
                    '', //Pro pack 
                    '', //Smartphone ProPack
                    '', //Reduction ProPack
                    '', //Surf ProPack
                    $this->checkSOHO($order->getIncrementId()), //SOHO segment                   
                    $this->ogoneData($order->getId()), //Ogone transaction ID
                    '1', //General conditions.
                     $this->getCoupon($order), //coupon
					 $line_item,
                    //$order->getData('StatusFlag'), //status_flag
					//date('d/m/Y', strtotime($order->getCreatedAt())), //event_dt
                ];
				$k++;
               // print_r($row);
            fputcsv($handle, $row);	
			}
 
		}
			}
            endforeach;
        }

		//die;
		//$directory_list = $this->objectManagerInt()->create('\Magento\Framework\App\Filesystem\DirectoryList');
		$baseDir = $this->directory_list->getRoot();
		/* Push code to remote server */
            $key_path = $baseDir . '/pub/media/upload';
            $WPAfile = $outputFile;
            $info = $this->ftpDetails();
        
            //Getting Server Details
          $username   = $info['Bireport_User'];
          $pwd        = $info['Bireport_ppk'];
          $host       = $info['Bireport_Host'];
          $reportPath = $info['Bireport_Path'];
		 
			//verify ftp access credentials	
		 if (($username != "") && ($pwd != "") && ($host != "")) {
                $sftp = new SFTP($host);
                $key = new RSA();
                $key->loadKey(file_get_contents($key_path.'/' . $pwd, true));
                
                if (!$sftp->login($username, $key)) {
                    echo 'Login Failed';
                    /* loging check */
                    if(isset($log_mode) && $log_mode==1){
                        $objectManager->get('Orange\Upload\Helper\Data')->logCreate('/var/log/BI_ReportErrorLog.log', $sftp->getLog());
                    }
                } else {
				    $this->updateOrderTable($orderIds);
					$sftp->put($reportPath . '/' . $filename, $WPAfile, NET_SFTP_LOCAL_FILEBIR);
				}
                /* loging check */
                if(isset($log_mode) && $log_mode==1){
                    $objectManager->get('Orange\Upload\Helper\Data')->logCreate('/var/log/BI_ReportErrorLog.log', $sftp->getLog());
                }
            } 
		
        $this->downloadCsv($outputFile);
    }
    public function ftpDetails() {
		$scopeConfig = $this->objectManagerInt()->create('Magento\Framework\App\Config\ScopeConfigInterface');
		$Credentials = array();
        $Credentials['Bireport_Host'] = $scopeConfig->getValue('bireport_ftpdetails/bidetail__configuration/bireport_host', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $Credentials['Bireport_User'] = $scopeConfig->getValue('bireport_ftpdetails/bidetail__configuration/bireport_username', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $Credentials['Bireport_ppk'] = $scopeConfig->getValue('bireport_ftpdetails/bidetail__configuration/bireport_key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $Credentials['Bireport_Path'] = $scopeConfig->getValue('bireport_ftpdetails/bidetail__configuration/bireport_path', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $Credentials;
    }
    public function iewAdvantage($orderItem) {
        $id = $orderItem->getProductId();
        $productType = $orderItem->getProductType();
        if ($productType == 'bundle') {
			$childItems = $orderItem->getChildrenItems();
			$productChildsku = '';
			foreach ($childItems as $childs) {
			    $vproduct = $childs->getProduct();
				if ($vproduct->getTypeId() == "virtual") {
					$productChildsku = $vproduct->getSku();
				}
			}
            $rtData = $this->getSKUProduct($productChildsku);
            if ($rtData == 'IEW') {
                return "Modem advantage";
            } else {
                return '';
            }
        } else {
            return '';
        }
    }

    public function dropDownValue($drop,$store,$type){
		$type = strtolower($type);
       if($type == 'prepaid'){
			return '';	
		}else{
			if($store == 1){
				if($drop == 1){
				return 'Vrij beroep met btw-nummer';
				}else if($drop == 2){
				return 'Vrij beroep zonder btw-nummer';	
				}else if($drop == 4){
				return 'Zelfstandige';	
				}else if($drop == 3){
				return 'Bedrijf';	
				}else{
				return '';	
				}
			}else{
				if($drop == 1){
				return 'Profession libérale avec numéro de TVA';
				}else if($drop == 2){
				return 'Profession libérale sans numéro de TVA';	
				}else if($drop == 4){
				return 'Indépendant';	
				}else if($drop == 3){
				return 'Entreprise';	
				}else{
				return '';	
				}
			}
		}

    }
public function getreduction($orderitemm,$add_numberiew){
	$iew = $this->iewAdvantage($orderitemm);
	$subsidy_iew = $this->iew($orderitemm->getSku());
	if($iew != ''){
		return $add_numberiew;
	}else{
		return '';
	}
	
}
    public function productData($orderItem, $dat) {
        $productType = $orderItem->getProductType();
        $productName = $orderItem->getName();
		
        if ($productType == 'bundle') {
            if (strpos($productName, '+') !== false) {
                if ($dat != 'nint') {
                    $name = explode("+", trim($productName));
                    return ltrim($name['1']);
                } else {
                    $name = explode("+", trim($productName));
                    return ltrim($name['0']);
                }
            }
        } else {
            if ($dat == 'prod') {
                return ltrim($productName, ' ');
            } else {
                return '';
            }
        }		
    }
public function getgsm($gsm){	
	$gsm = str_replace('/','',$gsm);
	$gsm_value = '';
	if($gsm != ''){
	$gsm_data1 = substr($gsm,0,4);
	$gsm_data2 = substr($gsm,4);
	$gsm_value = $gsm_data1."/".$gsm_data2;
	}
	return $gsm_value;
}
    public function jupiterLoyaltybonus($orderItemm,$storeId) {
        $pid = $orderItemm->getProductId();
		$sku = $orderItemm->getSku();
		$rtData = $this->getSKUProduct($sku);
        $productType = $orderItemm->getProductType();
        if ($productType == 'bundle') {
			$sku = $this->getmodel($orderItemm);
			$virtualProductId =  $this->getSKUProductId($sku);
            $val = $this->getNintendoAttrdValue($pid,$storeId,$virtualProductId);
			$childItems = $orderItemm->getChildrenItems();
			$productChildsku = '';
			foreach ($childItems as $childs) {
			    $vproduct = $childs->getProduct();
				if ($vproduct->getTypeId() == "virtual") {
					$productChildsku = $vproduct->getSku();
				}
			}
            $rtData = $this->getSKUProduct($productChildsku);
			
            if ($rtData == 'IEW') {
			return "Modem advantage";
			}
            if($val == 24){
                return "Phone advantage";
            }else{
                 return "Loyalty advantage";                
            }
            
        } else {
            return '';
        }
    }
     public function getsubsidyprice($orderItemm,$storeId) {
	    $pid = $orderItemm->getProductId();
		$sku = $orderItemm->getSku();
        $productType = $orderItemm->getProductType();
        if ($productType == 'bundle') {
			$sku = $this->getmodel($orderItemm);
			$virtualProductId =  $this->getSKUProductId($sku);
			$childItems = $orderItemm->getChildrenItems();
			$productChildsku = '';
			$productChildprice = '';
			$productChildprice = $this->getVirtualPrice($virtualProductId,$storeId);
			return $productChildprice;
        } else {
			return '';
		}
    }
	public function getVirtualPrice($pid,$storeId) {
         $attrIDSql = "SELECT attribute_id FROM eav_attribute WHERE attribute_code = 'subscription_amount'";
         $attribute_id = $this->connectionEst()->fetchRow($attrIDSql);
         $attrId = $attribute_id['attribute_id'];
		 $price = '';
		 $attrIDSql = "SELECT value FROM catalog_product_entity_decimal where attribute_id='".$attrId."' and row_id='".$pid."' and store_id='".$storeId."'" ;
         $value = $this->connectionEst()->fetchRow($attrIDSql);
		 if (isset($value['value'])) {
			return  $price = $value['value'];
		 } else {
			 $attrIDSql = "SELECT value FROM catalog_product_entity_decimal where attribute_id='".$attrId."' and row_id='".$pid."' and store_id='0'" ;
			 $value = $this->connectionEst()->fetchRow($attrIDSql);
		 }
        if (isset($value['value'])) {
			return $price = $value['value'];
		}
        return $price;
    }

    public function getNintendoAttrdValue($pid,$storeId,$virtualProductId) {
		$bundledIds1 = $this->getBundledProductOptionDetails($pid);
        if (!$virtualProductId) {		
			$virtualProductId = $this->getVirtualProductInBundle($bundledIds1);
		}
        $attrId1 = $this->getAttributeIdData('subsidy_duration');
		return $this->getDurationData($virtualProductId, $attrId1,$storeId);
    }

	public function getDurationData($virtualID1, $attrId1,$storeId){
	
		$col2 = "SELECT * FROM catalog_product_entity_varchar WHERE attribute_id = '" . $attrId1 . "' AND row_id='" . $virtualID1 . "' AND store_id = '".$storeId."'";
        $ret2 = $this->connectionEst()->fetchAll($col2);
		/*echo $virtualID1; exit;
		echo $storeId; exit;
		print "<pre>";
		print_r($ret2);
		print "</pre>";
		exit;*/
        $durationValue = '';
		if(isset($ret2[0]['value'])){
			return $durationValue = $ret2[0]['value'];
		}
		if (!$durationValue) {
			$col2 = "SELECT * FROM catalog_product_entity_varchar WHERE attribute_id = '" . $attrId1 . "' AND row_id='" . $virtualID1 . "' AND store_id = 0";
			$ret2 = $this->connectionEst()->fetchAll($col2);
			$durationValue = '';
			if(isset($ret2[0]['value'])){
				return $durationValue = $ret2[0]['value'];
			} else {
				return $durationValue;
			}
		}
	
	}
    public function getNintendoAttrValueFetch($orderItem) {
        $id = $orderItem->getProductId();
		$sku = $orderItem->getSku();
        $typedata = $this->getSKUProduct($sku);
        $productType = $orderItem->getProductType();
		if ($productType == 'virtual' && $typedata = 'Postpaid') {
            //$bundledIds = $this->getBundledProductOptionDetails($id);
            //$virtualID = $this->getVirtualProductInBundle($bundledIds);
            $attrId = $this->getAttributeIdData('web_promo');
            return $this->getShortDescData($id, $attrId);
        } else {
            return '';
        }
    }

    public function getShortDescData($virtualID, $attrId) {
        $col1 = "SELECT * FROM catalog_product_entity_varchar WHERE attribute_id = '" . $attrId . "' AND row_id='" . $virtualID . "' AND store_id = 0";
        $ret1 = $this->connectionEst()->fetchAll($col1);
		if(isset($ret1[0]['value'])){
			return $ret1[0]['value'];
		}else{
			return '';
		}     
       
    }

    public function getAttributeIdData($data) {
        $col1 = "SELECT attribute_id FROM eav_attribute WHERE attribute_code = '" . $data . "'";
        $ret1 = $this->connectionEst()->fetchAll($col1);
        if(isset($ret1[0])){
            if(isset($ret1[0]['attribute_id'])){
                 return $ret1[0]['attribute_id'];
            }else{
                 return '';
            }           
        }else{
            return '';
        }
        
    }

    public function getVirtualProductInBundle($bundledIds) {
        $id = '';
        foreach ($bundledIds as $vid) {
            $col1 = "SELECT * FROM catalog_product_entity WHERE entity_id = '" . $vid . "'";
            $ret1 = $this->connectionEst()->fetchAll($col1);
            if ($ret1[0]['type_id'] == 'virtual') {
                $id = $ret1[0]['entity_id'];
            }
        }
        return $id;
    }
    
    public function getVirtualProductSKUInBundle($bundledIds) {
        $id = '';
        foreach ($bundledIds as $vid) {
            $col1 = "SELECT * FROM catalog_product_entity WHERE entity_id = '" . $vid . "'";
            $ret1 = $this->connectionEst()->fetchAll($col1);
            if ($ret1[0]['type_id'] == 'virtual') {
                $id = $ret1[0]['sku'];
            }
        }
        return $id;
    }

    public function getBundledProductOptionDetails($id) {
        $col = "SELECT * FROM catalog_product_bundle_selection WHERE parent_product_id = '" . $id . "'";
        $ret = $this->connectionEst()->fetchAll($col);
        $id = array();
        foreach ($ret as $key => $vid) {
            $id[$key] = $vid['product_id'];
        }
        return $id;
    }

    public function getScoringData($score) {
        if ($score == 0) {
            return "REF";
        } else if ($score == 1) {
            return "DEC";
        } else {
            return '';
        }
    }

    public function getNationalityData($qid) {		
        $residence_number = $this->boxData('residence_number', $qid);
		$passport_number = $this->boxData('passport_number', $qid);
		$nation = $this->boxData('nationality', $qid);
		if($passport_number != '' || $residence_number != ''){
            return "other";
        } else if ($nation == 'belgian') {
            return "belgian";
        }
		
    }

    public function getTitleDataValue($val) {
        if ($val == 1) {
            return "Mr.";
        } else if ($val == 2) {
            return "Mrs.";
        } else {
            return $val;
        }
    }

    public function boxData($val, $qid) {
        $collect1 = "SELECT * FROM orange_abandonexport_items where quote_id='" . $qid . "'";
        $rest1 = $this->connectionEst()->fetchAll($collect1);
       // echo $val; exit;
	   
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

    public function ogoneData($oid) {
        $collect	= "SELECT * FROM sales_order_payment WHERE parent_id = '" . $oid . "'"; //Defect - 5689 "Change PAYID insted of cstrxid"
        $rest 		= $this->connectionEst()->fetchAll($collect);
		if (isset($rest[0])) {
			return $rest[0]['cc_trans_id'];
        }
        return '';
    }

    public function jupiterClientType($finalValidation,$gsmnbr) {
		$gsm = $this->getGsmNumber($gsmnbr);
		
        if (isset($finalValidation)) {
            if ($finalValidation == 3 && $gsm == 'KEEP') {
                return "MNP";
            }else if ($finalValidation == 1) {
                return "migration";
            }else if($gsm == 'NEW'){
                return "new number";
            }else{
				return '';
			}
        }
        return '';
    }

    public function checkSOHO($id) {
        $col = "SELECT * FROM sales_order_grid WHERE increment_id = '" . $id . "'";
        $ret = $this->connectionEst()->fetchAll($col);
        if (count($ret)) {
            $gid = $ret[0]['customer_group'];
            $col1 = "SELECT * FROM customer_group WHERE customer_group_id ='" . $gid . "'";
            $ret1 = $this->connectionEst()->fetchAll($col1);
            if (count($ret1)) {
                $data = $ret1[0]['customer_group_code'];
                if ($data == 'SOHO') {
                    return 1;
                }
            }
        }
        return '';
    }

    public function proPackDetails($pr, $val) {
        $data = explode(',', $pr);
        if (in_array($val, $data)) {
            return 1;
        }
        return '';
    }

    public function productNameFetching($prodName, $orderItems) {
        $i = 0;
        foreach ($orderItems as $orderItem) {
            if ($orderItem->getName() == $prodName) {
                $i++;
            }
        }

        if ($i != 0) {
            return $i;
        }

        return '';
    }

    public function iew($sku) {
        $rtData = $this->getSKUProduct($sku);
        if ($rtData == 'IEW') {
            return "new IEW client";
        } else {
            return '';
        }
    }
	public function mobileclienttype($sku,$type,$modelsku) {
		  
 	  if($type == 'bundle')
	   {
	     return $this->iew(trim($modelsku));
	   }
	   else
	   {
	   return $this->iew($sku);
	   }
    }

	public function getmodel($orderItem){
		$productType = $orderItem->getProductType();
        $sku = $orderItem->getSku();
		
        if ($productType == 'bundle') {
		if (strpos($sku, '+') !== false) {
		$sku = explode('+',$sku);
		return $sku[1];
		}else{
			return $sku;
		}
		}else{
			return $sku;
		}
	}
    public function connectionEst() {
        $resource = $this->objectManagerInt()->get('Magento\Framework\App\ResourceConnection');
        return $resource->getConnection();
    }

    public function objectManagerInt() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        return $objectManager;
    }

    public function getSKUProduct($sku) {
        $col1 = "SELECT * FROM catalog_product_entity WHERE sku ='" . $sku . "'";
        $ret1 = $this->connectionEst()->fetchAll($col1);
        if (count($ret1)) {
            $attr_id = $ret1[0]['attribute_set_id'];
            $col2 = "SELECT * FROM eav_attribute_set WHERE attribute_set_id = '" . $attr_id . "'";
            $ret2 = $this->connectionEst()->fetchAll($col2);
            return $ret2[0]['attribute_set_name'];
        }
    }
	
	public function getSKUProductId($sku) {
        $col1 = "SELECT * FROM catalog_product_entity WHERE sku ='" . trim($sku) . "'";
        $ret1 = $this->connectionEst()->fetchAll($col1);
		if (isset($ret1[0]['entity_id'])) {
			return $ret1[0]['entity_id'];
		} else {
			return '';
		}
        
    }

    public function deliverMethod($method, $quote_id,$type) {
		$type = strtolower($type);
		if($type == 'prepaid'){
			return  '';
		}else{
			$deleiveryAddressInformation = $this->boxData('c_delivery_address',$quote_id);
			if ($deleiveryAddressInformation == 3) {
			   return "BPOST";
			} else if ($deleiveryAddressInformation == 2) {
				return "Other";
			} else if ($deleiveryAddressInformation == 1) {
				return "Current";
			}else{
				return "";
			}
		}
    }

    public function billingAndShipping($billing,$shipping,$type,$quote_id) {
		$delivery_data = '';
		 $bpostPostalLocation = $this->boxData('customerPostalLocation',$quote_id);
		$bpostMethod = $this->boxData('customerPostalCode',$quote_id);
		$billingShipping = $this->boxData('c_delivery_address',$quote_id);
       if($billingShipping != "2" || ($bpostPostalLocation && $bpostMethod)){
			$delivery_data = '';
		}else if($billingShipping == "2"){
        $comp = strcmp($billing['street'], $shipping['street']);
		
		if($comp && $shipping['firstname'] != ''){
			$result_data = $shipping['firstname'].",".$shipping['lastname'].",".$this->boxData('s_attention_n', $quote_id);
			$delivery_data = "$result_data";
		}else{
			//$delivery_data = 'To the attention of';
			$delivery_data = '';
		}
		}
		return $delivery_data;
    }

    public function getBpostData($shippingAddress, $quote_id) {
	    $bpostPostalLocation = $this->boxData('customerPostalLocation',$quote_id);
		$bpostMethod = $this->boxData('customerPostalCode',$quote_id);
        $c_delivery_address = $this->boxData('c_delivery_address',$quote_id);
        if ($bpostPostalLocation && $bpostMethod && $c_delivery_address == 3) {
            $data = array();
            $data ['street'] = ($shippingAddress['street']) ? : '';
            $data ['city'] = ($shippingAddress['city']) ? : '';
            $data ['postcode'] = ($shippingAddress['postcode']) ? : '';
            $data ['country_id'] = ($shippingAddress['country_id']) ? : '';
            $data ['lastname'] = ($shippingAddress['lastname']) ? : '';
            $data ['bpost_postal_location'] = ($shippingAddress['bpost_postal_location']) ? : '';
            $data ['bpost_method'] = ($shippingAddress['bpost_method']) ? : '';
            $result_data = json_encode($data);
			$result_data = "\"".$result_data."\"";
			return  $result_data;
        }
        return '';
    }

    public function downloadCsv($file) {
        if (file_exists($file)) {
            header('Content-Description: File Transfer');
			header('Content-Encoding: UTF-8');
            header('Content-Type: application/csv;charset=UTF-8');
            header('Content-Disposition: attachment; filename=' . basename($file));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
           // header('Content-Length: ' . filesize($file));
			echo "\xEF\xBB\xBF"; // UTF-8 BOM
            readfile($file);
        }
    }

    public function getProductLOB($type) {
        $sku = $type->getSku();
        $productType = $type->getProductType();
        $typedata = $this->getSKUProduct($sku);
        if ($productType == 'virtual' && $typedata == 'Postpaid') {
            $lob = 'Postpaid';
        } else if ($productType == 'simple' && ($typedata == 'Prepaid' || $typedata == 'Simcard')) {
            $lob = 'Prepaid';
        } else if ($productType == 'simple' && $typedata == 'handset') {
            $lob = 'Hardware';
        } else if ($productType == 'simple' && $typedata == 'Accessories') {
            $lob = 'Accessories';
        } else if ($productType == 'virtual' && $typedata == 'IEW') {
            $lob = 'IEW';
        } else {
            $lob = 'subsidy';
        }
		return $lob;
    }

    public function getNintendoName($orderItem) {
        $productType = $orderItem->getProductType();
        $productName = '';
        if ($productType == 'bundle') {
            $productName = $orderItem->getName();
        }
        return $productName;
    }

    public function getStore($storeId) {
        if ($storeId == '1') {
            $store = 'nl';
        } else {
            $store = 'fr';
        }
        return $store;
    }

    private function getGsmNumber($simType) {
        $sim = '';
        if ($simType == 1):
            $sim = 'KEEP';
        elseif ($simType == 2):
            $sim = 'NEW';
        endif;
        return $sim;
    }
	public function getcustomernumber($plan,$validation,$value,$designsimnumber){
		if(!$plan || $plan == 2 || $validation != 3 || $designsimnumber == 2){
			return '';
		}
		if($value == 1){
			$value = 'Yes';
		}else{
			$value = 'No';
		}
	
	return $value;
	}
	private function registered_with_belgian($quoteid){
		/* 1802 NID Project */
		//Order status whatever, this field value should be return 1.
		return 1;
	}
    /* 1802 NID Project */
    private function get_residence_value($quoteid) {
        $id_num = $this->boxData('id_number',$quoteid);
        $id_first_Char = substr($id_num, 0, 1);
        if (ctype_alpha($id_first_Char)) {
            return $id_num;
        } else {
            return '';
        }
    }
    private function get_identity_card_value($quoteid) {
        $id_num = $this->boxData('id_number',$quoteid);
        $id_first_Char = substr($id_num, 0, 1);
        if (ctype_digit($id_first_Char)) {
            return $id_num;
        } else {
            return '';
        }
    }
    private function getTariffPlan($plan,$simnumber,$validation){
        $tariff = '';
		if($simnumber == 2 || $validation !=3){
		return '';
		}
        if ($plan == 1){
            $tariff = 'POSTPAID';
        }
		else if ($plan == 2){
            $tariff = 'PREPAID';
        }
        return $tariff;
		
    }

    private function getOwnerInvoice($ownerType) {
        $isOwner = '';
        if ($ownerType == '1') {
            $isOwner = 'YES';
        } elseif ($ownerType == '0') {
            $isOwner = 'NO';
        }
    }
	public function getStatus_flag(){
		return '1';
	}
	public function customerscoring($quoteid,$order){
		$scoring = $this->boxData('scoringResponse', $quoteid);
		if($scoring == 'ACC' || $scoring == 'REF'){
			return $scoring;
		}
		$scoreData = $order->getScoreData();
		if($scoreData) {
			$scoringDeclineReason = unserialize($scoreData);

			if(isset($scoringDeclineReason['scoringResult'])) {
				return $scoringDeclineReason['scoringResult'];
			}
		}
		return '';
	}
	public function customerdecline($orderItem,$quoteid){
	$scoring = $this->boxData('scoringResponse', $quoteid);
		if($scoring == 'REF'){
			return $scoring;
		}else{
		return '';
		}
	}
	public function geteid_or_rpid($qid){
		$id_number = $this->boxData('id_number', $qid);
        $residence_number = $this->boxData('residence_number',$qid);
        $passport_number = $this->boxData('passport_number',$qid);
		if($id_number !=''){
			return $id_number;
		}else if($residence_number != ''){
			return $residence_number;
		}else if($passport_number != ''){
			return $passport_number;
		}else{
			return '';
		}
		
	}
	public function getpropack($postpaid,$orderIncrementId){
		
		$soho = $this->checkSOHO($orderIncrementId);
		$propack = '';
		if($soho == 1 && $postpaid->getProPacks() != ''){
			 $count = count(explode(",",$postpaid->getProPacks()));
			 if($count > 0){
				 $propack = $count;
			 }
		}
		return $propack;
	}
	public function getcurrentsim($simNumber,$validation){
		if($validation != 3){
			return '';
		}
		$result_data = "\t".$simNumber."\t";
		return $result_data;
	}
	public function getpassport($quotid){
		$passport = $this->boxData('passport_number',$quotid);
		$are_you_belgiam = $this->boxData('registered',$quotid);
		if($are_you_belgiam == 0 && $passport != ''){
			return $passport;
		}else{
			return '';
		}
	}
	public function getcustomer_number($quoteid,$plan){
		if($plan == 'yes'){
			$custnumber = $this->boxData('cu_ex_invoice_cust_number', $quoteid);
			$result_data = "\t".$custnumber."\t";
			return $result_data;
		}else{
			return '';
		}
	}
	public function getdeliveryzipcity($shippingAddress,$quoteid,$type){
		$bpostPostalLocation = $this->boxData('customerPostalLocation',$quoteid);
		$bpostMethod = $this->boxData('customerPostalCode',$quoteid);
		$billingShipping = $this->boxData('c_delivery_address',$quoteid);
		$type = strtolower($type);
       if($billingShipping != "2" || ($bpostPostalLocation && $bpostMethod)){
			return '';	
		}else{	
			$street =	$this->boxData('s_street', $quoteid);
			$deleiveryAddressInformation = $this->boxData('c_delivery_address',$quoteid);
			if($street != '' && $deleiveryAddressInformation == 2){
				$zipcity = $shippingAddress['postcode'] . ' ' . strtoupper(($shippingAddress['city'])) ? : '';
				return $zipcity;
			}else{
				return '';	
			}
		}
	}
	public function getdeliverystreet($quoteid,$type){
		$bpostPostalLocation = $this->boxData('customerPostalLocation',$quoteid);
		$bpostMethod = $this->boxData('customerPostalCode',$quoteid);
		$billingShipping = $this->boxData('c_delivery_address',$quoteid);
		$type = strtolower($type);
       if($billingShipping != "2" || ($bpostPostalLocation && $bpostMethod)){
			return '';	
		}else{
			$street =	$this->boxData('s_street', $quoteid);
			$deleiveryAddressInformation = $this->boxData('c_delivery_address',$quoteid);
			if($street != '' && $deleiveryAddressInformation == 2){
			$s_street = strtoupper($this->boxData('s_street', $quoteid));
			return $s_street;
			}else{
			return '';	
			}
		}
	}
	public function getdeliveryhouse($quoteid,$type){
		$bpostPostalLocation = $this->boxData('customerPostalLocation',$quoteid);
		$bpostMethod = $this->boxData('customerPostalCode',$quoteid);
		$billingShipping = $this->boxData('c_delivery_address',$quoteid);
		$type = strtolower($type);
       if($billingShipping != "2" || ($bpostPostalLocation && $bpostMethod)){
			return '';	
		}else{
			$s_number =	$this->boxData('s_number', $quoteid);
			$deleiveryAddressInformation = $this->boxData('c_delivery_address',$quoteid);
			if($s_number != '' && $deleiveryAddressInformation == 2){
			$s_number = $this->boxData('s_number', $quoteid);
			return $s_number;
			}else{
			return '';	
			}
		}
	}
		public function ogoneDataPostpaid($postPaidprice,$sohosegment,$bundleB2CPrice,$bundleSohoPrice,$ogoneTid,$gTotal){

		if($sohosegment == 1)
		{
		  if($postPaidprice <= $bundleSohoPrice)
		  {
		  $postPaidprice = 0;
		  }
		
		}
		else
		{
		  if($postPaidprice <= $bundleB2CPrice)
		  {
		  $postPaidprice = 0;
		  }
		}
		if($postPaidprice == 0 || $gTotal == 0  )
		{
		 return "";
		}
		else
		{
		 return $ogoneTid;
		}

	}
	
	public function getdeliverybus($quoteid,$type){
		$bpostPostalLocation = $this->boxData('customerPostalLocation',$quoteid);
		$bpostMethod = $this->boxData('customerPostalCode',$quoteid);
		$billingShipping = $this->boxData('c_delivery_address',$quoteid);
		$type = strtolower($type);

       if($billingShipping != 2 || ($bpostPostalLocation && $bpostMethod)){
			return '';	
		}else{
			$s_box =	$this->boxData('s_box', $quoteid);
			$deleiveryAddressInformation = $this->boxData('c_delivery_address',$quoteid);
			if($s_box != '' && $deleiveryAddressInformation == 2){
			$s_box = $this->boxData('s_box', $quoteid);
			return "\t".$s_box."\t";
			}else{
			return '';	
			}
		}
	}
	public function getinvoiceownerdob($orderItemm,$quoteid){
		$dob = '';
		$lob = $this->getProductLOB($orderItemm);
		$owner = $this->getinvoiceowner($orderItemm,$quoteid);
		if($lob == 'IEW'){
			if($this->boxData('is_teneuro_'.$orderItemm->getQuoteItemId(), $quoteid) == 'yes' && $owner=='0'){ 
				if($this->boxData('iew_dob_'.$orderItemm->getQuoteItemId(), $quoteid) != '' && $this->boxData('iew_contract_'.$orderItemm->getQuoteItemId(), $quoteid) == '0'){
					$dob = $this->boxData('iew_dob_'.$orderItemm->getQuoteItemId(), $quoteid);
					if($dob != ''){
						$dob = date('d/m/Y', strtotime($dob));
					}
				}
			}
		}else{
				if($this->boxData('ex_invoice', $quoteid) == 'yes'){
					if($this->boxData('cu_ex_invoice_cust_dob', $quoteid) != ''){
						$dob = $this->boxData('cu_ex_invoice_cust_dob', $quoteid);
						if($dob != ''){
							$dob = date('d/m/Y', strtotime($dob));
						}
					}
				}
			}
		
	return $dob;	
	}
	public function getExistingPlan($orderItemm,$quoteid){
		$lob = $this->getProductLOB($orderItemm);
		if($lob == 'IEW'){
			return $this->boxData('is_teneuro_'.$orderItemm->getItemId(), $quoteid);
		}else{
			return $this->boxData('ex_invoice', $quoteid);
		}
		
	}
	public function getCompanyLegal($quoteid){
		$dropdown = $this->boxData('tx_profile_dropdown', $quoteid);
		$legal = $this->boxData('legal_status', $quoteid);
		if($dropdown != ''){
			return $legal;
		}else{
			return '';
		}
		
	}
	public function getCompanyName($quoteid){
		$dropdown = $this->boxData('tx_profile_dropdown', $quoteid);
		$name = $this->boxData('company_name', $quoteid);
		if($dropdown != ''){
			return $name;
		}else{
			return '';
		}
		
	}
	public function getCompanyVat($quoteid){
		$dropdown = $this->boxData('tx_profile_dropdown', $quoteid);
		$vat = $this->boxData('vat_number', $quoteid);
		if($dropdown != ''){
			return $vat;
		}else{
			return '';
		}
		
	}
	public function getCoupon($order){
		$coupon = $order->getCouponCode();
		$couponcode = '';
		if($coupon != ''){
			$couponcode =  "$coupon";
		}else{
			$couponcode = '';
		}
		return "\t".$couponcode."\t";
	}
	public function CurrentOperator($operator,$validation){
		if($validation != 3){
			return '';
		}
		return $operator;
	}
	public function CurrentCustomerNumber($custnumber,$validation){
		if($validation != 3){
			return '';
		}
		return "\t".$custnumber."\t";
	}
	public function OwnerName($Ownername,$validation){
		if($validation != 3){
			return '';
		}
		return $Ownername;
	}
	public function FirstOwnerName($firstownername,$validation){
		if($validation != 3){
			return '';
		}
		return $firstownername;
	}
	public function getdateofbirth($quoteid){
		$dob = $this->boxData('c_dob', $quoteid);
		return $dob;
	}
	public function getvatnumber($quoteid,$orderItem){
		$vatnumber = $this->boxData('i_ind_copm', $quoteid);
		$type = $this->getProductLOB($orderItem);
		$type = strtolower($type);
		if($vatnumber && $type != 'prepaid'){
			return 1;
		}else{
			return '';
		}
	}
	public function getinvoiceowner($orderItemm,$quoteid){
		$owner = '';
		$lob = $this->getProductLOB($orderItemm);
		if($lob == 'IEW'){
			if($this->boxData('is_teneuro_'.$orderItemm->getQuoteItemId(), $quoteid) == 'yes'){ 
			    if ($this->boxData('iew_contract_'.$orderItemm->getQuoteItemId(), $quoteid) == '0') {
					if($this->boxData('iew_first_name_'.$orderItemm->getQuoteItemId(), $quoteid) != ''){
						$owner = 0;
					} else{
						$owner = 1;
					}
				}
			}
		}else{
				if($this->boxData('ex_invoice', $quoteid) == 'yes'){
					if($this->boxData('cu_ex_invoice_cust_surname', $quoteid) != ''){
						$owner = 0;
					}else{
						$owner = 1;
						}
				}
		}
		return $owner;
	}
	public function getinvoiceownerfname($orderItemm,$quoteid){
		$fname = '';
		$lob = $this->getProductLOB($orderItemm);
		if($lob == 'IEW'){
			if($this->boxData('is_teneuro_'.$orderItemm->getQuoteItemId(), $quoteid) == 'yes'){ 
				if($this->boxData('iew_first_name_'.$orderItemm->getQuoteItemId(), $quoteid) != '' && $this->boxData('iew_contract_'.$orderItemm->getQuoteItemId(), $quoteid) == '0'){
					$fname = $this->boxData('iew_first_name_'.$orderItemm->getQuoteItemId(), $quoteid);
				}
			}
		}else{
				if($this->boxData('ex_invoice', $quoteid) == 'yes'){
					if($this->boxData('cu_ex_invoice_cust_surname', $quoteid) != ''){
						$fname = $this->boxData('cu_ex_invoice_cust_surname', $quoteid);
					}
				}
		}
		return $fname;
	}
	public function getinvoiceownerlname($orderItemm,$quoteid){
		$lname = '';
		$lob = $this->getProductLOB($orderItemm);
		if($lob == 'IEW'){
			if($this->boxData('is_teneuro_'.$orderItemm->getQuoteItemId(), $quoteid) == 'yes'){ 
				if($this->boxData('iew_last_name_'.$orderItemm->getQuoteItemId(), $quoteid) != '' && $this->boxData('iew_contract_'.$orderItemm->getQuoteItemId(), $quoteid) == '0'){
					$lname = $this->boxData('iew_last_name_'.$orderItemm->getQuoteItemId(), $quoteid);
				}
			}
		}else{
				if($this->boxData('ex_invoice', $quoteid) == 'yes'){
					if($this->boxData('cu_ex_invoice_cust_firstname', $quoteid) != ''){
						$lname = $this->boxData('cu_ex_invoice_cust_firstname', $quoteid); 
					}
				}
		}
		return $lname;
	}
    private function getHeaders() {
        $heading = [
            __('created_date'),
            __('created_time'),
			__('updated_date'),
            __('updated_time'),
            __('uc_order_id'),
            __('order_status'),
            __('order_total'),
            __('product_count'),
            __('primary_email'),
            __('delivery_first_name'),
            __('delivery_last_name'),
            __('payment_method'),
            __('host'),
            __('sim_activated'),
            __('product'),
            __('model'),
            __('qty'),
            __('cost'),
            __('price'),
            __('nid'),
            __('catalogue'),
            __('Subsidy device name'),
            __('language'),
            __('title'),
            __('zipcity'),
            __('street'),
            __('house_number'),
            __('bus'),
            __('optin'),
            __('gsm_number'),
            __('current_operator'),
            __('current_gsm_number'),
            __('current_sim_number'),
            __('current_tarrif_plan'),
            __('current_customer_number'),
            __('are_you_the_owner_of_the_account'),
            __('first_name_owner'),
            __('last_name_owner'),
            __('telephone_number'),           
            __('nationality'),
            __('Place of birth'),
            __('date_of_birth'),
            __('Belgian_ID_document'),
            __('identity_card_number'),
            __('residence_permit_number'),
            __('passport_number'),
            __('add_to_existing_plan'),
            __('customer_number'),
            __('method_of_payment'),
            __('bank_account_number'),
            __('enter_vat_number'),
            __('delivery_attention_off'),
            __('delivery_zipcity'),
            __('delivery_street'),
            __('delivery_house_number'),
            __('delivery_bus'),
            __('shipping_total'),
            __('bpost_data'),
            __('delivery_method'),
			__('invoice_owner_first_name'),
            __('invoice_owner_last_name'),
            __('invoice_owner_date_of_birth'),
            __('invoice_owner'),
            __('business_profile'),
            __('business_company_name'),
            __('business_company_vat'),
            __('business_company_legal'),
            __('customer_scoring'),
            __('eid_or_rpid'),
            __('scoring_decline_reason'),
            __('Mobile Internet Client Type'),
            __('Subsidy Advantage'),
            __('Client type'),
            __('IEW Advantage'),
            __('Reduction on IEW'),
            __('Promo postpaid'),
            __('Pro pack'),           
            __('Smartphone ProPack'),
            __('Reduction ProPack'),
            __('Surf ProPack'),
            __('SOHO segment'),           
            __('Ogone transaction ID'),
            __('General conditions'),
            __('coupon'),
			__('line_item_id'),
			
        ];
		
		/* Removed Column
		__('Activation Request'),
			__('status_flag'), 
			__('event_dt')
			*/
        return $heading;
    }

    private function getOrderCollection() {
        return $this->_orderCollectionFactory->create()->addAttributeToSelect('*');
    }
	public function updateOrderTable($entityIds) {
	    $entityCount = count($entityIds);
		if ($entityCount > 0) {
		    $orderIds = implode(',',$entityIds);
			$tableName = $this->connectionEst()->getTableName('sales_order');
			$sql = "UPDATE ".$tableName." SET bi_tracking_flag=1 WHERE entity_id IN(".$orderIds.")"; 
			$this->connectionEst()->query($sql);
		}
    }
	
	public function getBiTrackingFlag($orderId) {
	    $tableName = $this->connectionEst()->getTableName('sales_order');
        $col1 = "SELECT bi_tracking_flag FROM ".$tableName." WHERE entity_id ='" . $orderId . "'";
        $ret1 = $this->connectionEst()->fetchAll($col1);
		if (isset($ret1[0]['bi_tracking_flag'])) {
			return $ret1[0]['bi_tracking_flag'];
		} else {
			return '';
		}
        
    }

}

