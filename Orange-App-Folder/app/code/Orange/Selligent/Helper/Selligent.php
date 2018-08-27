<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Orange\Selligent\Helper;

use Magento\Framework\App\ObjectManager;

/**
 * Adminhtml Catalog helper
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Selligent extends \Magento\Framework\App\Helper\AbstractHelper {
    //const XML_PATH_EMAIL_TEMPLATE_FIELD  = 'entra/mail/entra_email_template';
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
    protected $_timezoneInterface;

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

    /**
     * @param Magento\Framework\App\Helper\Context $context
     * @param Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     */
    public function __construct(
    \Magento\Framework\App\Helper\Context $context, \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder, \Magento\Framework\File\Csv $csvProcessor, \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface
    ) {
        $this->_scopeConfig = $context;
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
        $this->_logger = $context->getLogger();
        $this->scopeConfig = $context->getScopeConfig();
        $this->_timezoneInterface = $timezoneInterface;
        $this->csvProcessor = $csvProcessor;
    }

    /**
     * Return store configuration value of your template field that which id you set for template
     *
     * @param string $path
     * @param int $storeId
     * @return mixed
     */
    protected function getConfigValue($path, $storeId) {


        return $this->scopeConfig->getValue(
                        $path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId
        );
    }

    /** @return string */
    function rm_locale() {
        /** @var \Magento\Framework\ObjectManagerInterface $om */
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magento\Framework\Locale\Resolver $resolver */
        $resolver = $om->get('Magento\Framework\Locale\Resolver');
        return $resolver->getLocale();
    }

    /**
     * Return store 
     *
     * @return Store
     */
    public function getStore() {
        return $this->_storeManager->getStore();
    }

    public function orderProcess($orderD,$orderItem,$incrementId,$transcation_id) {
        $lang = $this->rm_locale();
        $langarray = explode('_', $lang);
        $langval = $langarray[0];
        $orders = $orderD;
        $items = $orders->getAllItems();
        $quoteid = $orders->getQuoteId();

        if ($orders->getShippingAddress() != "") {
            $shippingAddress = $orders->getShippingAddress();
        } else {
            $shippingAddress = $orders->getBillingAddress();
        }
        //$shippingAddress = $orders->getBillingAddress();
        //$street = $shippingAddress->getStreet();
		
        //echo '<pre>Orders: ';print_r($street);
		
		//Defect - 5689 "change House number Integer value"
		$street = $this->boxData('b_street', $quoteid);
		$BOX = $this->boxData('b_box', $quoteid);
		$NUMBER = $this->boxData('b_number', $quoteid);
		if($BOX == '0'){
		$BOX = '';
		}
		/*
		if($b_number != ''){
			preg_match_all('!\d+!', $b_number, $matches);
			$NUMBER =  $matches[0][0];
			
			if($BOX == '' || $BOX == 0){
				$BOX = str_replace($NUMBER,'',$b_number);
			}else{
				$BOX = $this->boxData('b_box', $quoteid);
			}
		}*/
		/****************** 5689 **************/
        $user_params = array();
        $created_time = $this->_timezoneInterface->date(new \DateTime($orders->getCreatedAt()))->format('Y-m-d H:i:s');
        $user_params['TITLE'] = $shippingAddress->getPrefix();
        $user_params['BRAND'] = $this->scopeConfig->getValue('selligent/selligent_configuration/brand');
        $user_params['PO_NUMBER'] = $incrementId;
        $user_params['FIRSTNAME'] = $shippingAddress->getFirstname(); // First name
        $user_params['NAME'] = $shippingAddress->getLastname(); //LastNmae
        $user_params['MAIL'] = $shippingAddress->getEmail(); // Email Address
        $user_params['STREET'] = $street;
        /*if (isset($street[1])) {
            $user_params['BOX'] = $street[1];
        } else {
            $user_params['BOX'] = $street[0];
        }*/
        $user_params['BOX'] 	= $BOX;
        $user_params['NUMBER'] 	= $NUMBER;
        $user_params['ZIP'] = $shippingAddress->getPostcode(); // postal code
        $user_params['CITY'] = $shippingAddress->getCity(); //city 
        $user_params['COUNTRY'] = $shippingAddress->getCountryId(); //country 
        $user_params['PRODUCT'] = $orderItem->getProductId();
        $user_params['PRODUCTNAME'] = $orderItem->getName();
        $user_params['LANG'] = $langval;
        $user_params['OPTIN'] = ($this->boxData('offer_subscription', $quoteid)) ? 1 : 0;
        $user_params['CREATED_DT'] = $created_time; //need clarification
        $user_params['SEND_DT'] = $created_time; //need clarification
        $user_params['BIRTHDAY'] = $shippingAddress->getDob();        
        $user_params['TRANSACTION_TYPE'] = $this->checkouttype($quoteid);
        $user_params['BIRTH_PLACE'] = $shippingAddress->getBirthPlace();
        $user_params['TRANSACTION_ID'] = $transcation_id;

        return $user_params;
    }

    public function checkouttype($quoteid) {
        $type = $this->boxData('pre_onepage', $quoteid);
        if ($type == 1) {
            return 'OnePageCheckout';
        } else {
            return 'ClassicCheckout';
        }
    }

    public function selligentCredentials() {
        $Credentials = array();
        $Credentials['Selligent_Username'] = $this->scopeConfig->getValue('selligent/selligent_configuration/selligent_userid', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $Credentials['Selligent_Pwd'] = $this->scopeConfig->getValue('selligent/selligent_configuration/selligent_password', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $Credentials['Selligent_EndPointURL'] = $this->scopeConfig->getValue('selligent/selligent_configuration/selligent_endpointurl', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $Credentials['Selligent_List'] = $this->scopeConfig->getValue('selligent/selligent_configuration/selligent_list', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $Credentials;
    }

    /* public function getCurlWebserviceCall($envelope_data,$headers_data)
      {
      $objectManager = ObjectManager::getInstance();
      $Credentials_data = $this->selligentCredentials();
      $curl = curl_init();

      curl_setopt($curl, CURLOPT_URL, $Credentials_data['Selligent_EndPointURL']);
      curl_setopt($curl, CURLOPT_POST, 1);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $envelope_data);

      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($curl, CURLOPT_HTTPHEADER, $headers_data);

      curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

      $response = curl_exec($curl);
      $objectManager->get('Orange\Upload\Helper\Data')->logCreate('/var/log/selligent_oos_response.log',$response);
      if (!curl_getinfo($curl, CURLINFO_HTTP_CODE)) {
      $error = curl_error($curl);
      curl_close($curl);
      } elseif (curl_errno($curl)) {
      $error = curl_error($curl);
      curl_close($curl);
      } else {
      curl_close($curl);
      }
      } */

    private function getEnvelope($orderD, $orderItem, $incrementId, $transcation_id) {

        $Credentials_data = $this->selligentCredentials();
        $OrderProces_details = $this->orderProcess($orderD, $orderItem, $incrementId, $transcation_id);
        $envelope = '';

        $envelope = '<?xml version="1.0" encoding="utf-8"?>';
        $envelope .= '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">';

        $envelope .= '<soap:Header>';
        $envelope .= '<AutomationAuthHeader xmlns="http://tempuri.org/">';
        $envelope .= '<Login>' . $Credentials_data['Selligent_Username'] . '</Login>';
        $envelope .= '<Password>' . $Credentials_data['Selligent_Pwd'] . '</Password>';
        $envelope .= '</AutomationAuthHeader>';
        $envelope .= '</soap:Header>';

        $envelope .= '<soap:Body>';
        $envelope .= '<CreateUser xmlns="http://tempuri.org/">';
        $envelope .= '<List>' . $Credentials_data['Selligent_List'] . '</List>';
        $envelope .= '<Changes>';

        foreach ($OrderProces_details as $key => $value) {
            $envelope .= '<Property>';
            $envelope .= '<Key>' . $key . '</Key>';
            $envelope .= '<Value>' . $value . '</Value>';
            $envelope .= '</Property>';
        }

        $envelope .= '</Changes>';

        $envelope .= '</CreateUser>';
        $envelope .= '</soap:Body>';
        $envelope .= '</soap:Envelope>';

        return $envelope;
    }

    public function getHeaders() {

        $Credentials_data = $this->selligentCredentials();
        $headers = array();
        $headers[] = 'Host: ' . parse_url($Credentials_data['Selligent_EndPointURL'], PHP_URL_HOST);
        $headers[] = 'Content-Type: text/xml; charset=utf-8';
        $headers[] = 'SOAPAction: http://tempuri.org/CreateUser';
        return $headers;
    }

    public function doSelligentCall($orderD, $incrementId, $transcation_id) {
        $headers_data = $this->getHeaders();
        $objectManager = ObjectManager::getInstance();
        $orderItems = $orderD->getAllVisibleItems();
        foreach ($orderItems as $orderItem) {           
            $items = (int) $orderItem->getQtyOrdered();
            for ($i = 1; $i <= $items; $i++) {
                $prepaidattributeSet = $objectManager->create('Magento\Eav\Api\AttributeSetRepositoryInterface');
                $attributeSetRepository = $prepaidattributeSet->get($orderItem->getProduct()->getAttributeSetId());
                $attributeSetName = $attributeSetRepository->getAttributeSetName();
                if ($attributeSetName == 'Simcard') {
                    $envelope_data = $this->getEnvelope($orderD, $orderItem, $incrementId, $transcation_id);
                    $scopeConfig = $objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');
                    $log_mode = $scopeConfig->getValue('custom_modulelog/modulelog__configuration/setting_mode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                    if (isset($log_mode) && $log_mode == 1) {
                        $objectManager->get('Orange\Upload\Helper\Data')->logCreate('/var/log/selligent_request.log', $envelope_data);
                    }
                    $Credentials_data = $this->selligentCredentials();

                    $curl = curl_init();

                    curl_setopt($curl, CURLOPT_URL, $Credentials_data['Selligent_EndPointURL']);

                    curl_setopt($curl, CURLOPT_POST, 1);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $envelope_data);

                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers_data);

                    curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

                    $response = curl_exec($curl);
                    if (isset($log_mode) && $log_mode == 1) {
                        $objectManager->get('Orange\Upload\Helper\Data')->logCreate('/var/log/selligent_response.log', $response);
                    }
                    if (!curl_getinfo($curl, CURLINFO_HTTP_CODE) || curl_errno($curl)) {
                        $error = curl_error($curl);
                        curl_close($curl);
                    } else {
                        curl_close($curl);
                    }
                }
            }
        }
    }

    /* abandon  cart functionality start 
     */

    public function doSelligentAbandonCartCall($quote, $resumeLinkWithSecurityKey) {
        $headers_data = $this->getHeaders();
        //echo '<pre>';print_r($headers_data);
        $envelope_data = $this->getEnvelopeAbandonCart($quote, $resumeLinkWithSecurityKey);
        $objectManager = ObjectManager::getInstance();
        $scopeConfig = $objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');
        $log_mode = $scopeConfig->getValue('custom_modulelog/modulelog__configuration/setting_mode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (isset($log_mode) && $log_mode == 1) {
            $objectManager->get('Orange\Upload\Helper\Data')->logCreate('/var/log/abandon_request.log', $envelope_data);
        }
        $Credentials_data = $this->selligentCredentials();

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $Credentials_data['Selligent_EndPointURL']);

        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $envelope_data);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers_data);

        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

        $response = curl_exec($curl);

        //	$objectManager->get('Orange\Upload\Helper\Data')->logCreate('/var/log/selligent_response.log.log',$response);
        if (!curl_getinfo($curl, CURLINFO_HTTP_CODE) || curl_errno($curl)) {
            $error = curl_error($curl);
            curl_close($curl);
        } else {
            curl_close($curl);
        }
        if (isset($log_mode) && $log_mode == 1) {
            $objectManager->get('Orange\Upload\Helper\Data')->logCreate('/var/log/abandon_response.log', $response);
        }
    }

    private function getEnvelopeAbandonCart($quote, $resumeLinkWithSecurityKey) {

        $Credentials_data = $this->selligentCredentials();
        $OrderProces_details = $this->abandonProcess($quote, $resumeLinkWithSecurityKey);
        $envelope = '';

        $envelope = '<?xml version="1.0" encoding="utf-8"?>';
        $envelope .= '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">';

        $envelope .= '<soap:Header>';
        $envelope .= '<AutomationAuthHeader xmlns="http://tempuri.org/">';
        $envelope .= '<Login>' . $Credentials_data['Selligent_Username'] . '</Login>';
        $envelope .= '<Password>' . $Credentials_data['Selligent_Pwd'] . '</Password>';
        $envelope .= '</AutomationAuthHeader>';
        $envelope .= '</soap:Header>';

        $envelope .= '<soap:Body>';
        $envelope .= '<CreateUser xmlns="http://tempuri.org/">';
        $envelope .= '<List>537</List>';
        $envelope .= '<Changes>';

        foreach ($OrderProces_details as $key => $value) {
            $envelope .= '<Property>';
            $envelope .= '<Key>' . $key . '</Key>';
            $envelope .= '<Value>' . $value . '</Value>';
            $envelope .= '</Property>';
        }

        $envelope .= '</Changes>';

        $envelope .= '</CreateUser>';
        $envelope .= '</soap:Body>';
        $envelope .= '</soap:Envelope>';

        return $envelope;
    }

    public function abandonProcess($quote, $resumeLinkWithSecurityKey) {
        $lang = $this->rm_locale();
        $langarray = explode('_', $lang);
        if ($quote->getStoreId() == 1) {
            $langval = 'nl';
        } else {
            $langval = 'fr';
        }
        // $quote = '';     

        $items = $quote->getAllVisibleItems();
        $objectManager = ObjectManager::getInstance();
        $_imagehelper = $objectManager->get('Magento\Catalog\Helper\Image');
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $user_params = array();
        $objectManager = ObjectManager::getInstance();
        $user_params['MAIL'] = $quote->getCustomerEmail();
        $user_params['NAME'] = $quote->getCustomerFirstname() . ' ' . $quote->getCustomerLastname();
        $user_params['CREATED_DT'] = $quote->getCreatedAt();
        $user_params['MSISDN'] = $this->scopeConfig->getValue('selligent/selligent_configuration/msisdn');
        $user_params['FIRSTNAME'] = $quote->getCustomerFirstname();
        $user_params['TITLE'] = $quote->getCustomerPrefix();
        $user_params['BIRTHDATE'] = '';
        $user_params['LANG'] = $langval;
        $user_params['ORDER_NR'] = '0';
        $i = 0;
        foreach ($items as $item) {
            $i++;
            $product = $item->getProduct();
            $_product = $objectManager->get('Magento\Catalog\Model\Product')->load($item->getProduct()->getId());
            //$product->load($product->getId());
            $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
            $user_params["P" . $i . "_NAME"] = $item->getName();
            $user_params["P" . $i . "_DESCR"] = $item->getDescription();
            $user_params["P" . $i . "_QUANTITY"] = (int) $item->getQty();
            $user_params["P" . $i . "_PRICE_NOW"] = $objectManager->get('Magento\Framework\Pricing\Helper\Data')->currency(number_format($item->getPrice(), 0, '', ''), true, false);
            $user_params["P" . $i . "_PRICE_MONTHLY"] = $objectManager->get('Magento\Framework\Pricing\Helper\Data')->currency(number_format($item->getSubscriptionAmount(), 0, '', ''), true, false);
            $user_params["P" . $i . "_EXTRA_INFO"] = '';
            $user_params["P" . $i . "_IMG_URL"] = $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $_product->getImage();
        }
        $user_params['PROMO'] = '';
        $user_params['ORD_PRICE_NOW'] = $objectManager->get('Magento\Framework\Pricing\Helper\Data')->currency(number_format($quote->getGrandTotal(), 0, '', ''), true, false);
        $user_params['ORD_PRICE_MONTHLY'] = $objectManager->get('Magento\Framework\Pricing\Helper\Data')->currency(number_format($quote->getSubscriptionTotal(), 0, '', ''), true, false);
        $user_params['ORD_QUANTITY'] = (int) $quote->getItemsQty();
        $user_params['ORD_DELIVERY_COST'] = '';
        $user_params['TOTAL_TO_PAY_FIRST_INV'] = $objectManager->get('Magento\Framework\Pricing\Helper\Data')->currency(number_format($quote->getGrandTotal(), 0, '', ''), true, false);
        if (strpos($resumeLinkWithSecurityKey, 'admin') !== false) {
            $removeadmin = str_replace("admin/", "", $resumeLinkWithSecurityKey);
            $user_params['BASKET_URL'] = $removeadmin;
        } else {
            $user_params['BASKET_URL'] = $resumeLinkWithSecurityKey;
        }
        return $user_params;
    }

    //outofstock
    public function stockalert($data) {
        $headers_data = $this->getHeaders();
        $envelope_data = $this->getEnvelopeoutofstock($data);

        $objectManager = ObjectManager::getInstance();
        $scopeConfig = $objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');
        $log_mode = $scopeConfig->getValue('custom_modulelog/modulelog__configuration/setting_mode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (isset($log_mode) && $log_mode == 1) {
            $objectManager->get('Orange\Upload\Helper\Data')->logCreate('/var/log/oos_selligent_request.log', $envelope_data);
        }
        //$this->getCurlWebserviceCall($envelope_data,$headers_data);

        $Credentials_data = $this->selligentCredentials();
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $Credentials_data['Selligent_EndPointURL']);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $envelope_data);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers_data);

        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

        $response = curl_exec($curl);
        //$objectManager->get('Orange\Upload\Helper\Data')->logCreate('/var/log/selligent_response.log.log',$response);
        if (!curl_getinfo($curl, CURLINFO_HTTP_CODE) || curl_errno($curl)) {
            $error = curl_error($curl);
            curl_close($curl);
        } else {
            curl_close($curl);
        }
        if (isset($log_mode) && $log_mode == 1) {
            $objectManager->get('Orange\Upload\Helper\Data')->logCreate('/var/log/oos_selligent_response.log', $response);
        }
    }

    public function getEnvelopeoutofstock($data) {
        $Credentials_data = $this->selligentCredentials();
        $OrderProces_details = $this->outofstockuserdata($data);

        $envelope = '';
        $envelope = '<?xml version="1.0" encoding="utf-8"?>';
        $envelope .= '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">';
        $envelope .= '<soap:Header>';
        $envelope .= '<AutomationAuthHeader xmlns="http://tempuri.org/">';
        $envelope .= '<Login>' . $Credentials_data['Selligent_Username'] . '</Login>';
        $envelope .= '<Password>' . $Credentials_data['Selligent_Pwd'] . '</Password>';
        $envelope .= '</AutomationAuthHeader>';
        $envelope .= '</soap:Header>';
        $envelope .= '<soap:Body>';
        $envelope .= '<CreateUser xmlns="http://tempuri.org/">';
        $envelope .= '<List>588</List>';
        $envelope .= '<Changes>';
        foreach ($OrderProces_details as $key => $value) {
            $envelope .= '<Property>';
            $envelope .= '<Key>' . $key . '</Key>';
            $envelope .= '<Value>' . $value . '</Value>';
            $envelope .= '</Property>';
        }
        $envelope .= '</Changes>';
        $envelope .= '</CreateUser>';
        $envelope .= '</soap:Body>';
        $envelope .= '</soap:Envelope>';
        return $envelope;
    }

    public function outofstockuserdata($data) {
        //$lang = $this->rm_locale();
        $lang = $data['lang'];
        $langarray = explode('_', $lang);
        $langval = $langarray[0];
        $user_params = array();
        $user_params['MAIL'] = $data['email'];
        $user_params['NAME'] = $data['name'];
        $user_params['CREATED_DT'] = $data['created_at'];
        $user_params['TITLE'] = $data['title'];
        $user_params['FIRSTNAME'] = $data['lastname'];
        $user_params['BIRTHDAY'] = '01/06/1990';
        $user_params['SMS'] = '0';
        $user_params['MSISDN'] = '0478/343434';
        $user_params['OPTIN_NEWSLETTER'] = '1';
        $user_params['LANG'] = $langval;
        $user_params['PRODUCTNAME'] = $data['product_name'];
        $user_params['PRODUCT_URL'] = $data['product_url'];
        $user_params['FULL_PRICE'] = $data['price'];
        $user_params['PRODUCT_IMG'] = $data['product_image'];
        return $user_params;
    }

    //outofstock

    public function connectionEst() {
        $objectManager = ObjectManager::getInstance();
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        return $resource->getConnection();
    }

    public function boxData($val, $qid) {

        $collect1 = "SELECT * FROM orange_abandonexport_items where quote_id='" . $qid . "'";
        $rest1 = $this->connectionEst()->fetchAll($collect1);
        if (count($rest1)) {
            $fstep = $rest1[0]['stepsecond'];
            $dat = unserialize($fstep);
            if (isset($dat[$val]) && $dat[$val] != '') {
                return $dat[$val];
            } else {
                return 0;
            }
        }
        return 0;
    }

}
