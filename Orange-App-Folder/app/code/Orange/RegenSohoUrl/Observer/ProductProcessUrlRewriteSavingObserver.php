<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Orange\RegenSohoUrl\Observer;

use Magento\Catalog\Model\Product;
use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\Framework\Event\ObserverInterface;
use Magento\UrlRewrite\Model\UrlRewriteFactory;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollection;

class ProductProcessUrlRewriteSavingObserver extends \Magento\CatalogUrlRewrite\Observer\ProductProcessUrlRewriteSavingObserver
{
    /**
     * @var ProductUrlRewriteGenerator
     */
    protected $productUrlRewriteGenerator;

    /**
     * @var UrlPersistInterface
     */
    protected $urlPersist;
	
	/**
     * @var urlrewriteCollection
     */	 
	protected $urlRewriteCollection;
	
	/**
     * @var urlrewriteFactory
     */	 
	protected $_urlRewriteFactory;
    /**
     * @param ProductUrlRewriteGenerator $productUrlRewriteGenerator
     * @param UrlPersistInterface $urlPersist
     */
    public function __construct(
        ProductUrlRewriteGenerator $productUrlRewriteGenerator,
        UrlPersistInterface $urlPersist,
		UrlRewriteFactory $urlRewriteFactory,
		UrlRewriteCollection $urlRewriteCollection
    ) {
        $this->productUrlRewriteGenerator = $productUrlRewriteGenerator;
        $this->urlPersist = $urlPersist;
		$this->_urlRewriteFactory = $urlRewriteFactory;
		$this->urlRewriteCollection = $urlRewriteCollection;
    }

    /**
     * Generate urls for UrlRewrite and save it in storage
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$objectManager->get('Psr\Log\LoggerInterface')->addDebug('PRODUCT PROCESS URL REWRITE SAVING OBSERVER');
        /** @var Product $product */
        $product = $observer->getEvent()->getProduct();

        if ($product->dataHasChangedFor('url_key')
            || $product->getIsChangedCategories()
            || $product->getIsChangedWebsites()
            || $product->dataHasChangedFor('visibility')
        ) {
            $this->urlPersist->deleteByData([
                UrlRewrite::ENTITY_ID => $product->getId(),
                UrlRewrite::ENTITY_TYPE => ProductUrlRewriteGenerator::ENTITY_TYPE,
                UrlRewrite::REDIRECT_TYPE => 0,
                UrlRewrite::STORE_ID => $product->getStoreId()
            ]);

            if ($product->isVisibleInSiteVisibility()) {
				$urls = $this->productUrlRewriteGenerator->generate($product);
				$this->urlPersist->replace($urls);
				/** Delete Existing SOHO url REwrites **/
				$stores = array();
				foreach ($urls as $url) {
					$stores[] = $url->getStoreId();
				}
				$stores = array_unique($stores);
				$objectManager->get('Psr\Log\LoggerInterface')->addDebug('SOHO product URL rewrites creation engaged');
				foreach($stores as $urlStore) {
					$urlRewrite = $this->urlRewriteCollection			
					->addFieldToFilter('entity_id', $product->getId())
					->addFieldToFilter('store_id', $urlStore)
					->addFieldToFilter('entity_type', 'custom');
					if($urlStore == 1) {
						$urlRewrite->addFieldToFilter('request_path', array( "like"=>'%zelfstandigen/%'));
					}
					else {
						$urlRewrite->addFieldToFilter('request_path', array( "like"=>'%independants/%'));
					}
					$urlRewrite = $this->urlRewriteCollection->load();	
	
					foreach($urlRewrite as $rewrites) {					
						$rewrites->delete();
					}					
				}
				
				foreach ($urls as $url) {	
					if($url->getRedirectType() != '301') {
						$requestPath = $url->getRequestPath();
						if($url->getStoreId() == 1) {
							$requestPath = 'zelfstandigen/'.$url->getRequestPath();//Format SOHO URL
						}
						if($url->getStoreId() == 2) {
							$requestPath = 'independants/'.$url->getRequestPath();//Format SOHO URL
						}										
						$objectManager->get('Psr\Log\LoggerInterface')->addDebug($requestPath.'   -   '.$url->getTargetPath());	
						
						$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
						$connection = $resource->getConnection();
						$tableName = $resource->getTableName('url_rewrite');
						$sql = "Delete FROM " . $tableName." Where request_path = '".$requestPath."'";
						$objectManager->get('Psr\Log\LoggerInterface')->addDebug($sql);
						$connection->query($sql);
						
						$this->generateSohoUrl($requestPath,$url->getTargetPath(),$url->getStoreId(),$product->getId());//Generate SOHO URL	
					}
				}				                
				$objectManager->get('Psr\Log\LoggerInterface')->addDebug('EOF SOHO product URL Rewrite generation');
            }
        }
    }
	
	private function generateSohoUrl($requestPath,$targetPath,$storeId,$productId)
	{
		try { 
			$urlRewriteModel = $this->_urlRewriteFactory->create();
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
			$objectManager->get('Psr\Log\LoggerInterface')->addDebug('Duplicate Product URL '.$requestPath);			
			return;
		}
		
	}
}
