<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FME\Geoipultimatelock\Observer;

use Magento\Store\Model\Store;

class DispatchObserver implements \Magento\Framework\Event\ObserverInterface
{

    protected $_geoipultimatelockFactory;
    protected $_geoipultimatelockHelper;
    protected $_storeManager;
    protected $_currentStoreView;
    protected $_layoutFactory;
    protected $_coreRegistry;
    public $filterProvider;
    protected $_productFactory;

    protected $_geoipultimatelockRestrict;
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
    /**
     * Page
     *
     * @var \Magento\Cms\Model\Page
     */
    protected $_page;

    public function __construct(
        \FME\Geoipultimatelock\Model\RuleFactory $geoipultimatelockFactory,
        \FME\Geoipultimatelock\Helper\Data $helper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Result\LayoutFactory $layoutFactory,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Framework\Url $urlBuilder,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \FME\Geoipultimatelock\Model\RestrictFactory $restrictFactory,
        \Magento\Cms\Model\Page $page
    ) 
{ 
     
     
     
     
     
     
     
     
     
    
        $this->_geoipultimatelockFactory = $geoipultimatelockFactory;
        $this->_geoipultimatelockHelper = $helper;
        $this->_storeManager = $storeManager;
        $this->_currentStoreView = $this->_storeManager->getStore();
        $this->_layoutFactory = $layoutFactory;
        $this->_httpContext = $httpContext;
        $this->_urlBuilder = $urlBuilder;
        $this->_coreRegistry = $coreRegistry;
        $this->filterProvider = $filterProvider;

        $this->_productFactory = $productFactory;
        
        $this->_geoipultimatelockRestrict = $restrictFactory;
        $this->_page = $page;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        if (!$this->_geoipultimatelockHelper->isEnabledInFrontend()) {
            return;
        }

        $request = $observer->getRequest();
        
        $remoteAddress = new \Magento\Framework\Http\PhpEnvironment\RemoteAddress($request);
        
        if ($this->_geoipultimatelockHelper->isWebCrawler($request)) {
            return;
        }
        
        $visitorIp = $remoteAddress->getRemoteAddress();
        
        // restricted IP(s) will be blocked immidiately
        if ($this->_isIpBlocked($visitorIp)) {
             echo "<span class='ip_blckr'>";
 printf('%s', $this->_getBlockedIpMsg());
 echo "</span>"; 			
            exit;
        }
            
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

        if ($product = $this->_coreRegistry->registry('current_product')) {
            foreach ($collection as $item) {
                $rule = $this->_geoipultimatelockFactory->create()->load($item->getId());

                if ($rule->getConditions()->validate($product)) {
                    $ruleProducts[$item->getId()] = $product->getId();
                }
            }

            $allRules = array_keys($ruleProducts);

            if (empty($allRules)) {
                return;
            } else {
                $collection->addIdFilter($allRules)
                        ->addPriorityFilter()
                        ->addLimit();
            }

            /* @var object as the collection will always have one item
             * the rule with higher priroity 
             */
            $item = $collection->getFirstItem();

            if ($item->getExceptionIps() != null) {
                $exceptionIps = $item->getExceptionIps();

                if ($this->_geoipultimatelockHelper->isIpAnException($visitorIp, $exceptionIps)) {
                    return;
                }
            }

            if ($item->getErrorUrlRedirect() != '') {
                $observer->getControllerAction()
                    ->getResponse()
                    ->setRedirect($item->getErrorUrlRedirect());
            } else {
				echo "<span class='ip_blckr'>";
                printf(
                    '%s', $this->filterProvider
                        ->getPageFilter()
                    ->filter($item->getErrorMsg())
                );
				echo "</span>";
                exit;
            }
        }
        
        $pageId = $this->_page->getIdentifier();
        
        if ($pageId !== null) {
            $collectionPageFilter = $collection->addPageFilter($pageId)
                    ->addPriorityFilter()
                    ->addLimit();
            $collection->clear();
            
            if (count($collectionPageFilter->getAllIds()) < 1) {
                return;
            }

            /* @var object as the collection will always have one item
             * the rule with higher priroity 
             */
            $item = $collectionPageFilter->getFirstItem();
            if ($item->getExceptionIps() != null) {
                $exceptionIps = $item->getExceptionIps();

                if ($this->_geoipultimatelockHelper->isIpAnException($visitorIp, $exceptionIps)) {
                    return;
                }
            }

            if ($item->getErrorUrlRedirect() != '') {
                $observer->getControllerAction()
                    ->getResponse()
                    ->setRedirect($item->getErrorUrlRedirect());
            } else {
				echo "<span class='ip_blckr'>";
                printf(
                    '%s', $this->filterProvider
                        ->getPageFilter()
                    ->filter($item->getErrorMsg())
                );
				echo "</span>";
                exit;
            }
        }
    }

    
    protected function _isIpBlocked($ip)
    {
        return $this->_geoipultimatelockHelper->isIpBlocked($ip);
    }
    
    protected function _getBlockedIpMsg()
    {
        $msg = $this->_geoipultimatelockHelper->getBlockedIpMsg();
        return $this->filterProvider
                ->getPageFilter()
                ->filter($msg);
    }
}
