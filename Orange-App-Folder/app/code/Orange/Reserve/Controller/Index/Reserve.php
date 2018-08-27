<?php

namespace Orange\Reserve\Controller\Index;

class Reserve extends \Magento\Framework\App\Action\Action {

    private $objectManager;
    private $helper;

    public function execute() {
        if ($this->getRequest()->isAjax()) {
            $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $this->helper = $this->objectManager->get('Orange\Reserve\Helper\Data');
            $myArray = explode(',', $this->helper->validationCriteria());
            $collection = $this->objectManager->create('Orange\Reserve\Model\Blacklist')->getCollection();
            $ipAddress = $this->getRequest()->getParam('ip');
            $reserve = array();
            $reserve['fname'] = $this->getRequest()->getParam('fname');
            $reserve['name'] = $this->getRequest()->getParam('name');
            $reserve['email'] = $this->getRequest()->getParam('email');
            $reserve['phone'] = $this->getRequest()->getParam('phone');
            $reserve['shop_id'] = $this->getRequest()->getParam('shop');
            $reserve['device'] = $this->getRequest()->getParam('sku');
            $reserve['pname'] = $this->getRequest()->getParam('pname');
            $reserve['saddress'] = $this->getRequest()->getParam('saddress');
            $reserve['ip_address'] = $this->getRequest()->getParam('ip');
			$reserve['shop_url'] = $this->getRequest()->getParam('shop_url');
            $valid = $this->ValidationData($reserve);
            if ($valid == 'succ') {

                if (count($myArray)) {

                    $helperIntervalInDays = $this->helper->intervalDays();
                    $helperOneDayOrderQTY = $this->helper->intervalPeriodOrder();

                    if ($helperIntervalInDays == '1') {
                        $dateStart = date('Y-m-d' . ' 00:00:00');
                        $dateEnd = date('Y-m-d' . ' 23:59:59');
                    } else {
                        $dateStart = date('Y-m-d' . ' 00:00:00', strtotime($helperIntervalInDays . 'Days'));
                        $dateEnd = date('Y-m-d' . ' 23:59:59');
                    }

                    $collection = $this->objectManager->create('Orange\Reserve\Model\Blacklist')->getCollection();

                    if (count($myArray) > 0) {

                        if (in_array(0, $myArray)) {
                            $collection->addFieldToFilter('name', $reserve['name']);
                        }
                        if (in_array(1, $myArray)) {
                            $collection->addFieldToFilter('firstname', $reserve['fname']);
                        }
                        if (in_array(2, $myArray)) {
                            $collection->addFieldToFilter('ip_address', $ipAddress);
                        }
                        if (in_array(3, $myArray)) {
                            $collection->addFieldToFilter('email_address', $reserve['email']);
                        }
                        $result = $this->fetchFromBlackListTable($dateStart, $dateEnd);
                        if (count($collection->getData()) >= 1) {
                            if (count($result) != 0) {
								$static = 'static';
                                if (count($result) >= $helperOneDayOrderQTY) {
                                    $static = 'static';
                                } elseif (count($result) < $helperOneDayOrderQTY) {
                                    $this->UpdateValidator($reserve);
									return;
                                }
								echo $static;
                            } else {
                                $this->UpdateValidator($reserve);
                            }
                        } elseif (count($collection->getData()) == 0) {
                            $this->UpdateValidator($reserve);
                        } else {
                            echo __("SomeThing Went Wrong Try again Later");
                        }
                    } else if (count($myArray) == 1) {
                        if (in_array(2, $myArray)) {
                            $collection->addFilter('ip_address', $ipAddress);
                            if (count($collection->getData()) == 0) {
                                echo 'static';
                            } else {
                                $this->UpdateValidator($reserve);
                            }
                        }
                    } else if (count($myArray) == 0) {
                        $this->UpdateValidator($reserve);
                    }
                } else {
                    $this->UpdateValidator($reserve);
                }
            } else {
                echo $valid;
            }
        } else {
            echo __('This is Not An Ajax Call');
            header("Location: http://" . $_SERVER['SERVER_NAME']);
            die();
        }
    }

    public function ValidationData($arrayValue) {

        if ($arrayValue['fname'] == '') {
            return "First Name is Empty";
        } else if ($arrayValue['name'] == '') {
            return "Last Name is Empty";
        } else if ($arrayValue['email'] == '') {
            return "Email is Empty";
        } else if ($arrayValue['phone'] == '') {
            return "Phone Number is Empty";
        } else {
            return "succ";
        }
    }

    public function fetchFromBlackListTable($dateStart, $dateEnd) {
        $resource = $this->objectManager->get('Magento\Framework\App\ResourceConnection');
        $connectionDb = $resource->getConnection();
        $collectionReserve = "SELECT * FROM reserve_reserve WHERE created_date >= '" . $dateStart . "' and created_date <= '" . $dateEnd . "';";
        return $connectionDb->fetchAll($collectionReserve);
    }

    public function UpdateValidator($reserve) {
        $modelData = $this->objectManager->create('Orange\Reserve\Model\Reserveprod')->trigger($reserve);

        //$this->sendReserverEmail($modelData,$store->getStore()->getCode());

        if ($modelData) {
            $model = $this->objectManager->create('Orange\Reserve\Model\Reserve');
            $otherDet = json_encode($modelData);
            $model->setReserveId($modelData['rid']);
            $model->setCustomerName($modelData['fname']);
            $model->setSku($modelData['sku']);
            $model->setShopId($modelData['rid']);
            $model->setOtherDetails($otherDet);
            $model->save();

            $store = $this->objectManager->get('Magento\Store\Model\StoreManagerInterface');
            $this->sendReserverEmail($modelData, $store->getStore()->getCode());
            /*
              $transportbuilder = $this->objectManager->create('Magento\Framework\Mail\Template\TransportBuilder');
              $requestemailtemplate = 7;
              $templateOptions = array('area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $store->getStore()->getId());

              if ($requestemailtemplate) {
              $transport = $transportbuilder->setTemplateIdentifier($requestemailtemplate)
              ->setTemplateOptions($templateOptions)
              ->setTemplateVars($modelData)
              ->setFrom(array('name' => $modelData['fname'], 'email' => $modelData['email']))
              ->addTo($modelData['email'])
              ->getTransport();

              //$transport->sendMessage();
              }
             */
        }
        $this->success($modelData);
    }

    public function success($modelData) {
        $daataVale = $this->helper->curlSuccess();
        $dataValue = str_replace("[customer firstname]", $modelData['fname'], $daataVale);
        $dataValue = str_replace("[device]", $modelData['pname'], $dataValue);
        $dataValue = str_replace("[shop name]", $modelData['store'], $dataValue);
        $dataValue = str_replace("[business day]", $modelData['date'], $dataValue);

        $dat = '<div class="row"><div class="col-xs-12 padding-xs-b-m">' . $dataValue . '</div></div>';

        echo $dat;
    }

    public function sendReserverEmail($modelData, $store) 
	{
		//Mail sender
		$ob = \Magento\Framework\App\ObjectManager::getInstance();
		$scopeConfig = $ob->create('Magento\Framework\App\Config\ScopeConfigInterface');
		$adminemail = $scopeConfig->getValue('trans_email/ident_custom2/email');
		$adminname = $scopeConfig->getValue('trans_email/ident_custom2/name');
		$storeModel = $ob->get('Magento\Store\Model\StoreManagerInterface');
		$transportbuilder = $ob->create('Magento\Framework\Mail\Template\TransportBuilder');
		
		$data['customer_first_name'] = $modelData['fname'];
		$data['device_name'] = $modelData['pname'];
		$data['shop_name'] = $modelData['store'];//need to check live response hence hardcoded
		$shopaddress = strip_tags($modelData['store']);
		$a  = explode("</strong>", $modelData['store']);
		$shoppaddress1 = '';
		$shoppaddress2 = '';
		if(count($a) == 2) {
			$shopaddress = strip_tags($a[0]);
			$asecondAddress = explode(' ',$a[1]);
			if($asecondAddress > 2) {
				$a1 = array_slice($asecondAddress,-2,2);
				$shoppaddress2 = implode(" ",$a1);
				array_pop($asecondAddress);
				array_pop($asecondAddress);
				$shopaddress1 = implode(" ",$asecondAddress);
			}
		}
		$data['shop_address'] = $shopaddress;
		$data['shop_address1'] = $shopaddress1;
		$data['shop_address2'] = $shoppaddress2;
		
		$data['reservation_number'] = $modelData['rid'];
		$data['reservation_validity'] = $modelData['date'];
		$data['locator'] = $modelData['shop_url'];//need to check live response hence hardcoded
		
		/** Sample Data **/
		// $data = array();
		// $data['customer_first_name'] = 'Bala'; 
		// $data['device_name'] = 'Samsung';
		// $data['shop_name'] = 'MOBISTAR RUE NEUVE';//need to check live response hence hardcoded
		// $data['shop_address'] = '<strong>MOBISTAR RUE NEUVE</strong><br />AVENUE DU BOURGET 3<br />1140 ANTWERPEN';
		// $data['reservation_number'] = '019CC;2015000008';
		// $data['reservation_validity'] = '17/09/2016';
		// $data['locator'] = 'shops.mobistar.be/antwerpen_de_keyserlei';//need to check live response hence hardcoded
		// $modelData = array();
		// $modelData['email'] = 'BG00478569@TechMahindra.com';
		/******************/
		
		if($store=="nl"){
			$confirmemailtemplate = 'Click and Reserve NL';
		}
		elseif($store=="fr") {
			$confirmemailtemplate = 'Click and Reserve FR';
		}
		
		//
		$emailTemplateId = '';
	    $om = \Magento\Framework\App\ObjectManager::getInstance();
		$colection = $om->create('Magento\Email\Model\ResourceModel\Template\Collection');
		$colection->addFieldToFilter("template_code",$confirmemailtemplate)->getFirstItem();
		foreach($colection as $col) {
			$emailTemplateId = $col->getId();
		}
		
		if($emailTemplateId) {
			$templateOptions = array('area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $storeModel->getStore()->getId());
			$transportconfirm = $transportbuilder->setTemplateIdentifier($emailTemplateId)
							->setTemplateOptions($templateOptions)
							->setTemplateVars($data)
							->setFrom(array('name' => $adminname, 'email' => $adminemail))
							->addTo($modelData['email'])
							->getTransport();	
			$transportconfirm->sendMessage();
		}
		
    }
}
