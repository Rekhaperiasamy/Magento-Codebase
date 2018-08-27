<?php
/**
 * Generate Abandoned report daily, execute by cronjob
 */

namespace Orange\Seo\Controller\Download;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Quote\Model\QuoteFactory;
ini_set('memory_limit','1536M');
class Abandonedreport extends \Magento\Framework\App\Action\Action
{
    protected $_quoteFactory;
	protected $resultPageFactory;
	protected $_orderCollectionFactory;
	protected $_timezoneInterface;
	
	 public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
		\Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
		\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
		\Magento\Quote\Model\QuoteFactory $_quoteFactory
		 
    )
    {
        parent::__construct($context);
		$this->resultPageFactory = $resultPageFactory;
		$this->_orderCollectionFactory = $orderCollectionFactory;
		$this->_timezoneInterface 	= $timezoneInterface;
		$this->_quoteFactory = $_quoteFactory;
		 
    }
    public function execute()
    {
	    // Check current day is first day otherwise create daily report.
		$current_day 	= date('d'); 
		if($current_day == '01'){
			$fromDate 	= date("Y-m-d H:i:s", mktime(0, 0, 0, date("m")-1, 1));
			$toDate 	= date('Y-m-d H:i:s',strtotime('-1 second',strtotime(date('m').'/01/'.date('Y'))));
			$fname_date = date("Ym", mktime(0, 0, 0, date("m")-1, 1));
		}else{
			$time 		= time();
			$fromDate 	= date("Y-m-01 00:00:00", $time);
			$toDate 	= date('Y-m-d 23:59:59', strtotime("-1 days"));
			$fname_date = date("Ym", $time);
		}
		// Create Folder for monthly order report 
		$dir       = "common-header/abandonedreport";
		if (is_dir($dir) === false) {
			mkdir($dir);
			chmod($dir,0777);
		} 
		$filename 	= "Abandoned_Order_Report" . $fname_date . ".csv";
        $outputFile = BP . "/common-header/abandonedreport/" .$filename;
		
		// Get files from directory check any of the file is experied
		//$ExpDate 	= date("Y-m-d H:i:s", mktime(0, 0, 0, date("m")-24, 1));
		$expredate 	= date("Ym", mktime(0, 0, 0, date("m")-24, 1));
		$expredate 	= "Abandoned_Order_Report" . $expredate;
		
		$files = array_diff(scandir($dir), array('.', '..'));
		foreach($files as $file){
			$d_file = explode('.',$file);
			$d_file = $d_file[0];
			if($file < $expredate ){
				$DeleteFile = BP . "/common-header/abandonedreport/" .$file;
				if (file_exists($DeleteFile)) {
					unlink($DeleteFile); // delete experied files
				}
			}
		}
		
	    $scopeConfig = $this->objectManagerInt()->create('Magento\Framework\App\Config\ScopeConfigInterface');
		$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
		$discountSohoPrice = $scopeConfig->getValue('soho/soho_configuration/soho_discount', $storeScope);
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $abandonCartCollection = $objectManager->create('Orange\Abandonexport\Model\Items');
		$statusarray = array('processing','complete');
		$where = "status ='pending' || status ='pending_payment' || status ='cancel' || status =''";
        $items = $abandonCartCollection->getCollection()->addFieldToFilter('create_at', array('from' => $fromDate, 'to' => $toDate));
		$items->getSelect()->joinLeft(array('refer' => 'sales_order'),"refer.quote_id = main_table.quote_id",['orderquoteid'=>'refer.quote_id','status'=>'refer.status','totalqty'=>'refer.total_qty_ordered']);
		$items->getSelect()->group('main_table.id');
        $heading = $this->getHeaders();
		$handle = fopen($outputFile, 'w');
        fputcsv($handle, $heading);
		$j=1;
        foreach ($items as $order) {
		    if ($order->getStepsecond()) {
				$stepTwoData = unserialize($order->getStepsecond());
			} else {
				$stepTwoData = unserialize($order->getStepfirst());
			}
			if (!$stepTwoData || strtolower($order->getStatus())=="processing" || strtolower($order->getStatus())=="complete") {
				continue;
			}
			$quoteData = $this->_quoteFactory->create()->load($order->getQuoteId());
			if (!$quoteData->getData() || $quoteData->getItemsQty() < 1) {
				continue;
			}
			$ipAddressValue = $order->getIpaddress();
			$displayIpaddress = '';
			if($ipAddressValue) {
				$ipAddress = explode(',',$ipAddressValue);
				if(isset($ipAddress[0])) {
					$displayIpaddress = $ipAddress[0];
				}
			}
			
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
			$quoteid = $order->getQuoteId();
			$orderItems = $quoteData->getAllVisibleItems();
			if(count($orderItems) < 1) {
				continue;
			}
			
		   // print "<pre>";
			//print_r($stepTwoData);
			//print "</pre>";
			//exit;
			if ($order->getOrderStatus()) {
			  $status = $order->getOrderStatus();
			} else {
				$status = $order->getStatus();
			}
			if ($status == "orderRefusal") {
				$status = "Scoring Refused";
			}
			if($status == "canceled") {
				$status = "Cancelled";
			}
			if($status == "pending_payment") {
				$status = "Pending_Payment";
			}
			
			$soho = '';
			$customerType = $quoteData->getCustomerGroupId();
			if($customerType == 4) {
				$soho = 1;
			}
			//echo $quoteid.'here'; exit;
			$created_time = $this->_timezoneInterface
									->date(new \DateTime($order->getCreatedAt()))
									->format('H:i:s');
			
			foreach ($orderItems as $orderItem) {
				$methodOfPaymentName = '';
				$owner = '';
				if($this->getFormValue('ex_invoice', $stepTwoData) == 'yes'){
					if($this->getFormValue('cu_ex_invoice_cust_surname', $stepTwoData) != ''){
						$owner = 0;
					} else {
						$owner = 1;
					}
				} elseif($this->getFormValue('ex_invoice', $stepTwoData) == 'no') { 
					$owner = '';
				}
				if($this->getFormValue('scoringResponse', $stepTwoData) == 'ACC'){ $scoring_decline = '';}else{ $scoring_decline = $this->getFormValue('scoringResponse', $stepTwoData);}
				if($this->getFormValue('cust_telephone',$stepTwoData) != '9999999999'){ $tele = $this->getFormValue('cust_telephone',$stepTwoData);}else{ $tele = '';}
				if($customerType == 4) {
					$originalPrice = $orderItem->getPrice() / (1+($discountSohoPrice/100));
					$productType = $orderItem->getProductType();
					if ($productType == 'bundle') {
						$bundleprice = $this->getsubsidyprice($orderItem,$quoteData->getStoreId());
						$subscriptionAmount = $bundleprice / (1+($discountSohoPrice/100));
					}else{
							$subscriptionAmount = $orderItem->getSubscriptionAmount() / (1+($discountSohoPrice/100));	
						}
				} else {
					$originalPrice = $orderItem->getPrice();
					$productType = $orderItem->getProductType();
					if ($productType == 'bundle') {
						$bundleprice = $this->getsubsidyprice($orderItem,$order->getStoreId());
						$subscriptionAmount = $bundleprice;
					}else{
						$subscriptionAmount = $orderItem->getSubscriptionAmount();	
					}
				}
				$bbox = $this->getFormValue('b_box',$stepTwoData);
				if ($bbox) {
					$bbox = "\t".$bbox."\t";
				}
				$productLob = $this->getProductLOB($orderItem);
				$postpaidModel = $this->objectManagerInt()->create('Orange\Checkout\Model\Postpaid');
				
				$postpaidModelCollections = $postpaidModel->getCollection()->addFieldToFilter('quote_id',$quoteid)->addFieldToFilter('item_id',$orderItem->getItemId());
				$postpaidvalue = $this->getFormValue('totalvirtualproduct', $stepTwoData);
				$postpaidArrayCount = 0;
				if($postpaidvalue) {
				  $postpaidArray = explode(',',$postpaidvalue);
				  $postpaidArrayCount = count($postpaidArray);
				}
				
				if ($postpaidArrayCount > 0 && $postpaidModelCollections->count() > 0) {
				
					$transfertType = $this->getFormValue('transfer_type', $stepTwoData);
					if($quoteData->getAccountNumber()) {
						$methodOfPaymentName = 'Domiciliation';
					} else {
					    if ($status !='orderRefusal' && $status) {
							$methodOfPaymentName = 'Virement';
						} 
					}
					$propackCount = 0;
				    foreach($postpaidArray as $postpaidArrayss) {
						$QuoteItemPostpaid = explode("_",$postpaidArrayss);
						if(isset($QuoteItemPostpaid['0']) && $QuoteItemPostpaid['0'] != $orderItem->getId()) {
						 continue;
						}
					  if ($soho == "1") {
							$propack = $this->getFormValue('propack_'.$postpaidArrayss.'[]', $stepTwoData);
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
					if ($propackCount == 0) {
						$propackCount = '';
                    }			
					foreach($postpaidArray as $postpaidArrays) {
						$QuoteItemPostpaid = explode("_",$postpaidArrays);
						if(isset($QuoteItemPostpaid['0']) && $QuoteItemPostpaid['0'] != $orderItem->getId()) {
						 //echo $orderItem->getId().'id'.$QuoteItemPostpaid['0']; exit;
						 continue;
						} 
						
						$addToExistingPlan = $this->getFormValue('ex_invoice',$stepTwoData);
						$iewItems = $this->getFormValue('iew_items',$stepTwoData);
						if ($iewItems) {
							$iewarrays = explode(',',$iewItems);
							if(in_array($orderItem->getId(),$iewarrays)) {
								$addToExistingPlan = $this->getFormValue('is_teneuro_'.$orderItem->getId(),$stepTwoData);
							}
						}
						
					    $designSimNumber = $this->getFormValue('design_sim_number-'.$postpaidArrays, $stepTwoData);
					    $designTeExistingNumberFinalValidation = $this->getFormValue('design_te_existing_number_final_validation-'.$postpaidArrays, $stepTwoData);
						$subscriptionType        = '';
						$designTeExistingNumber = '';
						$currentOperator = '';
						$networkCustomerNumber = '';
						$simcardNumber = '';
						$billInName = '';
						$holdersName = '';
						$holderName = '';
						if ($designTeExistingNumberFinalValidation == '3') {
						   $subscriptionType        = $this->getFormValue('subscription_type-'.$postpaidArrays, $stepTwoData);
							$designTeExistingNumber = $this->getFormValue('design_te_existing_number-'.$postpaidArrays, $stepTwoData);
							$currentOperator = $this->getFormValue('current_operator-'.$postpaidArrays, $stepTwoData);
							$networkCustomerNumber = $this->getFormValue('network_customer_number-'.$postpaidArrays, $stepTwoData);
							$simcardNumber = $this->getFormValue('simcard_number-'.$postpaidArrays, $stepTwoData);
							if($simcardNumber) {
								$simcardNumber = "\t".$simcardNumber."\t";
							}
							if($networkCustomerNumber) {
								$networkCustomerNumber = "\t".$networkCustomerNumber."\t";
							}
							$billInName = $this->getFormValue('bill_in_name-'.$postpaidArrays, $stepTwoData);
							$holdersName = $this->getFormValue('holders_name-'.$postpaidArrays, $stepTwoData);
							$holderName = $this->getFormValue('holder_name-'.$postpaidArrays, $stepTwoData);
                        }	
						$smartphoneProPack = '';
						$reductionProPack = '';
						$surfProPack = '';
						if ($soho == "1") {
							$propack = $this->getFormValue('propack_'.$postpaidArrays.'[]', $stepTwoData);
							if ($propack == "Smartphone Propack") {
								$smartphoneProPack = '1';
							} else if ($propack == "Reduction Propack"){
							   $reductionProPack = '1';
							} else if ($propack == "Surf Propack"){
								$surfProPack = '1';
							}
                        } else {
							$propack = '';
                        }						
						
						$created_time = $this->_timezoneInterface
											->date(new \DateTime($order->getCreateAt()))
											->format('H:i:s');
						$cuExInvoiceCustNumber = $this->getFormValue('cu_ex_invoice_cust_number',$stepTwoData);
						$cuExIEWIInvoiceCustNumber = $this->getFormValue('iew_telephone_'.$orderItem->getId(),$stepTwoData);
						if ($cuExIEWIInvoiceCustNumber) {
							$cuExInvoiceCustNumber = $cuExIEWIInvoiceCustNumber;
						}
						if($cuExInvoiceCustNumber && strtolower($addToExistingPlan) == 'yes') {
							$cuExInvoiceCustNumber = "\t".$cuExInvoiceCustNumber."\t";
						} else {
							$cuExInvoiceCustNumber = '';
						}
						
						$row = [
					date('d/m/Y', strtotime($order->getCreateAt())),//created_date
					$created_time,//created_time
					$order->getQuoteId(),//uc_order_id
					$status,//order_status
					$quoteData->getGrandTotal(),//order_total
					$quoteData->getItemsQty(),//product_count
					$stepTwoData['email'],//primary_email
					$stepTwoData['first_name'],//delivery_first_name
					$stepTwoData['last_name'],//delivery_last_name
					$quoteData->getPayment()->getMethod(),//payment_method
					$quoteData->getRemoteIp(),//host
					'0',//sim_activated
					ltrim($this->productData($orderItem, 'prod'),' '),//product
					$this->getmodel($orderItem),//model
					'1',//qty
					round($subscriptionAmount,2),//cost
					round($originalPrice,2),//price
					$orderItem->getProductId(),//nid
					$this->getProductLOB($orderItem),//catalogue
					$this->productData($orderItem, 'nint'),//Subsidy device name
					$this->getStore($quoteData->getStoreId()),//language
					$this->getTitleDataValue($this->getFormValue('gender',$stepTwoData)),//title
					$this->getFormValue('b_postcode_city',$stepTwoData) . ' ' . strtoupper($this->getFormValue('b_city',$stepTwoData)),//zipcity
					strtoupper($this->getFormValue('b_street',$stepTwoData)),//street
					$this->getFormValue('b_number',$stepTwoData),//house_number
					$bbox,//bus
					($this->getFormValue('offer_subscription',$stepTwoData)) ? 1 : 0,//optin
					$this->getGsmNumber($designSimNumber),//gsm_number
					$subscriptionType,//current_operator
					$this->getgsm($designTeExistingNumber),//current_gsm_number
					$simcardNumber,//current_sim_number
					$this->getTariffPlan($currentOperator,$designSimNumber,$designTeExistingNumberFinalValidation),//current_tarrif_plan
					$networkCustomerNumber,//current_customer_number
					$this->getcustomernumber($currentOperator,$designTeExistingNumberFinalValidation,$billInName,$designSimNumber),//are_you_the_owner_of_the_account
					$holdersName,//first_name_owner
					$holderName,//last_name_owner
					$tele,//telephone_number
					'',//nationality
					$this->getFormValue('cust_birthplace',$stepTwoData),//Place of birth
					$this->getFormValue('c_dob',$stepTwoData),//date_of_birth
					$this->registered_with_belgian($stepTwoData),//Belgian_ID_document
					$this->idCardNumber($orderItem,$stepTwoData, 'id_no'),//identity_card_number
					$this->idCardNumber($orderItem,$stepTwoData, 'res_no'),//residence_permit_number
					$this->getpassport($stepTwoData),//passport_number
					$addToExistingPlan,//add_to_existing_plan
					$cuExInvoiceCustNumber,//customer_number
					$methodOfPaymentName,//method_of_payment
					$quoteData->getAccountNumber(),//bank_account_number
					$this->getFormValue('i_ind_copm',$stepTwoData)? 1 : '',//enter_vat_number
					$this->billingAndShipping($stepTwoData,$productLob),//delivery_attention_off
					$this->getdeliveryzipcity($stepTwoData,$productLob),//delivery_zipcity
					$this->getdeliverystreet($stepTwoData,$productLob),//delivery_street
					$this->getdeliveryhouse($stepTwoData,$productLob),//delivery_house_number
					$this->getdeliverybus($stepTwoData,$productLob),//delivery_bus
					'0',//shipping_total
					'',//bpost_data
					$this->deliverMethod($stepTwoData),//delivery_method
					$this->getinvoiceownerfname($orderItem,$stepTwoData), //invoice_owner_first_name
                    $this->getinvoiceownerlname($orderItem,$stepTwoData), //invoice_owner_last_name
                    $this->getinvoiceownerdob($orderItem,$stepTwoData), //invoice_owner_date_of_birth
                    $this->getinvoiceowner($orderItem,$stepTwoData), //invoice_owner
					$this->dropDownValue($this->getFormValue('tx_profile_dropdown',$stepTwoData),$quoteData->getStoreId(),$stepTwoData),//business_profile
					$this->getBusinessCompanyName($stepTwoData),//business_company_name
					$this->getVatNumber($stepTwoData),//business_company_vat
					$this->getLegalStatus($stepTwoData),//business_company_legal
					$customerScoring,//customer_scoring
					$this->geteid_or_rpid($stepTwoData),//eid_or_rpid
					$scoringDeclineReasonValue,//scoring_decline_reason
					$this->iew($orderItem->getSku()),//Mobile Internet Client Type
					$this->jupiterLoyaltybonus($orderItem,$quoteData->getStoreId()),//Subsidy Advantage
					$this->jupiterClientType($designTeExistingNumberFinalValidation,$designSimNumber),//Client type
					$this->iewAdvantage($orderItem),//IEW Advantage
					$this->getreduction($orderItem),//Reduction on IEW
					$this->getNintendoAttrValueFetch($orderItem),//Promo postpaid
					$propackCount,//$this->getpropack($propack,$soho),//Pro pack
					$smartphoneProPack,//Smartphone ProPack
					$reductionProPack,//Reduction ProPack
					$surfProPack,//Surf ProPack
					$soho,//SOHO segment
					'',//Ogone transaction ID
					$this->getFormValue('terms_condition',$stepTwoData),//General conditions
					"\t".$quoteData->getCouponCode()."\t",//coupon
				];
						fputcsv($handle, $row);
					}
				} else {
						$row = [
							date('d/m/Y', strtotime($order->getCreateAt())),//created_date
							$created_time,//created_time
							$order->getQuoteId(),//uc_order_id
							$status,//order_status
							$quoteData->getGrandTotal(),//order_total
							$quoteData->getItemsQty(),//product_count
							$stepTwoData['email'],//primary_email
							$stepTwoData['first_name'],//delivery_first_name
							$stepTwoData['last_name'],//delivery_last_name
							$quoteData->getPayment()->getMethod(),//payment_method
							$quoteData->getRemoteIp(),//host
							'0',//sim_activated
							ltrim($this->productData($orderItem, 'prod'),' '),//product
							$this->getmodel($orderItem),//model
							'1',//qty
							round($subscriptionAmount,2),//cost
							round($originalPrice,2),//price
							$orderItem->getProductId(),//nid
							$this->getProductLOB($orderItem),//catalogue
							$this->productData($orderItem, 'nint'),//Subsidy device name
							$this->getStore($quoteData->getStoreId()),//language
							$this->getTitleDataValue($this->getFormValue('gender',$stepTwoData)),//title
							$this->getFormValue('b_postcode_city',$stepTwoData) . ' ' . strtoupper($this->getFormValue('b_city',$stepTwoData)),//zipcity
							strtoupper($this->getFormValue('b_street',$stepTwoData)),//street
							$this->getFormValue('b_number',$stepTwoData),//house_number
							$bbox,//bus
							($this->getFormValue('offer_subscription',$stepTwoData)) ? 1 : 0,//optin
							'',//gsm_number
							'',//current_operator
							'',//current_gsm_number
							'',//current_sim_number
							'',//current_tarrif_plan
							'',//current_customer_number
							'',//are_you_the_owner_of_the_account
							'',//first_name_owner
							'',//last_name_owner
							$tele,//telephone_number
							'',//nationality
							$this->getFormValue('cust_birthplace',$stepTwoData),//Place of birth
							$this->getDateOfBirth($this->getFormValue('c_dob',$stepTwoData),$productLob),//date_of_birth
							$this->registered_with_belgian($stepTwoData),//Belgian_ID_document
							'',//identity_card_number
							'',//residence_permit_number
							'',//passport_number
							'',//add_to_existing_plan
							'',//customer_number
							'',//method_of_payment
							'',//bank_account_number
							$this->getFormValue('i_ind_copm',$stepTwoData)? 1 : '',//enter_vat_number
							$this->billingAndShipping($stepTwoData,$this->getProductLOB($orderItem)),//delivery_attention_off
							$this->getdeliveryzipcity($stepTwoData,$productLob),//delivery_zipcity
							$this->getdeliverystreet($stepTwoData,$productLob),//delivery_street
							$this->getdeliveryhouse($stepTwoData,$productLob),//delivery_house_number
							$this->getdeliverybus($stepTwoData,$productLob),//delivery_bus
							'0',//shipping_total
							$this->getBpostData($stepTwoData),//bpost_data
							$this->deliverMethod($stepTwoData),//delivery_method
							'',//invoice_owner_first_name
							'',//invoice_owner_last_name
							'',//invoice_owner_date_of_birth
							'',//invoice_owner
							$this->dropDownValue($this->getFormValue('tx_profile_dropdown',$stepTwoData),$quoteData->getStoreId(),$stepTwoData),//business_profile
							$this->getBusinessCompanyName($stepTwoData),//business_company_name
							$this->getVatNumber($stepTwoData),//business_company_vat
							$this->getLegalStatus($stepTwoData),//business_company_legal
							'',//customer_scoring
							'',//eid_or_rpid
							'',//scoring_decline_reason
							'',//Mobile Internet Client Type
							'',//Subsidy Advantage
							'',//Client type
							'',//IEW Advantage
							'',//Reduction on IEW
							'',//Promo postpaid
							'',//Pro pack
							'',//Smartphone ProPack
							'',//Reduction ProPack
							'',//Surf ProPack
							$soho,//SOHO segment
							'',//Ogone transaction ID
							$this->getFormValue('terms_condition',$stepTwoData),//General conditions
							"\t".$quoteData->getCouponCode()."\t",//coupon
						];
						fputcsv($handle, $row);
						//if($j==100){break;}
						$j++;
				}
			}
		}

			echo 'Done';
			exit;
	}
	
	private function ckeckorder($quoteid){
		$col2 = "SELECT * FROM sales_order WHERE quote_id = '" . $quoteid . "'";
        $ret2 = $this->connectionEst()->fetchAll($col2);
		$count = count($ret2);
		return $count;   
}

	public function connectionEst() {
        $resource = $this->objectManagerInt()->get('Magento\Framework\App\ResourceConnection');
        return $resource->getConnection();
    }
	
	public function objectManagerInt() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        return $objectManager;
    }
	
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
	public function downloadCsv($file) {

        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/csv');
            header('Content-Disposition: attachment; filename=' . basename($file));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            ob_clean();
            flush();
            readfile($file);

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
	
	public function getProductLOB($type) {
        $sku = $type->getSku();
        $productType = $type->getProductType();
        $typedata = strtolower($this->getSKUProduct($sku));
        if ($productType == 'virtual' && $typedata == 'postpaid') {
            $lob = 'Postpaid';
        } else if ($productType == 'simple' && ($typedata == 'prepaid' || $typedata == 'simcard')) {
            $lob = 'Prepaid';
        } else if ($productType == 'simple' && $typedata == 'handset') {
            $lob = 'Hardware';
        } else if ($productType == 'simple' && $typedata == 'accessories') {
            $lob = 'Accessories';
        } else if ($productType == 'virtual' && $typedata == 'iew') {
            $lob = 'IEW';
        } else {
            $lob = 'subsidy';
        }
		return $lob;
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
	public function getSKUProduct($sku) {
        $col1 = "SELECT * FROM catalog_product_entity WHERE sku ='" . trim($sku) . "'";
        $ret1 = $this->connectionEst()->fetchAll($col1);
        if (count($ret1)) {
            $attr_id = $ret1[0]['attribute_set_id'];
            $col2 = "SELECT * FROM eav_attribute_set WHERE attribute_set_id = '" . $attr_id . "'";
            $ret2 = $this->connectionEst()->fetchAll($col2);
            return $ret2[0]['attribute_set_name'];
        }
		
    }
	public function getStore($storeId) {
        if ($storeId == '1') {
            $store = 'nl';
        } else {
            $store = 'fr';
        }
        return $store;
    }
	public function getTitleDataValue($val) {
        if ($val == 1) {
            return "Mr.";
        } else if ($val == 2) {
            return "Mrs.";
        } else if(strtolower($val) == "mr") {
			return "Mr.";
		} else {
            return $val;
        }
    }
	public function getFormValue($field,$formvalue) {
		if (isset($formvalue[$field])) {
		  return $formvalue[$field];
		}
		return '';
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
	
	
	private function getTariffPlan($currentOperator,$gsmnumber,$finalvalidation) {
        $tariff = '';
		$gsmnumber = $this->getGsmNumber($gsmnumber);
		if($gsmnumber != 'NEW' && $finalvalidation == '3'){
			if ($currentOperator == '1') {
				$tariff = 'POSTPAID';
			} else if ($currentOperator == '2') {
				$tariff = 'Prepaid';
			}
		}
        return $tariff;
    }
	public function getNationalityData($orderitemm,$stepTwoData) {
		$lobtype = $this->getProductLOB($orderitemm);
		$nation = $this->getFormValue('nationality', $stepTwoData);
		$residence_number = $this->getFormValue('residence_number', $stepTwoData);
		$passport_number = $this->getFormValue('passport_number', $stepTwoData);
		
		if($lobtype == 'Hardware' && $lobtype == 'Accessories' && $lobtype == 'Prepaid' ){
			return '';
		}else if($lobtype != 'Prepaid' && $lobtype != 'Accessories' && $lobtype != 'Hardware'){
			if($passport_number != '' || $residence_number != ''){
            return "other";
        } else if ($nation == 'belgian') {
            return "belgian";
        }
		}else{
			return "";
		}
    }
	private function registered_with_belgian($stepTwoData){
		/* 1802 NID Project */
		if ($this->getFormValue('nationality',$stepTwoData) == 'belgian') {
			return 1;
		} else {
			return 0;
		}
	}

	public function idCardNumber($orderitemm,$stepTwoData,$type) {
		$nation = $this->getNationalityData($orderitemm,$stepTwoData);
		if ($nation == "belgian") {
		    $id_num = $this->getFormValue('id_number',$stepTwoData);
		    $id_first_Char = substr($id_num, 0, 1);
            if (($type == 'res_no') && ctype_alpha($id_first_Char)) {
				return  $id_num;
            } else if (($type == 'id_no') && ctype_digit($id_first_Char)) {
				return  $id_num;
            } else {
				return '';
            }
		}
	}
	
	public function billingAndShipping($stepTwoData, $type) {
		$delivery_data = '';
		$bpostPostalLocation = $this->getFormValue('customerPostalLocation',$stepTwoData);
		$bpostMethod = $this->getFormValue('customerPostalCode',$stepTwoData);
		$billingShipping = $this->getFormValue('c_delivery_address',$stepTwoData);
       if($billingShipping != "2" || ($bpostPostalLocation && $bpostMethod)){
			$delivery_data = '';
		} else if($billingShipping == "2"){
			$comp = strcmp($this->getFormValue('b_street',$stepTwoData), $this->getFormValue('s_street',$stepTwoData));
			
			if($comp){
				$result_data = $this->getFormValue('s_firstname',$stepTwoData).",".$this->getFormValue('s_name',$stepTwoData).",".$this->getFormValue('s_attention_n',$stepTwoData);
				$delivery_data = "$result_data";
			}else{
				//$delivery_data = 'To the attention of';
				$delivery_data = '';
			}
		
		}
		if ($delivery_data == ",,") {
			$delivery_data = '';
		}
		return $delivery_data;
    }
	public function getBpostData($stepTwoData) {
	    $bpostPostalLocation = $this->getFormValue('customerPostalLocation',$stepTwoData);
		$bpostMethod = $this->getFormValue('customerPostalCode',$stepTwoData);
		$cDeliveryAddress = $this->getFormValue('c_delivery_address',$stepTwoData);
        if ($bpostPostalLocation && $bpostMethod && $cDeliveryAddress == 3) {
            $data = array();
            $data ['street'] = ($this->getFormValue('customerStreet',$stepTwoData)) ? : '';
            $data ['city'] = ($this->getFormValue('customerCity',$stepTwoData)) ? : '';
            $data ['postcode'] = ($this->getFormValue('customerPostalLocation',$stepTwoData)) ? : '';
            $data ['country_id'] = 'BE';
            $data ['lastname'] = ($this->getFormValue('customerLastName',$stepTwoData)) ? : '';
            $data ['bpost_postal_location'] = ($this->getFormValue('customerPostalLocation',$stepTwoData)) ? : '';
            $data ['bpost_method'] = ($bpostMethod) ? : '';
            $result_data = json_encode($data);
			$result_data = "\"".$result_data."\"";
			return  $result_data;
        }
        return '';
    }
	 public function deliverMethod($stepTwoData) {
			$deleiveryAddressInformation = $this->getFormValue('c_delivery_address',$stepTwoData);
			if ($deleiveryAddressInformation == 3) {
			   $bpostMethod = $this->getFormValue('customerPostalCode',$stepTwoData);
			   $deliveryMethod = $this->getFormValue('deliveryMethod',$stepTwoData);
			   if ($bpostMethod) {
				   if($deliveryMethod && strtolower($deliveryMethod) == "pugo") {
						return "CLICK_COLLECT_PUGO";
				   } else {
						return "CLICK_COLLECT_BPACK247";
				   }
			   } else {
					return "";
			   }
			} else if ($deleiveryAddressInformation == 2) {
				return "Other";
			} else {
				return "Current";
			}
    }
	public function dropDownValue($drop,$store,$stepTwoData){
	    $iIndCcopm = $this->getFormValue('i_ind_copm', $stepTwoData);
		if (!$iIndCcopm) {
			return "";
		}
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
		
		
        if(trim($drop) == 'Selecteer' || trim($drop) == 'SÃ©lectionnez' || trim($drop) == 'selectionnez'){
            $drop = '';
        }
        return $drop;
    }
	public function customerscoring($orderItem,$stepTwoData){
		$lobtype = trim($this->getProductLOB($orderItem));
		$scoring = $this->getFormValue('scoringResponse', $stepTwoData);
		if($lobtype == 'Hardware' && $lobtype == 'Accessories' && $lobtype == 'Prepaid' ){
			return '';
		}else if($scoring == 'ACC' && $lobtype != 'Prepaid' && $lobtype != 'Accessories' && $lobtype != 'Hardware'){
			return $scoring;
		}
	}
	public function iew($sku) {
        $rtData = $this->getSKUProduct($sku);
        if ($rtData == 'IEW') {
            return "new IEW client";
        } else {
            return '';
        }
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
			$productChildsku = $this->getmodel($orderItemm);
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
	public function getNintendoAttrdValue($pid,$storeId,$virtualProductId) {
		$bundledIds1 = $this->getBundledProductOptionDetails($pid);
        if (!$virtualProductId) {		
			$virtualProductId = $this->getVirtualProductInBundle($bundledIds1);
		}
        $attrId1 = $this->getAttributeIdData('subsidy_duration');
		return $this->getDurationData($virtualProductId, $attrId1,$storeId);
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
	public function iewAdvantage($orderItem) {
        $id = $orderItem->getProductId();
        $productType = $orderItem->getProductType();
        if ($productType == 'bundle') {
			$childItems = $orderItem->getChildrenItems();
			$productChildsku = '';
			$productChildsku = $this->getmodel($orderItem);
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
	public function getShortDescData($virtualID, $attrId) {
        $col1 = "SELECT * FROM catalog_product_entity_varchar WHERE attribute_id = '" . $attrId . "' AND row_id='" . $virtualID . "' AND store_id = 0";
        $ret1 = $this->connectionEst()->fetchAll($col1);
		if(isset($ret1[0]['value'])){
			return $ret1[0]['value'];
		}else{
			return '';
		}     
       
    }
	
	public function getreduction($orderitemm){
		$iew = $this->iewAdvantage($orderitemm);
		$subsidy_iew = $this->iew($orderitemm->getSku());
		if($iew != ''){
			return 'Yes';
		} else {
			return '';
		}
	}
	public function proPackDetails($pr, $val) {
        if ($pr == $val) {
			return '1';
		}
        return '';
    }
	public function getpropack($postpaid,$soho){
		if($soho == 1 && $postpaid->getProPacks() != ''){
			return count(explode(",",$postpaid->getProPacks()));
		}else{
			return '';
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
	public function getDurationData($virtualID1, $attrId1,$storeId){
	
		$col2 = "SELECT * FROM catalog_product_entity_varchar WHERE attribute_id = '" . $attrId1 . "' AND row_id='" . $virtualID1 . "' AND store_id = '".$storeId."'";
        $ret2 = $this->connectionEst()->fetchAll($col2);
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
	public function geteid_or_rpid($stepTwoData){
		$id_number = $this->getFormValue('id_number', $stepTwoData);
        $residence_number = $this->getFormValue('residence_number',$stepTwoData);
        $passport_number = $this->getFormValue('passport_number',$stepTwoData);
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
	public function getsubsidyprice($orderItemm,$storeId) {
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
			$productChildprice = '';
			$productChildprice = $this->getVirtualPrice($virtualProductId,$storeId);
			return $productChildprice;
        }else{
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
	
	public function getdeliveryzipcity($stepTwoData,$type){
	   $bpostPostalLocation = $this->getFormValue('customerPostalLocation',$stepTwoData);
	   $bpostMethod = $this->getFormValue('customerPostalCode',$stepTwoData);
	   $billingShipping = $this->getFormValue('c_delivery_address',$stepTwoData);
       if($billingShipping != "2" || ($bpostPostalLocation && $bpostMethod)){
			$delivery_data = '';
		} else if($billingShipping == "2"){
			$street =	$this->getFormValue('s_street', $stepTwoData);
			$deleiveryAddressInformation = $this->getFormValue('c_delivery_address',$stepTwoData);
			if($street != '' && $deleiveryAddressInformation == 2){
				$zipcity = $this->getFormValue('s_postcode_city',$stepTwoData) . ' ' . strtoupper(($this->getFormValue('s_city',$stepTwoData))) ? : '';
			return $zipcity;
			}else{
				return '';	
			}
		}
	}
	public function getdeliverystreet($stepTwoData,$type){
	   $bpostPostalLocation = $this->getFormValue('customerPostalLocation',$stepTwoData);
	   $bpostMethod = $this->getFormValue('customerPostalCode',$stepTwoData);
       $billingShipping = $this->getFormValue('c_delivery_address',$stepTwoData);
       if($billingShipping != "2" || ($bpostPostalLocation && $bpostMethod)){
			$delivery_data = '';
		} else if($billingShipping == "2"){
			$street =	$this->getFormValue('s_street', $stepTwoData);
			$deleiveryAddressInformation = $this->getFormValue('c_delivery_address',$stepTwoData);
			if($street != '' && $deleiveryAddressInformation == 2){
				$s_street = strtoupper($this->getFormValue('s_street', $stepTwoData));
			return $s_street;
			} else {
				return '';	
			}
		}
	}
	public function getdeliveryhouse($stepTwoData,$type){
	   $bpostPostalLocation = $this->getFormValue('customerPostalLocation',$stepTwoData);
	   $bpostMethod = $this->getFormValue('customerPostalCode',$stepTwoData);
       $billingShipping = $this->getFormValue('c_delivery_address',$stepTwoData);
       if($billingShipping != "2" || ($bpostPostalLocation && $bpostMethod)){
			$delivery_data = '';
		} else if($billingShipping == "2"){
			$s_number =	$this->getFormValue('s_number', $stepTwoData);
			$deleiveryAddressInformation = $this->getFormValue('c_delivery_address',$stepTwoData);
			if($s_number != '' && $deleiveryAddressInformation == 2){
				$s_number = $this->getFormValue('s_number', $stepTwoData);
				return $s_number;
			}else{
				return '';	
			}
		}
	}
	public function getdeliverybus($stepTwoData,$type){
	   $bpostPostalLocation = $this->getFormValue('customerPostalLocation',$stepTwoData);
	   $bpostMethod = $this->getFormValue('customerPostalCode',$stepTwoData);
       $billingShipping = $this->getFormValue('c_delivery_address',$stepTwoData);
       if($billingShipping != "2" || ($bpostPostalLocation && $bpostMethod)){
			$delivery_data = '';
		} else if($billingShipping == "2"){
			$s_box =	$this->getFormValue('s_box', $stepTwoData);
			$deleiveryAddressInformation = $this->getFormValue('c_delivery_address',$stepTwoData);
			if($s_box != '' && $deleiveryAddressInformation == 2){
				$s_box = $this->getFormValue('s_box', $stepTwoData);
				return "\t".$s_box."\t";
			}else{
				return '';	
			}
		}
	}
	
	
	public function getLegalStatus($stepTwoData) {
		$iIndCcopm = $this->getFormValue('i_ind_copm', $stepTwoData);
		$txProfileDropdown = $this->getFormValue('tx_profile_dropdown', $stepTwoData); 
		if ($iIndCcopm == "1" && $txProfileDropdown) {
			$legalStatus =	$this->getFormValue('legal_status', $stepTwoData);
			if ($legalStatus == "Associations") {
				return "LandbouwVennootschap";
			}
			return  $legalStatus;
		}
		return "";
	}
	public function getVatNumber($stepTwoData) {
		$iIndCcopm = $this->getFormValue('i_ind_copm', $stepTwoData);
		$txProfileDropdown = $this->getFormValue('tx_profile_dropdown', $stepTwoData); 
		if ($iIndCcopm == "1" && $txProfileDropdown && $txProfileDropdown!="2") {
			$vatNumber =	$this->getFormValue('vat_number', $stepTwoData);
			return $vatNumber;
		}
		return "";
	}
	
	public function getBusinessCompanyName($stepTwoData) {
		$iIndCcopm = $this->getFormValue('i_ind_copm', $stepTwoData);
		$txProfileDropdown = $this->getFormValue('tx_profile_dropdown', $stepTwoData); 
		if ($iIndCcopm == "1" && $txProfileDropdown && $txProfileDropdown=="3") {
			$companyName =	$this->getFormValue('company_name', $stepTwoData);
			return $companyName;
		}
		return "";
	}
	public function getpassport($stepTwoData){
		$passport = $this->getFormValue('passport_number',$stepTwoData);
		$are_you_belgiam = $this->getFormValue('registered',$stepTwoData);
		if($are_you_belgiam == 0 && $passport != ''){
		return $passport;
		}else{
			return '';
		}
	}
	
	public function getDateOfBirth($dob,$type){
		$type = strtolower($type);
		return $dob;
	}
	
	public function getinvoiceowner($orderItemm,$stepTwoData){
		$owner = '';
		$lob = $this->getProductLOB($orderItemm);
		if($lob == 'IEW'){
			if($this->getFormValue('is_teneuro_'.$orderItemm->getItemId(),$stepTwoData) == 'yes'){
                if ($this->getFormValue('iew_contract_'.$orderItemm->getItemId(), $stepTwoData) == '0') {			
					if($this->getFormValue('iew_first_name_'.$orderItemm->getItemId(), $stepTwoData) != ''){
						$owner = 0;
					} else{
						$owner = 1;
					}
				}
			}
		}else{
				if($this->getFormValue('ex_invoice', $stepTwoData) == 'yes'){
					if($this->getFormValue('cu_ex_invoice_cust_surname', $stepTwoData) != ''){
						$owner = 0;
					}else{
						$owner = 1;
						}
				}
		}
		return $owner;
	}
	public function getinvoiceownerfname($orderItemm,$stepTwoData){
		$fname = '';
		$lob = $this->getProductLOB($orderItemm);
		if($lob == 'IEW'){
		    $lob = $this->getProductLOB($orderItemm);
			if($this->getFormValue('is_teneuro_'.$orderItemm->getItemId(), $stepTwoData) == 'yes'){ 
				if($this->getFormValue('iew_first_name_'.$orderItemm->getItemId(), $stepTwoData) != '' && $this->getFormValue('iew_contract_'.$orderItemm->getItemId(), $stepTwoData) == '0' ){
					$fname = $this->getFormValue('iew_first_name_'.$orderItemm->getItemId(), $stepTwoData);
				}
			}
		}else{
				if($this->getFormValue('ex_invoice', $stepTwoData) == 'yes'){
					if($this->getFormValue('cu_ex_invoice_cust_surname', $stepTwoData) != ''){
						$fname = $this->getFormValue('cu_ex_invoice_cust_surname', $stepTwoData);
					}
				}
		}
		return $fname;
	}
	public function getinvoiceownerlname($orderItemm,$stepTwoData){
		$lname = '';
		$lob = $this->getProductLOB($orderItemm);
		if($lob == 'IEW'){
			if($this->getFormValue('is_teneuro_'.$orderItemm->getItemId(), $stepTwoData) == 'yes'){ 
				if($this->getFormValue('iew_last_name_'.$orderItemm->getItemId(), $stepTwoData) != '' && $this->getFormValue('iew_contract_'.$orderItemm->getItemId(), $stepTwoData) == '0'){
					$lname = $this->getFormValue('iew_last_name_'.$orderItemm->getItemId(), $stepTwoData);
				}
			}
		}else{
				if($this->getFormValue('ex_invoice', $stepTwoData) == 'yes'){
					if($this->getFormValue('cu_ex_invoice_cust_firstname', $stepTwoData) != ''){
						$lname = $this->getFormValue('cu_ex_invoice_cust_firstname', $stepTwoData); 
					}
				}
		}
		return $lname;
	}
	public function getinvoiceownerdob($orderItemm,$stepTwoData){
		$dob = '';
		$lob = $this->getProductLOB($orderItemm);
		$owner = $this->getinvoiceowner($orderItemm,$stepTwoData);
		if($lob == 'IEW'){
			if($this->getFormValue('is_teneuro_'.$orderItemm->getItemId(), $stepTwoData) == 'yes' && $owner=='0'){ 
			    if ($this->getFormValue('iew_contract_'.$orderItemm->getItemId(), $stepTwoData) == '0') {
					if($this->getFormValue('iew_dob_'.$orderItemm->getItemId(), $stepTwoData) != ''){
						$dob = $this->getFormValue('iew_dob_'.$orderItemm->getItemId(), $stepTwoData);
						if($dob != ''){
							$dob = date('d/m/Y', strtotime($dob));
						}
					}
				}
			}
		}else{
				if($this->getFormValue('ex_invoice', $stepTwoData) == 'yes'){
					if($this->getFormValue('cu_ex_invoice_cust_dob', $stepTwoData) != ''){
						$dob = $this->getFormValue('cu_ex_invoice_cust_dob', $stepTwoData);
						if($dob != ''){
							$dob = date('d/m/Y', strtotime($dob));
						}
					}
				}
			}
		
	return $dob;	
	}
	
	
	
}
