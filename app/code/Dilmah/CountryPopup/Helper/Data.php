<?php
namespace Dilmah\CountryPopup\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    protected $countryFactory;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Directory\Model\CountryFactory $countryFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->countryFactory = $countryFactory;
    }

    /**
     * @param $store
     * @return mixed|string
     */
    public function getStoreCountryCode($store)
    {
        if ($store->getId()==1){
            return 'GLOBAL';
        }
        return $this->scopeConfig->getValue(
            \Magento\Config\Model\Config\Backend\Admin\Custom::XML_PATH_GENERAL_COUNTRY_DEFAULT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param $countryCode
     * @return null|string
     */
    public function getCountryNameByCode($countryCode){
        if (empty($countryCode)){
            return null;
        }
        $country = $this->countryFactory->create()->loadByCode($countryCode);
        return $country->getName('en_US');
    }

    /**
     * @return array
     */
    public function getVisitorGeoData()
    {
        return [
            'country_code' => getenv('GEOIP_COUNTRY_CODE'),
            'country_name' => getenv('GEOIP_COUNTRY_NAME')
        ];
    }

    /**
     * @param $store
     * @return array
     */
    public function processStoreData($store){

        if ($store->getId()==1){
            $country_code = 'GLOBAL';
            $country_name = 'Global';
        }else{
            $country_code = $this->getStoreCountryCode($store);
            $country_name = $this->getCountryNameByCode($country_code);
        }

        $store_code = $store->getCode();
        $store_url = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);

        return  [
            'country_code' => $country_code,
            'country_name' => $country_name,
            'store_code' => $store_code,
            'url' => $store_url
        ];
    }
}