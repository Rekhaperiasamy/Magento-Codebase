<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FME\Geoipultimatelock\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\App\Filesystem\DirectoryList;
use FME\Geoipultimatelock\Model\RestrictFactory;
use Magento\Cms\Model\PageFactory;

class Data extends AbstractHelper
{

    protected $_resource;
    protected $_geoipultimatelockRestrict;

    /**
     * Logger
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     *
     * @var Magento\Framework\View\Asset\Repository
     */
    protected $_gdsAssetRepo;
    protected $_directoryList;

    /**
     * Page factory
     *
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $_pageFactory;

    const XML_PATH_ENABLED = 'geoipultimatelock/general/enable_in_frontend';
    const XML_PATH_BLOCKED_IP_MSG = 'geoipultimatelock/general/blocked_ip_msg';

    public function __construct(Context $context, Repository $repo, DirectoryList $directoryList, RestrictFactory $restrictFactory, PageFactory $pageFactory)
    {
        parent::__construct($context);
        $this->_resource = \Magento\Framework\App\ObjectManager::getInstance()
                ->get('\Magento\Framework\App\ResourceConnection');
        $this->_gdsAssetRepo = $repo;
        $this->_logger = $context->getLogger();
        $this->_directoryList = $directoryList;

        $this->_geoipultimatelockRestrict = $restrictFactory;
        $this->_pageFactory = $pageFactory;
    }

    /**
     *
     * check the module is enabled, frontend
     *
     * @param mix $store
     * @return string
     */
    public function isEnabledInFrontend($store = null)
    {
        $isEnabled = true;
        $enabled = $this->scopeConfig->getValue(self::XML_PATH_ENABLED, ScopeInterface::SCOPE_STORE);
        if ($enabled == null || $enabled == '0') {
            $isEnabled = false;
        }

        return $isEnabled;
    }

    public function getBlockedIpMsg()
    {

        $text = $this->scopeConfig->getValue(self::XML_PATH_BLOCKED_IP_MSG, ScopeInterface::SCOPE_STORE);

        if ($text == null) {
            $text = __("Hi! You do not have access to the website at the moment!");
        }

        return $text;
    }

    public function getAllowedCurrency()
    {
        
        return $this->_currency->getConfigAllowCurrencies();
    }

    public function getContinentsName($key)
    {
        $continentsArr = array(
            1 => 'Africa',
            2 => 'Asia',
            3 => 'Europe',
            4 => 'North America',
            5 => 'Oceania',
            6 => 'South America',
            7 => 'Others'
        );

        if (isset($continentsArr[$key])) {
            return $continentsArr[$key];
        }
    }

    public function getcontinent($country = "")
    {

        switch ($country) {
            case "Algeria":
                    $continent = 1;
                break;
                
            case "Angola":
                    $continent = 1;
                break;
                
            case "Benin":
                    $continent = 1;
                break;
                
            case "Botswana":
                    $continent = 1;
                break;
                
            case "Burkina Faso":
                    $continent = 1;
                break;
                
            case "Burundi":
                    $continent = 1;
                break;
                
            case "Cameroon":
                    $continent = 1;
                break;
                
            case "Cape Verde":
                    $continent = 1;
                break;
                
            case "Central African Republic":
                    $continent = 1;
                break;
                
            case "Chad":
                    $continent = 1;
                break;
                
            case "Comoros":
                    $continent = 1;
                break;
                
            case "Congo":
                    $continent = 1;
                break;
                
            case "Congo, The Democratic Republic of the":
                    $continent = 1;
                break;
                
            case "Djibouti":
                    $continent = 1;
                break;
                
            case "Egypt":
                    $continent = 1;
                break;
                
            case "Equatorial Guinea":
                    $continent = 1;
                break;
                
            case "Eritrea":
                    $continent = 1;
                break;
                
            case "Ethiopia":
                    $continent = 1;
                break;
                
            case "Gabon":
                    $continent = 1;
                break;
                
            case "Gambia":
                    $continent = 1;
                break;
                
            case "Ghana":
                    $continent = 1;
                break;
                
            case "Guinea":
                    $continent = 1;
                break;
                
            case "Guinea-Bissau":
                    $continent = 1;
                break;
                
            case "Cote D'Ivoire":
                    $continent = 1;
                break;
                
            case "Kenya":
                    $continent = 1;
                break;
                
            case "Lesotho":
                    $continent = 1;
                break;
                
            case "Liberia":
                    $continent = 1;
                break;
                
            case "Libyan Arab Jamahiriya":
                    $continent = 1;
                break;
                
            case "Madagascar":
                    $continent = 1;
                break;
                
            case "Malawi":
                    $continent = 1;
                break;
                
            case "Mali":
                    $continent = 1;
                break;
                
            case "Mauritania":
                    $continent = 1;
                break;
                
            case "Mauritius":
                    $continent = 1;
                break;
                
            case "Morocco":
                    $continent = 1;
                break;
                
            case "Mozambique":
                    $continent = 1;
                break;
                
            case "Namibia":
                    $continent = 1;
                break;
                
            case "Niger":
                    $continent = 1;
                break;
                
            case "Nigeria":
                    $continent = 1;
                break;
                
            case "Rwanda":
                    $continent = 1;
                break;
                
            case "Sao Tome and Principe":
                    $continent = 1;
                break;
                
            case "Senegal":
                    $continent = 1;
                break;
                
            case "Seychelles":
                    $continent = 1;
                break;
                
            case "Sierra Leone":
                    $continent = 1;
                break;
                
            case "Somalia":
                    $continent = 1;
                break;
                
            case "South Africa":
                    $continent = 1;
                break;
                
            case "Sudan":
                    $continent = 1;
                break;
                
            case "Swaziland":
                    $continent = 1;
                break;
                
            case "Tanzania, United Republic of":
                    $continent = 1;
                break;
                
            case "Togo":
                    $continent = 1;
                break;
                
            case "Tunisia":
                    $continent = 1;
                break;
                
            case "Uganda":
                    $continent = 1;
                break;
                
            case "Zambia":
                    $continent = 1;
                break;
                
            case "Zimbabwe":
                    $continent = 1;
                break;
                
            case "Afghanistan":
                    $continent = 2;
                break;
                
            case "Bahrain":
                    $continent = 2;
                break;
                
            case "Bangladesh":
                    $continent = 2;
                break;
                
            case "Bhutan":
                    $continent = 2;
                break;
                
            case "Brunei Darussalam":
                    $continent = 2;
                break;
                
            case "Myanmar":
                    $continent = 2;
                break;
                
            case "Cambodia":
                    $continent = 2;
                break;
                
            case "China":
                    $continent = 2;
                break;
                
            case "Timor-Leste":
                    $continent = 2;
                break;
                
            case "India":
                    $continent = 2;
                break;
                
            case "Indonesia":
                    $continent = 2;
                break;
                
            case "Iran, Islamic Republic of":
                    $continent = 2;
                break;
                
            case "Iraq":
                    $continent = 2;
                break;
                
            case "Israel":
                    $continent = 2;
                break;
                
            case "Japan":
                    $continent = 2;
                break;
                
            case "Jordan":
                    $continent = 2;
                break;
                
            case "Kazakhstan":
                    $continent = 2;
                break;
                
            case "Korea, Democratic People's Republic of":
                    $continent = 2;
                break;
                
            case "Korea, Republic of":
                    $continent = 2;
                break;
                
            case "Kuwait":
                    $continent = 2;
                break;
                
            case "Kyrgyzstan":
                    $continent = 2;
                break;
                
            case "Lao People's Democratic Republic":
                    $continent = 2;
                break;
                
            case "Lebanon":
                    $continent = 2;
                break;
                
            case "Malaysia":
                    $continent = 2;
                break;
                
            case "Maldives":
                    $continent = 2;
                break;
                
            case "Mongolia":
                    $continent = 2;
                break;
                
            case "Nepal":
                    $continent = 2;
                break;
                
            case "Oman":
                    $continent = 2;
                break;
                
            case "Pakistan":
                    $continent = 2;
                break;
                
            case "Philippines":
                    $continent = 2;
                break;
                
            case "Qatar":
                    $continent = 2;
                break;
                
            case "Russian Federation":
                    $continent = 2;
                break;
                
            case "Saudi Arabia":
                    $continent = 2;
                break;
                
            case "Singapore":
                    $continent = 2;
                break;
                
            case "Sri Lanka":
                    $continent = 2;
                break;
                
            case "Syrian Arab Republic":
                    $continent = 2;
                break;
                
            case "Tajikistan":
                    $continent = 2;
                break;
                
            case "Thailand":
                    $continent = 2;
                break;
                
            case "Turkey":
                    $continent = 2;
                break;
                
            case "Turkmenistan":
                    $continent = 2;
                break;
                
            case "United Arab Emirates":
                    $continent = 2;
                break;
                
            case "Uzbekistan":
                    $continent = 2;
                break;
                
            case "Vietnam":
                    $continent = 2;
                break;
                
            case "Yemen":
                    $continent = 2;
                break;
                
            case "Albania":
                    $continent = 3;
                break;
                
            case "Andorra":
                    $continent = 3;
                break;
                
            case "Armenia":
                    $continent = 3;
                break;
                
            case "Austria":
                    $continent = 3;
                break;
                
            case "Azerbaijan":
                    $continent = 3;
                break;
                
            case "Belarus":
                    $continent = 3;
                break;
                
            case "Belgium":
                    $continent = 3;
                break;
                
            case "Bosnia and Herzegovina":
                    $continent = 3;
                break;
                
            case "Bulgaria":
                    $continent = 3;
                break;
                
            case "Croatia":
                    $continent = 3;
                break;
                
            case "Cyprus":
                    $continent = 3;
                break;
                
            case "Czech Republic":
                    $continent = 3;
                break;
                
            case "Denmark":
                    $continent = 3;
                break;
                
            case "Estonia":
                    $continent = 3;
                break;
                
            case "Finland":
                    $continent = 3;
                break;
                
            case "France":
                    $continent = 3;
                break;
                
            case "Georgia":
                    $continent = 3;
                break;
                
            case "Germany":
                    $continent = 3;
                break;
                
            case "Greece":
                    $continent = 3;
                break;
                
            case "Hungary":
                    $continent = 3;
                break;
                
            case "Iceland":
                    $continent = 3;
                break;
                
            case "Ireland":
                    $continent = 3;
                break;
                
            case "Italy":
                    $continent = 3;
                break;
                
            case "Latvia":
                    $continent = 3;
                break;
                
            case "Liechtenstein":
                    $continent = 3;
                break;
                
            case "Lithuania":
                    $continent = 3;
                break;
                
            case "Luxembourg":
                    $continent = 3;
                break;
                
            case "Macedonia":
                    $continent = 3;
                break;
                
            case "Malta":
                    $continent = 3;
                break;
                
            case "Moldova, Republic of":
                    $continent = 3;
                break;
                
            case "Monaco":
                    $continent = 3;
                break;
                
            case "Montenegro":
                    $continent = 3;
                break;
                
            case "Netherlands":
                    $continent = 3;
                break;
                
            case "Norway":
                    $continent = 3;
                break;
                
            case "Poland":
                    $continent = 3;
                break;
                
            case "Portugal":
                    $continent = 3;
                break;
                
            case "Romania":
                    $continent = 3;
                break;
                
            case "San Marino":
                    $continent = 3;
                break;
                
            case "Serbia":
                    $continent = 3;
                break;
                
            case "Slovakia":
                    $continent = 3;
                break;
                
            case "Slovenia":
                    $continent = 3;
                break;
                
            case "Spain":
                    $continent = 3;
                break;
                
            case "Sweden":
                    $continent = 3;
                break;
                
            case "Switzerland":
                    $continent = 3;
                break;
                
            case "Ukraine":
                    $continent = 3;
                break;
                
            case "United Kingdom":
                    $continent = 3;
                break;
                
            case "Antigua and Barbuda":
                    $continent = 4;
                break;
                
            case "Bahamas":
                    $continent = 4;
                break;
                
            case "Barbados":
                    $continent = 4;
                break;
                
            case "Belize":
                    $continent = 4;
                break;
                
            case "Canada":
                    $continent = 4;
                break;
                
            case "Costa Rica":
                    $continent = 4;
                break;
                
            case "Cuba":
                    $continent = 4;
                break;
                
            case "Dominica":
                    $continent = 4;
                break;
                
            case "Dominican Republic":
                    $continent = 4;
                break;
                
            case "El Salvador":
                    $continent = 4;
                break;
                
            case "Grenada":
                    $continent = 4;
                break;
                
            case "Guatemala":
                    $continent = 4;
                break;
                
            case "Haiti":
                    $continent = 4;
                break;
                
            case "Honduras":
                    $continent = 4;
                break;
                
            case "Jamaica":
                    $continent = 4;
                break;
                
            case "Mexico":
                    $continent = 4;
                break;
                
            case "Nicaragua":
                    $continent = 4;
                break;
                
            case "Panama":
                    $continent = 4;
                break;
                
            case "Saint Kitts and Nevis":
                    $continent = 4;
                break;
                
            case "Saint Lucia":
                    $continent = 4;
                break;
                
            case "Saint Vincent and the Grenadines":
                    $continent = 4;
                break;
                
            case "Trinidad and Tobago":
                    $continent = 4;
                break;
                
            case "United States":
                    $continent = 4;
                break;
                
            case "Australia":
                    $continent = 5;
                break;
                
            case "Fiji":
                    $continent = 5;
                break;
                
            case "Kiribati":
                    $continent = 5;
                break;
                
            case "Marshall Islands":
                    $continent = 5;
                break;
                
            case "Micronesia, Federated States of":
                    $continent = 5;
                break;
                
            case "Nauru":
                    $continent = 5;
                break;
                
            case "New Zealand":
                    $continent = 5;
                break;
                
            case "Palau":
                    $continent = 5;
                break;
                
            case "Papua New Guinea":
                    $continent = 5;
                break;
                
            case "Samoa":
                    $continent = 5;
                break;
                
            case "Solomon Islands":
                    $continent = 5;
                break;
                
            case "Tonga":
                    $continent = 5;
                break;
                
            case "Tuvalu":
                    $continent = 5;
                break;
                
            case "Vanuatu":
                    $continent = 5;
                break;
                
            case "Argentina":
                    $continent = 6;
                break;
                
            case "Bolivia":
                    $continent = 6;
                break;
                
            case "Brazil":
                    $continent = 6;
                break;
                
            case "Chile":
                    $continent = 6;
                break;
                
            case "Colombia":
                    $continent = 6;
                break;
                
            case "Ecuador":
                    $continent = 6;
                break;
                
            case "Guyana":
                    $continent = 6;
                break;
                
            case "Paraguay":
                    $continent = 6;
                break;
                
            case "Peru":
                    $continent = 6;
                break;
                
            case "Suriname":
                    $continent = 6;
                break;
                
            case "Uruguay":
                    $continent = 6;
                break;
                
            case "Venezuela":
                    $continent = 6;
                break;
                
            default:
                    $continent = 7;
        }

        return $continent;
    }

    /**
     * get countries in an array
     * @return array $results
     */
    public function getCountries()
    {
        //get read connection
        $read = $this->_resource->getConnection('core_read');
        $cl = $this->_resource->getTableName('geoip_cl');
        $query = "SELECT * FROM {$cl} ORDER BY cn";
        $results = $read->fetchAll($query);

        return $results;
    }

    /**
     * get countries list by main_table.id
     * @param int $groupId
     * @return array
     */
    public function getGroupedCountries($groupId)
    {
        //get read connection
        $read = $this->_resource->getConnection('core_read');

        $table = $this->_resource->getTableName('fme_geoipultimatelock');

        $query = "SELECT countries_list"
                . " FROM {$table} "
                . " WHERE geoipultimatelock_id = '{$groupId}'";

        $results = $read->fetchRow($query);

        return unserialize($results['countries_list']);
    }

    public function getInfoByIp($remoteIp)
    {
        $result = array();

        if (filter_var($remoteIp, FILTER_VALIDATE_IP)) {
            $read = $this->_resource->getConnection('core_read');
            $select = $read->select()
                    ->from(array('gcsv' => $this->_resource->getTableName('geoip_csv')))
                    ->where('gcsv.end >= INET_ATON(?)', $remoteIp)
                    ->order('gcsv.end ASC')
                    ->limit(1);

            return $read->fetchRow($select);
        }

        return $result;
    }

    public function isWebCrawler($request)
    {
        if (preg_match("/Googlebot/", $_SERVER['HTTP_USER_AGENT'])) {
            $remoteAddress = new \Magento\Framework\Http\PhpEnvironment\RemoteAddress($request);
                    
            //hostname is assigned to $hostname
            $hostname = $remoteAddress->getRemoteHost();
            
            if (preg_match("/googlebot.com/", $hostname)) {
                // returns true if googlebot.com is found in hostname
                return true;
            }
        }

        return false;
    }

    public function isIpAnException($ip, $exceptionIps)
    {
        $flag = false;
        $exceptionIpsArr = preg_split('@,@', $exceptionIps, null, PREG_SPLIT_NO_EMPTY);
        
        /* if current ip is not an exception, proceed */
        if (in_array($ip, $exceptionIpsArr)) {
            $flag = true;
        }

        return $flag;
    }

    public function validateIps($ips)
    {
        $validatedIps = array();
        foreach ($ips as $ip) {
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                $validatedIps[] = $ip;
            }
        }

        return $validatedIps;
    }

    public function getMediaType($type = 'url')
    {
        $media = $this->_urlBuilder->getBaseUrl(array('_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA));
        if ($type == 'path') {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $dir = $objectManager->get('\Magento\Framework\App\Filesystem\DirectoryList');

            $media = $dir->getPath($dir::MEDIA);
        }

        return $media;
    }

    /**
     * Get 404 file not found url
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    protected function _getNotFoundUrl($route = '', $params = array('_direct' => 'core/index/notFound'))
    {
        return $this->_getUrl($route, $params);
    }

    public function prepareCsv($fileName = '')
    {
        $file = '/geoipultimatelock/' . $fileName . '.csv';
        
        $media = $this->getMediaType('path');
        $csvPath = $media . $file;

        return $csvPath;
    }

    public function getPathToFile()
    {
        return $this->_directoryList->getPath('app') . '/code/FME/Geoipultimatelock/view/adminhtml/web/geoipultimatelock/';
    }

    public function getViewFileUrl()
    {
        return $this->_urlBuilder->getBaseUrl(array('_type' => \Magento\Framework\UrlInterface::URL_TYPE_STATIC, '_current' => true)) . 'adminhtml/Magento/backend/en_US/FME/Geoipultimatelock/view/adminhtml/web/geoipultimatelock/';
    }

    public function prepareCsvParts($csvFilePath)
    {
        $fh = fopen($csvFilePath, 'r');
        
        if ($fh) {
            $fileno = 0;
            $lineno = 0;
            $startofnewfile = true;
            $lastlineno = 0;
            $media = $this->getMediaType('path') . '/geoipultimatelock/';

            while ($rowData = fgets($fh)) {
                //Create new file
                if ($startofnewfile) {
                    $startofnewfile = false;
                    $lastlineno = 0;
                    //Create a file with unique name
                    $file = $media . "GeoIPCountryWhois_" . $fileno . ".csv";

                    $fw = fopen($file, 'w');
                }

                //write csv Line to the taret file in append mode.
                $fwrite = fwrite($fw, $rowData);
                //Count line numbers
                $lineno++;
                //Reached the limit of file now prepare to start new file
                if ($lineno == 2000) {
                    $lastlineno = $lineno;
                    fclose($fw);
                    $lineno = 0;
                    $startofnewfile = true;
                    $fileno++;
                }
            }

            if ($lastlineno == 0) {
                fclose($fw);
            }

            return true;
        } else {
            throw new \Magento\Exception(__('File GeoIPCountryWhois.csv do not exists'));
        }

        return false;
    }

    public function isIpBlocked($ip, $isActive = true)
    {

	    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $log_mode = $this->scopeConfig->getValue('custom_modulelog/modulelog__configuration/setting_mode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            if(isset($log_mode) && $log_mode==1){
                $objectManager->get('Orange\Upload\Helper\Data')->logCreate('/var/log/blockerip.log',$ip."remote ipaddress");
            }
            
        $flag = false;

        $collection = $this->_geoipultimatelockRestrict->create()
                ->getCollection();
      
        $result = $collection->addStatusFilter($isActive)
                ->isIpBlocked($ip);
            if(isset($log_mode) && $log_mode==1){
                $objectManager->get('Orange\Upload\Helper\Data')->logCreate('/var/log/blockerip.log',$result->count()."resultCount");
            }
		
        if ($result->count() > 0) {
            $flag = true;
        }

        return $flag;
    }

    public function getCmsPageModel()
    {
        return $this->_pageFactory->create();
    }
}
