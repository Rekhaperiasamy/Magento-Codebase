<?php

namespace Orange\Reserve\Model;

use Magento\Framework\App\ObjectManager;

class Zipcode extends \Magento\Framework\Model\AbstractModel {

    private $helperData;
    private $errorData;

    public function zipcodeValidation($postcode, $sku, $name,$click) {        
        $this->helperData = $this->objectManager()->get('Orange\Reserve\Helper\Data');
        return $this->doCall($postcode, $sku, $name,$click);
    }

    public function doCall($postcode, $sku, $name,$click) {
        $objectManager = $this->objectManager();
        $helperZipcode = $this->helperData->zipcodeCall();        
        $validation = $this->helperData->errorCodeZipcode();
        $url = $helperZipcode['url'] . "?deviceid=" . $sku . "&postcode=" . $postcode;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSLCERTPASSWD, $helperZipcode['sslpass']);
        curl_setopt($curl, CURLOPT_SSLCERT, BP . $helperZipcode['pem']);
        curl_setopt($curl, CURLOPT_SSLKEY, BP . $helperZipcode['key']);
        curl_setopt($curl, CURLOPT_CAINFO, BP . $helperZipcode['cer']);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_VERBOSE, 3);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSLVERSION, 3);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        $curl_info = curl_getinfo($curl);

        curl_close($curl);

        if ($curl_info['http_code'] != 200) {
            echo __($validation['9']);
        }
        $this->logCreate('/var/log/click_reserve_zip.log', $data);
        $pieces = explode("<?xml", $data);        
        $result = $this->dataMassager($pieces[0], $sku, $name, $postcode,$click);        
        return $result;
    }

    public function dataMassager($resultVal, $skuData, $productName, $postcode,$click) {
        $val = json_decode($resultVal);        
        $validation = $this->helperData->errorCodeZipcode();
		//getting common click and reserve configuration value for P3 – 39146639 – Click and Reserve
        $clickAndReserveEnable = $this->helperData->clickAndReserveEnable();
        $verify = (array) $val;
		$dat = '';
        if (isset($verify['code']) && $verify['code'] == 400) {
            $dat .= __($validation['8']);            
        }
        if ($val === null) {
            $dat .= __($validation['8']);            
        } else {
            foreach ($val as $key => $data) {
                if (isset($data->shopid)) {
                    if ($key == 0) {
                        $dat .= '<h3 class="margin-xs-b-m margin-xs-t-l"> ' . __("Disponible à") . '</h3>';
                    }
                    $dat .= '<div class="shop-wrapper" id="' . $data->shopid . '">';
                    $dat .= '<div class="row">';
                    $dat .= '<div class="col-xs-12 col-sm-8 col-md-4">';
                    $dat .= '<i class="oi oi-location font-24 pull-left margin-xs-b-l margin-xs-r-s" id="' . $data->shopid . '"></i>';
                    $dat .= '<div>';
                    $dat .= $data->shopdesc;
                    $dat .= '<div class="clearfix"></div><span class="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><a target="_blank" href="//'.$data->url.'" class="orange">' . __("Détails du magasin") . '</a>';
                    $dat .= '</div>';
                    $dat .= '</div>';
                    $dat .= '<div class="col-xs-12 col-sm-12 col-md-8" id="' . $data->shopid . '">';
                    $dat .= '<div class="row margin-xs-t-s margin-sm-t-s margin-md-t-n">';
                    $dat .= '<div class="col-xs-12 padding-xs-t-s padding-md-t-n">';
                    if ($data->stock > 1) {
                        $dat .= '<div id="curl_first_reserve_product_' . $data->shopid . '" class="curl_first_reserve_product">
			<span class="green"><i class="oi oi-tick"></i> &nbsp;' . __("En stock") . '</span>
                        <br>';
						//Checking whether common click and reserve configuration is enable or not for P3 – 39146639 – Click and Reserve
						if($click && $clickAndReserveEnable) {
                       $dat .= ' <div class="btn btn-default btn-sm margin-xs-t-s reserve_product" name="reserve_product" id="reserve_product_' . $data->shopid . '">
                         ' . __("Réservez") . '</div>
                        <input type="hidden" name="device_id" id="device_id' . $data->shopid . '" value="' . $skuData . '">
                        <input type="hidden" name="shop" id="shop_id' . $data->shopid . '" value="' . $data->shopid . '">
                        <input type="hidden" name="shop_address" id="shop_address' . $data->shopid . '" value="' . $data->shopdesc . '">
						<input type="hidden" name="shop_url" id="shop_url' . $data->shopid . '" value="' . $data->url . '">
                        <input type="hidden" name="product_name" id="product_name' . $data->shopid . '" value="' . $productName . '">';
						}
						$dat .= '</div>';
                    } else {
                        $dat .= '<span class = "red"><i class = "oi oi-close_delete"></i> &nbsp;
                        ' . __("Indisponible") . '</span>';
                    }

                    $dat .= '</div>';
                    $dat .= '</div>';
                    $dat .= '</div>';
                    $dat .= '</div>';
                    $dat .= '</div>';
                } else {
                    $dat .= __($validation['7']);                    
                }
            }
        }
		return $dat;
    }
    
    public function objectManager() {
        return \Magento\Framework\App\ObjectManager::getInstance();
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

}
