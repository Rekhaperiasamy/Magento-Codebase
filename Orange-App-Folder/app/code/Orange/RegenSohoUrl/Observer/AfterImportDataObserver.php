<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Orange\RegenSohoUrl\Observer;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\CatalogImportExport\Model\Import\Product as ImportProduct;
use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;
use Magento\Framework\Event\Observer;
use Magento\Framework\App\ResourceConnection;
use Magento\ImportExport\Model\Import as ImportExport;
use Magento\Store\Model\Store;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Service\V1\Data\UrlRewriteFactory;
use Magento\UrlRewrite\Model\OptionProvider;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Model\Product\Visibility;
use Magento\UrlRewrite\Model\UrlRewriteFactory as RewriteFactory;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollection;
/**
 * Class AfterImportDataObserver
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AfterImportDataObserver extends \Magento\CatalogUrlRewrite\Observer\AfterImportDataObserver
{
    /**
     * Url Key Attribute
     */
    const URL_KEY_ATTRIBUTE_CODE = 'url_key';

    /** @var \Magento\CatalogUrlRewrite\Service\V1\StoreViewService */
    protected $storeViewService;

    /** @var \Magento\Catalog\Model\Product */
    protected $product;

    /** @var array */
    protected $productsWithStores;

    /** @var array */
    protected $products = [];

    /** @var \Magento\CatalogUrlRewrite\Model\ObjectRegistryFactory */
    protected $objectRegistryFactory;

    /** @var \Magento\CatalogUrlRewrite\Model\ObjectRegistry */
    protected $productCategories;

    /** @var UrlFinderInterface */
    protected $urlFinder;

    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $storeManager;

    /** @var UrlPersistInterface */
    protected $urlPersist;

    /** @var UrlRewriteFactory */
    protected $urlRewriteFactory;

    /** @var \Magento\CatalogImportExport\Model\Import\Product */
    protected $import;

    /** @var \Magento\Catalog\Model\ProductFactory $catalogProductFactory */
    protected $catalogProductFactory;

    /** @var array */
    protected $acceptableCategories;

    /** @var \Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator */
    protected $productUrlPathGenerator;

    /** @var array */
    protected $websitesToStoreIds;

    /** @var array */
    protected $storesCache = [];

    /** @var array */
    protected $categoryCache = [];

    /** @var array */
    protected $websiteCache = [];

    /** @var array */
    protected $vitalForGenerationFields = [
        'sku',
        'url_key',
        'url_path',
        'name',
        'visibility',
    ];
	
	/**
     * @var rewriteFactory
     */	 
	protected $_rewriteFactory;
	
	/**
     * @var urlrewriteCollection
     */	 
	protected $urlRewriteCollection;
	
    /**
     * @param \Magento\Catalog\Model\ProductFactory $catalogProductFactory
     * @param \Magento\CatalogUrlRewrite\Model\ObjectRegistryFactory $objectRegistryFactory
     * @param \Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator $productUrlPathGenerator
     * @param \Magento\CatalogUrlRewrite\Service\V1\StoreViewService $storeViewService
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param UrlPersistInterface $urlPersist
     * @param UrlRewriteFactory $urlRewriteFactory
     * @param UrlFinderInterface $urlFinder
     * @throws \InvalidArgumentException
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Catalog\Model\ProductFactory $catalogProductFactory,
        \Magento\CatalogUrlRewrite\Model\ObjectRegistryFactory $objectRegistryFactory,
        \Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator $productUrlPathGenerator,
        \Magento\CatalogUrlRewrite\Service\V1\StoreViewService $storeViewService,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        UrlPersistInterface $urlPersist,
        UrlRewriteFactory $urlRewriteFactory,
        UrlFinderInterface $urlFinder,
		RewriteFactory $rewriteFactory,
		UrlRewriteCollection $urlRewriteCollection
    ) {
        $this->urlPersist = $urlPersist;
        $this->catalogProductFactory = $catalogProductFactory;
        $this->objectRegistryFactory = $objectRegistryFactory;
        $this->productUrlPathGenerator = $productUrlPathGenerator;
        $this->storeViewService = $storeViewService;
        $this->storeManager = $storeManager;
        $this->urlRewriteFactory = $urlRewriteFactory;
        $this->urlFinder = $urlFinder;
		$this->_rewriteFactory = $rewriteFactory;
		$this->urlRewriteCollection = $urlRewriteCollection;
    }

    /**
     * Action after data import.
     * Save new url rewrites and remove old if exist.
     *
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        $this->import = $observer->getEvent()->getAdapter();
        if ($products = $observer->getEvent()->getBunch()) {
            foreach ($products as $product) {
                $this->_populateForUrlGeneration($product);
				// meta title genrate dynamically
				//$this->metaGeneration($product);
            }
            $productUrls = $this->generateUrls();
						
            if ($productUrls) {
                $this->urlPersist->replace($productUrls);
				
				$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
				$objectManager->get('Psr\Log\LoggerInterface')->addDebug('AFTER IMPORT DATA OBSERVER');
				$objectManager->get('Psr\Log\LoggerInterface')->addDebug('SOHO product import URL rewrites creation engaged');
				foreach ($productUrls as $url) {
					if($url->getRedirectType() != '301') {					
						$requestPath = '';
						if($url->getStoreId() == 1) {
							$requestPath = 'zelfstandigen/'.$url->getRequestPath();//Format SOHO URL
						}
						if($url->getStoreId() == 2) {
							$requestPath = 'independants/'.$url->getRequestPath();//Format SOHO URL
						}
						/** Remove Duplicate URL Rewrites **/
						$urlRewrite = $this->urlRewriteCollection
							->addFieldToFilter('request_path', array( "like"=>'%'.$requestPath.'%'))								
							->addFieldToFilter('store_id', $url->getStoreId())
							->addFieldToFilter('entity_type', 'custom');
						
						$urlRewrite = $this->urlRewriteCollection->load();							
						foreach($urlRewrite as $rewrites) {					
							$rewrites->delete();
						}
						$urlRewrite->clear()->getSelect()->reset('where');
						$objectManager->get('Psr\Log\LoggerInterface')->addDebug($requestPath.'   -   '.$url->getTargetPath());
						
						$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
						$connection = $resource->getConnection();
						$tableName = $resource->getTableName('url_rewrite');
						$sql = "Delete FROM " . $tableName." Where request_path = '".$requestPath."'";
						$objectManager->get('Psr\Log\LoggerInterface')->addDebug($sql);
						$connection->query($sql);
						
						$this->generateSohoUrl($requestPath,$url->getTargetPath(),$url->getStoreId(),$url->getEntityId());//Generate SOHO URL
					}
				}
				$objectManager->get('Psr\Log\LoggerInterface')->addDebug('EOF SOHO product import URL Rewrite generation');	
				$objectManager->get('Psr\Log\LoggerInterface')->addDebug('EOF AFTER IMPORT DATA OBSERVER');				
			}
        }
    }
	
	private function generateSohoUrl($requestPath,$targetPath,$storeId,$productId)
	{
		try { 
			$urlRewriteModel = $this->_rewriteFactory->create();
			/* set current store id */
			$urlRewriteModel->setStoreId($storeId);
			/* this url is not created by system so set as 0 */
			$urlRewriteModel->setIsSystem(0);
			/* unique identifier - set random unique value to id path */
			$urlRewriteModel->setEntityId($productId);
			/* set type of rewrite */
			$urlRewriteModel->setEntityType('custom');
			/* set actual url path to target path field */
			$urlRewriteModel->setTargetPath($targetPath);
			/* set requested path which you want to create */			
			$urlRewriteModel->setRequestPath($requestPath);
			/* set current store id */
			$urlRewriteModel->save();		
		}
		catch(\Exception $e) {
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();			
			$objectManager->get('Psr\Log\LoggerInterface')->addDebug('Duplicate Product Import URL '.$requestPath);			
			return;
		}
		
	}
	
	public function metaGeneration($product) {
		$categoryName = '';
		$metaTitle = '';
		$id = '';
		if (isset($product['sku'])) {
			$products = $this->catalogProductFactory->create();
			$id = $products->getIdBySku($product['sku']);
		} else {
			return true;
		}
		if (isset($product['name']) && $product['name'] && $id) {
		     $productload = $products->load($id);
			 $iconimageid = $productload->getProductIconImage();
			 $attr = $productload->getResource()->getAttribute('product_icon_image');
			 if ($attr->usesSource() && $iconimageid) {
				$categoryName = $attr->getSource()->getOptionText($iconimageid);
			 }
			if ($categoryName) {
				$categoryName = ucfirst($categoryName);
			}
			if ($categoryName == "smartphone") {
				$categoryName = 'Smartphones';
			} 
			if ($categoryName) {
				$metaTitle = $product['name'].' - '.$categoryName." | Orange";
			} else {
				$metaTitle = $product['name'].$categoryName." | Orange";
			}
			if (isset($product['store_view_code']) && $product['store_view_code']=='fr') {
				$productload->setStoreId(2);
			} else if(isset($product['store_view_code']) && $product['store_view_code']=='nl') {
				$productload->setStoreId(1);
			} else {
				$productload->setStoreId(0);
			}
			$productload->setMetaTitle($metaTitle);
			$productload->save();
		}
	}

}
