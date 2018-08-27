<?php

namespace Orange\Webform\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Mnp extends \Magento\Framework\App\Action\Action {

    protected $resultPageFactory;
    protected $_transportBuilder;
    protected $scopeConfig;
    protected $request;

    public function __construct(
    Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory,
            ScopeConfigInterface $scopeConfig) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
        //$this->_transportBuilder = $transportBuilder;
    }

    public function execute() {
        $model = $this->_objectManager->create('Orange\Webform\Model\Mnpform');
        $transportbuilder = $this->_objectManager->create('Magento\Framework\Mail\Template\TransportBuilder');
        $store = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
		$storeCode = $store->getStore()->getCode();
        $messager = $this->_objectManager->create('\Magento\Framework\Message\ManagerInterface');
        $redirect = $this->_objectManager->create('\Magento\Framework\Controller\ResultFactory');
        $data = $this->getRequest()->getPostValue();
        if ($data) {
		    $data['current_mobile_phone_number'] = str_replace("/"," ", $data['current_mobile_phone_number']);
			$data['orange_mobile_phone_number'] = str_replace("/"," ", $data['orange_mobile_phone_number']);
		    if($storeCode=="nl"){$confirmemailtemplate = 3;}
			elseif($storeCode=="fr"){$confirmemailtemplate = 5;}
		    if (isset($data['do_you_want_to_receive_interesting_offers_and_the_latest_Orange'])) {
				$data['do_you_want_to_receive_interesting_offers_and_the_latest_Orange'] = __('Oui');
			} else {
				$data['do_you_want_to_receive_interesting_offers_and_the_latest_Orange'] = __('Non');
			}
			if($data['card_type'] =='1'){  // 5611 Condition for check cart type
				if(isset($data['bill_in_name']) && $data['bill_in_name'] =='1') {
					$data['bill_in_name'] = __('Oui');
				} else {
					$data['bill_in_name'] = __('Non');
				}
			}else{
				unset($data['bill_in_name']);  
				unset($data['holders_name']);
				unset($data['holder_name']);
				unset($data['network_customer_number']);
			}
			if(isset($data['card_type']) && $data['card_type'] =='1') {
				$data['card_type'] = __('Abonnement');
			} else {
				$data['card_type'] = __('Carte prépayée');
			}
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
			$dateobject = new \DateTime('now', new \DateTimeZone('CET'));
			$data['create_date'] = $dateobject->format('Y-m-d H:i:s');
			$magentoDateObject = $this->_objectManager->create('Magento\Framework\Stdlib\DateTime\DateTime');
			$data['create_at'] = $dateobject->format('Y-m-d');
			$data['create_time'] = $dateobject->format('H:i:s');	
		    $model->setData($data);
            $model->save();
            $data['lastid'] = $model->getId();
            $requestemailtemplate = $data['requestid'];
            //$confirmemailtemplate = $data['confirmid'];
            //print_r($data); exit;
			$data['firstname'] = ucfirst(strtolower(trim($data['firstname'])));
			$data['lastname']  =  ucfirst(strtolower(trim($data['lastname'])));
            $templateOptions = array('area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $store->getStore()->getId());
            $senderInfo = ['name' => $data['firstname'], 'email' => $data['email']];
            if ($requestemailtemplate && $reciever) {
                $transport = $transportbuilder->setTemplateIdentifier($requestemailtemplate)
                        ->setTemplateOptions($templateOptions)
                        ->setTemplateVars($data)
                        ->setFrom(array('name' => $senderEntraName, 'email' => $senderEntraemail))
                        ->addTo($receiverInfo)
						->addCc($reciverINCC)
                        ->getTransport();
                //print "<pre>";
                //print_r($transport); 
                //print "</pre>";
                //exit;
                $transport->sendMessage();
            }
            if ($confirmemailtemplate && $senderEntraemail) {
                $transportconfirm = $transportbuilder->setTemplateIdentifier($confirmemailtemplate)
                        ->setTemplateOptions($templateOptions)
                        ->setTemplateVars($data)
                        ->setFrom(array('name' => $senderEntraName, 'email' => $senderEntraemail))
                        ->addTo($data['email'])
                        ->getTransport();
                $transportconfirm->sendMessage();
            }

           // $messager->addSuccess(__('your form submited successfully'));
           // $resultRedirect = $redirect->create(ResultFactory::TYPE_REDIRECT);
           // $resultRedirect->setUrl($this->_redirect->getRefererUrl());
			$resultPage = $this->_resultPageFactory->create();
			$resultPage->getConfig()->getTitle()->set(__('MNP'));
            return $resultPage;
        } else {
            $messager->addError(__('Your form is error'));
            $resultRedirect = $redirect->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }

        //$model->getCollection()
        return $this->_resultPageFactory->create();
    }
	
	public function entraCredentials() {
        $Credentials = array();
        $Credentials['Entra_EmailSender'] = $this->scopeConfig->getValue('entra/mnp_entra_configuration/mnp_entra_sender', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $Credentials['Entra_EmailSenderName'] = $this->scopeConfig->getValue('entra/mnp_entra_configuration/mnp_entra_sender_name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $Credentials['Entra_EmailReciever'] = $this->scopeConfig->getValue('entra/mnp_entra_configuration/mnp_entra_reciever', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$Credentials['Entra_EmailReciever_Name'] = $this->scopeConfig->getValue('entra/mnp_entra_configuration/mnp_entra_receiver_name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $Credentials['Entra_Email_Reciever_Cc'] = $this->scopeConfig->getValue('entra/mnp_entra_configuration/mnp_entra_reciever_cc', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $Credentials;
    }

}
