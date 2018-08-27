<?php
namespace Orange\Export\Model;

class Orderexport extends \Magento\Framework\Model\AbstractModel
{
	protected $_orderCollectionFactory;
	protected $_scopeconfig;
	protected $_postpaidModel;
	protected $_resource;
	protected $_logger;
	protected $_timezoneInterface;
	protected $_directoryList;
	protected $_exportHelper;
	protected $_exportData;
	
    /**
     * Initialize resource model
     *
     * @return void
     */
    public function __construct(
		\Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
		\Orange\Checkout\Model\Postpaid $postpaidModel,
		\Orange\Export\Logger\Logger $logger,
		\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
		\Magento\Framework\App\Filesystem\DirectoryList $directoryList,
		\Orange\Export\Helper\Data $exportHelper
	)
    {
		ini_set('memory_limit','2000M');
		$this->_orderCollectionFactory = $orderCollectionFactory;
		$this->_scopeconfig = $scopeConfigInterface;
		$this->_postpaidModel = $postpaidModel;	
		$this->_logger = $logger;
		$this->_timezoneInterface = $timezoneInterface;
		$this->_directoryList = $directoryList;
		$this->_exportHelper = $exportHelper;
		$this->_exportData = array();
        $this->_init('Orange\Export\Model\ResourceModel\Orderexport');
    }
	/**
	 * Generated Order Report and update custom order export table
	 */
	public function setExportData(
		$fromDt = NULL, 
		$toDt = NULL, 
		$status = NULL, 
		$limit = NULL, 
		$page = NULL, 
		$exportType = NULL,
		$reportType = NULL
	)
	{						
		$connection = $this->connectionEst();
		$resource = $this->resourceEst();
		$from = $fromDt;
		$to = $toDt;		
		$row = array();
		if($from && $to) {		
			$this->_logger->info('ORDER DATA MIGRATION TO CUSTOM EXPORT STARTED AT '.date("Y-m-d H:i:s"));		
		
			$from = $from.' 00:00:00';	
			$to = $to. ' 23:59:59';
			
			$fromDate = new \DateTime($from);
			$toDate = new \DateTime($to);	
				
			$orderCollection = $this->_orderCollectionFactory->create()								
								->addAttributeToFilter('created_at', array('from' => $fromDate, 'to' => $toDate));			
			if($status) {
				$orderCollection->addAttributeToFilter('status', $status);
			}
			if($limit) {
				if(!$page) {
					$page = 1;
				}				
				$offset = ($page - 1) * $limit;				
				$orderCollection->getSelect()->limit($limit,$offset);
			}
		}
		else {
			$this->_logger->info('ORDER DATA MIGRATION TO CUSTOM EXPORT CRON JOB STARTED AT '.date("Y-m-d H:i:s"));						
			$lasInsertRecord = $this->getLastInsertRecord(); //get last order from order export table
			if($lasInsertRecord == '') {
				$lasInsertRecord = 0;				
				$currentDate = date('Y-m-d 00:00:00', strtotime('today - 1 days'));					
				//$currentDate = date("2017-11-08 00:00:00"); // current date	
				$orderCollection = $this->_orderCollectionFactory->create()									
									->addAttributeToFilter('created_at', array('from' => $currentDate));	
				//$orderCollection->getSelect()->limit(10,0);
			}
			else {
				//$lasInsertRecord = '300160070'; #######
				$orderCollection = $this->_orderCollectionFactory->create()									
									->addAttributeToFilter('increment_id', array('gt' => $lasInsertRecord));
			}
		}
		
		$this->_logger->info($orderCollection->getSelect());		
		/** Subsidy Amount for SOHO and B2C */
		$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
		$bundleB2CPrice = $this->_scopeconfig->getValue('soho/soho_configuration/amount_subsidy', $storeScope);
		$bundleSohoPrice = $this->_scopeconfig->getValue('soho/soho_configuration/amount_subsidy_soho', $storeScope);
				
		$this->_logger->info('FETCHING DATA FROM ORDER COLLECTION');
		foreach ($orderCollection as $order):
			$isRecordExist = $this->checkRecordExists($order->getIncrementId()); //Check whether data already exists in export table			
			if($isRecordExist == 0) {//Delta calculation
				$orderItems 		= $order->getAllVisibleItems();
				$allItems 			= $order->getAllItems();
				$customerType 		= $order->getCustomerGroupId();
				$billingAddress 	= $order->getBillingAddress();
				$shippingAddress 	= $order->getShippingAddress();
				$quoteid 			= $order->getQuoteId();
				if($quoteid !='') {
					$abandonedOrderData = $this->getQuoteDataFromAbandoned($quoteid);//Get the data from Abandoned order table
					if($abandonedOrderData) {
						$stepData = $abandonedOrderData[0]['stepsecond'];
						$checkoutData = unserialize($stepData);//unserialize the checkout data
					}
					else {
						$checkoutData = array();
					}
					$created_time = $this->_timezoneInterface
									->date(new \DateTime($order->getCreatedAt()))
									->format('H:i:s');//Created time CET format
					$created_date_formatted = $this->_timezoneInterface
									->date(new \DateTime($order->getCreatedAt()))
									->format('d/m/Y'); //Created Date for admin download CET format
					//$created_date = $order->getCreatedAt();
					$created_date = $this->_timezoneInterface
									->date(new \DateTime($order->getCreatedAt()))
									->format('Y-m-d'); // Created date for report generation CET format
					/** Billing and Shipping Data **/
					$delivery_data = '';
					$postalCity = '';
					$deliveryStreet = '';
					$deliveryHouse = '';
					$deliveryBus = '';
					$bpostPostalLocation = (isset($checkoutData['customerPostalLocation']) ? $checkoutData['customerPostalLocation']: '');
					$bpostMethod = (isset($checkoutData['customerPostalCode']) ? $checkoutData['customerPostalCode']: '');
					$billingShipping = (isset($checkoutData['c_delivery_address']) ? $checkoutData['c_delivery_address'] : '');
					if($billingShipping != "2" || ($bpostPostalLocation && $bpostMethod)){
						$delivery_data = '';
						$postalCity = '';
						$deliveryStreet = '';
						$deliveryHouse = '';
						$deliveryBus = '';
					}
					else if($billingShipping == "2"){
						$comp = strcmp($billingAddress['street'], $shippingAddress['street']);		
						if($comp && $shippingAddress['firstname']!= ''){
							$result_data = $shippingAddress['firstname'].",".$shippingAddress['lastname'].",".$checkoutData['s_attention_n'];
							$delivery_data = "$result_data";
						}
						else{			
							$delivery_data = '';
						}
					}
					else {
						$street = (isset($checkoutData['s_street']) ? $checkoutData['s_street']: '');
						$deliveryAddressInformation = $checkoutData['c_delivery_address'];						
						$deliveryHouse = $checkoutData['s_number'];
						$deliveryBus = $checkoutData['s_box'];
						if($street != '' && $deliveryAddressInformation == 2){
							$postalCity =  $shippingAddress['postcode'] . ' ' . strtoupper(($shippingAddress['city'])) ? : '';
							$deliveryStreet = strtoupper($checkoutData['s_street']);							
						}else{
							$postalCity = '';
							$deliveryStreet = '';
						}
						if($deliveryHouse != '' && $deliveryAddressInformation == 2){
							$deliveryHouse = $checkoutData['s_number'];
						}
						else {
							$deliveryHouse = '';
						}
						if($deliveryBus != '' && $deliveryAddressInformation == 2){
							$deliveryBus = "\t".$checkoutData['s_box']."\t";							
						}
						else {
							$deliveryBus = '';
						}
					}
					/** DELIVERY METHOD **/																
					if ($billingShipping == 3) {
					   $deliveryMethod = "BPOST";
					} else if ($billingShipping == 2) {
						$deliveryMethod = "Other";
					} else if ($billingShipping == 1) {
						$deliveryMethod = "Current";
					}else{
						$deliveryMethod = "";
					}				
					foreach ($orderItems as $orderItem):
						$productLob = $this->getProductLOB($orderItem);													
						/** Invoice Data **/
						$invoiceFname = '';	
						$invoiceLname = '';
						$invoiceOwner = '';
						$invoiceOwnerDob = '';
						if($productLob == 'IEW'){
							if(isset($checkoutData['is_teneuro_'.$orderItem->getQuoteItemId()]) && $checkoutData['is_teneuro_'.$orderItem->getQuoteItemId()] == 'yes'){ 
								if($checkoutData['iew_first_name_'.$orderItem->getQuoteItemId()] != '' && $checkoutData['iew_contract_'.$orderItem->getQuoteItemId()] == '0'){
									$invoiceFname = $checkoutData['iew_first_name_'.$orderItem->getQuoteItemId()];
								}
								if($checkoutData['iew_last_name_'.$orderItem->getQuoteItemId()] != '' && $checkoutData['iew_contract_'.$orderItem->getQuoteItemId()] == '0'){
									$invoiceLname = $checkoutData['iew_last_name_'.$orderItem->getQuoteItemId()];
								}
								if ($checkoutData['iew_contract_'.$orderItem->getQuoteItemId()] == '0') {
									if($checkoutData['iew_first_name_'.$orderItem->getQuoteItemId()] != ''){
										$invoiceOwner = 0;
									} else{
										$invoiceOwner = 1;
									}
								}
							}
							if(isset($checkoutData['is_teneuro_'.$orderItem->getQuoteItemId()]) && $checkoutData['is_teneuro_'.$orderItem->getQuoteItemId()] == 'yes' && $invoiceOwner =='0'){ 
								$invoiceOwnerDob = (isset($checkoutData['iew_dob_'.$orderItem->getQuoteItemId()]) ? $checkoutData['iew_dob_'.$orderItem->getQuoteItemId()]: '');
								if($invoiceOwnerDob != ''){
									$invoiceOwnerDob = date('d/m/Y', strtotime($invoiceOwnerDob));
								}
							}
						}
						else{
							if(isset($checkoutData['ex_invoice']) && $checkoutData['ex_invoice'] == 'yes'){
								if($checkoutData['cu_ex_invoice_cust_surname'] != ''){
									$invoiceFname = $checkoutData['cu_ex_invoice_cust_surname'];
									$invoiceOwner = 0;
								}
								else {
									$invoiceOwner = 1;
								}
								if(isset($checkoutData['cu_ex_invoice_cust_firstname']) && $checkoutData['cu_ex_invoice_cust_firstname'] != ''){
									$invoiceLname = $checkoutData['cu_ex_invoice_cust_firstname']; 
								}
								if(isset($checkoutData['cu_ex_invoice_cust_dob']) && $checkoutData['cu_ex_invoice_cust_dob'] != ''){
									$invoiceOwnerDob = $checkoutData['cu_ex_invoice_cust_dob'];
									if($invoiceOwnerDob != ''){
										$invoiceOwnerDob = date('d/m/Y', strtotime($invoiceOwnerDob));
									}
								}
							}
						}						
						/** TAX Profile **/
						$profile = (isset($checkoutData['tx_profile_dropdown']) ? $checkoutData['tx_profile_dropdown']:'');
						$taxProfile = $this->getTaxProfileData($profile,$order->getStoreId(),$quoteid,$productLob);
						
						/** Scoring **/
						$scoringResponse = '';
						$scoringData = (isset($checkoutData['scoringResponse']) ? $checkoutData['scoringResponse']: '');
						if($scoringData == 'ACC' || $scoringData == 'REF'){
							$scoringResponse = $scoringData;
						}
						$scoreData = $order->getScoreData();
						if($scoreData) {
							$scoringDeclineReason = unserialize($scoreData);

							if(isset($scoringDeclineReason['scoringResult'])) {
								$scoringResponse =  $scoringDeclineReason['scoringResult'];
							}
						}
						/** EID or PID **/
						$eidOrPid = '';
						$id_number = (isset($checkoutData['id_number']) ? $checkoutData['id_number']: '');
						$residence_number = (isset($checkoutData['residence_number']) ? $checkoutData['residence_number']: '');
						$passport_number = (isset($checkoutData['passport_number']) ? $checkoutData['passport_number']: '');
						if($id_number !=''){
							$eidOrPid = $id_number;
						}else if($residence_number != ''){
							$eidOrPid = $residence_number;
						}else if($passport_number != ''){
							$eidOrPid = $passport_number;
						}else{
							$eidOrPid = '';
						}
						/** Mobile Client **/
						$mobileClient = '';
						if($productLob == 'bundle')
						{	
							$rtData = $this->getSKUProduct(trim($this->getmodel($orderItem)));
							if ($rtData == 'IEW') {
								$mobileClient = "new IEW client";
							}						
						}
						else
						{
							$rtData = $this->getSKUProduct($orderItem->getSku());
							if ($rtData == 'IEW') {
								$mobileClient = "new IEW client";
							} 						
						}
						if($billingAddress->getTelephone() != '9999999999'){ $tele = $billingAddress->getTelephone();}else{ $tele = '';}
						
						$originalPrice = $orderItem->getOriginalPrice(); //Get SOHO Price for B2C					
						$productType = $orderItem->getProductType(); //Get product Type
						if ($productType == 'bundle') {
							$bundleprice = $this->getsubsidyprice($orderItem,$order->getStoreId()); //Get subscription price from subsidy								
						}
						if($customerType == 4) {								
							$originalPrice = $this->_exportHelper->getSohoPrice($originalPrice);//Get SOHO Price of subsidy
							if ($productType == 'bundle') {																		
								$subscriptionAmount = $this->_exportHelper->getSohoPrice($bundleprice);
							}
							else{									
								$subscriptionAmount = $this->_exportHelper->getSohoPrice($orderItem->getSubscriptionAmount()); //Get subsciption price for soho
							}						
						} 
						else {								
							if ($productType == 'bundle') {									
								$subscriptionAmount = $bundleprice;
							}
							else{
								$subscriptionAmount = $orderItem->getSubscriptionAmount();	//Get subscription price for B2C
							}
						}
						$bbox = (isset($checkoutData['b_box']) ? $checkoutData['b_box'] : '');
												
						$transfertType = (isset($checkoutData['transfer_type']) ? $checkoutData['transfer_type'] : ''); //Transfer Type
						if($order->getAccountNumber()) {
							$paymentMethod = 'Domiciliation'; //Payment method
						} 
						else {
							$paymentMethod = 'Virement'; //Payment method
						}
						
						/** Belgian registered**/
						$belgiumRegistered = '';
						if(isset($checkoutData['nationality']) && $checkoutData['nationality'] == 'belgian' && $checkoutData['id_number'] != ''){
							$belgiumRegistered = 1;
						}else if(isset($checkoutData['nationality']) && $checkoutData['nationality'] == 'other'){
							$belgiumRegistered= 0;
						}
	
						/** Passport **/
						$passportData = '';
						$passport = (isset($checkoutData['passport_number']) ? $checkoutData['passport_number']: '');
						$are_you_belgiam = (isset($checkoutData['registered']) ? $checkoutData['registered']: '');
						if($are_you_belgiam == 0 && $passport != ''){
							$passportData = $passport;
						}
						
						/** VAT Number **/
						$vatData = '';
						$vatnumber = ((isset($checkoutData['i_ind_copm'])) ? $checkoutData['i_ind_copm'] : '');
						$type = strtolower($productLob);
						if($vatnumber && $type != 'prepaid'){
							$vatData = 1;
						}
						
						/** Nationality Data **/
						$nationality = '';
						$residence_number = (isset($checkoutData['residence_number']) ? $checkoutData['residence_number']: '');
						$passport_number = (isset($checkoutData['passport_number']) ? $checkoutData['passport_number']: '');
						$nation = (isset($checkoutData['nationality']) ? $checkoutData['nationality']: '');
						if($passport_number != '' || $residence_number != ''){
							$nationality = "other";
						} else if ($nation == 'belgian') {
							$nationality = "belgian";
						}
						/** ID Number **/
						$id_number = '';							
						$id_num = (isset($checkoutData['id_number']) ? $checkoutData['id_number']: '');
						$id_first_Char = substr($id_num, 0, 1);
						if (ctype_digit($id_first_Char)) {
							$id_number = $id_num;
						}
						/** Residence Number **/
						$residence_number = '';
						$residence_num = (isset($checkoutData['id_number']) ? $checkoutData['id_number']: '');
						$residence_first_Char = substr($residence_num, 0, 1);
						if (ctype_alpha($residence_first_Char)) {
							$residence_number = $id_num;
						}
						$orderStatus = (($order->getStatus() == 'processing') ? 'paid': $order->getStatus());
						/** Fetch the postpaid details if any exists for the item */
						$postpaidModelCollection = $this->_postpaidModel
													->getCollection()
													->addFieldToFilter('quote_id',$quoteid)
													->addFieldToFilter('item_id',$orderItem->getQuoteItemId());					
						if ($postpaidModelCollection->count() > 0 ) {																													
							foreach($postpaidModelCollection as $postpaidCollection) {							
								/** Existing Plan **/
								$add_existing_plan 	= $this->getExistingPlan($postpaidCollection,$quoteid);							
								if($productLob == 'IEW'){
									$add_existing_plan = (isset($checkoutData['is_teneuro_'.$orderItem->getItemId()]) ? $checkoutData['is_teneuro_'.$orderItem->getItemId()] : '');
								}else{
									$add_existing_plan = (isset($checkoutData['ex_invoice']) ? $checkoutData['ex_invoice'] : '');
								}
								/** Jupiter Client Type **/
								$gsm = ($postpaidCollection->getDesignSimNumber() == 1) ? 'KEEP' : 'NEW';
								$finalValidation = $postpaidCollection->getDesignTeExistingNumberFinalValidation();
								if (isset($finalValidation)) {
									if ($finalValidation == 3 && $gsm == 'KEEP') {
										$jupiterClientType = "MNP";
									}else if ($finalValidation == 1) {
										$jupiterClientType = "migration";
									}else if($gsm == 'NEW'){
										$jupiterClientType = "new number";
									}else{
										$jupiterClientType = '';
									}
								}
								/** IEW Advantage **/
								$iewAdvantage = $this->iewAdvantage($orderItem);
								$postPaidprice  	= round($originalPrice,2);
								$sohosegment     	= $this->checkSOHO($order->getIncrementId());
								$ogoneTid 			= $this->ogoneData($order->getId());
								try {										
									$row [] = [												
										'created_date' => ($exportType == 'admin-download' ? $created_date_formatted: $created_date),
										'created_time' => $created_time,
										'uc_order_id' => $order->getIncrementId(),
										'order_status' => $orderStatus,
										'order_total' => $order->getGrandTotal(),
										'product_count' => $order->getData('total_qty_ordered'),
										'primary_email' => $order->getCustomerEmail(),
										'delivery_first_name' => str_replace("'", "\\'",$billingAddress->getFirstname()),
										'delivery_last_name' => str_replace("'", "\\'",$billingAddress->getLastname()),
										'payment_method' => $order->getPayment()->getMethod(),
										'host' => $order->getRemoteIp(),
										'sim_activated' => $order->getData('Active'),
										'product' => str_replace("'", "\\'",ltrim($this->productData($orderItem, 'prod'), ' ')),
										'model' => $this->getmodel($orderItem),
										'qty' => $orderItem->getQtyOrdered(),
										'cost' => round($subscriptionAmount,2),
										'price' => round($originalPrice,2),
										'nid' => $orderItem->getProductId(),
										'catalogue' => $productLob,
										'subsidy_device_name' => $this->productData($orderItem, 'nint'),
										'language' => (($order->getStoreId() == 1) ? 'nl' : 'fr'),
										'title' => (($billingAddress->getPrefix() == 1) ? 'Mr.' : 'Mrs.'),
										'zipcity' => str_replace("'", "\\'",$billingAddress->getPostcode() . ' ' . strtoupper($billingAddress->getCity())),
										'street' => str_replace("'", "\\'",strtoupper((isset($checkoutData['b_street']) ? $checkoutData['b_street'] : ''))),
										'house_number' => (isset($checkoutData['b_number']) ? $checkoutData['b_number'] : ''),
										'bus' => $bbox,
										'optin' => (isset($checkoutData['offer_subscription']) && $checkoutData['offer_subscription']!='' ? 1 : 0),
										'gsm_number' => (($postpaidCollection->getDesignSimNumber() == 1) ? 'KEEP' : 'NEW'),
										'current_operator' => (($postpaidCollection->getDesignTeExistingNumberFinalValidation() != 3) ? '' : $postpaidCollection->getSubscriptionType()),
										'current_gsm_number' => $this->getgsm($postpaidCollection->getDesignTeExistingNumber()),
										'current_sim_number' => (($postpaidCollection->getDesignTeExistingNumberFinalValidation() != 3) ? '' : $postpaidCollection->getSimcardNumber()),
										'current_tarrif_plan' => $this->getTariffPlan($postpaidCollection->getCurrentOperator(),$postpaidCollection->getDesignSimNumber(),$postpaidCollection->getDesignTeExistingNumberFinalValidation()),
										'current_customer_number' => (($postpaidCollection->getDesignTeExistingNumberFinalValidation() != 3) ? '' : $postpaidCollection->getNetworkCustomerNumber()),
										'are_you_the_owner_of_the_account' => $this->getcustomernumber($postpaidCollection->getCurrentOperator(),$postpaidCollection->getDesignTeExistingNumberFinalValidation(),$postpaidCollection->getBillInName(),$postpaidCollection->getDesignSimNumber()),
										'first_name_owner' => (($postpaidCollection->getDesignTeExistingNumberFinalValidation() != 3) ? '' : str_replace("'", "\\'",$postpaidCollection->getHolderFirstname())),
										'last_name_owner' => (($postpaidCollection->getDesignTeExistingNumberFinalValidation() != 3) ? '' : str_replace("'", "\\'",$postpaidCollection->getHoldersName())),
										'telephone_number' => $tele,
										'nationality' => $nationality,
										'place_of_birth' => (isset($checkoutData['cust_birthplace']) ? str_replace("'", "\\'",$checkoutData['cust_birthplace']) : ''),
										'date_of_birth' => (isset($checkoutData['c_dob']) ? $checkoutData['c_dob'] : ''),
										'are_you_registered_with_belgian_government' => $belgiumRegistered,
										'identity_card_number' => $id_number,
										'rijksregister_number' => $residence_number,
										'passport_number' => $passportData,
										'add_to_existing_plan' => $add_existing_plan,
										'customer_number' => (($add_existing_plan == 'yes') ? $checkoutData['cu_ex_invoice_cust_number'] : ''),
										'method_of_payment' => $paymentMethod,
										'bank_account_number' => $order->getAccountNumber(),
										'enter_vat_number' => $vatData,
										'delivery_attention_off' => str_replace("'", "\\'",$delivery_data),
										'delivery_zipcity' => str_replace("'", "\\'",$postalCity),
										'delivery_street' => str_replace("'", "\\'",$deliveryStreet),
										'delivery_house_number' => str_replace("'", "\\'",$deliveryHouse),
										'delivery_bus' => $deliveryBus,
										'shipping_total' => '0',
										'bpost_data' => '',
										'delivery_method' => $deliveryMethod,
										'invoice_owner_first_name' => str_replace("'", "\\'",$invoiceFname),
										'invoice_owner_last_name' => str_replace("'", "\\'",$invoiceLname),
										'invoice_owner_date_of_birth' => $invoiceOwnerDob,
										'invoice_owner' => $invoiceOwner,
										'business_profile' => $taxProfile,
										'business_company_name' => (isset($checkoutData['tx_profile_dropdown']) && $checkoutData['tx_profile_dropdown']!='' ? str_replace("'", "\\'",$checkoutData['company_name']) : ''),
										'business_company_vat' => (isset($checkoutData['tx_profile_dropdown']) && $checkoutData['tx_profile_dropdown']!='' ? $checkoutData['vat_number'] : ''),
										'business_company_legal' => (isset($checkoutData['tx_profile_dropdown']) && $checkoutData['tx_profile_dropdown']!='' ? $checkoutData['legal_status'] : ''),
										'customer_scoring' => $scoringResponse,
										'eid_or_rpid' => $eidOrPid,
										'scoring_decline_reason' => '',											
										'mobile_internet_client_type' => $mobileClient,
										'subsidy_advantage' => $this->jupiterLoyaltybonus($orderItem,$order->getStoreId()),
										'client_type' => $jupiterClientType,
										'iew_advantage' => $iewAdvantage,
										'reduction_on_iew' => ($iewAdvantage!='' ? $add_existing_plan : ''),
										'promo_postpaid' => $this->getNintendoAttrValueFetch($orderItem),
										'pro_pack' => $this->getpropack($postpaidCollection->getProPacks(),$order->getIncrementId()),
										'smartphone_propack' => $this->proPackDetails($postpaidCollection->getProPacks(), 'Smartphone ProPack'),
										'reduction_propack' => $this->proPackDetails($postpaidCollection->getProPacks(), 'Reduction ProPack'),
										'surf_propack' => $this->proPackDetails($postpaidCollection->getProPacks(), 'Surf ProPack'),
										'soho_segment' => $this->checkSOHO($order->getIncrementId()),
										'ogone_transaction_id' => $this->ogoneDataPostpaid($postPaidprice,$sohosegment,$bundleB2CPrice,$bundleSohoPrice,$ogoneTid,$order->getGrandTotal()),
										'general_conditions' => '1',
										'coupon' => ($order->getCouponCode()!='' ? "\t".$order->getCouponCode()."\t" : '')
									];										
								} catch(\Exception $e) {	
									$this->_logger->info($e->getMessage());
									$this->_logger->info(json_encode($row));
									$emailTempVariables['report_type'] = 'Order Report';
									$emailTempVariables['report_log'] = $e->getMessage();
									$emailTempVariables['error_description'] = $row;
									$this->_exportHelper->sendReportExceptionEmail($emailTempVariables);										
								}																											
							}
						}
						else {								
							/** BPOST Data **/								
							try {
								$bpostData = '';
								$bpostPostalLocation = (isset($checkoutData['customerPostalLocation']) ? $checkoutData['customerPostalLocation']: '');
								$bpostMethod = (isset($checkoutData['customerPostalCode']) ? $checkoutData['customerPostalCode']: '');
								$c_delivery_address = (isset($checkoutData['c_delivery_address']) ? $checkoutData['c_delivery_address'] : '');
								if ($bpostPostalLocation && $bpostMethod && $c_delivery_address == 3) {
									$data = array();
									$data ['street'] = (isset($shippingAddress['street']) ? $shippingAddress['street']: "");
									$data ['city'] = (isset($shippingAddress['city']) ? $shippingAddress['city']:"");
									$data ['postcode'] = (isset($shippingAddress['postcode']) ? $shippingAddress['postcode']: "");
									$data ['country_id'] = (isset($shippingAddress['country_id']) ? $shippingAddress['country_id']: "");
									$data ['lastname'] = (isset($shippingAddress['lastname']) ? $shippingAddress['lastname']: "");
									$data ['bpost_postal_location'] = (isset($shippingAddress['bpost_postal_location']) ? $shippingAddress['bpost_postal_location']: "");
									$data ['bpost_method'] = (isset($shippingAddress['bpost_method']) ? $shippingAddress['bpost_method']: "");
									$bpostData = str_replace("'", "\\'",json_encode($data));
									//$bpostData = "\"".$bpostData."\"";							
								}
								$row [] = [
									'created_date' => ($exportType == 'admin-download' ? $created_date_formatted: $created_date),
									'created_time' => $created_time,
									'uc_order_id' => $order->getIncrementId(),
									'order_status' => $orderStatus,
									'order_total' => $order->getGrandTotal(),
									'product_count' => $order->getData('total_qty_ordered'),
									'primary_email' => $order->getCustomerEmail(),
									'delivery_first_name' => str_replace("'", "\\'",$billingAddress->getFirstname()),
									'delivery_last_name' => str_replace("'", "\\'",$billingAddress->getLastname()),
									'payment_method' => $order->getPayment()->getMethod(),
									'host' => $order->getRemoteIp(),
									'sim_activated' => $order->getData('Active'),
									'product' => str_replace("'", "\\'",ltrim($this->productData($orderItem, 'prod'), ' ')),
									'model' => $this->getmodel($orderItem),
									'qty' => $orderItem->getQtyOrdered(),
									'cost' => round($subscriptionAmount,2),
									'price' => round($originalPrice,2),
									'nid' => $orderItem->getProductId(),
									'catalogue' => $productLob,
									'subsidy_device_name' => $this->productData($orderItem, 'nint'),
									'language' => (($order->getStoreId() == 1) ? 'nl' : 'fr'),
									'title' => (($billingAddress->getPrefix() == 1) ? 'Mr.' : 'Mrs.'),
									'zipcity' => str_replace("'", "\\'",$billingAddress->getPostcode() . ' ' . strtoupper($billingAddress->getCity())),
									'street' => str_replace("'", "\\'",strtoupper((isset($checkoutData['b_street']) ? $checkoutData['b_street'] : ''))),
									'house_number' => (isset($checkoutData['b_number']) ? $checkoutData['b_number'] : ''),
									'bus' => $bbox,
									'optin' => (isset($checkoutData['offer_subscription']) && $checkoutData['offer_subscription']!='' ? 1 : 0),
									'gsm_number' => '',
									'current_operator' => '',
									'current_gsm_number' => '',
									'current_sim_number' => '',
									'current_tarrif_plan' => '',
									'current_customer_number' => '',
									'are_you_the_owner_of_the_account' => '',
									'first_name_owner' => '',
									'last_name_owner' => '',
									'telephone_number' => $tele,
									'nationality' => '',
									'place_of_birth' => (isset($checkoutData['cust_birthplace']) ? str_replace("'", "\\'",$checkoutData['cust_birthplace']) : ''),
									'date_of_birth' => (isset($checkoutData['c_dob']) ? str_replace("'", "\\'",$checkoutData['c_dob']) : ''),
									'are_you_registered_with_belgian_government' => '',
									'identity_card_number' => '',
									'rijksregister_number' => '',
									'passport_number' => '',
									'add_to_existing_plan' => '',
									'customer_number' => '',
									'method_of_payment' => '',
									'bank_account_number' => '',
									'enter_vat_number' => $vatData,
									'delivery_attention_off' => str_replace("'", "\\'",$delivery_data),
									'delivery_zipcity' => str_replace("'", "\\'",$postalCity),
									'delivery_street' => str_replace("'", "\\'",$deliveryStreet),
									'delivery_house_number' => $deliveryHouse,
									'delivery_bus' => $deliveryBus,
									'shipping_total' => '0',
									'bpost_data' => $bpostData,
									'delivery_method' => $deliveryMethod,
									'invoice_owner_first_name' => '',
									'invoice_owner_last_name' => '',
									'invoice_owner_date_of_birth' => '',
									'invoice_owner' => '',
									'business_profile' => str_replace("'", "\\'",$taxProfile),
									'business_company_name' => (isset($checkoutData['tx_profile_dropdown']) && $checkoutData['tx_profile_dropdown']!='' ? str_replace("'", "\\'",$checkoutData['company_name']) : ''),
									'business_company_vat' => (isset($checkoutData['tx_profile_dropdown']) && $checkoutData['tx_profile_dropdown']!='' ? $checkoutData['vat_number'] : ''),
									'business_company_legal' => (isset($checkoutData['tx_profile_dropdown']) && $checkoutData['tx_profile_dropdown']!='' ? $checkoutData['legal_status'] : ''),
									'customer_scoring' => '',
									'eid_or_rpid' => '',
									'scoring_decline_reason' => '',											
									'mobile_internet_client_type' => '',
									'subsidy_advantage' => '',
									'client_type' => '',
									'iew_advantage' => '',
									'reduction_on_iew' => '',
									'promo_postpaid' => '',
									'pro_pack' => '',
									'smartphone_propack' => '',
									'reduction_propack' => '',
									'surf_propack' => '',
									'soho_segment' => $this->checkSOHO($order->getIncrementId()),
									'ogone_transaction_id' => $this->ogoneData($order->getId()),
									'general_conditions' => '1',
									'coupon' => ($order->getCouponCode()!='' ? $order->getCouponCode() : '')
								];									
							} catch(\Exception $e) {
								$this->_logger->info($e->getMessage());
								$this->_logger->info(json_encode($row));
								$emailTempVariables['report_type'] = 'Order Report';
								$emailTempVariables['report_log'] = $e->getMessage();
								$emailTempVariables['error_description'] = $row;
								$this->_exportHelper->sendReportExceptionEmail($emailTempVariables);									
							}								
						}
					endforeach;
				}
			}
		endforeach;	
		$this->_logger->info('ORDER DATA MIGRATION TO CUSTOM EXPORT COMPLETED AT '.date("Y-m-d H:i:s"));		
		$this->_exportData = $row;//Consolidated order data in array
		if($exportType == 'download' || $exportType == 'admin-download' || $exportType == 'scheduled') {
			if($reportType == 'today_report') {
				$this->_logger->info('REPORT GENERATION STARTED AT '.date("Y-m-d H:i:s"));
				$donwloadFile = $this->generateReport($fromDt, $toDt, $status, $limit, $page,$exportType,$reportType,$this->_exportData); //Generate CSV Report
				$this->_logger->info('REPORT GENERATION ENDED AT '.date("Y-m-d H:i:s"));
				return $donwloadFile;
			}
			else if(($from && $to) || $exportType == 'scheduled') {	
				$this->_logger->info('REPORT GENERATION STARTED AT '.date("Y-m-d H:i:s"));
				$dumpToExportTable = $this->dumpToExportTable($this->_exportData);
				$donwloadFile = $this->generateReport($fromDt, $toDt, $status, $limit, $page,$exportType,$reportType,$this->_exportData); //Generate CSV Report
				$this->_logger->info('REPORT GENERATION ENDED AT '.date("Y-m-d H:i:s"));
				return $donwloadFile;
			}			
		}		
		return;		
	}
	
	/**
	 * @Param OrderItem
	 * @Param StoreID
	 * @Return virtual product price from bundled product
	 */
	private function getsubsidyprice($orderItem,$storeId) 
	{
	    $pid = $orderItem->getProductId();
		$sku = $orderItem->getSku();
        $productType = $orderItem->getProductType();
        if ($productType == 'bundle') {
			$sku = $this->getmodel($orderItem);
			$virtualProductId =  $this->getSKUProductId($sku);
			$childItems = $orderItem->getChildrenItems();
			$productChildsku = '';
			$productChildprice = '';
			$productChildprice = $this->getVirtualPrice($virtualProductId,$storeId);
			return $productChildprice;
        } 
		else {
			return '';
		}
    }
	
	/**
	 * @Param ProductId
	 * @Param StoreId
	 * @Return Subscription amount
	 */
	private function getVirtualPrice($pid,$storeId) 
	{
		$price = '';				
		$query = "SELECT b.value FROM eav_attribute a,catalog_product_entity_decimal b  
					WHERE a.attribute_code = 'subscription_amount' and a.attribute_id = b.attribute_id 
					and b.row_id = '".$pid."' and b.store_id='".$storeId."'";
        $response = $this->connectionEst()->fetchRow($query);
		if (isset($response['value'])) {
			$price = $response['value'];
		} 
		else {
			$query = "SELECT b.value FROM eav_attribute a,catalog_product_entity_decimal b  
						WHERE a.attribute_code = 'subscription_amount' and a.attribute_id = b.attribute_id 
						and b.row_id = '".$pid."' and b.store_id='0'";
			$response = $this->connectionEst()->fetchRow($query);
			$price = $response['value'];
		}
        return $price;
    }
	
	/**
	 * @Param OrderItem
	 * @Return SKU	 
	 */
	private function getmodel($orderItem)
	{
		$productType = $orderItem->getProductType();
        $sku = $orderItem->getSku();		
        if ($productType == 'bundle') {
			if (strpos($sku, '+') !== false) {
				$sku = explode('+',$sku);
				return $sku[1];
			}
			else{
				return $sku;
			}
		}
		else{
			return $sku;
		}
	}
	
	/** 
	 * @Param SKU
	 * @Return ProductId	 
	 */
	private function getSKUProductId($sku) {
        $col1 = "SELECT * FROM catalog_product_entity WHERE sku ='" . trim($sku) . "'";
        $ret1 = $this->connectionEst()->fetchAll($col1);
		if (isset($ret1[0]['entity_id'])) {
			return $ret1[0]['entity_id'];
		} else {
			return '';
		}
        
    }
	
	/**
	 * @Return Connection Object
	 * Establish Database connection
	 */
	private function connectionEst() {
        $resource = $this->resourceEst();
        return $resource->getConnection();
    }
	
	/**
	 * @Return Database resource object
	 * Create Resource for database
	 */
	private function resourceEst() {
		$resource = $this->objectManagerInt()->get('Magento\Framework\App\ResourceConnection');
		return $resource;
	}
	
	/**
	 * @Return Objectmanager instance
	 */
    private function objectManagerInt() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        return $objectManager;
    }
	
	/**
	 * @Return Array
	 * Abandoned order data
	 */
	private function boxData($val, $qid) {
        $collect1 = "SELECT * FROM orange_abandonexport_items where quote_id='" . $qid . "'";
        $rest1 = $this->connectionEst()->fetchAll($collect1);       	   
        if (count($rest1)) {
            $fstep = $rest1[0]['stepsecond'];
            $dat = unserialize($fstep);
            if (isset($dat[$val]) && $dat[$val] != '') {
                return $dat[$val];
            } 
			else {
                return '';
            }
        }
        return '';
    }
	
	/**
	 * @Return Array
	 * Quote data from Abandoned Order table
	 */
	private function getQuoteDataFromAbandoned($quoteId) {
		$query = "SELECT * FROM orange_abandonexport_items where quote_id='" . $quoteId . "'";
        $abandonedReportData = $this->connectionEst()->fetchAll($query);
		if (count($abandonedReportData)) {
			return $abandonedReportData;
		}
		else {
			return;
		}
	}
	
	private function productData($orderItem, $dat) {
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
        } 
		else {
            if ($dat == 'prod') {
                return ltrim($productName, ' ');
            } else {
                return;
            }
        }		
    }
	
	/**
	 * @Param Product Type
	 * @Return String LOB
	 * LOB of product
	 */	 
	private function getProductLOB($type) {
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
	
	/**
	 * @Param ProductSku
	 * @Return String
	 * Attribute Set of sku
	 */
	private function getSKUProduct($sku) {		
		$query = "SELECT b.attribute_set_name FROM catalog_product_entity a,eav_attribute_set b 
					WHERE a.sku ='".$sku."' and b.attribute_set_id = a.attribute_set_id";
        $response = $this->connectionEst()->fetchAll($query);
        if (count($response)) {
            return $response[0]['attribute_set_name'];
        }
		else {
			return '';
		}
    }
	
	/**
	 * @Param GSM Number
	 * @Return String
	 * Formatted value of gsm number
	 */
	private function getgsm($gsm){	
		$gsm = str_replace('/','',$gsm);
		$gsm_value = '';
		if($gsm != ''){
			$gsm_data1 = substr($gsm,0,4);
			$gsm_data2 = substr($gsm,4);
			$gsm_value = $gsm_data1."/".$gsm_data2;
		}
		return $gsm_value;
	}	
	
	/**
	 * @Param SIM Plan
	 * @Param SIM Number
	 * @Param SIM Validation
	 * @Return SIM Type
	 */
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
	
	/**
	 * @Param SIM Plan
	 * @Param SIM Validation
	 * @Param SIM Existing or new
	 * @Param SIM Design
	 */
	private function getcustomernumber($plan,$validation,$value,$designsimnumber){
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
	
	/** 
	 * @Param TaxId
	 * @Param StoreId
	 * @Param ProductLOB
	 * @Return String
	 * Tax Profile
	 */
	private function getTaxProfileData($taxId,$store,$productLob){		
		if(strtolower($productLob) == 'prepaid'){
			return '';	
		}else{
			if($store == 1){
				if($taxId == 1){
				return 'Vrij beroep met btw-nummer';
				}else if($taxId == 2){
				return 'Vrij beroep zonder btw-nummer';	
				}else if($taxId == 4){
				return 'Zelfstandige';	
				}else if($taxId == 3){
				return 'Bedrijf';	
				}else{
				return '';	
				}
			}else{
				if($taxId == 1){
				return 'Profession libérale avec numéro de TVA';
				}else if($taxId == 2){
				return 'Profession libérale sans numéro de TVA';	
				}else if($taxId == 4){
				return 'Indépendant';	
				}else if($taxId == 3){
				return 'Entreprise';	
				}else{
				return '';	
				}
			}
		}
    }
	
	/**
	 * @Param OrderItem
	 * @Param StoreId
	 * @Return Loyalty Bonus
	 */
	private function jupiterLoyaltybonus($orderItem,$storeId) {
        $pid = $orderItem->getProductId();
		$sku = $orderItem->getSku();
		$rtData = $this->getSKUProduct($sku);
        $productType = $orderItem->getProductType();
        if ($productType == 'bundle') {
			$sku = $this->getmodel($orderItem);
			$virtualProductId =  $this->getSKUProductId($sku);
            $val = $this->getNintendoAttrdValue($pid,$storeId,$virtualProductId);
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
	
	/**
	 * @Param ProductId
	 * @Param StoreId
	 * @Param TariffPlanId
	 * @Return Tariff Duration
	 */
	private function getNintendoAttrdValue($pid,$storeId,$virtualProductId) {
		$bundledIds1 = $this->getBundledProductOptionDetails($pid);
        if (!$virtualProductId) {		
			$virtualProductId = $this->getVirtualProductInBundle($bundledIds1);
		}
        $attrId1 = $this->getAttributeIdData('subsidy_duration');
		return $this->getDurationData($virtualProductId, $attrId1,$storeId);
    }
	
	/**
	 * @Param ProductId	  
	 * @Return Tariff Plan Id
	 */
	private function getBundledProductOptionDetails($id) {
        $col = "SELECT product_id FROM catalog_product_bundle_selection WHERE parent_product_id = '" . $id . "'";
        $ret = $this->connectionEst()->fetchAll($col);
        $id = array();
        foreach ($ret as $key => $vid) {
            $id[$key] = $vid['product_id'];
        }
        return $id;
    }
	
	/**
	 * @Param Nintendo Ids
	 * @Return Tariff Plan Ids
	 */
	private function getVirtualProductInBundle($bundledIds) {
        $id = '';
        foreach ($bundledIds as $vid) {
            $col1 = "SELECT entity_id FROM catalog_product_entity WHERE entity_id = '" . $vid . "'";
            $ret1 = $this->connectionEst()->fetchAll($col1);
            if ($ret1[0]['type_id'] == 'virtual') {
                $id = $ret1[0]['entity_id'];
            }
        }
        return $id;
    }
	
	/** 
	 * @Param AttributeCode
	 * @Return AttributeId
	 */
	private function getAttributeIdData($data) {
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
	
	/**
	 * @Param Tariff Plan Id
	 * @Param Duration Attribute ID
	 * @Param StoreID
	 * @Return Subsidy Duration
	 */
	private function getDurationData($virtualID1, $attrId1,$storeId){
	
		$col2 = "SELECT value FROM catalog_product_entity_varchar WHERE attribute_id = '" . $attrId1 . "' AND row_id='" . $virtualID1 . "' AND store_id = '".$storeId."'";
        $ret2 = $this->connectionEst()->fetchAll($col2);
        $durationValue = '';
		if(isset($ret2[0]['value'])){
			return $durationValue = $ret2[0]['value'];
		}
		if (!$durationValue) {
			$col2 = "SELECT value FROM catalog_product_entity_varchar WHERE attribute_id = '" . $attrId1 . "' AND row_id='" . $virtualID1 . "' AND store_id = 0";
			$ret2 = $this->connectionEst()->fetchAll($col2);
			$durationValue = '';
			if(isset($ret2[0]['value'])){
				return $durationValue = $ret2[0]['value'];
			} else {
				return $durationValue;
			}
		}	
	}
	
	/**
	 * @Param OrderItem
	 * @Return Advantage Plan
	 */
	private function iewAdvantage($orderItem) {
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
	
	/**
	 * @Param Propack 
	 * @Param OrderIncrementId
	 * @Return PropackData
	 */	 
	private function getpropack($propacks,$orderIncrementId){
		
		$soho = $this->checkSOHO($orderIncrementId);
		$propack = '';
		if($soho == 1 && $propacks != ''){
			 $count = count(explode(",",$propacks));
			 if($count > 0){
				 $propack = $count;
			 }
		}
		return $propack;
	}
	
	/**
	 * @Param OrderID
	 * @Return Flag for SOHO
	 */
	private function checkSOHO($id) {
		$query = "select b.customer_group_code from sales_order_grid a,customer_group b 
					where a.increment_id = '" . $id . "' and  a.customer_group = b.customer_group_id";					
        $response = $this->connectionEst()->fetchAll($query);
        if (count($response)) {
			$data = $response[0]['customer_group_code'];
			if ($data == 'SOHO') {
				return 1;
			}
        }
        return '';
    }
	
	/**
	 * @Param Propack from order
	 * @Param Propack Text
	 * Retrun Flag for propack exist in order
	 */
	private function proPackDetails($pr, $val) {
        $data = explode(',', $pr);
        if (in_array($val, $data)) {
            return 1;
        }
        return '';
    }
	
	/**
	 * @Param Postpaid tariff amount
	 * @Param SOHO segment
	 * @Param Nintendo B2C price
	 * @Param Nintendo SOHO Price
	 * @Param Ogone Transaction ID
	 * @Param Order Total
	 * @Return Transaction ID
	 */
	private function ogoneDataPostpaid($postPaidprice,$sohosegment,$bundleB2CPrice,$bundleSohoPrice,$ogoneTid,$gTotal){
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
	
	/** 
	 * @Param ORder ID
	 * @Return Ogone Transaction ID
	 */
	private function ogoneData($oid) {
        $collect	= "SELECT cc_trans_id FROM sales_order_payment WHERE parent_id = '" . $oid . "'"; //Defect - 5689 "Change PAYID insted of cstrxid"
        $rest 		= $this->connectionEst()->fetchAll($collect);
		if (isset($rest[0])) {
			return $rest[0]['cc_trans_id'];
        }
        return '';
    }
	
	/**
	 * @Param Order Item
	 * @Return Order Short Description
	 */
	private function getNintendoAttrValueFetch($orderItem) {
        $id = $orderItem->getProductId();
		$sku = $orderItem->getSku();
        $typedata = $this->getSKUProduct($sku);
        $productType = $orderItem->getProductType();
		if ($productType == 'virtual' && $typedata = 'Postpaid') {            
            $attrId = $this->getAttributeIdData('web_promo');
            return $this->getShortDescData($id, $attrId);
        } else {
            return '';
        }
    }
	
	/**
	 * @Param Tariff Plan Id
	 * @Param ShortDescription Attribute ID
	 * @Return Short description data
	 */
	private function getShortDescData($virtualID, $attrId) {
        $col1 = "SELECT value FROM catalog_product_entity_varchar WHERE attribute_id = '" . $attrId . "' AND row_id='" . $virtualID . "' AND store_id = 0";
        $ret1 = $this->connectionEst()->fetchAll($col1);
		if(isset($ret1[0]['value'])){
			return $ret1[0]['value'];
		}else{
			return '';
		}            
    }
	
	/**
	 * @Return Last Insert Record from order export table
	 */
	private function getLastInsertRecord() {
		$resource = $this->resourceEst();
		$query = "SELECT uc_order_id FROM ".$resource->getTableName('orange_order_export')." ORDER BY export_id DESC LIMIT 1";	
        $result = $this->connectionEst()->fetchAll($query);
		if(isset($result[0]['uc_order_id'])){
			return $result[0]['uc_order_id'];
		}else{
			return '';
		}
		
	}
	
	/**
	 * @Param OrderId
	 * @Return flag if record exists in export table
	 */
	private function checkRecordExists($orderId) {
		$resource = $this->resourceEst();
		$query = "SELECT EXISTS(SELECT 1 FROM ".$resource->getTableName('orange_order_export')." WHERE uc_order_id =".$orderId.") AS flag";	
        $result = $this->connectionEst()->fetchAll($query);
		if(isset($result[0]['flag'])){
			return $result[0]['flag'];
		}else{
			return '';
		}				
	}
	
	/**
	 * @Param FromDate
	 * @Param ToDate
	 * @Param Status
	 * @Param Limit
	 * @Param Page
	 * @Param ExportType
	 * @Return CSV
	 * Generate and Download the report
	 */
	public function generateReport($from = NULL, $to = NULL, $status = NULL, $limit = NULL, $page = NULL, $exportType = NULL, $reportType = NULL, $exportData = NULL) {
	
		// Check current day is first day otherwise create daily report.
		$current_day 	= date('d'); 
		if($current_day == '01'){
			$fromDate = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m")-1, 1));
			$toDate = date('Y-m-d H:i:s',strtotime('-1 second',strtotime(date('m').'/01/'.date('Y'))));
			$fileDate = date("Ym", mktime(0, 0, 0, date("m")-1, 1));
		}
		else{
			$time = time();
			$fromDate = date("Y-m-01 00:00:00", $time);
			$toDate = date('Y-m-d 23:59:59', strtotime("-1 days"));
			$fileDate = date("Ym", $time);
		}				
		
		//$fromDate = '2017-11-08 00:00:00'; ############
		
		$headers = $this->getHeaders();
		
		$reportCollection = $this->getCollection();
		if($exportType == 'download' || $exportType == 'admin-download') {
			if($reportType != 'today_report') {			
				$reportFromDate = $this->_timezoneInterface
							->date(new \DateTime($from))
							->format('Y-m-d');
				$reportToDate = $this->_timezoneInterface
							->date(new \DateTime($to))
							->format('Y-m-d');			
				$reportCollection->addFieldToFilter('created_date', array('from' => $reportFromDate,'to' => $reportToDate));
				if($status) {
					$reportCollection->addFieldToFilter('order_status', array('in' => $status));
				}
				if($limit) {
					if(!$page) {
						$page = 1;
					}				
					$offset = ($page - 1) * $limit;				
					$reportCollection->getSelect()->limit($limit,$offset)
								->order(array('uc_order_id ASC'));							 		
				}
			}
			$fromFileDate = $this->_timezoneInterface
						->date(new \DateTime($from))
						->format('Ym');					
			$toFileDate =$this->_timezoneInterface
						->date(new \DateTime($to))
						->format('Ym');						
			$fileDate = $fromFileDate.'_'.$toFileDate;
			$outputFile = $this->getReport($fileDate);			
		}
		elseif($exportType == 'scheduled') {
			//Generate monthly report daily if via CRON (Can't append datas into existing)
			$outputFile = $this->getReport($fileDate);			
			$reportFromDate = $this->_timezoneInterface
						->date(new \DateTime($fromDate))
						->format('Y-m-01');
			$reportCollection->addFieldToFilter('created_date', array('from' => $reportFromDate));
			$reportCollection->getSelect()->order(array('uc_order_id ASC'));		
		}

		$handle = fopen($outputFile, 'w+');
		fputcsv($handle, $headers);//generate headers of CSV file
		if($reportType == 'today_report') {
			foreach($exportData as $report) {
				$reportData = array_values($report);
				fputcsv($handle, $reportData);			
			}
		}
		else {
			foreach($reportCollection as $report) {			
				$reportData = $report->getData();
				array_shift($reportData);
				fputcsv($handle, $reportData);			
			}
		}
		if($exportType == 'download' || $exportType == 'admin-download') {
			//Download CSV only when download parameter is sent
			$this->downloadCsv($outputFile,$exportType);
		}
		return $outputFile;
	}
	
	/**
	 * @Param Date
	 * @Return Filename with Date
	 */
	private function getReport($fileDate) {		
		$reportPath = $this->_exportHelper->getOrderReportPath();
		if (is_dir($reportPath) === false) {
			mkdir($reportPath);
			chmod($reportPath,0777);
		}		
		$reportName = "C_WebOrders_" . $fileDate . ".csv";
        $outputFile = $reportPath."/".$reportName;
		return $outputFile;
	}
	
	/**
	 * Return CSV Headers
	 */
	private function getHeaders() {		
        $heading = [
            __('created_date'),
            __('created_time'),
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
			
        ];
        return $heading;
	}
	
	/**
	 * @Param FilePath with name
	 * @Return CSV
	 */
	public function downloadCsv($file,$exportType) {
        if (file_exists($file)) {
            header('Content-Description: File Transfer');
			header('Content-Encoding: UTF-8');
            header('Content-Type: application/csv;charset=UTF-8');
            header('Content-Disposition: attachment; filename=' . basename($file));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
			echo "\xEF\xBB\xBF"; // UTF-8 BOM
            readfile($file);
			if($exportType == 'admin-download') {
				unlink($file);
			}
			exit; //Added exit for downloading file else it will redirect
        }
    }
	
	/**
	 * @Param Days
	 * @Return NULL
	 */
	public function flushOldData($days = NULL) {			
		if(!$days) {
			$days = 730; //2 Years
		}
		$expiredDate = date('Y-m-d', strtotime('today - '.(int)$days.' days'));		
		$resource = $this->resourceEst();
		$query = "Delete FROM ". $resource->getTableName('orange_order_export')." WHERE created_date < '" . $expiredDate . "'";			
        $response = $this->connectionEst()->query($query);		
        return;
	}
	
	private function dumpToExportTable($exportData) {
		try {
			$resource = $this->resourceEst();
            $tableName = $resource->getTableName('orange_order_export');
            return $this->connectionEst()->insertMultiple($tableName, $exportData);
        } catch (\Exception $e) {
            if ($e->getCode() === self::ERROR_CODE_DUPLICATE_ENTRY
                && preg_match('#SQLSTATE\[23000\]: [^:]+: 1062[^\d]#', $e->getMessage())
            ) {
                $this->_logger->info($row);
				$emailTempVariables['report_type'] = 'Order Report';
				$emailTempVariables['report_log'] = $e->getMessage();
				$emailTempVariables['error_description'] = $exportData;
				$this->_exportHelper->sendReportExceptionEmail($emailTempVariables);
            }
            throw $e;
        }
	}
}