<?php
namespace Orange\Crossellorange\Helper;
use Magento\Framework\App\ObjectManager;

class RoadSixtyFive extends \Magento\Framework\App\Helper\AbstractHelper
{   
    protected $_storeManager;
	protected $_url;
	protected $_road65Uname;
    protected $_road65Pd;    
	
	public function __construct(
        \Magento\Framework\App\Helper\Context $context,
         \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
    ) {
        $this->_scopeConfig = $context;
        parent::__construct($context);               
        $this->_storeManager = $storeManager; 
		$this->_road65Pd     = $this->scopeConfig->getValue('common/road_sixtyfive_config/road_sixty_five_pd', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$this->_road65Uname  = $this->scopeConfig->getValue('common/road_sixtyfive_config/road_sixty_five_uname', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);		
		$this->_url          = $this->scopeConfig->getValue('common/road_sixtyfive_config/url', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }	
	public function getZipCity($zipcode_data){
		$zipcode = $zipcode_data;	
        $output = "";		
        
		$postXML = '<?xml version="1.0" encoding="utf-8"?>
			<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
				<soap:Body>
					<run xmlns="www.d-trix.com/ws">
						<input>
							<dtrix service="road65_quickzip" login="'.$this->_road65Uname.'" password="'.$this->_road65Pd.'">
								<p name="zipcity">'.$zipcode.'</p>
							</dtrix>
						</input>
					</run>
				</soap:Body>
			</soap:Envelope>
						';
		$return_data_dum = $this->doXMLCurl($this->_url, $postXML);
		$p = xml_parser_create();
		xml_parse_into_struct($p, $return_data_dum, $vals, $index);
		xml_parser_free($p);
		$result = $vals;	
		//echo '<pre>'; print_r($result); exit;		
		$streetName = array();
		$streetNameId = array();
		$output = array();
		//unset($output);
		foreach($result as  $key => $info){	
			if($info['tag'] == 'ROW'){		
				$output[$info['attributes']['ZIPCODE']][]= array($info['attributes']['ZIPCODE'],$info['attributes']['CITYNAME']);					
			}	
		}		
		return $output;
	}
	public function getallZipCodes(){

		$output = "";
		$streetName = array();
		$streetNameId = array();
		$output = array();
		for($i=1;$i<=9;$i++)
		{
     		
        $url = 'https://ws.wdmb.be/ws/DTrix.asmx';
		$postXML = '<?xml version="1.0" encoding="utf-8"?>
			<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
				<soap:Body>
					<run xmlns="www.d-trix.com/ws">
						<input>
							<dtrix service="road65_quickzip" login="ManuelPM" password="'.$this->_road65Pd.'">
								<p name="zipcity">'.$i.'</p>
							</dtrix>
						</input>
					</run>
				</soap:Body>
			</soap:Envelope>
						';
		$return_data_dum = $this->doXMLCurl($url, $postXML);
		$p = xml_parser_create();
		xml_parse_into_struct($p, $return_data_dum, $vals, $index);
		xml_parser_free($p);
		$result = $vals;	
		 	foreach($result as  $key => $info){	
			if($info['tag'] == 'ROW'){		
				$output[$info['attributes']['ZIPCODE']][]= array($info['attributes']['ZIPCODE'],$info['attributes']['CITYNAME']);					
			}	
		}
			
	  }
	
     	
		return $output;
	}
	public function getZipCityCheckout($zipcode_data){		
		$zipcode = $zipcode_data;		
        
		$postXML = '<?xml version="1.0" encoding="utf-8"?>
			<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
				<soap:Body>
					<run xmlns="www.d-trix.com/ws">
						<input>
							<dtrix service="road65_quickzip" login="'.$this->_road65Uname.'" password="'.$this->_road65Pd.'">
								<p name="zipcity">'.$zipcode.'</p>
							</dtrix>
						</input>
					</run>
				</soap:Body>
			</soap:Envelope>
						';
		$return_data_dum = $this->doXMLCurl($this->_url, $postXML);
		$p = xml_parser_create();
		xml_parse_into_struct($p, $return_data_dum, $vals, $index);
		xml_parser_free($p);
		$result = $vals;			
		$streetName = array();
		$streetNameId = array();
		$output = array();
		//unset($output);
		foreach($result as  $key => $info){	
			if($info['tag'] == 'ROW'){		
				$output[]= $info['attributes']['ZIPCODE'].' '.$info['attributes']['CITYNAME'];					
			}	
		}		
		return $output;
	}
	public function getStreetNameId($zipcode_data,$street){
		$zipcode = $zipcode_data;				
		$streetName = $street;
        
		$postXML = '<?xml version="1.0" encoding="utf-8"?>
			<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
				<soap:Body>
					<run xmlns="www.d-trix.com/ws">
						<input>
							<dtrix service="road65_quickstreet" login="'.$this->_road65Uname.'" password="'.$this->_road65Pd.'">
								<p name="zipcode">'.$zipcode.'</p>
								<p name="streetname">'.$streetName.'</p>
							</dtrix>
						</input>
					</run>
				</soap:Body>
			</soap:Envelope>
						';
		$return_data_dum = $this->doXMLCurl($this->_url, $postXML);
		$p = xml_parser_create();
		xml_parse_into_struct($p, $return_data_dum, $vals, $index);
		xml_parser_free($p);
		$result = $vals;	
		//echo '<pre>'; print_r($result); exit;		
		$streetName = array();
		$streetNameId = array();
		$output = array();
		//unset($output);
		foreach($result as  $key => $info){	
			if($info['tag'] == 'ROW'){		
					$output[$info['attributes']['ZIPCODE']][]= array($info['attributes']['ZIPCODE'],$info['attributes']['STREETNAME']);
			}	
		}		
		return $output;
	}

	public function doXMLCurl($url, $postXML) {
    $CURL = curl_init();
    curl_setopt($CURL, CURLOPT_URL, $url);
    curl_setopt($CURL, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($CURL, CURLOPT_POST, 1);
    curl_setopt($CURL, CURLOPT_HTTPHEADER, array(
        "Content-type: text/xml;charset=\"utf-8\"",
        "Accept: text/xml",
        "Cache-Control: no-cache",
        "Pragma: no-cache",
        "SOAPAction: \"www.d-trix.com/ws/run\"",
        "Content-length: " . strlen($postXML),
    ));
    curl_setopt($CURL, CURLOPT_POSTFIELDS, $postXML);
    curl_setopt($CURL, CURLOPT_HEADER, false);
    curl_setopt($CURL, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($CURL, CURLOPT_RETURNTRANSFER, true);
    $xmlResponse = curl_exec($CURL);
    return $xmlResponse;
	}
}