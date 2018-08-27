<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Orange\RegenSohoUrl\Observer;

use Magento\Catalog\Model\Category;
use Magento\CatalogUrlRewrite\Model\CategoryUrlRewriteGenerator;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\UrlRewrite\Model\UrlRewriteFactory;
use Magento\CatalogUrlRewrite\Observer\UrlRewriteHandler;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollection;

class CategoryProcessUrlRewriteSavingObserver extends \Magento\CatalogUrlRewrite\Observer\CategoryProcessUrlRewriteSavingObserver
{
    /** @var CategoryUrlRewriteGenerator */
    protected $categoryUrlRewriteGenerator;

    /** @var UrlPersistInterface */
    protected $urlPersist;

    /** @var UrlRewriteHandler */
    protected $urlRewriteHandler;
	
	/**
     * @var urlrewriteFactory
     */	 
	protected $_urlRewriteFactory;
	
	/**
     * @var urlrewriteCollection
     */	 
	protected $urlRewriteCollection;
	
    /**
     * @param CategoryUrlRewriteGenerator $categoryUrlRewriteGenerator
     * @param UrlPersistInterface $urlPersist
     * @param UrlRewriteHandler $urlRewriteHandler
     */
    public function __construct(
        CategoryUrlRewriteGenerator $categoryUrlRewriteGenerator,
        UrlPersistInterface $urlPersist,
        UrlRewriteHandler $urlRewriteHandler,
		UrlRewriteFactory $urlRewriteFactory,
		UrlRewriteCollection $urlRewriteCollection
    ) {
        $this->categoryUrlRewriteGenerator = $categoryUrlRewriteGenerator;
        $this->urlPersist = $urlPersist;
        $this->urlRewriteHandler = $urlRewriteHandler;
		$this->_urlRewriteFactory = $urlRewriteFactory;
		$this->urlRewriteCollection = $urlRewriteCollection;
    }

    /**
     * Generate urls for UrlRewrite and save it in storage
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var Category $category */
        $category = $observer->getEvent()->getCategory();
        if ($category->getParentId() == Category::TREE_ROOT_ID) {
            return;
        }
        if ($category->dataHasChangedFor('url_key')
            || $category->dataHasChangedFor('is_anchor')
            || $category->getIsChangedProductList()
        ) {
            $urlRewrites = array_merge(
                $this->categoryUrlRewriteGenerator->generate($category),
                $this->urlRewriteHandler->generateProductUrlRewrites($category)
            );
            $this->urlPersist->replace($urlRewrites);
			/** Delete Existing SOHO url REwrites **/
			$stores = array();
			foreach ($urlRewrites as $url) {
				$stores[] = $url->getStoreId();
			}
			$stores = array_unique($stores);
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			foreach($stores as $urlStore) {
				$urlRewrite = $this->urlRewriteCollection			
					->addFieldToFilter('entity_id', $category->getId())
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
				$urlRewrite->clear()->getSelect()->reset('where');				
			}	
				
			
			$objectManager->get('Psr\Log\LoggerInterface')->addDebug('SOHO category URL rewrites creation engaged');
			foreach ($urlRewrites as $url) {
				if($url->getRedirectType() != '301') {
					$requestPath = $url->getRequestPath();
					if (preg_match("/zelfstandigen/i", $requestPath) == 0 && preg_match("/independants/i", $requestPath) == 0) {
					
						if($url->getStoreId() == 1) {
							$requestPath = 'zelfstandigen/'.$url->getRequestPath();//Format SOHO URL
						}
						if($url->getStoreId() == 2) {
							$requestPath = 'independants/'.$url->getRequestPath();//Format SOHO URL
						}
						/** Remove Duplicate URL's **/
						$urlRewrite = $this->urlRewriteCollection
							->addFieldToFilter('request_path', array( "like"=>'%'.$requestPath.'%'))								
							->addFieldToFilter('store_id', $url->getStoreId())
							->addFieldToFilter('entity_type', 'custom');
						
						$urlRewrite = $this->urlRewriteCollection->load();							
						foreach($urlRewrite as $rewrites) {					
							$rewrites->delete();
						}
						$urlRewrite->clear()->getSelect()->reset('where');
						
						$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
						$connection = $resource->getConnection();
						$tableName = $resource->getTableName('url_rewrite');
						$sql = "Delete FROM " . $tableName." Where request_path = '".$requestPath."'";
						$objectManager->get('Psr\Log\LoggerInterface')->addDebug($sql);
						$connection->query($sql);
						
						$objectManager->get('Psr\Log\LoggerInterface')->addDebug($requestPath.'   -   '.$url->getTargetPath());							
						$this->generateSohoUrl($requestPath,$url->getTargetPath(),$url->getStoreId(),$category->getId());//Generate SOHO URL
					}
				}
			}
			$objectManager->get('Psr\Log\LoggerInterface')->addDebug('EOF SOHO Category URL Rewrite generation');							
        }
    }
	
	private function generateSohoUrl($requestPath,$targetPath,$storeId,$categoryId)
	{
		try { 
			$urlRewriteModel = $this->_urlRewriteFactory->create();
			/* set current store id */
			$urlRewriteModel->setStoreId($storeId);
			/* this url is not created by system so set as 0 */
			$urlRewriteModel->setIsSystem(0);
			/* unique identifier - set random unique value to id path */
			$urlRewriteModel->setEntityId($categoryId);
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
			$objectManager->get('Psr\Log\LoggerInterface')->addDebug('Duplicate Category URL '.$requestPath);			
			return;
		}
		
	}
}
