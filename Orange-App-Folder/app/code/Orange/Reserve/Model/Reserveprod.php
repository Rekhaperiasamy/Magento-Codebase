<?php

namespace Orange\Reserve\Model;

use Magento\Framework\App\ObjectManager;

class Reserveprod extends \Magento\Framework\Model\AbstractModel {

    private $private_key;
    private $responseData;
    private $helperData;
    
    public function objectManager() {
        return \Magento\Framework\App\ObjectManager::getInstance();
    }
    
    public function trigger($reserve) {
        //$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->helperData = $this->objectManager()->get('Orange\Reserve\Helper\Data');

        $callTrigger = array('HelloWorld', 'CreateSession', 'CREATE_CLICRESV', 'CloseSession');

        foreach ($callTrigger as $trig) {
            $output = $this->doCall($trig, $reserve);
        }
        return $this->returnDatavalue($reserve);
    }

    public function returnDatavalue($reserve) {
        //$validation = explode('||', $this->helperData->errorCodeZipcode());	
        $validation = $this->helperData->errorCodeZipcode();
        $dateobject = new \DateTime('now', new \DateTimeZone('CET'));

        if ($dateobject->format('l') == 'Saturday') {
            $dateobject->modify('+2 days');
        } else {
            $dateobject->modify('+1 day');
        }

        $jsonDecodeValue = json_decode($this->responseData);
        $convertedArray = $jsonDecodeValue->Rows[0];

        $status = $convertedArray->status;
        $statusDesc = $convertedArray->status_desc;
        $orderID = $convertedArray->order_identify;

        if ($status != 9) {
            $objectManager = $this->objectManager();
            $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
            $storeId = $storeManager->getStore()->getId();
            $model = $objectManager->create('Orange\Errormessage\Model\Errormessage');
            $tableCollection = $model->getCollection()
                    ->addFieldToFilter('code', array('eq' => $status))
                    ->addFieldToFilter('store_id', array('eq' => $storeId));
            foreach ($tableCollection as $collection) {
                echo $collection->getData('message');
            }
        }

        /* if ($status == 0) {
          //0: not in stock
          $this->logCreate('/var/log/click_reserve.log', (array) $convertedArray);
          echo $validation['3'];
          exit;
          }
          if ($status == 1) {
          //1: in stock, cannot be reserved
          $this->logCreate('/var/log/click_reserve.log',(array) $convertedArray);
          echo $validation['3'];
          exit;
          }

          if($status == 2) {
          //2
          $orderID = $convertedArray->order_identify;
          if ($orderID == '') {
          $this->logCreate('/var/log/click_reserve.log',(array) $convertedArray);
          echo $validation['3'];
          exit;
          }

          } */

        $data = array();
        $data['pname'] = $reserve['pname'];
        $data['sku'] = $reserve['device'];
        $data['fname'] = $reserve['fname'];
        $data['email'] = $reserve['email'];
        $data['shop_url'] = $reserve['shop_url'];
        $data['rid'] = $orderID;
        //$data['store'] = strip_tags(, '<br />');
        $data['store'] = str_replace("<br />", " ", $reserve['saddress']);
        $data['date'] = $dateobject->format('d/m/Y');
        $data['ip_address'] = $reserve['ip_address'];
        $data['response'] = $jsonDecodeValue;

        return $data;
    }

    private function getHeaders($request) {
        $headers = array();
        $headers[] = 'Content-Type: text/xml';
        $headers[] = 'Pragma: no-cache';
        $headers[] = 'Content-length: ' . strlen($request);

        return $headers;
    }

    public function doCall($trig, $reserve) {
        unset($headers);
        unset($request);
        $cred = $this->helperData->reserveCall();

        $request = $this->getEnvelope($trig, $reserve);
        $this->logCreate('/var/log/click_reserve.log', $request);

        $headers = $this->getHeaders($request);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_SSLVERSION, 3);
        //curl_setopt($curl, CURLOPT_TIMEOUT, 300);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS);

        curl_setopt($curl, CURLOPT_SSLCERT, BP . $cred['pem']);
        curl_setopt($curl, CURLOPT_SSLKEY, BP . $cred['key']);
        curl_setopt($curl, CURLOPT_CAINFO, BP . $cred['cer']);

        curl_setopt($curl, CURLOPT_URL, $cred['url']);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, htmlspecialchars_decode(htmlspecialchars($request)));

        $response = curl_exec($curl);

        $validation = $this->getResult($trig, $response);
    }

    private function getEnvelope($switchCall, $reserve) {

        $envelope = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:prin="http://principal.wsyield.com/">
        <soapenv:Header/><soapenv:Body>' . $this->setParams($switchCall, $reserve) . '</soapenv:Body></soapenv:Envelope>';

        return $envelope;
    }

    public function getResult($trig, $response) {
        $this->logCreate('/var/log/click_reserve.log', $trig . "====>" . $response);
        if ($trig === "CreateSession") {
            preg_match("/<CreateSessionResult>(.*)<\/CreateSessionResult>/", $response, $preg_result);
            $this->private_key = $preg_result[1];
        }
        if ($trig === "CREATE_CLICRESV") {
            preg_match("/<InteropResult>(.*)<\/InteropResult>/", $response, $preg_result1);
            $this->responseData = $preg_result1[1];
        }
    }

    public function logCreate($fileName, $data) {
        $scopeConfig = $this->objectManager()->create('Magento\Framework\App\Config\ScopeConfigInterface');
        $log_mode = $scopeConfig->getValue('custom_modulelog/modulelog__configuration/setting_mode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (isset($log_mode) && $log_mode == 1) {
        $writer = new \Zend\Log\Writer\Stream(BP . "$fileName");
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($data);
        }
    }

    function csv_to_array() {
        $filename = BP . "/common-header/cert/mappingShopsMobistarIdMercatorId.csv";
        $delimiter = ',';
        $file_handle = fopen($filename, 'r');
        while (!feof($file_handle)) {
            $line_of_text[] = fgetcsv($file_handle, 1024);
            foreach ($line_of_text as $k => $value) {
                $array[$value[7]] = $value[5];
            }
        }
        fclose($file_handle);
        return $array;
    }

    function setParams($switch, $reserve) {
        $reserveData = $this->helperData->reserveCall();
        $uname = $reserveData['uname'];
        $pass = $reserveData['pass'];
        $armiscode = $this->csv_to_array();

        if (count($armiscode)) {
            if (array_key_exists($reserve['shop_id'], $armiscode)) {
                $armisc = $armiscode[$reserve['shop_id']];
            } else {
                $this->logCreate('/var/log/click_reserve.log', 'Armiscode ====> ' . $reserve['shop_id']);
                //$validation = explode('||', $this->helperData->errorCodeZipcode());				
                $validation = $this->helperData->errorCodeZipcode();

                echo $validation['0'];
                exit;
            }
        }
        $dateobj = new \DateTime('now', new \DateTimeZone('CET'));
        $date = $dateobj->format('dmYHms');
        $hashcode = sha1("#|1" . $uname . "|2" . sha1($pass) . "|3" . $date . "#");

        switch ($switch) {
            case "CreateSession":
                $params = '';
                $params = '<prin:CreateSession>';
                $params .= '  <!--Optional:-->';
                $params .= '   <prin:Login>' . $uname . '</prin:Login>';
                $params .= '   <!--Optional:-->';
                $params .= '   <prin:Password>' . sha1($pass) . '</prin:Password>';
                $params .= '   <!--Optional:-->';
                $params .= '   <prin:TimeStamp>' . $dateobj->format('dmYHms') . '</prin:TimeStamp>';
                $params .= '   <!--Optional:-->';
                $params .= '   <prin:Hashcode>' . $hashcode . '</prin:Hashcode>';
                $params .= '</prin:CreateSession>';
                break;
            case "HelloWorld":
                $params = '<prin:HelloWorld/>';
                break;
            case "CREATE_CLICRESV":
                $params = '<prin:Interop>';
                $params .= '  <prin:LoginName>' . $uname . '</prin:LoginName>';
                $params .= '  <prin:PrivateKey>' . $this->private_key . '</prin:PrivateKey>';
                $params .= '  <prin:Outing>JSon</prin:Outing>';
                $params .= '  <prin:Group>SQL</prin:Group>';
                $params .= '  <prin:Function>CREATE_CLICRESV</prin:Function>';
                $params .= '  <prin:Parameters><![CDATA[<customer_title>' . 'mr' . '</customer_title>]]>
                                        <![CDATA[<customer_firstname>' . $reserve['fname'] . '</customer_firstname>]]>
                                        <![CDATA[<customer_name>' . $reserve['name'] . '</customer_name>]]>
                                        <![CDATA[<customer_email>' . $reserve['email'] . '</customer_email>]]>
                                        <![CDATA[<customer_mobile>' . $reserve['phone'] . '</customer_mobile>]]>
                                        <![CDATA[<id_product>' . $reserve['device'] . '</id_product>]]>
                                        <![CDATA[<aramiscode>' . $armisc . '</aramiscode>]]></prin:Parameters>';

                $params .= '</prin:Interop>';
                break;
            case "CloseSession":
                $params = '<prin:CloseSession>';
                $params .= '  <!--Optional:-->';
                $params .= '   <prin:Login>' . $uname . '</prin:Login>';
                $params .= '   <!--Optional:-->';
                $params .= '   <prin:Key>' . $this->private_key . '</prin:Key>';
                $params .= '</prin:CloseSession>';
                break;
        }

        return $params;
    }

}
