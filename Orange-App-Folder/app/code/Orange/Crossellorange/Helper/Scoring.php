<?php

namespace Orange\Crossellorange\Helper;

use Magento\Framework\App\ObjectManager;

class Scoring extends \Magento\Framework\App\Helper\AbstractHelper {

    public function __construct(
    \Magento\Framework\App\Helper\Context $context, \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
    ) {
        $this->_scopeConfig = $context;
        parent::__construct($context);
    }

    public function getCustomerCheck($idCardNumber, $fName, $lName, $dob, $requestType, $scoringType, $vatnumber) {
        $request_data_post = $this->getRequestData($idCardNumber, $fName, $lName, $dob, $requestType, $scoringType, $vatnumber);
        $scoringCheckRequest = $this->getCustomerCheckEnvelope($request_data_post);
        $headers = $this->getHeaders($scoringCheckRequest);
        $response = $this->getCurl($headers, $scoringCheckRequest);
        //$response = "FALSE";//for testing in local negative scenario                
        $finalResponse = $this->getCustomerCheckResult($response);
        return $finalResponse;
    }

	public function getScoringCheck($Email, $idCardNumber, $fName, $lName, $dob, $HSCount, $ScoringType, $ModemCount, $Msisdn, $IdType, $StreetName, $StreetNumber, $ZIP, $City, $Country, $PaymentNumber, $NbrSimCards, $Requestor, $vatnumber, $customerstatus, $nationalRegisterIDNumber, $checkDocValidation) {
		$request_data_post_scoring = $this->getRequestDataScoring($Email, $idCardNumber, $fName, $lName, $dob, $HSCount, $ScoringType, $ModemCount, $Msisdn, $IdType, $StreetName, $StreetNumber, $ZIP, $City, $Country, $PaymentNumber, $NbrSimCards, $Requestor, $vatnumber, $customerstatus, $nationalRegisterIDNumber, $checkDocValidation);
        $scoringScoreRequest = $this->getCustomerScoreEnvelope($request_data_post_scoring);
        $headers = $this->getHeaders($scoringScoreRequest);
        $responseScore = $this->getCurlForScoring($headers, $scoringScoreRequest);
        $finalResponse = $this->getCustomerScoreResult($responseScore);
        return $finalResponse;
    }

    public function objectManager() {
        return ObjectManager::getInstance();
    }

    private function getCustomerScoreResult($responseScore) {
        $scoringResponse = $responseScore;
        $scoringResult = "";
        $errorCode = "";
        $transactionId = '';
        $idCardNumber = '';
		$nationalRegisterIDNumber = '';
        $returnMessage = '';
        $scoringReason = '';
        $initalScore = '';
        $initialCreditLimit = '';

        $xmlparser = xml_parser_create();
        xml_parse_into_struct($xmlparser, $scoringResponse, $values);
        //put the log
        $objectManager = $this->objectManager();
        $scopeConfig = $objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');
        $log_mode = $scopeConfig->getValue('custom_modulelog/modulelog__configuration/setting_mode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (isset($log_mode) && $log_mode == 1) {
            $objectManager->get('Orange\Upload\Helper\Data')->logCreate('/var/log/scoring_response.log', $responseScore);
        }
        foreach ($values as $val) {
            if ($val['tag'] == 'FAULTCODE') {
                $faultCode = $val['value'];
                $faultString = $val['value'];
            } else {
                $faultCode = '';
                $faultString = '';
            }
            if ($faultCode == '' && $faultString == '') {
                if ($val['tag'] == 'ERRORCODE') {
                    $errorCode = $val['value'];
                    $objectManager->get('Magento\Checkout\Model\Session')->setScoringCode($errorCode);
                }
                if ($val['tag'] == 'TRANSACTIONID') {
                    $transactionId = $val['value'];
                }
                if ($val['tag'] == 'IDCARDNUMBER') {
                    $idCardNumber = $val['value'];
                }
				if ($val['tag'] == 'NATIONALREGISTERIDNUMBER') {
                    $nationalRegisterIDNumber = $val['value'];
                }
                if ($val['tag'] == 'RETURNMESSAGE') {
                    $returnMessage = $val['value'];
                }
                if ($val['tag'] == 'SCORINGRESULT') {
                    $scoringResult = $val['value'];
                }
                if ($val['tag'] == 'SCORINGDATE') {
                    $scoringDate = $val['value'];
                }
                if ($val['tag'] == 'SCORINGREASON') {
                    $scoringReason = $val['value'];
                }
                if ($val['tag'] == 'INITIALSCORE') {
                    $initalScore = $val['value'];
                }
                if ($val['tag'] == 'INITIALCREDITLIMIT') {
                    $initialCreditLimit = $val['value'];
                }
            }
        }
        $scoringData = array('idCardNumber' => $idCardNumber, 'errorCode' => $errorCode, 'transactionId' => $transactionId, 'returnMessage' => $returnMessage,
            'scoringResult' => $scoringResult, 'scoringReason' => $scoringReason, 'initalScore' => $initalScore, 'initialCreditLimit' => $initialCreditLimit, 'nationalRegisterIDNumber' => $nationalRegisterIDNumber);
        //$objectManager->get('Magento\Checkout\Model\Session')->setScoreData(serialize($scoringData));
        $objectManager->get('Magento\Checkout\Model\Session')->getQuote()->setScoreData(serialize($scoringData))->save();
        if ($scoringResult == "ACC") {
            return $scoringResult;
        }
		 if ($scoringResult == "REF") {
            return $scoringResult;
        }
        if (!$scoringResult) {
            $scoringResult = "DEC";
        }
        if ($scoringResult == 'DEC' || $errorCode == '0') {
            return $scoringResult;
        }
    }

    private function getCurlForScoring($headers, $scoringCheckRequest) {
        $url = $this->scopeConfig->getValue('common/numbervalidation_configuration/scoring_url');
        $CURL = curl_init();
        $uploadDir = '/common-header/scoring/';

        // options
        curl_setopt($CURL, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($CURL, CURLOPT_POST, 1);
        curl_setopt($CURL, CURLOPT_SSLVERSION, 3);
        curl_setopt($CURL, CURLOPT_TIMEOUT, 300);
        curl_setopt($CURL, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($CURL, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($CURL, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($CURL, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS);
        curl_setopt($CURL, CURLOPT_HEADER, 0);

        // credentials
        curl_setopt($CURL, CURLOPT_CAINFO, BP . $this->scopeConfig->getValue('common/numbervalidation_configuration/number_cer_file'));
        curl_setopt($CURL, CURLOPT_SSLKEY, BP . $this->scopeConfig->getValue('common/numbervalidation_configuration/number_key_file'));
        curl_setopt($CURL, CURLOPT_SSLCERT, BP . $this->scopeConfig->getValue('common/numbervalidation_configuration/number_pem_file'));

        // request
        curl_setopt($CURL, CURLOPT_URL, $url);
        curl_setopt($CURL, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($CURL, CURLOPT_POSTFIELDS, htmlspecialchars_decode(htmlspecialchars($scoringCheckRequest)));
        curl_setopt($CURL, CURLOPT_CONNECTTIMEOUT, 30);

        $scoringResponse = curl_exec($CURL);

        $err = curl_errno($CURL);
        $objectManager = $this->objectManager();
        $scopeConfig = $objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');
        $log_mode = $scopeConfig->getValue('custom_modulelog/modulelog__configuration/setting_mode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        //if (isset($log_mode) && $log_mode == 1) {
            $objectManager->get('Orange\Upload\Helper\Data')->logCreate('/var/log/customer_scoring_request_response.log', '---REQUEST----');
            $objectManager->get('Orange\Upload\Helper\Data')->logCreate('/var/log/customer_scoring_request_response.log', $scoringCheckRequest);
            $objectManager->get('Orange\Upload\Helper\Data')->logCreate('/var/log/customer_scoring_request_response.log', '---RESPONSE----');
        //}
        if ($err) {
           // if (isset($log_mode) && $log_mode == 1) {
                $objectManager->get('Orange\Upload\Helper\Data')->logCreate('/var/log/customer_scoring_request_response.log', curl_error($CURL));
            //}
        }
        //if (isset($log_mode) && $log_mode == 1) {
            $objectManager->get('Orange\Upload\Helper\Data')->logCreate('/var/log/customer_scoring_request_response.log', $scoringResponse);
        //}
        return $scoringResponse;
    }

    private function getCurl($headers, $scoringCheckRequest) {
        $CURL = curl_init();
        $uploadDir = '/common-header/scoring/';
        $url = $this->scopeConfig->getValue('common/numbervalidation_configuration/scoring_customer_url');

        curl_setopt($CURL, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($CURL, CURLOPT_POST, 1);
        curl_setopt($CURL, CURLOPT_SSLVERSION, 3);
        curl_setopt($CURL, CURLOPT_TIMEOUT, 300);
        curl_setopt($CURL, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($CURL, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($CURL, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($CURL, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS);
        curl_setopt($CURL, CURLOPT_HEADER, 0);

        curl_setopt($CURL, CURLOPT_CAINFO, BP . $this->scopeConfig->getValue('common/numbervalidation_configuration/number_cer_file'));
        curl_setopt($CURL, CURLOPT_SSLKEY, BP . $this->scopeConfig->getValue('common/numbervalidation_configuration/number_key_file'));
        curl_setopt($CURL, CURLOPT_SSLCERT, BP . $this->scopeConfig->getValue('common/numbervalidation_configuration/number_pem_file'));

        curl_setopt($CURL, CURLOPT_URL, $url);
        curl_setopt($CURL, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($CURL, CURLOPT_POSTFIELDS, htmlspecialchars_decode(htmlspecialchars($scoringCheckRequest)));
        curl_setopt($CURL, CURLOPT_CONNECTTIMEOUT, 30);

        $scoringResponse = curl_exec($CURL);
        $err = curl_errno($CURL);
        $objectManager = $this->objectManager();
        $scopeConfig = $objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');
        $log_mode = $scopeConfig->getValue('custom_modulelog/modulelog__configuration/setting_mode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        //if (isset($log_mode) && $log_mode == 1) {
            $objectManager->get('Orange\Upload\Helper\Data')->logCreate('/var/log/customer_check_request_response.log', '---REQUEST----');
            $objectManager->get('Orange\Upload\Helper\Data')->logCreate('/var/log/customer_check_request_response.log', $scoringCheckRequest);
            $objectManager->get('Orange\Upload\Helper\Data')->logCreate('/var/log/customer_check_request_response.log', '---RESPONSE----');
        //}
        if ($err) {
            //if (isset($log_mode) && $log_mode == 1) {
                $objectManager->get('Orange\Upload\Helper\Data')->logCreate('/var/log/customer_check_request_response.log', curl_error($CURL));
            //}
        }
        //if (isset($log_mode) && $log_mode == 1) {
            $objectManager->get('Orange\Upload\Helper\Data')->logCreate('/var/log/customer_check_request_response.log', $scoringResponse);
        //}
        return $scoringResponse;
    }

    private function getCustomerCheckResult($response) {
        $scoringResponse = $response;
        //for explicit xml to array conversion
        $xmlparser = xml_parser_create();
        xml_parse_into_struct($xmlparser, $scoringResponse, $values);
        foreach ($values as $val) {
            if ($val['tag'] == 'V1:TOBESCORED') {
                //print_r($val['value']);                
                $toBeScored = $val['value'];
            } elseif ($val['tag'] == 'V1:CUSTOMERSTATUS') {
                $customerStatus = $val['value'];
            }
        }
        if ($scoringResponse) {
            if (isset($toBeScored) && $toBeScored == 'Y') {
                $scoreData['tobescored'] = $toBeScored;
                $scoreData['customerstatus'] = $customerStatus;
                return $scoreData;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    private function getCustomerScoreEnvelope($request_data_post_scoring) {
        $request_data = $request_data_post_scoring;
        $serviceName = 'ScoringService';
        $service = 'GetScoringRequest';
        $envelope = '<?xml version="1.0" encoding="utf-8"?><soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:v1="http://mobistar.be/Scoring/' . $serviceName . '/v1"><soapenv:Header/><soapenv:Body>';
        $envelope .= '<v1:' . $service . '>';
        $invalid_chars = array(
            ')' => '',
            '(' => '',
        );
        foreach ($request_data as $key => $values) {
            foreach ($values as $key => $value) {
                $value = strtr($value, $invalid_chars);

                if (function_exists('transliteration_get')) {
                    $value = transliteration_get($value);
                }
                if (trim($value) != '') {
                    $envelope .= '<v1:' . $key . '>' . trim($value) . '</v1:' . $key . '>';
                }
            }
        }

        $envelope .= '</v1:' . $service . '></soapenv:Body></soapenv:Envelope>';
        $scoringCheckRequest = $envelope;
        return $scoringCheckRequest;
    }

    private function getCustomerCheckEnvelope($request_data_post) {
        $request_data = $request_data_post;
        $invalid_chars = array(
            ')' => '',
            '(' => '',
        );
        $serviceName = 'CustomerCheck';
        $service = 'CustomerCheckRequest';
        $envelope = '<?xml version="1.0" encoding="utf-8"?><soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:v1="http://mobistar.be/Scoring/' . $serviceName . '/v1"><soapenv:Header/><soapenv:Body>';
        $envelope .= '<v1:' . $service . '>';
        foreach ($request_data as $key => $values) {
            foreach ($values as $key => $value) {
                $value = strtr($value, $invalid_chars);

                if (function_exists('transliteration_get')) {
                    $value = transliteration_get($value);
                }
                if (trim($value) != '') {
                    $envelope .= '<v1:' . $key . '>' . trim($value) . '</v1:' . $key . '>';
                }
            }
        }
        $envelope .= '</v1:' . $service . '></soapenv:Body></soapenv:Envelope>';
        $scoringCheckRequest = $envelope;
        return $scoringCheckRequest;
    }

    private function getHeaders($scoringCheckRequest) {
        $headers = array();
        $headers[] = 'Content-type: text/xml';
        $headers[] = 'Pragma: no-cache';
        $headers[] = 'Content-Length: ' . strlen($scoringCheckRequest);
        return $headers;
    }

	private function getRequestDataScoring($Email, $idCardNumber, $fName, $lName, $dob, $HSCount, $ScoringType, $ModemCount, $Msisdn, $IdType, $StreetName, $StreetNumber, $ZIP, $City, $Country, $PaymentNumber, $NbrSimCards, $Requestor, $vatnumber, $customerstatus, $nationalRegisterIDNumber, $checkDocValidation) {
	
        return $request_data = array('GetScoring' => array(
                'RequestId' => '',
                'Requestor' => $Requestor,
                'Agent' => '',
                'ScoringType' => $ScoringType,
                'HSCount' => $HSCount, // number of handsets in order
                'INSCount' => '',
                'ModemCount' => $ModemCount,
                'CustCode' => '',
                'Msisdn' => $Msisdn,
                'Sim' => '',
                'Imei' => '',
                'VAT' => $vatnumber,
                'OrganizationType' => '',
                'LegalOrganizationName' => '',
                'IdType' => $IdType,
                'IdCardNumber' => $idCardNumber, // 590994755840 OK, 591873682540 NOK
                'NationalRegisterIDNumber' => $nationalRegisterIDNumber,
                'CheckDocValidation' => $checkDocValidation,
                'FirstName' => $fName,
                'LastName' => $lName,
                'IdNationality' => '',
                'IdIssuingCountry' => '',
                'BirthDate' => $dob,
                'Email' => $Email,
                'StreetName' => $StreetName,
                'StreetNumber' => $StreetNumber,
                'StreetBox' => '',
                'ZIP' => $ZIP,
                'City' => $City,
                'Country' => $Country,
                'PaymentMode' => '',
                'PaymentNumber' => $PaymentNumber,
                'NbrSimCards' => $NbrSimCards,
                'DeliveryMode' => '',
                'ReconcilliationError' => '',
                'Customertype' => $customerstatus,
                'NbrExistingSimCards' => '',
        ));
    }

    private function getRequestData($idCardNumber, $fName, $lName, $dob, $requestType, $scoringType, $vatnumber) {
        return $request_data = array('CustomerCheck' => array(
                'CustCode' => '',
                'IDCard' => $idCardNumber,
                'Fname' => $fName,
                'Lname' => $lName,
                'Birthdate' => $dob,
                'VAT' => $vatnumber,
                'CompanyName' => '',
                'MSISDN' => '',
                'FCLI' => '',
                'Street' => '',
                'StreetNumber' => '',
                'StreetBox' => '',
                'Zip' => '',
                'City' => '',
                'Country' => '',
                'Email' => '',
                'RequestType' => $requestType,
                'ScoringType' => $scoringType
        ));
    }
	//get base64 encrypt value
	public function getBase64EncryptVal($nationalRegisterIDNumber) {
		$nationalId = $nationalRegisterIDNumber;
		$secretKey = $this->scopeConfig->getValue('common/base64_configuration/secret_key');
		$iv = $this->scopeConfig->getValue('common/base64_configuration/iv');
		$method = $this->scopeConfig->getValue('common/base64_configuration/method');
		// PCSK#5 Padding to number.
		$blocksize = strlen($secretKey);
		$pad = $blocksize - (strlen($nationalId) % $blocksize);
		$nationalIdNo = $nationalId . str_repeat(chr($pad), $pad);
		// Encrypt number with openssl method.
		$nationalRegIDNumber = openssl_encrypt($nationalIdNo, $method, $secretKey, OPENSSL_ZERO_PADDING, $iv);
		return $nationalRegIDNumber;
	}

}
