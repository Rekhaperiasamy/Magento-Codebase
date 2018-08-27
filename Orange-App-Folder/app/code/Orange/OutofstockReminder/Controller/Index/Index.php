<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Orange\OutofstockReminder\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Framework\App\Action\Action {

    protected $_resultPageFactory;
    private $objectManager;

    public function __construct(Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory,
    //\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
            \Magento\Framework\Stdlib\DateTime\DateTime $date, \Magento\CatalogInventory\Api\StockStateInterface $stockItem, \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->resourceConnection = $resourceConnection;
        //$this->_timezoneInterface = $timezoneInterface;	
        $this->date = $date;
        $this->stockItem = $stockItem;
        parent::__construct($context);
    }

    public function execute() {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $data = array();
            $data['name'] = $this->test_input($this->getRequest()->getParam('name'));
            $data['productid'] = $this->test_input($this->getRequest()->getParam('id'));
            $data['lastname'] = $this->test_input($this->getRequest()->getParam('firstname'));
            $data['title'] = $this->test_input($this->getRequest()->getParam('title'));
            $data['email'] = $this->test_input($this->getRequest()->getParam('email'));
            $data['product_url'] = $this->test_input($this->getRequest()->getParam('url'));
            $data['product_name'] = $this->test_input($this->getRequest()->getParam('productname'));
            $data['sku'] = $this->test_input($this->getRequest()->getParam('sku'));
            $data['price'] = $this->test_input($this->getRequest()->getParam('price'));
			$data['product_image'] = $this->test_input($this->getRequest()->getParam('product_image'));
            $valid = $this->validationsFunction($data['name'], $data['lastname'], $data['email']);

            if ($valid == "succ") {
                $data['created_at'] = $this->date->gmtDate();
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $resolver = $objectManager->get('Magento\Framework\Locale\Resolver');
                $data['lang'] = $resolver->getLocale();
                $data['status'] = 'requested';
                $data['option_newsletter'] = 'newsletter';
                $data['dob'] = 'dob';
                $data['sms'] = 'sms';
                $data['msisdn'] = 'msisdn';
                $this->modelSaverData($data);
                echo $valid;
            } else {
                echo $valid;
            }
        } else {
            echo __('This is Not An Ajax Call');
            header("Location: http://" . $_SERVER['SERVER_NAME']);
            die();
        }
    }

    public function modelSaverData($data) {
        try {
            $model = $this->_objectManager->create('Orange\OutofstockReminder\Model\OutofstockReminder');
            $model->setData($data);
            $model->save();
            return;
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\RuntimeException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong.'));
        }
    }

    public function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    public function validationsFunction($firstname, $lastname, $email) {
        if ($firstname == "") {
            return "fname";
        } else if ($lastname == "") {
            return "lastname";
        } else if ($email == "") {
            return "email";
        } else {
            return "succ";
        }
    }

}
