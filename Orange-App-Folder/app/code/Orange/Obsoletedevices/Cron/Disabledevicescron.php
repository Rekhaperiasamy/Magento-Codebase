<?php

namespace Orange\ObsoleteDevices\Cron;

class Disabledevicescron {

    protected $_logger;
    protected $_scopeConfig;
    protected $_cacheTypeList;
    protected $_cacheFrontendPool;

    public function __construct(\Psr\Log\LoggerInterface $logger, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Framework\ObjectManagerInterface $objectManager, \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList, \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool) {
        $this->_logger = $logger;
        $this->scopeConfig = $scopeConfig;
        $this->_objectManager = $objectManager;
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheFrontendPool = $cacheFrontendPool;
    }

    public function execute() {
        try {
            $configDays = $this->scopeConfig->getValue('common/obsolete_device_config/obsolete_device_period');
            $magentoDateObject = $this->_objectManager->create('Magento\Framework\Stdlib\DateTime\DateTime');
            $currentdate = $magentoDateObject->gmtDate();
            $now = strtotime($currentdate);
            $cacheClear = '';

            $productCollection = $this->_objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
            $collection = $productCollection->create()
                    ->addAttributeToSelect('*')
                    ->addAttributeToFilter('obsolete_device', '1')
                    ->addAttributeToFilter('status', 1)
                    ->addAttributeToFilter('type_id', 'simple')
                    ->load();

            foreach ($collection as $product) {
                $productdata = $this->_objectManager->create('Magento\Catalog\Model\ProductFactory')->create();
                $productdata->load($product->getId());
                if ($productdata->getObsoleteCreateddate()) {
                    $deviceDays = $this->_getDays($now, $productdata->getObsoleteCreateddate());
                    if ($deviceDays > $configDays) {
                        $cacheClear = 'yes';
                        $productdata->setStatus(0);
                        if ($productdata->save()) {
                            $this->_objectManager->get('Orange\Upload\Helper\Data')->logCreate('/var/log/obsolete_device_cron.log', $product->getStore()->getStoreId() . '-->' . $productdata->getId() . '-->' . $productdata->getName() . ' is disabled');
                        }
                    }
                }
            }

            if ($cacheClear) {
                $types = array('config', 'layout', 'block_html', 'collections', 'reflection', 'db_ddl', 'eav', 'config_integration', 'config_integration_api', 'full_page', 'translate', 'config_webservice');
                foreach ($types as $type) {
                    $this->_cacheTypeList->cleanType($type);
                }
                foreach ($this->_cacheFrontendPool as $cacheFrontend) {
                    $cacheFrontend->getBackend()->clean();
                }
            }
        } catch (\Exception $e) {
            $this->_objectManager->get('Orange\Upload\Helper\Data')->logCreate('/var/log/obsolete_device_cron.log', 'cron execution failed');
            $this->_objectManager->get('Orange\Upload\Helper\Data')->logCreate('/var/log/obsolete_device_cron.log', $e->getMessage());
        }
    }

    protected function _getDays($now, $deviceStartDate) {
        $datediff = $now - strtotime($deviceStartDate);
        return floor($datediff / (60 * 60 * 24));
    }

}
