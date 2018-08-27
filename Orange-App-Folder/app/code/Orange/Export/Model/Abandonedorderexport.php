<?php
namespace Orange\Export\Model;

class Abandonedorderexport extends \Magento\Framework\Model\AbstractModel
{
	protected $_scopeconfig;
	protected $_postpaidModel;
	protected $_resource;
	protected $_logger;
	protected $_timezoneInterface;
	protected $_directoryList;
	protected $_abandonedOrder;
	protected $_quoteFactory;
	protected $_exportHelper;	
	protected $_exportData;
	
    /**
     * Initialize resource model
     *
     * @return void
     */
    public function __construct(
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
		\Orange\Checkout\Model\Postpaid $postpaidModel,
		\Orange\Export\Logger\Logger $logger,
		\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
		\Magento\Framework\App\Filesystem\DirectoryList $directoryList,
		\Orange\Abandonexport\Model\Items $abandonedOrder,
		\Magento\Quote\Model\QuoteFactory $quoteFactory,
		\Orange\Export\Helper\Data $exportHelper
	)
    {
		$this->_scopeconfig = $scopeConfigInterface;
		$this->_postpaidModel = $postpaidModel;	
		$this->_logger = $logger;
		$this->_timezoneInterface = $timezoneInterface;
		$this->_directoryList = $directoryList;
		$this->_abandonedOrder = $abandonedOrder;
		$this->_quoteFactory = $quoteFactory;
		$this->_exportHelper = $exportHelper;
		$this->_exportData = array();
        $this->_init('Orange\Export\Model\ResourceModel\Abandonedorderexport');
    }
	
	public function setExportData(
		$fromDt = NULL, 
		$toDt = NULL, 
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
		$abandonedOrderCollection = $this->_abandonedOrder->getCollection();
		$abandonedOrderCollection->getSelect()->joinLeft(array('order' => 'sales_order'),"order.quote_id = main_table.quote_id",['orderquoteid'=>'order.quote_id','status'=>'order.status','totalqty'=>'order.total_qty_ordered']);
		if($from && $to) {	
			$this->_logger->info('ABANDONED ORDER DATA MIGRATION TO CUSTOM EXPORT STARTED AT '.date("Y-m-d H:i:s"));						
			
			$from = $from.' 00:00:00';	
			$to = $to. ' 23:59:59';
			
			$fromDate = new \DateTime($from);
			$toDate = new \DateTime($to);
			$abandonedOrderCollection->addFieldToFilter('create_at', array('from' => $fromDate,'to' => $toDate));
			if($limit) {
				if(!$page) {
					$page = 1;
				}				
				$offset = ($page - 1) * $limit;				
				$abandonedOrderCollection->getSelect()->limit($limit,$offset);
			}
		}
		else {
			$this->_logger->info('ABANDONED ORDER DATA MIGRATION TO CUSTOM EXPORT CRON JOB STARTED AT '.date("Y-m-d H:i:s"));						
			$lasInsertRecord = $this->getLastInsertRecord(); //get last order from order export table
			if($lasInsertRecord == '') {
				$lasInsertRecord = 0;
				$currentDate = date("Y-m-d 00:00:00", strtotime('today - 1 days')); // current date	
				//$currentDate = date("2017-11-28 00:00:00"); // current date	############
				$abandonedOrderCollection->addFieldToFilter('main_table.create_at', array('from' => $currentDate));				
			}
			else {			
				$abandonedOrderCollection->addFieldToFilter('main_table.quote_id', array('gt' => $lasInsertRecord));
			}
		}		
		$abandonedOrderCollection->getSelect()->group('main_table.id');
		$this->_logger->info($abandonedOrderCollection->getSelect());				
				
		$this->_logger->info('FETCHING DATA FROM ABANDONED ORDER COLLECTION');
		foreach ($abandonedOrderCollection as $abandonedOrder) {
			$isRecordExist = $this->checkRecordExists($abandonedOrder->getQuoteId()); //Check whether data already exists in export table
			if($isRecordExist == 0) {				
				if ($abandonedOrder->getStepsecond()) {
					$stepTwoData = unserialize($abandonedOrder->getStepsecond());
				} else {
					$stepTwoData = unserialize($abandonedOrder->getStepfirst());
				}
				if (!$stepTwoData || strtolower($abandonedOrder->getStatus())=="processing" || strtolower($abandonedOrder->getStatus())=="complete") {
					continue;
				}
				$quoteId = $abandonedOrder->getQuoteId();
				$quoteData = $this->_quoteFactory->create()->load($quoteId);
				if (!$quoteData->getData() || $quoteData->getItemsQty() < 1) {
					continue;
				}
				$quoteItems = $quoteData->getAllVisibleItems();
				if(count($quoteItems) < 1) {
					continue;
				}
				$created_date_formatted = $this->_timezoneInterface
									->date(new \DateTime($abandonedOrder->getCreateAt()))
									->format('d/m/Y');
				$created_date = $abandonedOrder->getCreateAt();
				$created_time = $this->_timezoneInterface
											->date(new \DateTime($abandonedOrder->getCreateAt()))
											->format('H:i:s');
				/** Get Order Status **/
				$orderStatusDet = '';
				if ($abandonedOrder->getOrderStatus()) {
				  $orderStatus = $abandonedOrder->getOrderStatus();
				} else {
					$orderStatus = $abandonedOrder->getStatus();
				}
				if ($orderStatus == "orderRefusal") {
					$orderStatusDet = "Scoring Refused";
				}
				/* @TODO remove
				/* if($orderStatus == "canceled") {
					$orderStatusDet = "Cancelled";
				} */
				if($orderStatus == "pending_payment") {
					$orderStatusDet = "Pending_Payment";
				}
				/** Customer Type **/
				$soho = '';
				$customerType = $quoteData->getCustomerGroupId();
				if($customerType == 4) {
					$soho = 1;
				}
				/** BUS **/
				$bbox = (isset($stepTwoData['b_box']) ? $stepTwoData['b_box'] : '');
				
				foreach ($quoteItems as $quoteItem) {
					$productLob = $this->getProductLOB($quoteItem);
						
					/** Fetch the postpaid details if any exists for the item */
					$originalPrice = $quoteItem->getPrice(); //Get SOHO Price for B2C					
					$productType = $quoteItem->getProductType(); //Get product Type
					if ($productType == 'bundle') {
						$bundleprice = $this->getsubsidyprice($quoteItem,$quoteData->getStoreId());
					}
					if($customerType == 4) {													
						$originalPrice = $this->_exportHelper->getSohoPrice($originalPrice);//Get SOHO Price of subsidy							
						if ($productType == 'bundle') {																
							$subscriptionAmount = $this->_exportHelper->getSohoPrice($bundleprice); //Get subsciption price for soho
						}
						else{								
							$subscriptionAmount = $this->_exportHelper->getSohoPrice($quoteItem->getSubscriptionAmount()); //Get subsciption price for soho
						}						
					} 
					else {							
						if ($productType == 'bundle') {								
							$subscriptionAmount = $bundleprice;
						}
						else{
							$subscriptionAmount = $quoteItem->getSubscriptionAmount();	//Get subscription price for B2C
						}
					}
					$postpaidModelCollection = $this->_postpaidModel
												->getCollection()
												->addFieldToFilter('quote_id',$quoteId)
												->addFieldToFilter('item_id',$quoteItem->getItemId());	
					$postpaidProducts = (isset($stepTwoData['totalvirtualproduct']) ? $stepTwoData['totalvirtualproduct']: '');
					$postpaidProductCount = 0;
					if($postpaidProducts) {
					  $postpaidProducts = explode(',',$postpaidProducts);
					  $postpaidProductCount = count($postpaidProducts);
					}
					$tele = '';						
					if(isset($stepTwoData['cust_telephone']) && $stepTwoData['cust_telephone'] != '9999999999'){ 
						$tele = $stepTwoData['cust_telephone'];
					}						
					/** Residence Number **/
					$residence_number = '';
					$id_num = $stepTwoData['id_number'];
					$id_first_Char = substr($id_num, 0, 1);
					if (ctype_alpha($id_first_Char)) {
						$residence_number =  $id_num;
					} 
					/** Nationality Data **/
					$id_number = (isset($stepTwoData['id_number']) ? $stepTwoData['id_number']: '');
					$residence_number = (isset($stepTwoData['residence_number']) ? $stepTwoData['residence_number']: '');
					$passport_number = (isset($stepTwoData['passport_number']) ? $stepTwoData['passport_number']: '');
					
					$nationality = '';					
					if($productLob == 'Hardware' && $productLob == 'Accessories' && $productLob == 'Prepaid' ){
						$nationality = '';
					}
					else if($productLob != 'Prepaid' && $productLob != 'Accessories' && $productLob != 'Hardware'){
						if($passport_number != '' || $residence_number != ''){
							$nationality = "other";
						} 
						else if ($nationality == 'belgian') {
							$nationality = "belgian";
						}
					}						
					
					/** ID Number **/
					$id_number = '';
					if ($nationality == "belgian") {
						$id_num = $stepTwoData['id_number'];
						$id_first_Char = substr($id_num, 0, 1);
						if (ctype_digit($id_first_Char)) {
							$id_number = $id_num;
						} 
					}
	
					/** Belgian registered**/	
					$belgiumRegistered = '';						
					if($stepTwoData['nationality'] == 'belgian' && $stepTwoData['id_number'] != ''){
						$belgiumRegistered = 1;
					}else if($stepTwoData['nationality'] == 'other'){
						$belgiumRegistered = 0;
					}					
					
					/** EID or PID **/
					$eidOrPid = '';						
					if($id_number !=''){
						$eidOrPid = $id_number;
					}else if($residence_number != ''){
						$eidOrPid = $residence_number;
					}else if($passport_number != ''){
						$eidOrPid = $passport_number;
					}
					
					$transfertType = (isset($stepTwoData['transfer_type']) ? $stepTwoData['transfer_type']: '');
					$methodOfPaymentName = '';
					if($quoteData->getAccountNumber()) {
						$methodOfPaymentName = 'Domiciliation';
					} else {
						if ($orderStatus !='orderRefusal' && $orderStatus) {
							$methodOfPaymentName = 'Virement';
						} 
					}
					/** Billing and Shipping Data **/
					$delivery_data = '';
					$zipcity = '';
					$street = '';
					$delivery_house = '';
					$delivery_bus = '';
					$bpostPostalLocation = (isset($stepTwoData['customerPostalLocation']) ? $stepTwoData['customerPostalLocation']: '');
					$bpostMethod = (isset($stepTwoData['customerPostalCode'])? $stepTwoData['customerPostalCode']: '');
					$billingShipping = (isset($stepTwoData['c_delivery_address'])? $stepTwoData['c_delivery_address']: '');
					$deleiveryAddressInformation = (isset($stepTwoData['c_delivery_address']) ? $stepTwoData['c_delivery_address']:'');
					if($billingShipping != "2" || ($bpostPostalLocation && $bpostMethod)){
						$delivery_data = '';
					} 
					else if($billingShipping == "2"){
						$street = (isset($stepTwoData['s_street']) ? $stepTwoData['s_street'] : '');					
						$delivery_bus = (isset($stepTwoData['s_box']) ? $stepTwoData['s_box']: '');
						$delivery_house = (isset($stepTwoData['s_number']) ? $stepTwoData['s_number']: '');
						if($street != '' && $deleiveryAddressInformation == 2){
							$zipcity = $stepTwoData['s_postcode_city'] . ' ' . strtoupper(($stepTwoData['s_city'])) ? : '';	
							$street = strtoupper($stepTwoData['s_street']);
						}
						else{
							$zipcity = '';
						}
						if($delivery_house != '' && $deleiveryAddressInformation == 2){
							$delivery_house = $stepTwoData['s_number'];
						}					
						if($delivery_bus != '' && $deleiveryAddressInformation == 2){
							$delivery_bus = "\t".$delivery_bus."\t";						
						}
						$comp = strcmp($stepTwoData['b_street'], $stepTwoData['s_street']);
						if($comp){
							$result_data = $stepTwoData['s_firstname'].",".$stepTwoData['s_name'].",".$stepTwoData['s_attention_n'];
							$delivery_data = "$result_data";
						}					
					}							
					$delivery_Method = "";
					if ($deleiveryAddressInformation == 3) {
						$bpostMethod = (isset($stepTwoData['customerPostalCode'])? $stepTwoData['customerPostalCode']: '');
						$deliveryMethod = $bpostMethod = (isset($stepTwoData['deliveryMethod'])? $stepTwoData['deliveryMethod']: '');
						if ($bpostMethod) {
							if($deliveryMethod && strtolower($deliveryMethod) == "pugo") {
								$delivery_Method =  "CLICK_COLLECT_PUGO";
							}
							else {
								$delivery_Method = "CLICK_COLLECT_BPACK247";
							}
						} 							
					} 
					else if ($deleiveryAddressInformation == 2) 
					{
						$delivery_Method = "Other";
					} 
					else {
						$delivery_Method = "Current";
					}
					
					/** Invoice Data **/
					$invoiceFname = '';	
					$invoiceLname = '';
					$invoiceOwner = '';
					$invoiceOwnerDob = '';
					if($productLob == 'IEW'){
						if(isset($stepTwoData['is_teneuro_'.$quoteItem->getItemId()]) && $stepTwoData['is_teneuro_'.$quoteItem->getItemId()] == 'yes'){ 
							if(isset($stepTwoData['iew_first_name_'.$quoteItem->getItemId()]) && $stepTwoData['iew_first_name_'.$quoteItem->getItemId()] != '' && $stepTwoData['iew_contract_'.$quoteItem->getItemId()] == '0'){
								$invoiceFname = $stepTwoData['iew_first_name_'.$quoteItem->getItemId()];
							}
							if(isset($stepTwoData['iew_last_name_'.$quoteItem->getItemId()]) && $stepTwoData['iew_last_name_'.$quoteItem->getItemId()] != '' && $stepTwoData['iew_contract_'.$quoteItem->getItemId()] == '0'){
								$invoiceLname = $stepTwoData['iew_last_name_'.$quoteItem->getItemId()];
							}
							if (isset($stepTwoData['iew_contract_'.$quoteItem->getItemId()]) && $stepTwoData['iew_contract_'.$quoteItem->getItemId()] == '0') {
								if($stepTwoData['iew_first_name_'.$quoteItem->getItemId()] != ''){
									$invoiceOwner = 0;
								} else{
									$invoiceOwner = 1;
								}
							}
						}
						if(isset($stepTwoData['is_teneuro_'.$quoteItem->getItemId()]) && $stepTwoData['is_teneuro_'.$quoteItem->getItemId()] == 'yes' && $invoiceOwner =='0'){ 
							$invoiceOwnerDob = $stepTwoData['iew_dob_'.$quoteItem->getItemId()];
							if($invoiceOwnerDob != ''){
								$invoiceOwnerDob = date('d/m/Y', strtotime($invoiceOwnerDob));
							}
						}
					}
					else{
						if(isset($stepTwoData['ex_invoice']) && $stepTwoData['ex_invoice'] == 'yes'){
							if($stepTwoData['cu_ex_invoice_cust_surname'] != ''){
								$invoiceFname = $stepTwoData['cu_ex_invoice_cust_surname'];
								$invoiceOwner = 0;
							}
							else {
								$invoiceOwner = 1;
							}
							if(isset($stepTwoData['cu_ex_invoice_cust_firstname']) && $stepTwoData['cu_ex_invoice_cust_firstname'] != ''){
								$invoiceLname = $stepTwoData['cu_ex_invoice_cust_firstname']; 
							}
							if(isset($stepTwoData['cu_ex_invoice_cust_dob']) && $stepTwoData['cu_ex_invoice_cust_dob'] != ''){
								$invoiceOwnerDob = $stepTwoData['cu_ex_invoice_cust_dob'];
								if($invoiceOwnerDob != ''){
									$invoiceOwnerDob = date('d/m/Y', strtotime($invoiceOwnerDob));
								}
							}
						}
					}		
					/** TAX Profile **/
					$profile = (isset($stepTwoData['tx_profile_dropdown']) ? $stepTwoData['tx_profile_dropdown']: '');
					$taxProfile = $this->getTaxProfileData($profile,$quoteData->getStoreId(),$quoteId,$productLob);
					/** Company Data **/
					$companyName = '';
					$vatNumber = '';
					$companyLegal = '';
					$iIndCcopm = (isset($stepTwoData['i_ind_copm']) ? $stepTwoData['i_ind_copm']: '');
					$txProfileDropdown = (isset($stepTwoData['tx_profile_dropdown']) ? $stepTwoData['tx_profile_dropdown']: ''); 
					if ($iIndCcopm == "1" && $txProfileDropdown && $txProfileDropdown=="3") {
						$companyName =	$stepTwoData['company_name'];					
					}
					/** VAT **/				
					if ($iIndCcopm == "1" && $txProfileDropdown && $txProfileDropdown!="2") {
						$vatNumber = $stepTwoData['vat_number'];					
					}
					/** Legal **/				
					if ($iIndCcopm == "1" && $txProfileDropdown) {
						$legalStatus =	$stepTwoData['legal_status'];
						if ($legalStatus == "Associations") {
							$companyLegal = "LandbouwVennootschap";
						}					
					}
					/** Scoring **/
					$scoreData = $quoteData->getScoreData();
					$scoringDeclineReasonValue = '';
					$customerScoring = '';
					if($scoreData) {				   
						$scoringDeclineReason = unserialize($scoreData);
						if(isset($scoringDeclineReason['scoringReason'])) {
							$scoringDeclineReasonValue = $scoringDeclineReason['scoringReason'];
						}
						
						if (isset($scoringDeclineReason['scoringResult'])) {
							$customerScoring = $scoringDeclineReason['scoringResult'];
						}
						
						if(isset($scoringDeclineReason['scoringResult']) && ($scoringDeclineReason['scoringResult']=="ACC" || $scoringDeclineReason['scoringResult']=="REF")) {
							$scoringDeclineReasonValue = '';
						}									
					}
					
					/** Mobile Client **/
					$mobileClient = '';
					if($productLob == 'bundle')
					{	
						$rtData = $this->getSKUProduct(trim($this->getmodel($quoteItem)));
						if ($rtData == 'IEW') {
							$mobileClient = "new IEW client";
						}						
					}
					else
					{
						$rtData = $this->getSKUProduct($quoteItem->getSku());
						if ($rtData == 'IEW') {
							$mobileClient = "new IEW client";
						}						
					}											
					$query = '';
					if ($postpaidModelCollection->count() > 0 ) {
						$propackCount = 0;
						$smartphoneProPack = '';
						$reductionProPack = '';
						$surfProPack = '';
						if($postpaidProductCount > 0) {
							foreach($postpaidProducts as $postpaidProduct) {
								$QuoteItemPostpaid = explode("_",$postpaidProduct);
								if(isset($QuoteItemPostpaid['0']) && $QuoteItemPostpaid['0'] != $quoteItem->getId()) {
								 continue;
								}
								if ($soho == "1") {
									$propack = (isset($stepTwoData['propack_'.$postpaidProduct.'[]']) ? $stepTwoData['propack_'.$postpaidProduct.'[]']: '');
									if ($propack == "Smartphone Propack") {
										$smartphoneProPack = '1';
										$propackCount++;
									} else if ($propack == "Reduction Propack"){
									   $reductionProPack = '1';
									   $propackCount++;
									} else if ($propack == "Surf Propack"){
										$surfProPack = '1';
										$propackCount++;
									}
								}
							}
						}
						if ($propackCount == 0) {
							$propackCount = '';
						}							
						if($postpaidProductCount > 0) {
							foreach($postpaidProducts as $postpaidProduct) {
								try {
									$quoteItemPostpaid = explode("_",$postpaidProduct);
									if(isset($quoteItemPostpaid['0']) && $quoteItemPostpaid['0'] != $quoteItem->getId()) {							 
										continue;
									} 
									$simNumber = (isset($stepTwoData['design_sim_number-'.$postpaidProduct]) ? $stepTwoData['design_sim_number-'.$postpaidProduct]: '');
									$gsmNumber = '';
									if ($simNumber == 1):
										$gsmNumber = 'KEEP';
									elseif ($simNumber == 2):
										$gsmNumber = 'NEW';
									endif;
									$addToExistingPlan = (isset($stepTwoData['ex_invoice']) ? $stepTwoData['ex_invoice']: '');
									$iewItems = (isset($stepTwoData['iew_items']) ? $stepTwoData['iew_items']: '');
									if ($iewItems) {
										$iewarrays = explode(',',$iewItems);
										if(in_array($quoteItem->getId(),$iewarrays)) {
											$addToExistingPlan = $stepTwoData['is_teneuro_'.$quoteItem->getId()];
										}
									}
									$designTeExistingNumberFinalValidation = (isset($stepTwoData['design_te_existing_number_final_validation-'.$postpaidProduct]) ? $stepTwoData['design_te_existing_number_final_validation-'.$postpaidProduct]: '');
									$subscriptionType        = '';
									$designTeExistingNumber = '';
									$currentOperator = '';
									$networkCustomerNumber = '';
									$simcardNumber = '';
									$billInName = '';
									$holdersName = '';
									$holderName = '';
									if ($designTeExistingNumberFinalValidation == '3') {
										$subscriptionType        =  (isset($stepTwoData['subscription_type-'.$postpaidProduct]) ? $stepTwoData['subscription_type-'.$postpaidProduct]: '');
										$designTeExistingNumber =  (isset($stepTwoData['design_te_existing_number-'.$postpaidProduct]) ? $stepTwoData['design_te_existing_number-'.$postpaidProduct]: '');
										$currentOperator =  (isset($stepTwoData['current_operator-'.$postpaidProduct]) ? $stepTwoData['current_operator-'.$postpaidProduct]:'');
										$networkCustomerNumber =  (isset($stepTwoData['network_customer_number-'.$postpaidProduct]) ? $stepTwoData['network_customer_number-'.$postpaidProduct]: '');
										$simcardNumber =  (isset($stepTwoData['simcard_number-'.$postpaidProduct]) ? $stepTwoData['simcard_number-'.$postpaidProduct]: '');
										if($simcardNumber) {
											$simcardNumber = "\t".$simcardNumber."\t";
										}
										if($networkCustomerNumber) {
											$networkCustomerNumber = "\t".$networkCustomerNumber."\t";
										}
										$billInName = (isset($stepTwoData['bill_in_name-'.$postpaidProduct]) ? $stepTwoData['bill_in_name-'.$postpaidProduct]: '');
										$holdersName = (isset($stepTwoData['holders_name-'.$postpaidProduct]) ? $stepTwoData['holders_name-'.$postpaidProduct]: '');
										$holderName = (isset($stepTwoData['holder_name-'.$postpaidProduct]) ? $stepTwoData['holder_name-'.$postpaidProduct]: '');
									}
									$cuExInvoiceCustNumber = (isset($stepTwoData['cu_ex_invoice_cust_number']) ?$stepTwoData['cu_ex_invoice_cust_number']:'');
									$cuExIEWIInvoiceCustNumber = (isset($stepTwoData['iew_telephone_'.$quoteItem->getId()]) ? $stepTwoData['iew_telephone_'.$quoteItem->getId()]:'' );
									if ($cuExIEWIInvoiceCustNumber) {
										$cuExInvoiceCustNumber = $cuExIEWIInvoiceCustNumber;
									}
									if($cuExInvoiceCustNumber && strtolower($addToExistingPlan) == 'yes') {
										$cuExInvoiceCustNumber = "\t".$cuExInvoiceCustNumber."\t";
									}
									else {
										$cuExInvoiceCustNumber = '';
									}
									/** Jupiter Client Type **/
									$gsm = (($gsmNumber == 1) ? 'KEEP' : 'NEW');
									$finalValidation = $designTeExistingNumberFinalValidation;
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
									$iewAdvantage = $this->iewAdvantage($quoteItem);
																		
										$row [] = [	
											'quote_id' => $quoteId,
											'created_date' => ($exportType == 'admin-download' ? $created_date_formatted: $created_date),
											'created_time' => $created_time,
											'order_status' => $orderStatusDet,
											'order_total' => $quoteData->getGrandTotal(),
											'product_count' => $quoteData->getItemsQty(),
											'primary_email' => $stepTwoData['email'],
											'delivery_first_name' => str_replace("'", "\\'",$stepTwoData['first_name']),
											'delivery_last_name' => str_replace("'", "\\'",$stepTwoData['last_name']),
											'payment_method' => $quoteData->getPayment()->getMethod(),
											'host' => $quoteData->getRemoteIp(),
											'sim_activated' => '0',
											'product' => str_replace("'", "\\'",ltrim($this->productData($quoteItem, 'prod'), '')),
											'model' => $this->getmodel($quoteItem),
											'qty' => '1',
											'cost' => round($subscriptionAmount,2),
											'price' => round($originalPrice,2),
											'nid' => $quoteItem->getProductId(),
											'catalogue' => $productLob,
											'subsidy_device_name' => str_replace("'", "\\'",$this->productData($quoteItem, 'nint')),
											'language' => (($quoteData->getStoreId() == 1) ? 'nl' : 'fr'),
											'title' => $this->getNamePrefix($stepTwoData['gender']),
											'zipcity' => str_replace("'", "\\'",$stepTwoData['b_postcode_city']." ".$stepTwoData['b_city']),
											'street' => str_replace("'", "\\'", strtoupper($stepTwoData['b_street'])),
											'house_number' => $stepTwoData['b_number'],
											'bus' => $bbox,
											'optin' => (isset($stepTwoData['offer_subscription']) && $stepTwoData['offer_subscription']!='' ? 1 : 0),
											'gsm_number' => $gsmNumber,
											'current_operator' => $subscriptionType,
											'current_gsm_number' => $this->getgsm($designTeExistingNumber),
											'current_sim_number' => $simcardNumber,
											'current_tarrif_plan' => $this->getTariffPlan($currentOperator,$simNumber,$designTeExistingNumberFinalValidation),
											'current_customer_number' => $networkCustomerNumber,
											'are_you_the_owner_of_the_account' => $this->getcustomernumber($currentOperator,$designTeExistingNumberFinalValidation,$billInName,$simNumber),
											'first_name_owner' => str_replace("'", "\\'",$holdersName),
											'last_name_owner' => str_replace("'", "\\'",$holderName),
											'telephone_number' => $tele,
											'nationality' => str_replace("'", "\\'",$nationality),
											'place_of_birth' => str_replace("'", "\\'",$stepTwoData['cust_birthplace']),
											'date_of_birth' => str_replace("'", "\\'",$stepTwoData['c_dob']),
											'are_you_registered_with_belgian_government' => str_replace("'", "\\'",$belgiumRegistered),
											'identity_card_number' => $id_number,
											'rijksregister_number' => $residence_number,
											'passport_number' => (isset($stepTwoData['passport_number']) ? $stepTwoData['passport_number'] : " "),
											'add_to_existing_plan' => $addToExistingPlan,
											'customer_number' => $cuExInvoiceCustNumber,
											'method_of_payment' => $methodOfPaymentName,
											'bank_account_number' => $quoteData->getAccountNumber(),
											'enter_vat_number' => (isset($stepTwoData['i_ind_copm'])? 1 : " "),
											'delivery_attention_off' => str_replace("'", "\\'",$delivery_data),
											'delivery_zipcity' => str_replace("'", "\\'",$zipcity),
											'delivery_street' => str_replace("'", "\\'",strtoupper($street)),
											'delivery_house_number' => str_replace("'", "\\'",$delivery_house),
											'delivery_bus' => $delivery_bus,
											'shipping_total' => '0',
											'bpost_data' => '',
											'delivery_method' => str_replace("'", "\\'",$delivery_Method),
											'invoice_owner_first_name' => str_replace("'", "\\'",$invoiceFname),
											'invoice_owner_last_name' => str_replace("'", "\\'",$invoiceLname),
											'invoice_owner_date_of_birth' => str_replace("'", "\\'",$invoiceOwnerDob),
											'invoice_owner' => str_replace("'", "\\'",$invoiceOwner),
											'business_profile' => $taxProfile,
											'business_company_name' => str_replace("'", "\\'",$companyName),
											'business_company_vat' => $vatNumber,
											'business_company_legal' => $companyLegal,
											'customer_scoring' => $customerScoring,
											'eid_or_rpid' => $eidOrPid,
											'scoring_decline_reason' => $scoringDeclineReasonValue,
											'mobile_internet_client_type' => $mobileClient,
											'subsidy_advantage' => $this->jupiterLoyaltybonus($quoteItem,$quoteData->getStoreId()),
											'client_type' => $jupiterClientType,
											'iew_advantage' => $iewAdvantage,
											'reduction_on_iew' => (($iewAdvantage !='') ? 'Yes': ''),
											'promo_postpaid' => str_replace("'", "\\'",$this->getNintendoAttrValueFetch($quoteItem)),
											'pro_pack' => $propackCount,
											'smartphone_propack' => $smartphoneProPack,
											'reduction_propack' => $reductionProPack,
											'surf_propack' => $surfProPack,
											'soho_segment' => $soho,
											'ogone_transaction_id' => '',
											'general_conditions' => (isset($stepTwoData['terms_condition']) ? $stepTwoData['terms_condition']: ''),
											'coupon' => $quoteData->getCouponCode()
										];															
									
								} catch(\Exception $e) {											
									$this->_logger->info($e->getMessage());
									$this->_logger->info(json_encode($row));
									$emailTempVariables['report_type'] = 'Abandoned Order Report';
									$emailTempVariables['report_log'] = $e->getMessage();
									$emailTempVariables['error_description'] = $row;
									$this->_exportHelper->sendReportExceptionEmail($emailTempVariables);																	
								}
							}
						}
					}
					else {
						try {
							$bpostData = '';
							$bpostPostalLocation = $stepTwoData['customerPostalLocation'];
							$bpostMethod = $stepTwoData['customerPostalCode'];
							$c_delivery_address = (isset($stepTwoData['c_delivery_address']) ? $stepTwoData['c_delivery_address'] : '');
							if ($bpostPostalLocation && $bpostMethod && $c_delivery_address == 3) {
								$data = array();
								$data ['street'] = ($stepTwoData['customerStreet']) ? : "";
								$data ['city'] = ($stepTwoData['customerCity']) ? : '';
								$data ['postcode'] = ($stepTwoData['customerPostalLocation']) ? : '';
								$data ['country_id'] = 'BE';
								$data ['lastname'] = ($stepTwoData['customerLastName']) ? : '';
								$data ['bpost_postal_location'] = ($stepTwoData['customerPostalLocation']) ? : '';
								$data ['bpost_method'] = '';
								$bpostData = json_encode($data);
								//$bpostData = "\"".$bpostData."\"";							
							}								
							$row [] = [
								'quote_id' => $quoteId,
								'created_date' => ($exportType == 'admin-download' ? $created_date_formatted: $created_date),
								'created_time' => $created_time,
								'order_status' => $orderStatus,
								'order_total' => $quoteData->getGrandTotal(),
								'product_count' => $quoteData->getItemsQty(),
								'primary_email' => $stepTwoData['email'],
								'delivery_first_name' => str_replace("'", "\\'",$stepTwoData['first_name']),
								'delivery_last_name' => str_replace("'", "\\'",$stepTwoData['last_name']),
								'payment_method' => $quoteData->getPayment()->getMethod(),
								'host' => $quoteData->getRemoteIp(),
								'sim_activated' => '0',
								'product' => str_replace("'", "\\'",ltrim($this->productData($quoteItem, 'prod'), '')),
								'model' => $this->getmodel($quoteItem),
								'qty' => '1',
								'cost' => round($subscriptionAmount,2),
								'price' => round($originalPrice,2),
								'nid' => $quoteItem->getProductId(),
								'catalogue' => $productLob,
								'subsidy_device_name' => str_replace("'", "\\'",$this->productData($quoteItem, 'nint')),
								'language' => (($quoteData->getStoreId() == 1) ? 'nl' : 'fr'),
								'title' => $this->getNamePrefix($stepTwoData['gender']),
								'zipcity' => str_replace("'", "\\'",$stepTwoData['b_postcode_city']." ".$stepTwoData['b_city']),
								'street' => str_replace("'", "\\'", strtoupper($stepTwoData['b_street'])),
								'house_number' => strtoupper($stepTwoData['b_number']),
								'bus' => $bbox,
								'optin' => (isset($stepTwoData['offer_subscription']) && $stepTwoData['offer_subscription']!='' ? 1 : 0),
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
								'place_of_birth' => str_replace("'", "\\'",$stepTwoData['cust_birthplace']),
								'date_of_birth' => str_replace("'", "\\'",$stepTwoData['c_dob']),
								'are_you_registered_with_belgian_government' => '',
								'identity_card_number' => '',
								'rijksregister_number' => '',
								'passport_number' => '',
								'add_to_existing_plan' => '',
								'customer_number' => '',
								'method_of_payment' => '',
								'bank_account_number' => '',
								'enter_vat_number' => (isset($stepTwoData['i_ind_copm'])? 1 : " "),
								'delivery_attention_off' => str_replace("'", "\\'",$delivery_data),
								'delivery_zipcity' => str_replace("'", "\\'",$zipcity),
								'delivery_street' => str_replace("'", "\\'",strtoupper($street)),
								'delivery_house_number' => str_replace("'", "\\'",$delivery_house),
								'delivery_bus' => $delivery_bus,
								'shipping_total' => '0',
								'bpost_data' => str_replace("'", "\\'",$bpostData),
								'delivery_method' => str_replace("'", "\\'",$delivery_Method),
								'invoice_owner_first_name' => '',
								'invoice_owner_last_name' => '',
								'invoice_owner_date_of_birth' => '',
								'invoice_owner' => '',
								'business_profile' => $taxProfile,
								'business_company_name' => str_replace("'", "\\'",$companyName),
								'business_company_vat' => $vatNumber,
								'business_company_legal' => str_replace("'", "\\'",$companyLegal),
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
								'soho_segment' => $soho,
								'ogone_transaction_id' => '',
								'general_conditions' => (isset($stepTwoData['terms_condition']) ? $stepTwoData['terms_condition']: ''),
								'coupon' => $quoteData->getCouponCode()
							];																			
						} catch(\Exception $e) {		
							$this->_logger->info($e->getMessage());
							$this->_logger->info(json_encode($row));	
							$emailTempVariables['report_type'] = 'Abandoned Order Report';
							$emailTempVariables['report_log'] = $e->getMessage();
							$emailTempVariables['error_description'] = $row;
							$this->_exportHelper->sendReportExceptionEmail($emailTempVariables);													
						}
					}
				}
			}						
		}
		$this->_logger->info('ABANDONED ORDER DATA MIGRATION TO CUSTOM EXPORT COMPLETED AT '.date("Y-m-d H:i:s"));
		
		$this->_exportData = $row;//Consolidated abandoned order data in array		
		if($exportType == 'download' || $exportType == 'admin-download' || $exportType == 'scheduled') {
			if($reportType == 'today_report') {
				$this->_logger->info('ABANDONED REPORT GENERATION STARTED AT '.date("Y-m-d H:i:s"));
				$donwloadFile = $this->generateReport($fromDt, $toDt, $limit, $page,$exportType,$reportType,$this->_exportData);				
				return $donwloadFile;
			}
			else if(($from && $to) || $exportType == 'scheduled') {	
				$this->_logger->info('ABANDONED REPORT GENERATION STARTED AT '.date("Y-m-d H:i:s"));
				$dumpToExportTable = $this->dumpToExportTable($this->_exportData);
				$donwloadFile = $this->generateReport($fromDt, $toDt, $limit, $page,$exportType,$reportType,$this->_exportData);				
				return $donwloadFile;
			}
		}
		return;		
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
	 * @Return Last Insert Record from order export table
	 */
	private function getLastInsertRecord() {
		$resource = $this->resourceEst();
		$query = "SELECT quote_id FROM ".$resource->getTableName('orange_abandonorder_export')." ORDER BY export_id DESC LIMIT 1";	
        $result = $this->connectionEst()->fetchAll($query);
		if(isset($result[0]['quote_id'])){
			return $result[0]['quote_id'];
		}
		else {
			return '';
		}
		
	}
	
	private function getHeaders() {
        $heading = [			
            __('created_date'),
            __('created_time'),            
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
            __('coupon')
        ];

        return $heading;
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
	 * @Param quoteItem
	 * @Return SKU	 
	 */
	private function getmodel($quoteItem)
	{
		$productType = $quoteItem->getProductType();
        $sku = $quoteItem->getSku();		
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
	 * @Param quoteItem
	 * @Param StoreID
	 * @Return virtual product price from bundled product
	 */
	private function getsubsidyprice($quoteItem,$storeId) 
	{
	    $pid = $quoteItem->getProductId();
		$sku = $quoteItem->getSku();
        $productType = $quoteItem->getProductType();
        if ($productType == 'bundle') {
			$sku = $this->getmodel($quoteItem);
			$virtualProductId =  $this->getSKUProductId($sku);
			$childItems = $quoteItem->getChildrenItems();
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
	
	public function getNamePrefix($dataVal) {
        if ($dataVal == 1) {
            return "Mr.";
        } else if ($dataVal == 2) {
            return "Mrs.";
        } else if(strtolower($dataVal) == "mr") {
			return "Mr.";
		} else {
            return $dataVal;
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
	private function jupiterLoyaltybonus($quoteItem,$storeId) {
        $pid = $quoteItem->getProductId();
		$sku = $quoteItem->getSku();
		$rtData = $this->getSKUProduct($sku);
        $productType = $quoteItem->getProductType();
        if ($productType == 'bundle') {
			$sku = $this->getmodel($quoteItem);
			$virtualProductId =  $this->getSKUProductId($sku);
            $val = $this->getNintendoAttrdValue($pid,$storeId,$virtualProductId);
			$childItems = $quoteItem->getChildren();
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
	 * @Param quoteItem
	 * @Return Advantage Plan
	 */
	private function iewAdvantage($quoteItem) {
        $id = $quoteItem->getProductId();
        $productType = $quoteItem->getProductType();
        if ($productType == 'bundle') {
			$childItems = $quoteItem->getChildren();
			$productChildsku = '';
			foreach($childItems as $childs) {
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
	 * @Param quoteItem
	 * @Return Order Short Description
	 */
	private function getNintendoAttrValueFetch($quoteItem) {
        $id = $quoteItem->getProductId();
		$sku = $quoteItem->getSku();
        $typedata = $this->getSKUProduct($sku);
        $productType = $quoteItem->getProductType();
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
	 * @Param Date
	 * @Return Filename with Date
	 */
	private function getReport($fileDate) {
		//$basePath = $this->_directoryList->getRoot();
		$reportPath = $this->_exportHelper->getAbandonedOrderReportPath();
		if (is_dir($reportPath) === false) {
			mkdir($reportPath);
			chmod($reportPath,0777);
		}		
		$reportName = "Abandoned_Order_Report" . $fileDate . ".csv";
        $outputFile = $reportPath."/".$reportName;
		return $outputFile;
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
	public function generateReport($from = NULL, $to = NULL, $limit = NULL, $page = NULL, $exportType = NULL, $reportType = NULL, $exportData = NULL) {
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
		
		//$fromDate = '2018-01-02 00:00:00'; ############
		
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
				if($limit) {
					if(!$page) {
						$page = 1;
					}				
					$offset = ($page - 1) * $limit;				
					$reportCollection->getSelect()->limit($limit,$offset);										 		
				}
				$reportCollection->getSelect()->order(array('created_date ASC'));
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
			$reportCollection->getSelect()->order(array('quote_id ASC'));		
		}		
		$handle = fopen($outputFile, 'w+');
		fputcsv($handle, $headers);//generate headers of CSV file
		if($reportType == 'today_report') {
			foreach($exportData as $report) {
				array_shift($report);//Quote Id Shift
				fputcsv($handle, $report);			
			}
		}
		else {
			foreach($reportCollection as $report) {			
				$reportData = $report->getData();
				array_shift($reportData);//Quote Id Shift
				array_shift($reportData);//Autoincrement ID Shift
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
		$query = "Delete FROM ". $resource->getTableName('orange_abandonorder_export')." WHERE created_date < '" . $expiredDate . "'";			
        $response = $this->connectionEst()->query($query);		
        return;
	}
	
	/**
	 * @Param quoteId
	 * @Return flag if record exists in export table
	 */
	private function checkRecordExists($quoteId) {
		$resource = $this->resourceEst();
		$query = "SELECT EXISTS(SELECT 1 FROM ".$resource->getTableName('orange_abandonorder_export')." WHERE quote_id =".$quoteId.") AS flag";	
        $result = $this->connectionEst()->fetchAll($query);
		if(isset($result[0]['flag'])){
			return $result[0]['flag'];
		}else{
			return '';
		}				
	}
	
	/**
     * Converts incoming data to string format and escapes special characters.
     *
     * @param  $data
     * @return string
     */
    private function escapeString($data)
    {
        return htmlspecialchars((string)$data);
    }
	
	private function dumpToExportTable($exportData) {
		try {
			$resource = $this->resourceEst();
            $tableName = $resource->getTableName('orange_abandonorder_export');
            return $this->connectionEst()->insertMultiple($tableName, $exportData);
        } catch (\Exception $e) {
            if ($e->getCode() === self::ERROR_CODE_DUPLICATE_ENTRY
                && preg_match('#SQLSTATE\[23000\]: [^:]+: 1062[^\d]#', $e->getMessage())
            ) {
                $this->_logger->info($e->getMessage());
				$this->_logger->info($row);	
				$emailTempVariables['report_type'] = 'Abandoned Order Report';
				$emailTempVariables['report_log'] = $e->getMessage();
				$emailTempVariables['error_description'] = $row;
				$this->_exportHelper->sendReportExceptionEmail($emailTempVariables);	
            }
            throw $e;
        }
	}
}