<?php
namespace Orange\Webform\Controller\Index;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
class Activer extends \Magento\Framework\App\Action\Action {
    protected $resultPageFactory;
	protected $_transportBuilder;
	protected $scopeConfig;
	protected $request;
    public function __construct(
	Context $context,
	 \Magento\Framework\Stdlib\DateTime\DateTime $date,
	\Magento\Framework\App\ResourceConnection $resourceConnection,
	\Magento\Framework\View\Result\PageFactory $resultPageFactory,
	ScopeConfigInterface $scopeConfig){
		 $this->date = $date;
        $this->_resultPageFactory = $resultPageFactory;
		$this->resourceConnection = $resourceConnection;
		$this->scopeConfig = $scopeConfig;
        parent::__construct($context);
		//$this->_transportBuilder = $transportBuilder;
    }
    public function execute()
    {
	        $model = $this->_objectManager->create('Orange\Webform\Model\Activerform');
			$newmodel = $this->_objectManager->create('Orange\Webform\Model\Orderimport');
			$transportbuilder = $this->_objectManager->create('Magento\Framework\Mail\Template\TransportBuilder');
			$store = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
			$messager = $this->_objectManager->create('\Magento\Framework\Message\ManagerInterface');
			$redirect = $this->_objectManager->create('\Magento\Framework\Controller\ResultFactory');
			$data = $this->getRequest()->getPostValue();
			// print_r($data);
			// echo $data['onum'];
			// exit;
			//$totalorderCount = $model->getCollection()->addFieldToFilter('id',"1")->Count();
			$objDate = $this->_objectManager->create('Magento\Framework\Stdlib\DateTime\DateTime');
			if(isset($data)){

			    $totalorderCount = $model->getCollection()->addFieldToFilter('order_number',$data['onum'])->Count();
				if ($totalorderCount > 0) {
					echo json_encode(array("value" => __("Your Activation request is already received. One request is allowed per order")));
					exit;

				}
			    $order = $this->_objectManager->create('Magento\Sales\Model\Order')->loadByIncrementId($data['onum']);


				$newDate = $this->date->date('Y-m-d', strtotime(str_replace('/', '-', $data['dob'])));
				//echo $newDate;
			    if ($order->getId()) {
					$data['email'] = $order->getCustomerEmail();
					$data['firstname'] = $order->getBillingAddress()->getFirstname();
					$data['lastname'] = $order->getBillingAddress()->getLastname();
					$data['strftime'] = strftime("%d %B, %Y - %I:%M");
					$data['day'] = $this->getDay($store->getStore()->getCode());
					$pos = strpos($order->getDateOfBirth(), $newDate);
					if ($pos === false) {
					//echo __("Your date of birth not matched in your order");
					echo json_encode(array("id" => "first","value" => __("Your date of birth not matched in your order")));
					exit;

					}
					$connection = $this->resourceConnection->getConnection();
					$tableName = $this->resourceConnection->getTableName('sales_order');
					$sql = "UPDATE ".$tableName." SET Active = 1,StatusFlag = 2,bi_tracking_flag=0 WHERE entity_id = ".$order->getId()."";
					$connection->query($sql);
				}
				else if($order->getId() == "") {
					$collection = $newmodel->getCollection()->addFieldToFilter('orderid',$data['onum']);
					//echo "<pre>";

					if($collection->getData()){
						$data['email'] = $collection->getData()[0]['email'];;
					$data['firstname'] = $collection->getData()[0]['firstname'];
					$data['lastname'] = $collection->getData()[0]['lastname'];
					$data['strftime'] = strftime("%d %B, %Y - %I:%M");
					$data['day'] = $this->getDay($store->getStore()->getCode());

					//echo $collection->getData()[0]['dateofbirth'];

					$pos = strpos($collection->getData()[0]['dateofbirth'], $newDate);
					if ($pos === false) {
						echo json_encode(array("value" => __("Your date of birth not matched in your order")));
						exit;

					}

					}
					else {
					echo json_encode(array("value" => __("Your order id is invalid")));
						exit;

					}
				}
				else {
						echo json_encode(array("value" => __("Your order id is invalid")));
						exit;

				}

				$requestemailTemplate = $data['requestid'];
				$confirmemailTemplate = $data['confirmid'];
				/* multiple Email Configuration */
				$info = $this->entraCredentials();
				$senderEntraemail = $info['Entra_EmailSender'];
				$senderEntraName  = $info['Entra_EmailSenderName'];
				$reciever = $info['Entra_EmailReciever'];
				$adminname = $info['Entra_EmailReciever_Name'];
				$reciverCC    = $info['Entra_Email_Reciever_Cc'];
				$receiverInfo = explode(',',$reciever);
				$reciverINCC = explode(',',$reciverCC);
				/* End Mnp multiple email configuration */
				$model->setData($data);
				$model->setData('order_number',$data['onum']);
				$model->setData('date_of_birth',$newDate);
				$dateobject = new \DateTime('now', new \DateTimeZone('CET'));
				$model->setData('ActiverFormDate',$dateobject->format('Y-m-d H:i:s'));
                $magentoDateObject = $this->_objectManager->create('Magento\Framework\Stdlib\DateTime\DateTime');
				$model->setData('create_date',$dateobject->format('Y-m-d'));
				$model->setData('create_time',$dateobject->format('H:i:s'));
				$model->save();

				// Set order value and page title in session for tealium tag.
        $customerSession = $this->_objectManager->get('Magento\Customer\Model\Session');
        $customerSession->setData('tealium_order_id', $data['onum']);
        if (!empty($data['title'])) {
          $customerSession->setData('tealium_activer_page_title', $data['title']);
        }

				$email = $this->scopeConfig->getValue('trans_email/ident_support/email',ScopeInterface::SCOPE_STORE);
				$templateOptions = array('area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $store->getStore()->getId());
				$data['firstname'] = ucfirst(strtolower(trim($data['firstname'])));
				$data['lastname']  =  ucfirst(strtolower(trim($data['lastname'])));
				$senderInfo = ['name' => $data['firstname'],'email' => $data['email']];
				if ($requestemailTemplate && $reciever) {
					$data['order_number'] = $data['onum'];
					$data['date_of_birth'] = $data['dob'];
					$transport = $transportbuilder->setTemplateIdentifier($requestemailTemplate)
										->setTemplateOptions($templateOptions)
										->setTemplateVars($data)
										->setFrom(array('name' => $senderEntraName,'email' => $senderEntraemail))
										->addTo($receiverInfo)
										->addCc($reciverINCC)
										->getTransport();
					$transport->sendMessage();
				}
				if ($confirmemailTemplate && $senderEntraemail) {
					$transportconfirm = $transportbuilder->setTemplateIdentifier($confirmemailTemplate)
										->setTemplateOptions($templateOptions)
										->setTemplateVars($data)
										->setFrom(array('name' => $senderEntraName,'email' => $senderEntraemail))
										->addTo($data['email'])
										->getTransport();
					// //print "<pre>";
					// //print_r($transport);
					// //print "</pre>";
					// //exit;
					$transportconfirm->sendMessage();
				}
					echo json_encode(array("id" =>"success","code" =>$this->getbaseUrl(),"value" => __('Your form submited successfully')));
					exit;

				$resultPage = $this->_resultPageFactory->create();
				$resultPage->getConfig()->getTitle()->set(__('Activer'));
				return $resultPage;
			} else {
				echo json_encode(array("value" => __("Your form is error")));
				exit;

			}

		//$model->getCollection()
        return $this->_resultPageFactory->create();
    }
	public function getbaseUrl()
	{
	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
    return $storeManager->getStore()->getBaseUrl();
	}
	public function getDay($code) {
		$day = strftime("%A");
		if ($code == 'fr') {
			if($day == 'Monday') {
				return "lundi";
			} else if($day == 'Tuesday'){
				return "mardi";
			} else if($day == 'Wednesday') {
				return "mercredi";
			} else if($day == 'Thursday') {
				return "jeudi";
			} elseif($day == 'Friday') {
				return "vendredi";
			} else if($day == 'Saturday') {
				return "samedi";
			} else if($day == 'Sunday') {
				return "dimanche";
			}
		} else {
			return $day;
		}
	}
	public function entraCredentials() {
        $Credentials = array();
        $Credentials['Entra_EmailSender'] = $this->scopeConfig->getValue('entra/activer_entra_configuration/activer_entra_sender', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $Credentials['Entra_EmailSenderName'] = $this->scopeConfig->getValue('entra/activer_entra_configuration/activer_entra_sender_name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $Credentials['Entra_EmailReciever'] = $this->scopeConfig->getValue('entra/activer_entra_configuration/activer_entra_reciever', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$Credentials['Entra_EmailReciever_Name'] = $this->scopeConfig->getValue('entra/activer_entra_configuration/activer_entra_reciever_name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $Credentials['Entra_Email_Reciever_Cc'] = $this->scopeConfig->getValue('entra/activer_entra_configuration/activer_entra_reciever_cc', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $Credentials;
    }
}
