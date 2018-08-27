<?php

/**
 * Copyright © 2015 Orange . All rights reserved.
 */

namespace Orange\Reserve\Helper;

use \Magento\Store\Model\StoreManagerInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    protected $_storeManager;
    protected $scopeConfig;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(\Magento\Framework\App\Helper\Context $context
    ) {      
        $this->scopeConfig = $context->getScopeConfig();
        parent::__construct($context);
    }

    public function clickAndReserveEnable() {
        return $this->scopeConfig->getValue('click/reserve_configuration/click_reserve_enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function zipcodeCall() {
        $data = array();
        $data['url'] = $this->scopeConfig->getValue('click/reserve_configuration/zipcode_url', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $data['sslpass'] = $this->scopeConfig->getValue('click/reserve_configuration/zipcode_passData', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $data['pem'] = $this->scopeConfig->getValue('click/reserve_configuration/zipcode_pem_file', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $data['key'] = $this->scopeConfig->getValue('click/reserve_configuration/zipcode_key_file', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $data['cer'] = $this->scopeConfig->getValue('click/reserve_configuration/zipcode_cer_file', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $data;
    }

    public function getStoreId() {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $val = $objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getCode();

        return $val;
    }

    public function reserveCall() {
        $data = array();
        $data['url'] = $this->scopeConfig->getValue('click/reserve_configuration/reservecall_url', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $data['uname'] = $this->scopeConfig->getValue('click/reserve_configuration/reservecall_uname', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $data['pass'] = $this->scopeConfig->getValue('click/reserve_configuration/reservecall_pass', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $data['pem'] = $this->scopeConfig->getValue('click/reserve_configuration/reservecall_pem_file', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $data['key'] = $this->scopeConfig->getValue('click/reserve_configuration/reservecall_key_file', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $data['cer'] = $this->scopeConfig->getValue('click/reserve_configuration/reservecall_cer_file', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        return $data;
    }

    public function intervalPeriodOrder() {

        return $this->scopeConfig->getValue('click/reserve_configuration/interval_orders', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function intervalDays() {

        return $this->scopeConfig->getValue('click/reserve_configuration/period_days', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function validationCriteria() {

        return $this->scopeConfig->getValue('click/reserve_configuration/blacklist_based_codition', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function errorCodeZipcode() {
		$validation = array();
		for($i=0; $i<=9; $i++) {
			$validation["$i"] = $this->scopeConfig->getValue("click/reserve_error_configuration/curl_call_error$i", \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		}
		return $validation;
    }

    public function curlSuccess() {

        return $this->scopeConfig->getValue('click/reserve_error_configuration/curl_call_success', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
     public function numberValidation() {
        $data = array();
        $data['url'] = $this->scopeConfig->getValue('common/numbervalidation_configuration/number_url', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);       
        $data['pem'] = $this->scopeConfig->getValue('common/numbervalidation_configuration/number_pem_file', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $data['key'] = $this->scopeConfig->getValue('common/numbervalidation_configuration/number_key_file', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $data['cer'] = $this->scopeConfig->getValue('common/numbervalidation_configuration/number_cer_file', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $data;
    }
	/* customer number validation configuration*/
	public function customerNumberValidation() {
        $data = array();
        $data['url'] = $this->scopeConfig->getValue('common/customernumbervalidation_configuration/customer_number_url', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);       
        $data['pem'] = $this->scopeConfig->getValue('common/customernumbervalidation_configuration/customer_pem_file', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $data['key'] = $this->scopeConfig->getValue('common/customernumbervalidation_configuration/customer_key_file', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $data['cer'] = $this->scopeConfig->getValue('common/customernumbervalidation_configuration/customer_cerf_file', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $data;
    }
	
	public function vatValidation() {
        $data = array();
        $data['url'] = $this->scopeConfig->getValue('common/vatvalidation_configuration/vatnumber_url', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);       
        $data['username'] = $this->scopeConfig->getValue('common/vatvalidation_configuration/vatnumber_username', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $data['password'] = $this->scopeConfig->getValue('common/vatvalidation_configuration/vatnumber_password', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $data;
    }

}
