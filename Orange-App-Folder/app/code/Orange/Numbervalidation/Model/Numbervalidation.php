<?php

namespace Orange\Numbervalidation\Model;

use Magento\Framework\App\ObjectManager;

class Numbervalidation extends \Magento\Framework\Model\AbstractModel {
 
    protected function _construct(
	
	 ) {
        parent::_construct();		
    }

    /**
     * Object Manager intialize
     * @return type
     */
    public function objectManager() {
        $objectManager = ObjectManager::getInstance();
        return $objectManager;
    }

    public function getNumberVaidationData($number) {
	//32494094097	
        $curl = curl_init();
        $helper = $this->objectManager()->get('Orange\Reserve\Helper\Data')->numberValidation();
        if($helper['url'] != '' ){
            curl_setopt($curl, CURLOPT_URL, $helper['url'].$number);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_VERBOSE, 3);
            curl_setopt($curl, CURLOPT_SSLCERT, BP . $helper['pem']);
            curl_setopt($curl, CURLOPT_SSLKEY, BP . $helper['key']);
            curl_setopt($curl, CURLOPT_CAINFO, BP . $helper['cer']);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_SSLVERSION, 3);
            curl_setopt($curl, CURLOPT_HTTPGET, 1);
            $data = curl_exec($curl);	
            return $this->validationToFindOperator($data);
        }else{
            echo "configuration missing";
            exit;            
        }       
       
    }
	public function getcustomerNumberVaidationData($number) {
		$helper = $this->objectManager()->get('Orange\Reserve\Helper\Data')->customerNumberValidation();
        if($helper['url'] != '' ){
		//$url = 'https://wspartnerref.mobistar.be:9201/Billing/OBasBscsCasGetCustomerData/01/';
		$pemKeyFile = BP .'/pub/media/customernumberupload/'.$helper['pem'];
		$keyFile =  BP .'/pub/media/customernumberupload/'.$helper['key']; 
		$certFile = BP .'/pub/media/customernumberupload/'.$helper['cer']; 
		$url = $helper['url'];
	    $envelope_data = $this->getEnvelopedata($number);
		$headers = array();
		$headers[] = 'Content-type: text/xml';
		$headers[] = 'Pragma: no-cache';
		$headers[] = 'Content-Length: ' . strlen($envelope_data);
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		
		curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_SSLVERSION, 3);
		curl_setopt($curl, CURLOPT_TIMEOUT, 300);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curl, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS);
		curl_setopt($curl, CURLOPT_HEADER, 0);   
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);	
		curl_setopt($curl, CURLOPT_POSTFIELDS, $envelope_data);	
		curl_setopt($curl, CURLOPT_SSLCERT, $pemKeyFile);
		curl_setopt($curl, CURLOPT_SSLKEY,  $keyFile);
		curl_setopt($curl, CURLOPT_CAINFO, $certFile);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		$response = curl_exec($curl);
		$objectManager = $this->objectManager();
		$objectManager->get('Orange\Upload\Helper\Data')->logCreate('/var/log/numbervalidation.log', '---REQUEST----');
		$objectManager->get('Orange\Upload\Helper\Data')->logCreate('/var/log/numbervalidation.log', $envelope_data);
		$objectManager->get('Orange\Upload\Helper\Data')->logCreate('/var/log/numbervalidation.log', '---RESPONSE----');
		$objectManager->get('Orange\Upload\Helper\Data')->logCreate('/var/log/numbervalidation.log', $response);
		   
		return $this->validate_customer($response);
		}
    }
	
	public function getEnvelopedata($data)
	{
        $envelope = '';
        $envelope = '<?xml version="1.0" encoding="UTF-8"?>';
        $envelope .= '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:obas="http://mobd1/OBasBscsCas.Flows">';
        $envelope .= '<soapenv:Header />';
        $envelope .= '<soapenv:Body>';
        $envelope .= '<obas:OBasBscsCasGetCustomerData soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">';
        $envelope .= '<OBasBscsCasGetCustomerDataIn xsi:type="obas:__OBasBscsCasGetCustomerDataIn" xmlns:obas="http://localhost/OBasBscsCas/Flows/OBasBscsCasGetCustomerData">';
        $envelope .= '<Header xsi:type="obas:__Header">';
        $envelope .= '<Requestor xsi:type="obas:__Requestor">R2D2</Requestor>';
        $envelope .= '</Header>';
        $envelope .= '<CustomerCriteria xsi:type="obas:__CustomerCriteria">';
        $envelope .= '<CustCode xsi:type="obas:__CustCode">' .$data. '</CustCode>';
        $envelope .= '</CustomerCriteria>';
        
        $envelope .= '</OBasBscsCasGetCustomerDataIn>';
        $envelope .= '</obas:OBasBscsCasGetCustomerData>';
        $envelope .= '</soapenv:Body>';
        $envelope .= '</soapenv:Envelope>';
        return $envelope;
	}
	
	public function validate_customer($data) {
		
		if (strpos($data, 'Successful') !== false) {
    return 'yes';
		}else{
		  return  'no';
	  }
		  
    }
	
	public function validationToFindOperator($data) {
        $dataValue = json_decode($data, true);
        if (is_array($dataValue) && isset($dataValue[0]) && $dataValue[0]['BSCS_ID'] == '') {
            $data = 1;			
        }else if (is_array($dataValue) && isset($dataValue[0]) && $dataValue[0]['BSCS_ID'] != '') {

		   //postpaid
           $data = 2;
        }
        else {
            //other networks
           $data = 3;
			
        }
		return  $data;  
    }
	
}


