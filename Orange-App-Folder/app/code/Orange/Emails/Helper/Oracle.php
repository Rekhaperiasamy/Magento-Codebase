<?php

namespace Orange\Emails\Helper;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Mail\Template\TransportBuilder;
use phpseclib\Net\SFTP;
use phpseclib\Crypt\RSA;

define('NET_SFTP_LOCAL_FILE', 1);
define('NET_SSH2_LOGGING', 2);
$objectManager = \Magento\Framework\App\ObjectManager::getInstance();

class Oracle extends \Magento\Framework\App\Helper\AbstractHelper 
{
    /* Here section and group refer to name of section and group where you create this field in configuration */

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;
    protected $_logger;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var string
     */
    protected $temp_id;
    protected $_timezoneInterface;
    /**
     * @param Magento\Framework\App\Helper\Context $context
     * @param Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     */
    public function __construct(
    \Magento\Framework\App\Helper\Context $context, \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation, \Magento\Framework\App\Filesystem\DirectoryList $directory_list, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder, \Magento\Sales\Model\Order $_orderModel,
     \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,	\Magento\Framework\File\Csv $csvProcessor,
	 \Orange\Upload\Helper\Data $data
    ) {
        $this->_scopeConfig 		= $context;
        parent::__construct($context);
        $this->inlineTranslation 	= $inlineTranslation;
        $this->directory_list 		= $directory_list;
        $this->_transportBuilder 	= $transportBuilder;
        $this->_logger 				= $context->getLogger();
        $this->_storeManager 		= $storeManager;
        $this->csvProcessor 		= $csvProcessor;
        $this->scopeConfig 			= $context->getScopeConfig();
        $this->_orderModel 			= $_orderModel;
		$this->_timezoneInterface 	= $timezoneInterface;
		$this->_routingLog 			= $data;
		$this->_logFile 			= '/var/log/routingFile.log';
    }
	
	/**
	* Get Config value of system based on store ID
	**/
    protected function getConfigValue($path, $storeId) {
        return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }
	
	/**
	* Get Current Store Details
	**/
	public function getStore() {
        return $this->_storeManager->getStore();
    }

	/**
	* Get Entra Credential ( FTP / EMAIL config details )
	**/
    public function entraCredentials() {
        $routingCredentials = array();
        $routingCredentials['Oracle_Mode'] 				= $this->scopeConfig->getValue('oracle/oracle_configuration/oracle_mode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $routingCredentials['Oracle_EmailSender'] 		= $this->scopeConfig->getValue('oracle/oracle_configuration/oracle_sender', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $routingCredentials['Oracle_EmailReciever'] 	= $this->scopeConfig->getValue('oracle/oracle_configuration/oracle_reciever', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $routingCredentials['Oracle_EmailSenderName'] 	= $this->scopeConfig->getValue('oracle/oracle_configuration/oracle_sender_name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $routingCredentials['Oracle_Host'] 				= $this->scopeConfig->getValue('oracle/oracle_configuration/oracle_host', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $routingCredentials['Oracle_User'] 				= $this->scopeConfig->getValue('oracle/oracle_configuration/oracle_username', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $routingCredentials['Oracle_Password'] 			= $this->scopeConfig->getValue('oracle/oracle_configuration/oracle_key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $routingCredentials['Oracle_Path'] 				= $this->scopeConfig->getValue('oracle/oracle_configuration/oracle_path', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $routingCredentials;
    }
	
	/**
	* Get Server TimezoneInterface
	**/
    public function getTimeAccordingToTimeZone($dateTime)
    {
        // for get current time according to time zone
        $today = $this->_timezoneInterface->date()->format('YmdHis');
 
        // for convert date time according to magento time zone
        $dateTimeAsTimeZone = $this->_timezoneInterface
                                        ->date(new \DateTime($dateTime))
                                        ->format('YmdHis');
        return $dateTimeAsTimeZone;
    }

	/**
	* Generate WHA file for all Hardware Products ( Device, Tablets, Modems, Accessories, Connected Devices )
	**/
    public function getHardWareDetailss() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $discountSohoPrice = $this->scopeConfig->getValue('soho/soho_configuration/soho_discount', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $fileName = 'WHA' . $this->getTimeAccordingToTimeZone(date("YmdHis")); 
        $pFileName = $fileName . '.csv';
        $log_mode = $this->scopeConfig->getValue('custom_modulelog/modulelog__configuration/setting_mode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if(isset($log_mode) && $log_mode==1){
            $this->_routingLog->logCreate($this->_logFile, 'WHA Routing file header preparation process started');
        }
		
		// Prepare Fixed Header //
        $staticHeader1 = array(
            "DOC_ID",
            "DOC_LINE",
            "Oracle1",
            "Oracle2",
            "Oracle3",
            "Oracle4",
            "Oracle5",
            "Oracle6",
            "Oracle7",
            "Oracle8",
            "Oracle9",
            "Oracle10",
            "Article Desc");
        $staticHeader1Sub = array(
            "ORACLE11",
            "ORACLE12",
            "ORACLE13",
            "ORACLE14",
            "ORACLE15",
            "ORACLE16",
            "ORACLE17",
            "ORACLE18",
            "ORACLE19",
            "ORACLE20",
            "ORACLE21",
            "ORACLE22",
            "ORACLE23",
            "ORACLE24",
            "ORACLE25",
            "ORACLE26",
            "ORACLE27",
            "ORACLE28",
            "ORACLE29"
        );

		// Prepare Row2 Fixed Header //
        $staticHeader2 = array(
            "DOC",
            "LINENR",
            "CD",
            "DEALER",
            "ADRESS",
            "NR",
            "ZIP",
            "CITY",
            "SERVICE CODE",
            "PRICE_LIST",
            "1_TIME_SHIP_2 Y/N",
            "PO number",
            "Article Nr",
        );
        $staticHeader2Sub = array(
            "PAYMENT_TERMS",
            "CONTACT_PERSON",
            "PRICE_LIST",
            "SHIPPING_INSTR",
            "PACKING_INSTR",
            "OPENING_HOURS",
            "OPENING_DAYS",
            "REQUEST_DATE",
            "COST_CENTER",
            "ONE_TIME_BILL_TO_FLAG",
            "BILL_TO_VAT_NR",
            "BILL_TO_CUSTOMER_NAME",
            "BILL_TO_ADDRESS",
            "BILL_TO_POSTAL_CODE",
            "BILL_TO_CITY",
            "BILL_TO_ADDITIONAL_CUST_INFO",
            "Phone_number",
            "Email_address",
            "Pricing_date"
        );
        
        // Filtering only the orders with routing_flag = 0  //
		
		$orderstatus = array('processing','complete');
        $orders = $this->_orderModel->getCollection()
					->addFieldToFilter('is_export_csv', '0')
					->addFieldToFilter('status', $orderstatus)
					->addFieldToFilter('routing_flag','0');
					//->;
        $first_array = array(
            "R/" . $fileName,
            "0",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "Price",);
        $headerids = array();
        $orderCS = array();
            if(isset($log_mode) && $log_mode==1){
		$this->_routingLog->logCreate($this->_logFile, 'WHA Routing file header preparation process ended');
		
		// Logging Order Query //
		$this->_routingLog->logCreate($this->_logFile, 'WHA Query Execution : '.$orders->getSelect()->__toString());
		
		// Logging Order Count which needs to be processed //
		$this->_routingLog->logCreate($this->_logFile, 'WHA Pending Orders Count : '.count($orders));
            }	
        if (count($orders) > 0 ) {
			// Logging Count Condition passed //
            if(isset($log_mode) && $log_mode==1){
		$this->_routingLog->logCreate($this->_logFile, 'WHA Order process had been started');
            }
			
            $csvHeadCount = 1;
            $LINENR = 1;
            $ordercount = 1;
            foreach ($orders as $oKey => $order):
               
                if ($order->getStatusLabel() != 'Canceled') {
				
                    $customerType = $order->getCustomerGroupId();
                    $orderItemVal = array();
                    ob_start();
                    $items = $order->getAllItems();
					$discount_amount_b = '';
					$discount_amount_o = abs($order->getDiscountAmount());
					$discount_amount_i = 1;
                    /*                     * Load Order Item* */
                    foreach ($items as $item):
					if($item->getParentItemId() == "")
					{
                        //Temp Line to Fix the Fatal Error//
                        $product_c_sku = $objectManager->get('Magento\Catalog\Model\Product');
						
						$productRepository = $objectManager->get('\Magento\Catalog\Model\ProductRepository');
						
						
                        if ($product_c_sku->getIdBySku($item->getSku())):
							$prode = $productRepository->get($item->getSku());
							$items_attributeSetId = $prode->getAttributeSetId();
							
                            //$items_attributeSetId = $item->getProduct()->getAttributeSetId();
                            if ($items_attributeSetId == 12 || $items_attributeSetId == 13):
							
                                $dynamicHeaderItem[] = $item->getSku();
                                $headerName[] = $item->getName();
                                $skus[] = $item->getSku();

                                $headerids[] = $item->getName() . "~~" . $item->getSku();
                                if ($item->getQtyOrdered() == 0):
                                    $finalPriceSku = round($item->getQtyOrdered());
                                else:
                                    if ($item->getParentItemId() == ""):
                                        $itemPrice = $item->getPrice();
										    if($item->getDiscountAmount() > 0)
											{										
										    $itemPrice =  $itemPrice - ($item->getDiscountAmount()/$item->getQtyOrdered());
											$itemPrice = $itemPrice / (1 + ($discountSohoPrice / 100));
										      if($itemPrice <= 0)
											  {
											   $itemPrice = 0;
											  }
											}
											else
											{
											   $itemPrice = $item->getPrice() / (1 + ($discountSohoPrice / 100));
											}
										$finalPriceSku = round($item->getQtyOrdered()) . "(" . number_format($itemPrice, 2, '.', '') . ")";
                                       
                                    else:
                                        $parentItem = $objectManager->create('Magento\Sales\Model\Order\Item')->load($item->getParentItemId());
                                							 
                                        $parentItemPrice = $parentItem->getPrice() ;
										if($item->getDiscountAmount() > 0)
											{										
												/*Check First Iteration of Amount i */
    										    $parentItemPrice =  $parentItemPrice - ($item->getDiscountAmount()/$item->getQtyOrdered());
												$parentItemPrice =  $parentItemPrice / (1 + ($discountSohoPrice / 100));
											 /*End */
										      if($parentItemPrice <= 0)
											  {
											    $parentItemPrice = 0;
											 }
											}
											else
											{
											$parentItemPrice = $parentItem->getPrice()  / (1 + ($discountSohoPrice / 100)) ;
											}
										
                                            $finalPriceSku = round($item->getQtyOrdered()) . "(" . number_format($parentItemPrice, 2, '.', '') . ")";
                                       
                                    endif;
                                endif;
                                //$orderItemVal[$item->getSku()]= $finalPriceSku;                   
                                $OrderKeyValue = $item->getName() . "~~" . $item->getSku();
                                $orderItemVal[$OrderKeyValue] = $finalPriceSku;
                                $item = array();
								$discount_amount_i++;
                            endif;
                        endif;
						}
                    endforeach; //End Each for Order Items

                    /*                     * Check The Qty in Item Array* */
                    if ($this->isArrayEmpty($orderItemVal)):
                        $orderValues = "";
                    else:
                        $orderValues = 1;
                    endif;
                    /** Check the OrderValues is Empty or Not* */
                    if (isset($orderValues) && ($orderValues == 1)):
                        $shippingAddress = $order->getShippingAddress();
                        $model = $objectManager->create('Orange\Abandonexport\Model\Items');
						$abandonexport = $model->getCollection()->addFieldToFilter('quote_id',$order->getQuoteId());
						$tempSession = unserialize($abandonexport->getFirstItem()->getStepsecond());
				       /* Assigning Variable For City,Post code,Street Number */
					   	  if ($order->getStoreId() == 1)
						  {
							$storevalue   = 'NL';
							$busname = 'bus';
						  }	
                         else
						 {
                            $storevalue   = 'FR';
							$busname = 'bte';
                         }
						if(isset($tempSession['s_city']) && $tempSession['s_city'] != ''){
							$shipping_city = strtoupper($tempSession['s_city']);
						}else if(isset($tempSession['b_city']) && $tempSession['b_city'] != ''){
							$shipping_city = strtoupper($tempSession['b_city']);
						}else{
							$shipping_city = "";
						}
					    if(isset($tempSession['s_postcode_city']) && $tempSession['s_postcode_city'] != ''){
							$s_postcode_city = $tempSession['s_postcode_city'];
						}else if(isset($tempSession['b_postcode_city']) && $tempSession['b_postcode_city'] != ''){
							$s_postcode_city = $tempSession['b_postcode_city'];
						}else{
							$s_postcode_city = "";
						}
						if(isset($tempSession['s_number']) && $tempSession['s_number'] != ''){
							$s_number = $tempSession['s_number'];
						}else if(isset($tempSession['b_number']) && $tempSession['b_number'] != ''){
							$s_number = $tempSession['b_number'];
						}else
						{
							$s_number = "";
						}
						 if(isset($tempSession['first_name']) && $tempSession['first_name'] != ''){
							$dealer_name = $tempSession['first_name']." ".$tempSession['last_name'] ;
						}
						else
						{
						$dealer_name = "";
						}
							if(isset($tempSession['s_box']) && $tempSession['s_box'] != ''){
							$s_box = $busname." ".$tempSession['s_box'];
						}else if(isset($tempSession['b_box']) && $tempSession['b_box'] != ''){
							$s_box = $busname." ".$tempSession['b_box'];
						}else
						{
							$s_box = "";
						}
					    if(isset($tempSession['s_street']) && $tempSession['s_street'] != ''){
							$s_street = strtoupper($tempSession['s_street']);
						}else if(isset($tempSession['b_street']) && $tempSession['b_street'] != ''){
							$s_street = strtoupper($tempSession['b_street']);
						}else{
							$s_street = "";
						}
					    if(isset($tempSession['b_street']) && $tempSession['b_street'] != ''){
							$b_street = strtoupper($tempSession['b_street']);
						}else{
							$b_street = "";
						}
					    if(isset($tempSession['b_postcode_city']) && $tempSession['b_postcode_city'] != ''){
							$b_postcode_city = $tempSession['b_postcode_city'];
						}else{
							$b_postcode_city = "";
						}
						if(isset($tempSession['b_number']) && $tempSession['b_number'] != ''){
							$b_number = $tempSession['b_number'];
						}else{
							$b_number = "";
						}
						if(isset($tempSession['b_box']) && $tempSession['b_box'] != ''){
							$b_box = $busname." ".$tempSession['b_box'];
						}else{
							$b_box = "";
						}
					    if(isset($tempSession['b_city']) && $tempSession['b_city'] != ''){
							$b_city = strtoupper($tempSession['b_city']);
						}else{
							$b_city = "";
						}
					     /* Assigning Variable to Shipping instruction and Service code */
					   if(isset($tempSession['c_delivery_address']) && $tempSession['c_delivery_address'] == 3)
					   { 
							if(isset($tempSession['deliveryMethod']) && $tempSession['deliveryMethod'] == 'Pugo') {
								if(isset($tempSession['customerRcCode']))
								{
								$customerRcCode         =  '|'.$tempSession['customerRcCode'];
								}
								else
								{
								$customerRcCode         = "";
								}
								/*Customer Post Location  **/
								if(isset($tempSession['customerPostalLocation']))
								{
								$customerPostalLocation = '|'.$tempSession['customerPostalLocation'];
								}
								else
								{
								$customerPostalLocation = "";
								}
								if(isset($tempSession['pugoKeepMeInformedViaNotificationType']) && $tempSession['pugoKeepMeInformedViaNotificationType'] == 'e-mail') {
								$shipping_instruction  = '|BP|1|1|'.$storevalue.$customerRcCode.$customerPostalLocation;
								}
								else
								{
								$shipping_instruction  = '|BP|1|2|'.$storevalue.$customerRcCode.$customerPostalLocation;
								}
								$service_code          = 18;
								if(isset($tempSession['customerPhoneNumber']))
								{
								$bpostPhonenumber  = preg_replace("/[^0-9]/","",$tempSession['customerPhoneNumber']);
								}
								else
								{
								$bpostPhonenumber  = "";
								}
							
							}else
							{
							    	/*Customer Rcc code **/
								if(isset($tempSession['customerRcCode']))
								{
								$customerRcCode         =  '|'.$tempSession['customerRcCode'];
								}
								else
								{
								$customerRcCode         = "";
								}
								/*Customer Post Location  **/
								if(isset($tempSession['customerPostalLocation']))
								{
								$customerPostalLocation = '|'.$tempSession['customerPostalLocation'];
								}
								else
								{
								$customerPostalLocation = "";
								}
								$shipping_instruction  = '|BP|2|1|'.$storevalue.$customerRcCode.$customerPostalLocation;
								$service_code          = 73;
								if(isset($tempSession['customerPhoneNumber']))
								{
								$bpostPhonenumber  = preg_replace("/[^0-9]/","",$tempSession['customerPhoneNumber']);
								}
								else
								{
								$bpostPhonenumber  = "";
								}
							}
					   }else
					   {
						   $shipping_instruction  = "";
						   $service_code          =  83;
						   $bpostPhonenumber  = "";
					   }
                        $billingAddress = $order->getBillingAddress();
                        $date = date_create($order->getCreatedAt());
                        $street = $shippingAddress->getStreet();
                        $billstreet = $billingAddress->getStreet();
                        $dateTimeZone = $this->getTimeAccordingToTimeZone($order->getCreatedAt());
						$ofileName = 'WHA' . $dateTimeZone;
                        $data = array();
                        $data[] = 'R/' . $fileName;
                        $data[] = $ordercount;
                        if ($order->getStoreId() == 1):
                            $data[] = 'WEBPO002';
						
                        else:
                            $data[] = 'WEBPO001';
							
                        endif;
                        $data[] = $dealer_name;
                        $data[] = $s_street;
						if($s_box!='') {
							$data[] = $s_number." ".$s_box;
						}
						else {
							$data[] = $s_number;
						}
                        $data[] = $s_postcode_city;
                        $data[] = $shipping_city;
                        $data[] = $service_code;
                        $data[] = '';
                        $data[] = 'Y';
                        $data[] = $order->getIncrementId();
                      
					   
                        /* Date From Oracle 11* */
						if($shippingAddress->getVatNumber() == ""){
							$vat_numer  = "NA";
						}else{
							$vat_numer  = $shippingAddress->getVatNumber();
						}
						
                        $dataN = array();
                        $dataN[] = 'Paid';
                        $dataN[] = "";
                        $dataN[] = "";
                        $dataN[] = $shipping_instruction;
                        $dataN[] = "";
                        $dataN[] = "";
                        $dataN[] = "";
                        $dataN[] = "";
                        $dataN[] = sprintf("%04d", '0'); //"0000";
                        $dataN[] = "Y";
                        $dataN[] = $vat_numer;
                        $dataN[] = $billingAddress->getFirstname() . " " . $billingAddress->getLastname();
						if($b_box!='') {
							$dataN[] = $b_street." ".$b_number." ".$b_box;
						}
						else {
							$dataN[] = $b_street." ".$b_number;
						}
						$dataN[] = $b_postcode_city;
                        $dataN[] = $b_city;
                        $dataN[] = "";
                        $dataN[] = $bpostPhonenumber;
                        $dataN[] = $shippingAddress->getEmail();
                        $dataN[] = date("d-M-Y", strtotime($order->getCreatedAt()));

                        $orderCS[$order->getId()]['data'] = $data;
                        $orderCS[$order->getId()]['dataN'] = $dataN;
                        $orderCS[$order->getId()]['sku'] = $orderItemVal;
                        
                       
                        
                        $ordercount++;
                    endif; //End IF For Order Values is EMpty or Not

                    $LINENR++;
                }
			 // Setting flag to 1 to skip this order in next run
				$order->setRoutingFlag(1)->save();
			endforeach; //End Each For Orders
                        if(isset($log_mode) && $log_mode==1){
                            $this->_routingLog->logCreate($this->_logFile, 'WHA Routing file orders values preparation process started');
                        }
			$headerNewName = array();
			$dynamicNewHeaderitem = array();
			$headerids = array_unique($headerids);
			foreach ($headerids as $headerid):
				$sepHeaderid = explode('~~', $headerid);
				$headerNewName[] = $sepHeaderid[0];
				$dynamicNewHeaderitem[] = $sepHeaderid[1];
			endforeach;
			$headerCount = count($headerNewName);
			$headrIarray = array();
			for ($headrI = 13 + $headerCount; $headrI < 163; $headrI++) {
				$headrIarray[] = "";
			}

			$csvHeader1 = array_merge($staticHeader1, $headerNewName, $headrIarray, $staticHeader1Sub);
			$countheader1 = array_merge($staticHeader1, $headerNewName);
			$csvHeader2 = array_merge($staticHeader2, $dynamicNewHeaderitem, $headrIarray, $staticHeader2Sub);
			$apfinalarray = array();
			$skuLists = array_values(array_unique($headerids));
			$staticHeaderCount = count($countheader1);
			$baseDir = $this->directory_list->getRoot();
			$lcount  = count($orderCS);
			if(isset($log_mode) && $log_mode==1){
                            $this->_routingLog->logCreate($this->_logFile, 'Real Orders count : '.count($orderCS));
                        }
			if($lcount != "")
			{			
				foreach ($orderCS as $key => $value) {
			
					for ($k = 13; $k <= $staticHeaderCount; $k++) {
						if ($k == 13 || $k > 36) {
							$value['data'][] = "";
						} else {
							$value['data'][] = 0;
						}
					  
					}
					for ($pk = 13; $pk <= ($staticHeaderCount-1); $pk++) {
						if ($pk > 33) {
							$apfinalarray[] = "";
						} else {
							$apfinalarray[] = 0;
						}
					}
					
					$first_array = array_merge($first_array, $apfinalarray);
					$headrDataIarray = "";
					$headrDataIarray = array();
					for ($headrDataI = 13 + $headerCount; $headrDataI < 163; $headrDataI++) {
						$headrDataIarray[] = "";
					}
					$priceValue = array();
					foreach ($value['sku'] as $ke => $val) {
						$position = array_search($ke, $skuLists) + 13;
						$value['data'][$position] = $val;
					}
					$totalPriceCount = count($value['data']);
					for ($pk = 13; $pk < $totalPriceCount; $pk++) {
						$priceValue[$pk] = 0;
					}
					$finalArray = array();
					$finalArray = array_merge($first_array, $headrDataIarray);
					$finalArrayc = count($finalArray);
					$totalPriceNCount = $finalArrayc + count($value['dataN']);
					for ($pj = $finalArrayc; $pj <= $totalPriceNCount; $pj++) {
						$priceValueN[$pj] = "";
					}
					$finalArray = array_merge($finalArray, $priceValueN);
					$dynamicVal = array_merge($value['data'], $headrDataIarray, $value['dataN']);
		
					/* prinintg values into csv file */
					if ($csvHeadCount == 1) {
						$assocDataArray = array($csvHeader1, $csvHeader2, $finalArray, $dynamicVal);
					} else {
						$assocDataArray = array($dynamicVal);
					}
					$csvHeadCount = 2;
					$dir = $baseDir . '/pub/media/oracle/';
					if (!is_dir($dir)) {
						mkdir($dir);
					}
					if (is_file($dir . $pFileName)) {
						$fc = fopen($dir . $pFileName, 'a+');
					
					} else {
						$fc = fopen($dir . $pFileName, 'w+');
						
					}
					// Add UTF8 bom to fix Excel UTF8 CSV import problem. 
					//fwrite($fc, chr(239) . chr(187) . chr(191));
					foreach ($assocDataArray AS $values) {
					 	//fputcsv($fc,  array_map('utf8_encode', array_values($values)),";"," ");
						//fputcsv($fc,array_map('utf8_decode', array_values($values)),";"," ","\n"); //commented for P2 incident
						fwrite($fc, implode(';',array_map('utf8_decode', array_values($values))) . "\r\n");  //added for P2 incident
						//fputcsv($fc, array_map('utf8_decode', array_values($values)), ';', chr(0));
					}
				
					fclose($fc);
					ob_flush();
					/* prinintg values into csv file ends */
				}
         
				
				$position = 0;
				$dynamicVal = array();
				$assocDataArray = array();
				//mailing						
				$path = $baseDir . '/pub/media/oracle/';
				$WHAfile = $path . $pFileName;
				$info = $this->entraCredentials();

				//Getting Server Details
				$username = $info['Oracle_User'];
				$password = $info['Oracle_Password'];
				$host = $info['Oracle_Host'];
				$oraclePath = $info['Oracle_Path'];
				
				$key_path = $baseDir . '/pub/media/upload';

				//Checking the Oracle Mode and perform file transfer between server for Hardware
				if (($info['Oracle_Mode'] == 1) && ($username != "") && ($password != "") && ($host != "")) {
					if(isset($log_mode) && $log_mode==1){
                                            $this->_routingLog->logCreate($this->_logFile, 'WHA Routing file FTP process started');
                                        }
					$sftp = new SFTP($host);
					$key = new RSA();
					$key->loadKey(file_get_contents($key_path.'/'.$password, true));

					// Login to SFTP //
					if (!$sftp->login($username, $key)) {
						echo 'Login Failed';
                                                if(isset($log_mode) && $log_mode==1){
                                                    $this->_routingLog->logCreate($this->_logFile, $sftp->getLog().' SFTP Login Failed');
                                                }
					}
					
					// Push the file to SFTP //
					$response = $sftp->put($oraclePath . '/' . $pFileName, $WHAfile, NET_SFTP_LOCAL_FILE);
					if(!$response){
                                            if(isset($log_mode) && $log_mode==1){
						$this->_routingLog->logCreate($this->_logFile, $sftp->getLog().' WHA File push failed');
                                            }
					}else{
                                            if(isset($log_mode) && $log_mode==1){
						$this->_routingLog->logCreate($this->_logFile, 'WHA Routing file FTP process ended');
                                            }
					}
				}else{
                                    if(isset($log_mode) && $log_mode==1){
					$this->_routingLog->logCreate($this->_logFile, 'WHA Routing file mail process started');
                                    }
					// Oracle Mode Development : Mail Option //
				
					if (!empty($info['Oracle_EmailSender']) && !empty($info['Oracle_EmailReciever'])) {
						$sender = $info['Oracle_EmailSender'];
						$reciever = $info['Oracle_EmailReciever'];
					} else {
                                            if(isset($log_mode) && $log_mode==1){
						$this->_routingLog->logCreate($this->_logFile, 'Email Sender and Reciever configuration is missing. Pleas check Store > Configuration > Orange > Oracle Email >Oracle Email Configuration.');
                                            }
						exit();
						//die('Email Sender and Reciever not specified in backend Oracle Email confi');
					}
					$htmlbody = " Hereby Attachment is included for the file " . $pFileName;
					$to = $reciever; //Recipient Email Address			
					$subject = "Routing File Included " . $pFileName; //Email Subject
					$random_hash = md5(date('r', time()));
					$headers = "From: $sender\r\nReply-To: $sender";
					$headers .= "\r\nContent-Type: multipart/mixed; boundary=\"PHP-mixed-" . $random_hash . "\"";
					// Set your file path here
					$attachment = chunk_split(base64_encode(file_get_contents($WHAfile)));
					//define the body of the message.
					$message = "--PHP-mixed-$random_hash\r\n" . "Content-Type: multipart/alternative; 
				boundary=\"PHP-alt-$random_hash\"\r\n\r\n";
					$message .= "--PHP-alt-$random_hash\r\n" . "Content-Type: text/plain; 
				charset=\"iso-8859-1\"\r\n" . "Content-Transfer-Encoding: 7bit\r\n\r\n";
					//Insert the html message.
					$message .= $htmlbody;
					$message .="\r\n\r\n--PHP-alt-$random_hash--\r\n\r\n";
					//include attachment
					$message .= "--PHP-mixed-$random_hash\r\n" . "Content-Type: application/zip; 
				name=\"$pFileName\"\r\n" . "Content-Transfer-Encoding: 
				base64\r\n" . "Content-Disposition: attachment\r\n\r\n";
					$message .= $attachment;
					//$message .= "/r/n--PHP-mixed-$random_hash--";
					//send the email
					$mail = mail($to, $subject, $message, $headers);
					if($mail)
					{
					echo 'WHA Routing file was successfully mailed';
					echo '<br/>';
                                            if(isset($log_mode) && $log_mode==1){
						$this->_routingLog->logCreate($this->_logFile, 'WHA Routing file was successfully mailed');
                                            }
					}else{
					echo 'WHA Routing file was not mailed';
					echo '<br/>';
                                            if(isset($log_mode) && $log_mode==1){    
						$this->_routingLog->logCreate($this->_logFile, 'WHA Routing file was not mailed.');
                                            }
					}
                                        if(isset($log_mode) && $log_mode==1){
                                            $this->_routingLog->logCreate($this->_logFile, 'WHA Routing file mail process Ended');
                                        }
				}
			}
                        if(isset($log_mode) && $log_mode==1){
                            $this->_routingLog->logCreate($this->_logFile, 'WHA Routing file orders values preparation process ended');
                        
                            $this->_routingLog->logCreate($this->_logFile, 'WHA Order process had been ended');
                        }
			
		}
    }

    /**
     * Check if the array is empty or not
     *
     * @return bool
     */
    public function isArrayEmpty($array) {
        foreach ($array as $key => $val) {
            if (!empty($val))
                return false;
        }
        return true;
    }

    public function getPrepaidDetails() {
           
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $fileName = 'WPR' . $this->getTimeAccordingToTimeZone(date("YmdHis")); 
        $pFileName = $fileName . '.csv';
        //$this->emailForOracle($pFileName);        
	$log_mode = $this->scopeConfig->getValue('custom_modulelog/modulelog__configuration/setting_mode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if(isset($log_mode) && $log_mode==1){
            $this->_routingLog->logCreate($this->_logFile, 'WPR Routing file header preparation process started');
        }	
		
		
        $staticHeader1 = array(
            "DOC_ID",
            "Doc_LINE",
            "Oracle1",
            "Oracle2",
            "Oracle3",
            "Oracle4",
            "Oracle5",
            "Oracle6",
            "Oracle7",
            "Oracle8",
            "Oracle9",
            "Oracle10",
            "Article Desc");

        $staticHeader1Sub = array(
            "ORACLE11",
            "ORACLE12",
            "ORACLE13",
            "ORACLE14",
            "ORACLE15",
            "ORACLE16",
            "ORACLE17",
            "ORACLE18",
            "ORACLE19",
            "ORACLE20",
            "ORACLE21",
            "ORACLE22",
            "ORACLE23",
            "ORACLE24",
            "ORACLE25",
            "ORACLE26",
            "ORACLE27",
            "ORACLE28",
            "ORACLE29"
        );
        $staticHeader2 = array(
            "DOC",
            "LINENR",
            "CD",
            "DEALER",
            "ADRESS",
            "NR",
            "ZIP",
            "CITY",
            "SERVICE CODE",
            "PRICE_LIST",
            "1_TIME_SHIP_2 Y/N",
            "PO number",
            "Article Nr",
        );
        $staticHeader2Sub = array(
            "PAYMENT_TERMS",
            "CONTACT_PERSON",
            "PRICE_LIST",
            "SHIPPING_INSTR",
            "PACKING_INSTR",
            "OPENING_HOURS",
            "OPENING_DAYS",
            "REQUEST_DATE",
            "COST_CENTER",
            "ONE_TIME_BILL_TO_FLAG",
            "BILL_TO_VAT_NR",
            "BILL_TO_CUSTOMER_NAME",
            "BILL_TO_ADDRESS",
            "BILL_TO_POSTAL_CODE",
            "BILL_TO_CITY",
            "BILL_TO_ADDITIONAL_CUST_INFO",
            "Phone_number",
            "Email_address",
            "Pricing_date"
        );

        $first_array = array(
            "R/" . $fileName,
            "0",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "Price",);
        $orderCS = array();
        
        // Filtering only the orders which is not aready exported
			$orderstatus = array('processing','complete');
            $orders = $this->_orderModel->getCollection()->addFieldToFilter('is_export_csv', '0')->addFieldToFilter('status', $orderstatus)->addFieldToFilter('routing_flag_prepaid','0');
            if(isset($log_mode) && $log_mode==1){    
		$this->_routingLog->logCreate($this->_logFile, 'WPR Routing file header preparation process ended');
		
		// Logging Order Query //
		$this->_routingLog->logCreate($this->_logFile, 'WPR Query Execution : '.$orders->getSelect()->__toString());
		
		// Logging Order Count which needs to be processed //
		$this->_routingLog->logCreate($this->_logFile, 'WPR Pending Orders Count : '.count($orders));
            }	
        $headerids = array();
        if (count($orders) > 0) {
			// Logging Count Condition passed //
            if(isset($log_mode) && $log_mode==1){
		$this->_routingLog->logCreate($this->_logFile, 'WHA Order process had been started');
            }
			
            $csvHeadCount = 1;
            $LINENR = 1;
            /** Load Order * */
            foreach ($orders as $oKey => $order):
                //if ($order->getStatusLabel() != 'Canceled' && $order->getIncrementId() > 100001800) {
				if ($order->getStatusLabel() != 'Canceled') {
                    ob_start();
                    $items = $order->getAllItems();
                    $orderItemVal = "";
                    $orderItemVal = array();
                    /** Load Order Items* */

                    foreach ($items as $item):
                        $product_c_sku = $objectManager->get('Magento\Catalog\Model\Product');
                        if ($product_c_sku->getIdBySku($item->getSku())):
                            /* if($item->getSku() == '9088128'): */
                            $Items_attributeSetId = $item->getProduct()->getAttributeSetId();
                            if ($Items_attributeSetId == 14):
                                $dynamicHeaderItem[] = $item->getSku();
                                $headerName[] = $item->getName();
                                $skus[] = $item->getSku();
                                $finalPriceSku = round($item->getQtyOrdered());
                                // $orderItemVal[$item->getSku()]= $finalPriceSku;
                                $headerids[] = $item->getName() . "~~" . $item->getSku();
                                $OrderKeyValue = $item->getName() . "~~" . $item->getSku();
                                $orderItemVal[$OrderKeyValue] = $finalPriceSku;

                            //endif;
                            endif;
                        endif;
                    endforeach; //End For Each For Load Order Items

                    /*                     * Check The Qty in Item Array* */
                    if ($this->isArrayEmpty($orderItemVal)):
                        $orderValues = "";
                    else:
                        $orderValues = 1;
                    endif;
                    /*                     * Check the OrderValues is Empty or Not* */
					
                    if (isset($orderValues) && ($orderValues == 1)):
                        if ($order->getShippingAddress() != ""):
                            $shippingAddress = $order->getShippingAddress();
                        else:
                            $shippingAddress = $order->getBillingAddress();
                        endif;
						$model = $objectManager->create('Orange\Abandonexport\Model\Items');
						$abandonexport = $model->getCollection()->addFieldToFilter('quote_id',$order->getQuoteId());
						$tempSession = unserialize($abandonexport->getFirstItem()->getStepsecond());
						/* Assigning Variable For City,Post code,Street Number */
						if ($order->getStoreId() == 1)
						  {
							$busname = 'bus';
						  }	
                         else
						{
         					$busname = 'bte';
                        }
						if(isset($tempSession['s_city']) && $tempSession['s_city'] != ''){
							$shipping_city = strtoupper($tempSession['s_city']);
						}else if(isset($tempSession['b_city']) && $tempSession['b_city'] != ''){
							$shipping_city = strtoupper($tempSession['b_city']);
						}else{
							$shipping_city = "";
						}
						if(isset($tempSession['s_postcode_city']) && $tempSession['s_postcode_city'] != ''){
							$s_postcode_city = $tempSession['s_postcode_city'];
						}else if(isset($tempSession['b_postcode_city']) && $tempSession['b_postcode_city'] != ''){
							$s_postcode_city = $tempSession['b_postcode_city'];
						}else{
							$s_postcode_city = "";
						}
					    if(isset($tempSession['s_number']) && $tempSession['s_number'] != ''){
							$s_number = $tempSession['s_number'];
						}else if(isset($tempSession['b_number']) && $tempSession['b_number'] != ''){
							$s_number = $tempSession['b_number'];
						}else{
							$s_number = "";
						}
						if(isset($tempSession['s_box']) && $tempSession['s_box'] != ''){
							$s_box = $busname." ".$tempSession['s_box'];
						}else if(isset($tempSession['b_box']) && $tempSession['b_box'] != ''){
							$s_box = $busname." ".$tempSession['b_box'];
						}else
						{
							$s_box = "";
						}
					    if(isset($tempSession['s_street']) && $tempSession['s_street'] != ''){
							$s_street = strtoupper($tempSession['s_street']);
						}else if(isset($tempSession['b_street']) && $tempSession['b_street'] != ''){
							$s_street = strtoupper($tempSession['b_street']);
						}else{
							$s_street = "";
						}
					   if(isset($tempSession['first_name']) && $tempSession['first_name'] != '')
					   {
						$dealer_name = $tempSession['first_name']." ".$tempSession['last_name'] ;
						}
						else
						{
						$dealer_name = "";
						}
						
                        $street = $shippingAddress->getStreet();
                        $name = $shippingAddress->getFirstname() . " " . $shippingAddress->getLastname();
                        $data = array();
						$dateTimeZone = $this->getTimeAccordingToTimeZone($order->getCreatedAt());
						$ofileName = 'WPR' . $dateTimeZone;
                        $date = date_create($order->getCreatedAt());
                        $data[] = 'R/' . $fileName;
                        $data[] = $LINENR;
                        $data[] = 'WEBCH004';
                        $data[] = $dealer_name;
                        $data[] = $s_street;
						if($s_box!='') {
							$data[] = $s_number." ".$s_box;
						}
						else {
							$data[] = $s_number;
						}                        
                        $data[] = $s_postcode_city;
                        $data[] = $shipping_city;
                        $data[] = 25;
                        $data[] = '';
                        $data[] = 'Y';
                        $data[] = $order->getIncrementId();


                        $dataN = array();
              
                        $dataN[] = "";
                        $dataN[] = "";
                        $dataN[] = "";
                        $dataN[] = "";
                        $dataN[] = "";
                        $dataN[] = "";
                        $dataN[] = "";
                        $dataN[] = "";
                        $dataN[] = "";
                        $dataN[] = "";
                        $dataN[] = "";
                        $dataN[] = "";
                        $dataN[] = "";
                        $dataN[] = "";
                        $dataN[] = "";
                        $dataN[] = "";
                        $dataN[] = "";
                        $dataN[] = $shippingAddress->getEmail();
                        $dataN[] = "";

                        $orderCS[$order->getId()]['data'] = $data;
                        $orderCS[$order->getId()]['sku'] = $orderItemVal;
                        $orderCS[$order->getId()]['dataN'] = $dataN;
                        
                       
                        
                        $LINENR++;
                    endif;
                //}
                //changing flags 
                /* $ordersStoring = $objectManager->create('Magento\Sales\Model\Order')->load($order->getId());
                  $ordersStoring->setIsExportCsv('1');
                  $ordersStoring->save(); */
				}
			  // Setting a flag to skip this record in next cron run
              $order->setRoutingFlagPrepaid(1)->save();
            endforeach;
                if(isset($log_mode) && $log_mode==1){
                    $this->_routingLog->logCreate($this->_logFile, 'WPR Routing file orders values preparation process started');
                }	
            $headerNewName = array();
            $dynamicNewHeaderitem = array();
            $headerids = array_unique($headerids);
            /*             * Splitting Name and SKU * */
            foreach ($headerids as $headerid):

                $sepHeaderid = explode('~~', $headerid);
                $headerNewName[] = $sepHeaderid[0];
                $dynamicNewHeaderitem[] = $sepHeaderid[1];

            endforeach; //End for Headerids 
            $headerCount = count($headerNewName);
            $headrIarray = array();
            for ($headrI = 13 + $headerCount; $headrI < 163; $headrI++) {
                $headrIarray[] = "";
            }
            $csvHeader1 = array_merge($staticHeader1, $headerNewName, $headrIarray, $staticHeader1Sub);
            $countheader1 = array_merge($staticHeader1, $headerNewName);
            $csvHeader2 = array_merge($staticHeader2, $dynamicNewHeaderitem, $headrIarray, $staticHeader2Sub);
            $skuLists = array_values($headerids);

            $staticHeaderCount = count($countheader1);
            $baseDir = $this->directory_list->getRoot();
			if(isset($log_mode) && $log_mode==1){
                            $this->_routingLog->logCreate($this->_logFile, 'Real Orders count : '.count($orderCS));
                        }
			if(count($orderCS)){
				foreach ($orderCS as $key => $value) {
					for ($k = 13; $k <= $staticHeaderCount; $k++) {
						if ($k == 13) {
							$value['data'][] = "";
						} else {
							$value['data'][] = 0;
						}
					}
					$headrDataIarray = "";
					$headrDataIarray = array();
					for ($headrDataI = 13 + $headerCount; $headrDataI < 163; $headrDataI++) {
						$headrDataIarray[] = "";
					}
					$priceValue = array();
					$priceValueN = array();
					foreach ($value['sku'] as $ke => $val) {
						$position = array_search($ke, $skuLists) + 13;
						//  echo  $position . " ==> " . $ke . " ==> " . $val . '<br />';
						$value['data'][$position] = $val;
					}

					$TotalPriceCount = count($value['data']);
					for ($pk = 13; $pk < $TotalPriceCount; $pk++) {
						$priceValue[$pk] = 0;
					}
					$finalArray = array();
					$finalArray = array_merge($first_array, $headrDataIarray);
					$finalArrayc = count($finalArray);
					$TotalPriceNCount = $finalArrayc + count($value['dataN']);
					for ($pj = $finalArrayc; $pj <= $TotalPriceNCount; $pj++) {
						$priceValueN[$pj] = "";
					}
					$finalArray = array_merge($finalArray, $priceValueN);

					$dynamicVal = $value['data'];
					$dynamicVal = array_merge($value['data'], $headrDataIarray, $value['dataN']);

					/* prinintg values into csv file */
					if ($csvHeadCount == 1) {
					
						// $assocDataArray = array($csvHeader1, $csvHeader2, $dynamicVal);
						$assocDataArray = array($csvHeader1, $csvHeader2, $finalArray, $dynamicVal);
					} else {
					
						$assocDataArray = array($dynamicVal);
					}

					$csvHeadCount = 2;
					$dir = $baseDir . '/pub/media/oracle/';
					if (!is_dir($dir)) {
						mkdir($dir);
					}
					if (is_file($dir . $pFileName)) {
						$fc = fopen($dir . $pFileName, 'a+');
					} else {
						$fc = fopen($dir . $pFileName, 'w+');
					}
					// Add UTF8 bom to fix Excel UTF8 CSV import problem. 
					//fwrite($fc, chr(239) . chr(187) . chr(191));
					foreach ($assocDataArray AS $values) {
						//fputcsv($fc,array_map('utf8_decode', array_values($values)),";"," ","\n"); //commented for p2 incident
						 //fwrite($fc, implode(';',$values) . "\r\n");
						 //fputcsv($fc, array_map('utf8_decode', array_values($values)), ';', chr(0));
						 fwrite($fc, implode(';',array_map('utf8_decode', array_values($values))) . "\r\n"); //added for p2 incident
					}
					//print_r($values);
					fclose($fc);
					ob_flush();
					/* prinintg values into csv file ends */
			
				}
             
				$orderCS = array();
				$position = 0;
				$dynamicVal = array();
				$assocDataArray = array();
				//mailing	
         			
				$path = $baseDir . '/pub/media/oracle/';
				$WPAfile = $path . $pFileName;
				$info = $this->entraCredentials();

				//Getting server Details
				$username = $info['Oracle_User'];
				$password = $info['Oracle_Password'];
				$host = $info['Oracle_Host'];
				$oraclePath = $info['Oracle_Path'];
				/* assign key path from routing files */
				$key_path = $baseDir . '/pub/media/upload';
				//Checking the Oracle Mode and perform file transfer between server for Prepaid
				if (($info['Oracle_Mode'] == 1) && ($username != "") && ($password != "") && ($host != "")) {
                                        if(isset($log_mode) && $log_mode==1){
                                            $this->_routingLog->logCreate($this->_logFile, 'WPR Routing file FTP process started');
                                        }
					$sftp = new SFTP($host);
					$key = new RSA();
					$key->loadKey(file_get_contents($key_path.'/'. $password, true));

					if (!$sftp->login($username, $key)) {
						echo 'Login Failed';
                                                if(isset($log_mode) && $log_mode==1){
                                                    $this->_routingLog->logCreate($this->_logFile, $sftp->getLog().' SFTP Login Failed');
                                                }
					}

					$response = $sftp->put($oraclePath . '/' . $pFileName, $WPAfile, NET_SFTP_LOCAL_FILE);
					if(!$response){
                                            if(isset($log_mode) && $log_mode==1){
						$this->_routingLog->logCreate($this->_logFile, $sftp->getLog().' WPR File push failed');
                                            }
					}else{
                                            if(isset($log_mode) && $log_mode==1){
						$this->_routingLog->logCreate($this->_logFile, 'WPR Routing file FTP process ended');
                                            }
					}
				}else {
                                        if(isset($log_mode) && $log_mode==1){
                                            $this->_routingLog->logCreate($this->_logFile, 'WPR Routing file mail process started');
                                        }
					
					if (!empty($info['Oracle_EmailSender']) && !empty($info['Oracle_EmailReciever'])) {
						$sender = $info['Oracle_EmailSender'];
						$reciever = $info['Oracle_EmailReciever'];
					} else {
                                            if(isset($log_mode) && $log_mode==1){
						$this->_routingLog->logCreate($this->_logFile, 'Email Sender and Reciever configuration is missing. Pleas check Store > Configuration > Orange > Oracle Email >Oracle Email Configuration.');
                                            }
						exit;
						//die('Email Sender and Reciever not specified in backend Oracle Email confi');
					}
					$htmlbody = " Hereby Attachment is included for the file " . $pFileName;
					$to = $reciever; //Recipient Email Address			
					$subject = "Routing File Included " . $pFileName; //Email Subject
					$random_hash = md5(date('r', time()));
					$headers = "From: $sender\r\nReply-To: $sender";
					$headers .= "\r\nContent-Type: multipart/mixed; boundary=\"PHP-mixed-" . $random_hash . "\"";
					// Set your file path here
					$attachment = chunk_split(base64_encode(file_get_contents($WPAfile)));
					//define the body of the message.
					$message = "--PHP-mixed-$random_hash\r\n" . "Content-Type: multipart/alternative; 
				boundary=\"PHP-alt-$random_hash\"\r\n\r\n";
					$message .= "--PHP-alt-$random_hash\r\n" . "Content-Type: text/plain; 
				charset=\"iso-8859-1\"\r\n" . "Content-Transfer-Encoding: 7bit\r\n\r\n";
					//Insert the html message.
					$message .= $htmlbody;
					$message .="\r\n\r\n--PHP-alt-$random_hash--\r\n\r\n";
					//include attachment
					$message .= "--PHP-mixed-$random_hash\r\n" . "Content-Type: application/zip; 
				name=\"$pFileName\"\r\n" . "Content-Transfer-Encoding: 
				base64\r\n" . "Content-Disposition: attachment\r\n\r\n";
					$message .= $attachment;
					//$message .= "/r/n--PHP-mixed-$random_hash--";
					//send the email
					$mail = mail($to, $subject, $message, $headers);
					if($mail)
					{
					echo "Prepaid Mail success";
					echo "<br/>";
                                            if(isset($log_mode) && $log_mode==1){
						$this->_routingLog->logCreate($this->_logFile, 'WPR Routing file was successfully mailed');
                                            }
					}else{
					echo "Prepaid Mail Failed";
					echo "<br/>";
                                            if(isset($log_mode) && $log_mode==1){
						$this->_routingLog->logCreate($this->_logFile, 'WPR Routing file was not mailed.');
                                            }
					}
                                        if(isset($log_mode) && $log_mode==1){
                                            $this->_routingLog->logCreate($this->_logFile, 'WPR Routing file mail process Ended');
                                        }
				}
			}
        }
    }

}
