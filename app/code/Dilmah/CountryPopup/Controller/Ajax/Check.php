<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_CountryPopup
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2017 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\CountryPopup\Controller\Ajax;


/**
 * Check controller
 *
 * @method \Magento\Framework\App\RequestInterface getRequest()
 * @method \Magento\Framework\App\Response\Http getResponse()
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Check extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Dilmah\CountryPopup\Helper\Data
     */
    private $popupHelper;

    /**
     * Check constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Dilmah\CountryPopup\Helper\Data $popupHelper
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->storeManager = $storeManager;
        $this->popupHelper = $popupHelper;
    }

    /**
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    private function getCurrentStore()
    {
        return $this->storeManager->getStore();
    }

    /**
     * @return array
     */
    private function getVisitorGeoData(){
        return $this->popupHelper->getVisitorGeoData();
    }

    /**
     * @return array
     */
    private function getAvailableStoreData(){
        $availableStores = [];
        foreach ($this->storeManager->getStores() as $store) {
            $country_code = $this->popupHelper->getStoreCountryCode($store);
            $availableStores[$country_code] = $this->popupHelper->processStoreData($store);
        }
        return $availableStores;
    }

    /**
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        // visitor country
        $visitorCountry = $this->getVisitorGeoData();

        // current store
        $currentStore = $this->getCurrentStore();
        $visitedStore = $this->popupHelper->processStoreData($currentStore);

        // countries where country popup disabled
        $excludedCountryCodes = ['FR','NL','US'];

        // Store available countries
        $availableStores = $this->getAvailableStoreData();

        $visitorOptions = $this->getVisitorOptions(
            $visitorCountry,
            $visitedStore,
            $availableStores,
            $excludedCountryCodes
        );

        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($visitorOptions);
    }

    /**
     * @param $visitorCountry
     * @param $visitedStore
     * @param $availableStores
     * @param $excludedCountryCodes
     * @return array|null
     */
    private function getVisitorOptions($visitorCountry, $visitedStore, $availableStores, $excludedCountryCodes){
        if (
            $visitorCountry['country_code'] == $visitedStore['country_code'] ||
            in_array($visitedStore['country_code'], $excludedCountryCodes)
        ) {
            return null;
        } elseif (in_array($visitorCountry['country_code'], array_keys($availableStores))) {
            return [
                'current' => $visitedStore,
                'option' => $availableStores[$visitorCountry['country_code']]
            ];
        }elseif ($visitedStore['country_code']=='GLOBAL'){
            return null;
        }
        return [
            'current' => $visitedStore,
            'option' => $availableStores['GLOBAL']
        ];
    }
}
