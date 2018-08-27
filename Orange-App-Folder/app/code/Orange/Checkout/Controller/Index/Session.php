<?php
namespace Orange\Checkout\Controller\Index;

class Session extends \Magento\Framework\App\Action\Action
{
	const CHECKOUT_SESSION_LOG = '/var/log/checkout_session.log';
    protected $resultPageFactory;
	protected $_checkoutSession;
	protected $_orangeHelper;
	public function __construct(
       \Magento\Framework\App\Action\Context $context,
	   \Magento\Framework\View\Result\PageFactory $resultPageFactory,
	   \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
	   \Magento\Checkout\Model\Session $checkoutSession,
	   \Orange\Upload\Helper\Data $orangeHelper
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
		$this->resultJsonFactory = $resultJsonFactory;
		$this->_checkoutSession 	 = $checkoutSession;
		$this->_orangeHelper = $orangeHelper;
    }
	
	public function objectToArray( $object )
	{
		if( !is_object( $object ) && !is_array( $object ) ) {
			return $object;
		}
		if( is_object( $object ) ) {
			$object = get_object_vars( $object );
		}
		return array_map( 'objectToArray', $object );
	}
	
    /**
     * Flush cache storage
     *
     */
    public function execute()
    {		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$quote = $objectManager->create('Magento\Checkout\Model\Cart')->getQuote();	
		$model = $this->_objectManager->create('Orange\Abandonexport\Model\Items');
		$postpaidModel = $this->_objectManager->create('Orange\Checkout\Model\Postpaid');
		$vissibleItems = $quote->getAllVisibleItems();
		$abandonexport = $model->getCollection()->addFieldToFilter('quote_id',$quote->getId());
		$data_fetch = array();
		if ($abandonexport->count() > 0 ) {
			$id = $abandonexport->getFirstItem()->getId();
			$abandonexport = $this->_objectManager->create('Orange\Abandonexport\Model\Items')->load($id);
		} 
		else {
			$abandonexport = $this->_objectManager->create('Orange\Abandonexport\Model\Items');
		}
		if($this->getRequest()->getParam('accountnumber')) {
		    $data_fetch = $this->_checkoutSession->getNewcheckout();
			$data_fetch['v_bank_transfer_type'] = "Domiciliation";
			$data_fetch['v_account_number'] = $this->getRequest()->getParam('accountnumber');
			$this->_checkoutSession->setNewcheckout($data_fetch);
			$quote->setAccountNumber($this->getRequest()->getParam('accountnumber'));
			//Saved bank type in quote for P3 – 39196493 – information not retained when hitting Back on Ogone page
			$quote->setBankTransferType('Domiciliation');
			$quote->save();
		}
		if($this->getRequest()->getParam('transfertype') == "Virement") {
		    $data_fetch = $this->_checkoutSession->getNewcheckout();
			$data_fetch['v_bank_transfer_type'] = "Virement";
			$data_fetch['v_account_number'] = $this->getRequest()->getParam('accountnumber');
			$this->_checkoutSession->setNewcheckout($data_fetch);
			$quote->setAccountNumber($this->getRequest()->getParam('accountnumber'));
			//Saved bank type in quote for P3 – 39196493 – information not retained when hitting Back on Ogone page
			$quote->setBankTransferType('Virement');
			$quote->save();
		}
		$ipaddress = $this->getClientIp();
		$dataArray=array();
		if($this->getRequest()->getParam('aStep') == 'step1') {
			$deletecartJoin = $this->getRequest()->getParam('deletecartJoin');
			$deleteItemid = explode(',',$deletecartJoin);
			$datas = '';
			$dataObj =  json_decode($this->getRequest()->getParam('saveData'));
			foreach ($dataObj as $obj) {
				// Here you can access to every object value in the way that you want
				$dataArray[$obj->name] = $obj->value;
				$datavalue = explode("-",$obj->name);
				if (isset($datavalue[1])) {
					$datas[$datavalue[1]][$datavalue[0]] = $obj->value;
				}
			}	
			$cart = $objectManager->create('Magento\Checkout\Model\Cart');
			$quote = $objectManager->create('Magento\Checkout\Model\Cart')->getQuote();
			if ($deleteItemid) {
				foreach($deleteItemid as $deleteItem) {
					$explodeitemId = explode('_',$deleteItem);
					if ($explodeitemId[0]) {
						$totalQty = $dataArray['totalqty'.'-'.$explodeitemId[0]] - 1;
						$dataArray['totalqty'.'-'.$explodeitemId[0]] = $totalQty;
						$params[$explodeitemId[0]]['qty'] = $totalQty;
						$cart->updateItems($params);
						$cart->saveQuote();
						$cart->save();
					}
				}
			}			
			if ($datas) {
				$postpaidModel = $this->_objectManager->create('Orange\Checkout\Model\Postpaid');
				$postpaidModelCollections = $postpaidModel->getCollection()->addFieldToFilter('quote_id',$quote->getId());
				if ($postpaidModelCollections->count() > 0 ) {
					foreach($postpaidModelCollections as $postpaidCollection) {
						$deletePostpaidCollection = $this->_objectManager->create('Orange\Checkout\Model\Postpaid')->load($postpaidCollection->getId());
						$deletePostpaidCollection->delete();
					}
				} 
				foreach ($vissibleItems as $items) {
					if (!isset($datas[$items->getId()]['totalqty'])) {
						continue;
					}
					for ($x = 1; $x <= $datas[$items->getId()]['totalqty']; $x++) {
						$updateQty = $items->getQty();
						if ($datas[$items->getId()]['totalqty'] !=$items->getQty()) {
							$updateQty = $datas[$items->getId()]['totalqty'] - $items->getQty();
						}
						$postpaidModel = $this->_objectManager->create('Orange\Checkout\Model\Postpaid');
						$pitemId = $items->getId().'_'.$x;
						$value = $datas[$pitemId];
						if(isset($datas[$pitemId]) && $value['design_te_existing_number_final_validation'] !=2) {
							$postpaidModel->setDesignSimNumber($value['design_sim_number']);
							$existingNumber = '';
							if (array_key_exists('design_te_existing_number', $value)) {	
								$existingNumber = $value['design_te_existing_number'];
							}
							$postpaidModel->setQuoteId($quote->getId());
							$postpaidModel->setQty($x);
							$postpaidModel->setItemId($items->getId());
							$postpaidModel->setDesignTeExistingNumber(str_replace(array('/', ' '), array('', ''), $existingNumber));
							$postpaidModel->setSubscriptionType($value['subscription_type']);
							$postpaidModel->setCurrentOperator($value['current_operator']);
							$postpaidModel->setNetworkCustomerNumber($value['network_customer_number']);
							$postpaidModel->setSimcardNumber($value['simcard_number']);
							$postpaidModel->setBillInName($value['bill_in_name']);
							$postpaidModel->setHoldersName($value['holders_name']);
							$postpaidModel->setHolderFirstname($value['holder_name']);
							$existingNumberValidation = 0;
							if (array_key_exists('design_te_existing_number_final_validation', $value)) {	
								$existingNumberValidation = $value['design_te_existing_number_final_validation'];
							}
							$postpaidModel->setDesignTeExistingNumberFinalValidation($existingNumberValidation);
							$postpaidModel->save();
						}
					}
				}
			}
			//$this->_orangeHelper->logCreate(self::CHECKOUT_SESSION_LOG,'session Id:'.$this->_checkoutSession->getSessionId());
			//$this->_orangeHelper->logCreate(self::CHECKOUT_SESSION_LOG,'Quote Id step1:'.$this->_checkoutSession->getQuote()->getId());
			//$this->_orangeHelper->logCreate(self::CHECKOUT_SESSION_LOG,$dataArray);//LOG Session data
			$this->_checkoutSession->setNewcheckout($dataArray);
			$firstStepValue = serialize($dataArray);
			$abandonexport->setIpaddress($ipaddress);
			$abandonexport->setStepfirst($firstStepValue);
			$abandonexport->setCreateAt(date('Y-m-d H:i:s'));
			$abandonexport->setQuoteId($quote->getId());
			$abandonexport->setCheckStepStat($this->getRequest()->getParam('checkstepstat'));
			$abandonexport->save();	 
			return  $this->resultJsonFactory->create()->setData($dataArray);  	
		}
		else if($this->getRequest()->getParam('aStep') == 'step2')
		{
			$dataObj =  json_decode($this->getRequest()->getParam('saveData'));
			$postpaidModelCollections = $postpaidModel->getCollection()
											->addFieldToFilter('quote_id',$quote->getId());											
			if ($postpaidModelCollections->count() > 0 ) {
				foreach($postpaidModelCollections as $postpaidCollection) {
					$postpaidModel = $this->_objectManager->create('Orange\Checkout\Model\Postpaid')->load($postpaidCollection->getId());
					$postpaidModel->setProPacks('');	
					$postpaidModel->save();
				}
			}
			foreach ($dataObj as $obj)
			{
				// Here you can access to every object value in the way that you want
				$dataArray[$obj->name] = $obj->value;
				if(($obj->name == 'Smartphone ProPack' || $obj->name == 'Reduction ProPack' || $obj->name == 'Surf ProPack') && $obj->value!='')
				{
					$postpaidModel = $this->_objectManager->create('Orange\Checkout\Model\Postpaid');
					$items = trim($obj->value,",");
					$propackItems = explode(",",$items);
					$propackItems = array_unique($propackItems);
					foreach($propackItems as $propackItem) {
						$propackItemNumber = explode("_", $propackItem);
						$this->_objectManager->get('Psr\Log\LoggerInterface')->log(100,print_r($propackItemNumber,true));
						$itemNumber = $propackItemNumber[0];
						$itemQtyId = 1;
						if(count($propackItemNumber) > 1) {
							$itemQtyId = $propackItemNumber[1];
						}
						$postpaidModelCollections = $postpaidModel->getCollection()
														->addFieldToFilter('quote_id',$quote->getId())
														->addFieldToFilter('item_id',$itemNumber)
														->addFieldToFilter('qty',$itemQtyId);
						if ($postpaidModelCollections->count() > 0 ) {
							foreach($postpaidModelCollections as $postpaidCollection) {
								$postpaidModel = $this->_objectManager->create('Orange\Checkout\Model\Postpaid')->load($postpaidCollection->getId());
								$propacks = $postpaidModel->getProPacks().','.$obj->name;
								$propacks = trim($propacks,",");
								$propacks = array_unique(explode(",", $propacks));								
								$propacks = implode(",", $propacks);
								$postpaidModel->setProPacks($propacks);	
								$postpaidModel->save();
							}
						}
					}

				}
				if($obj->name =='iew_items')
				{
					$datas = json_decode($this->getRequest()->getParam('saveData'),true);
					$this->_updateIewItems($obj->value,$datas);
				}
				if($obj->name == 'gender'){
					$quote->getBillingAddress()->setPrefix($obj->value);				
				}
				if($obj->name == 'first_name'){
					$quote->getBillingAddress()->setFirstname($obj->value);				
				}
				if($obj->name == 'last_name'){
					$quote->getBillingAddress()->setLastname($obj->value);				
				}
				if($obj->name == 'email'){
					$quote->getBillingAddress()->setEmail($obj->value);				
				}
				if($obj->name == 'b_city'){
					$quote->getBillingAddress()->setCity($obj->value);
					$quote->getBillingAddress()->setRegion($obj->value);				
				}
				if($obj->name == 'b_street'){
					$quote->getBillingAddress()->setStreet($obj->value);				
				}
				if($obj->name == 'nationality'){				
					$quote->getBillingAddress()->setCountryId("BE");				
				}
				
				if($obj->name == 'b_postcode_city'){
					$quote->getBillingAddress()->setPostcode($obj->value);				
				}
				if($obj->name == 'cust_telephone'){
					$quote->getBillingAddress()->setTelephone($obj->value);				
				}
			}
			//$this->_orangeHelper->logCreate(self::CHECKOUT_SESSION_LOG,'session Id:'.$this->_checkoutSession->getSessionId());
			//$this->_orangeHelper->logCreate(self::CHECKOUT_SESSION_LOG,'Quote Id step2:'.$this->_checkoutSession->getQuote()->getId());
			//$this->_orangeHelper->logCreate(self::CHECKOUT_SESSION_LOG,$dataArray);//LOG Session data
			$this->_checkoutSession->setNewcheckout($dataArray);
			if($dataArray['c_dob'] != "")
			{
				$c_dob =  $dataArray['c_dob'];
				$c_dob = str_replace('/', '-', $c_dob);
				$dob = date("Y-m-d H:i:s", strtotime($c_dob));
				$quote->setDateOfBirth($dob);
				$quote->setCustomerPrefix($dataArray['gender']);
			 
			} 
			if(isset($dataArray['v_bank_transfer_type']) && $dataArray['v_bank_transfer_type'] != "Domiciliation")
			{
            $quote->setAccountNumber($this->getRequest()->getParam('accountnumber'));
			$quote->save();
			}
			/* shipping and payment method not loading proberly for one page checkout - 5694 */
			if ($this->getRequest()->getParam('shippingMethodStep') == 'freeshipping') {
				$shippingAddress = $quote->getShippingAddress();
				$shippingAddress->setCollectShippingRates(true)
								->collectShippingRates()
								->setShippingMethod('freeshipping_freeshipping'); 
				$quote->save();
			}
			/* end 5694 */
			$firstStepValue = serialize($dataArray);
			$abandonexport->setIpaddress($ipaddress);
			$abandonexport->setStepsecond($firstStepValue);
			$abandonexport->setCreateAt(date('Y-m-d H:i:s'));
			$abandonexport->setQuoteId($quote->getId());
			$abandonexport->setCheckStepStat($this->getRequest()->getParam('checkstepstat'));
			$abandonexport->save();		
			return  $this->resultJsonFactory->create()->setData($dataArray);
		}	
		else if($this->getRequest()->getParam('blurSession') == 'true')
		{
			$dataObj =  json_decode($this->getRequest()->getParam('saveData'), true);
			foreach ($dataObj as $obj)
			{
				// Here you can access to every object value in the way that you want
				$dataArray[$obj['name']] = $obj['value'];
				if ($this->getRequest()->getParam('currentStep') == 'step1_tab') {
					$dataArray['step'] = '';
				} 
				else if ($this->getRequest()->getParam('currentStep') == 'step2_tab') {
					$dataArray['step'] = 'step2';
				}
			    //$this->_orangeHelper->logCreate(self::CHECKOUT_SESSION_LOG,'session Id:'.$this->_checkoutSession->getSessionId());
			    //$this->_orangeHelper->logCreate(self::CHECKOUT_SESSION_LOG,'Quote Id step3:'.$this->_checkoutSession->getQuote()->getId());
				//$this->_orangeHelper->logCreate(self::CHECKOUT_SESSION_LOG,$dataArray);//LOG Session data
				$this->_checkoutSession->setNewcheckout($dataArray);
			}
			$responsedataArrays['success'] = 'true';
			$firstStepValue = serialize($dataArray);
			$abandonexport->setIpaddress($ipaddress);
			$abandonexport->setStepsecond($firstStepValue);
			$abandonexport->setQuoteId($quote->getId());
			$abandonexport->setCreateAt(date('Y-m-d H:i:s'));
			$abandonexport->save();
			return  $this->resultJsonFactory->create()->setData($responsedataArrays);
		} else if($this->getRequest()->getParam('removeFinalStep') == 'removefinal') {
			$abandonexport->setCheckStepStat($this->getRequest()->getParam('checkstepstat'));
			$abandonexport->save();
		}
	}
	private function _updatePropackItem($propack,$propackItems)
	{
		$propackItems = explode(",",$propackItems);
		$items = $this->_checkoutSession->getQuote()->getAllItems();
		foreach($items as $item):
			if(in_array($item->getId(), $propackItems)) {
				$updatedPropack = $item->getPropacks()!='' ? $item->getPropacks().','.$propack : $propack;
				$propacks = explode(",",$updatedPropack);
				$propacks = array_unique($propacks);
				$item->setPropacks(implode(",",$propacks));
				$item->save();
			}
		endforeach;
	}
    private function _updateIewItems($iewItems,$params)
	{
		$iewValueUpdate = 0;
		$IewItems = explode(",",$iewItems);			
		if(count($IewItems) > 0):
			$items = $this->_checkoutSession->getQuote()->getAllItems();
			foreach($items as $item):
				if(in_array($item->getId(), $IewItems)) {				
					$isTenEuroKey = array_search('is_teneuro_'.$item->getId(), array_column($params, 'name'));
					$hasTenEuro = $params[$isTenEuroKey]['value'];
					if($hasTenEuro == 'yes') {
						$item->setIewIsTeneuro($params[$isTenEuroKey]['value']);
						$item->setIewTelephone($params[array_search('iew_telephone_'.$item->getId(), array_column($params, 'name'))]['value']);
						$hasContract = $params[array_search('iew_contract_'.$item->getId(), array_column($params, 'name'))]['value'];
						$item->setIewContract($hasContract);
						if($hasContract == 0) {
							$item->setIewFirstname($params[array_search('iew_first_name_'.$item->getId(), array_column($params, 'name'))]['value']);
							$item->setIewLastname($params[array_search('iew_last_name_'.$item->getId(), array_column($params, 'name'))]['value']);
							$item->setIewDob($params[array_search('iew_dob_'.$item->getId(), array_column($params, 'name'))]['value']);
						}
						else {
							$item->setIewFirstname(NULL);
							$item->setIewLastname(NULL);
							$item->setIewDob(NULL);
						}						
					}
					else {
						$item->setIewIsTeneuro(NULL);
						$item->setIewTelephone(NULL);
						$item->setIewContract(NULL);
						$item->setIewFirstname(NULL);
						$item->setIewLastname(NULL);
						$item->setIewDob(NULL);
					}
					$item->save(); 
				}
			endforeach;
		endif;
	}
	
	public function getClientIp() 
	{
		$ipaddress = '';
		if (getenv('HTTP_CLIENT_IP'))
			$ipaddress = getenv('HTTP_CLIENT_IP');
		else if(getenv('HTTP_X_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
		else if(getenv('HTTP_X_FORWARDED'))
			$ipaddress = getenv('HTTP_X_FORWARDED');
		else if(getenv('HTTP_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_FORWARDED_FOR');
		else if(getenv('HTTP_FORWARDED'))
		   $ipaddress = getenv('HTTP_FORWARDED');
		else if(getenv('REMOTE_ADDR'))
			$ipaddress = getenv('REMOTE_ADDR');
		else
			$ipaddress = 'UNKNOWN';
		return $ipaddress;
	}

}