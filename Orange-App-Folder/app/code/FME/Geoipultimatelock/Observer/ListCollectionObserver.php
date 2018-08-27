<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FME\Geoipultimatelock\Observer;

class ListCollectionObserver implements \Magento\Framework\Event\ObserverInterface
{

    protected $_geoipultimatelockFactory;
    protected $_geoipultimatelockHelper;
    protected $_storeManager;
    protected $_coreRegistry;
    protected $_productFactory;
    protected $_logger;

    /**
     * using current context to add values and
     * avoid request refresh for cookie values
     * @var \Magento\Framework\App\Http\Context
     */
    protected $_httpContext;

    /**
     *
     * @var \Magento\Framework\Url $urlBuilder
     */
    protected $_urlBuilder;

    public function __construct(\FME\Geoipultimatelock\Model\RuleFactory $geoipultimatelockFactory, \FME\Geoipultimatelock\Helper\Data $helper, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\Registry $coreRegistry, \Magento\Catalog\Model\ProductFactory $productFactory, \Psr\Log\LoggerInterface $logger)
    {

        $this->_geoipultimatelockFactory = $geoipultimatelockFactory;
        $this->_geoipultimatelockHelper = $helper;
        $this->_storeManager = $storeManager;
        $this->_currentStoreView = $this->_storeManager->getStore();
        $this->_coreRegistry = $coreRegistry;
        $this->_productFactory = $productFactory;
        $this->_logger = $logger;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {

		$request = $observer->getRequest();
        if ($this->_geoipultimatelockHelper->isEnabledInFrontend() === "0" || !$request) {
            return;
        }

        $remoteAddress = new \Magento\Framework\Http\PhpEnvironment\RemoteAddress($request);
        
        if ($this->_geoipultimatelockHelper->isWebCrawler($request)) {
            return;
        }
        
        $visitorIp = $remoteAddress->getRemoteAddress(); //'12.130.147.244'; //
        $infoByIp = $this->_geoipultimatelockHelper->getInfoByIp($visitorIp);

        $geoipultimatelock = $this->_geoipultimatelockFactory->create();
        $expression = new \Zend_Db_Expr('LOWER(countries_list)');
        $collection = $geoipultimatelock->getCollection()
                ->addFieldToFilter('countries_list', array('neq' => ''))
                ->addFieldToFilter($expression, array('like' => '%' . strtolower($infoByIp['cn']) . '%'))
                ->addFieldToFilter($expression, array('nlike' => '%' . strtolower($infoByIp['cn']) . ' %')) // add space so the whole string matches
                ->addStoreFilter($this->_storeManager->getStore(), false)
                ->addStatusFilter();

        if ($collection->count() < 1) {
            return;
        }
        
        $ruleProducts = array();

        $excludeProducts = array();

        $productCollection = $observer->getCollection();
        $productIds = $productCollection->getAllIds();

        foreach ($collection as $item) {
            foreach ($productIds as $productId) {
                if ($item->getConditions()->validateByEntityId($productId)) {
                    $ruleProducts[$item->getId()][] = $productId;
                }
            }
        }

        foreach ($ruleProducts as $ruleId => $productId) {
            foreach ($productId as $pid) {
                // return rule ids for matching product id
                $id = array_keys($ruleProducts, $pid);

                $collection->addIdFilter($id)
                        ->addPriorityFilter()
                        ->addLimit();

                $rule = $collection->getFirstItem();

                if ($rule->getExceptionIps() != null) {
                    $exceptionIps = $rule->getExceptionIps();

                    if ($this->_geoipultimatelockHelper->isIpAnException($visitorIp, $exceptionIps)) {
                        continue;
                    }
                }

                $excludeProducts[] = $pid;
            }
        }

        if (empty($excludeProducts)) {
            return;
        }

        $finalExcludeProducts = array_values(array_unique($excludeProducts));

        $observer->getEvent()
                ->getCollection()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('entity_id', array('nin' => $finalExcludeProducts));
    }
}
