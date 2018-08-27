<?php
namespace Orange\RegenSohoUrl\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollection;
use Magento\Store\Model\Store;
use Magento\UrlRewrite\Model\UrlRewriteFactory;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory as PageCollectionFactory;

class RegenerateSohoUrlCommand extends Command
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
     * @var ProductRepositoryInterface
     */
    protected $collection;
	
	/**
     * @var urlrewriteCollection
     */	 
	protected $urlRewriteCollection;
	
	/**
     * @var urlrewriteFactory
     */	 
	protected $_urlRewriteFactory;
	
	/**
     * @var PageCollectionFactory
     */
    protected $pageCollectionFactory;

    public function __construct(
        \Magento\Framework\App\State $state,
        Collection $collection,
        ProductUrlRewriteGenerator $productUrlRewriteGenerator,
        UrlPersistInterface $urlPersist,
		UrlRewriteCollection $urlRewriteCollection,
		UrlRewriteFactory $urlRewriteFactory,
		PageCollectionFactory $pageCollectionFactory
    ) {
        //$state->setAreaCode('adminhtml');
        $this->collection = $collection;
        $this->productUrlRewriteGenerator = $productUrlRewriteGenerator;
        $this->urlPersist = $urlPersist;
		$this->urlRewriteCollection = $urlRewriteCollection;
		$this->_urlRewriteFactory = $urlRewriteFactory;
		$this->pageCollectionFactory = $pageCollectionFactory;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('orange:regensohourl')
            ->setDescription('Regenerate url for SOHO context')
            ->addArgument(
                'pids',
                InputArgument::IS_ARRAY,
                'Products to regenerate'
            )
            ->addOption(
                'store', 's',
                InputOption::VALUE_REQUIRED,
                'Use the specific Store View',
                Store::DEFAULT_STORE_ID
            )
			->addOption(
                'type', 't',
                InputOption::VALUE_REQUIRED,
                'Enter URL Generation Type'                
            )
            ;
        return parent::configure();
    }

    public function execute(InputInterface $inp, OutputInterface $out)
    {
        $store_id = $inp->getOption('store');
		
		$UrlGenerationType = $inp->getOption('type');
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		if($UrlGenerationType == 'intermediate') {
			$urlRewrite = $this->urlRewriteCollection			
				->addFieldToFilter('target_path', array( "like"=>'%intermediate/listing%'))
				->addFieldToFilter('store_id', $store_id);
			$urlRewrite = $this->urlRewriteCollection->load();	
			
			foreach($urlRewrite as $rewrite) {	
				if($store_id == '2') {
					$requestPath = "independants/".$rewrite->getRequestPath();
				}
				else {
					$requestPath = "zelfstandigen/".$rewrite->getRequestPath();
				}				
				$objectManager->get('Psr\Log\LoggerInterface')->addDebug('*******************************************');
				$objectManager->get('Psr\Log\LoggerInterface')->addDebug($requestPath);
				
				$path = explode("/",$requestPath);
				$uniquePath = array_unique($path);
				$generatedPath = implode("/",$uniquePath);
				// $this->urlPersist->deleteByData(
					// [
						// UrlRewrite::ENTITY_ID => 0,
						// UrlRewrite::REQUEST_PATH => $generatedPath,
					// ]
				// );
				$objectManager->get('Psr\Log\LoggerInterface')->addDebug($generatedPath);
				$objectManager->get('Psr\Log\LoggerInterface')->addDebug($rewrite->getTargetPath());
				
								
				$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
				$connection = $resource->getConnection();
				$tableName = $resource->getTableName('url_rewrite');
				$sql = "Delete FROM " . $tableName." Where request_path = '".$generatedPath."'";
				$objectManager->get('Psr\Log\LoggerInterface')->addDebug($sql);
				$connection->query($sql);
				$objectManager->get('Psr\Log\LoggerInterface')->addDebug('*******************************************');
				$this->generateSohoUrl($generatedPath,$rewrite->getTargetPath(),$store_id);//Generate SOHO URL
				$out->writeln('<error>'.$generatedPath.' generated for '.$rewrite->getTargetPath().'</error>');
			}			
		}
		
		if($UrlGenerationType == 'static') {
			
			$collection = $this->pageCollectionFactory->create()						
						->addFieldToFilter('is_active', 1);
			foreach($collection as $page) {
				if($store_id == '2') {
					$requestPath = "independants/".$page->getIdentifier();
				}
				else {
					$requestPath = "zelfstandigen/".$page->getIdentifier();
				}
				
				$path = explode("/",$requestPath);
				$uniquePath = array_unique($path);
				$generatedPath = implode("/",$uniquePath);
				$this->urlPersist->deleteByData(
					[
						UrlRewrite::ENTITY_ID => 0,
						UrlRewrite::REQUEST_PATH => $generatedPath,
					]
				);
				$this->generateSohoUrl($generatedPath,$page->getIdentifier(),$store_id);//Generate SOHO URL
				$out->writeln('<error>'.$generatedPath.' generated for '.$page->getIdentifier().'</error>');				
			}
		}
		
		if($UrlGenerationType == 'others') {
			
			$pages[]='checkout';
			$pages[]='checkout/cart';
			$pages[]='checkout/index/session';
			$pages[]='checkout/onepage/success';
			$pages[]='checkout/cart/tempo';
			$pages[]='checkout/index/OrderRefusePage';
			
			foreach($pages as $pageUrl) {
				if($store_id == '2') {
					$requestPath = "independants/".$pageUrl;
				}
				else {
					$requestPath = "zelfstandigen/".$pageUrl;
				}
				
				$path = explode("/",$requestPath);
				$uniquePath = array_unique($path);
				$generatedPath = implode("/",$uniquePath);
				$this->urlPersist->deleteByData(
					[
						UrlRewrite::ENTITY_ID => 0,
						UrlRewrite::REQUEST_PATH => $generatedPath,
					]
				);
				$this->generateSohoUrl($generatedPath,$pageUrl,$store_id);//Generate SOHO URL
				$out->writeln('<error>'.$generatedPath.' generated for '.$pageUrl.'</error>');				
			}
		}
		
		if($UrlGenerationType == 'nintendo') {
			$this->collection->addFilter('type_id', 'bundle');
			$this->collection->addAttributeToSelect(['name','url_key','context_visibility','store_id']);
			$this->collection->addStoreFilter($store_id);
			$list = $this->collection->load();
			foreach($list as $product)
			{
				if($product->getContextVisibility() == '4' || $product->getContextVisibility() == '32000') {
					$targetPath = 'catalog/product/view/id/'.$product->getId();
					if($store_id == '2') {						
						$requestPath = "independants/".$product->getUrlKey();
					}
					else {						
						$requestPath = "zelfstandigen/".$product->getUrlKey();
					}
					$objectManager->get('Psr\Log\LoggerInterface')->addDebug($requestPath);
					$objectManager->get('Psr\Log\LoggerInterface')->addDebug($targetPath);
					
									
					$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
					$connection = $resource->getConnection();
					$tableName = $resource->getTableName('url_rewrite');
					$sql = "Delete FROM " . $tableName." Where request_path = '".$requestPath."'";
					$objectManager->get('Psr\Log\LoggerInterface')->addDebug($sql);
					$connection->query($sql);
					$objectManager->get('Psr\Log\LoggerInterface')->addDebug('*******************************************');
					$this->generateSohoUrl($requestPath,$targetPath,$store_id);//Generate SOHO URL					
					
					$out->writeln('<error>'.$requestPath.' generated for '.$targetPath.'-'.$product->getContextVisibility().'</error>');
				}else{
					$objectManager->get('Psr\Log\LoggerInterface')->addDebug("Failed Records ====>".$product->getId().'-'.$product->getContextVisibility());
				}
			}
		}
		/** Generate product URL rewrite **/
		
        // $pids = $inp->getArgument('pids');
        // if( !empty($pids) )
            // $this->collection->addIdFilter($pids);

        // $this->collection->addAttributeToSelect(['url_path', 'url_key']);
        // $list = $this->collection->load();
        // foreach($list as $product)
        // {
            // if($store_id === Store::DEFAULT_STORE_ID)
                // $product->setStoreId($store_id);

            // $this->urlPersist->deleteByData([
                // UrlRewrite::ENTITY_ID => $product->getId(),
                // UrlRewrite::ENTITY_TYPE => ProductUrlRewriteGenerator::ENTITY_TYPE,
                // UrlRewrite::REDIRECT_TYPE => 0,
                // UrlRewrite::STORE_ID => $store_id
            // ]);
            // try {
                // $this->urlPersist->replace(
                    // $this->productUrlRewriteGenerator->generate($product)
                // );
            // }
            // catch(\Exception $e) {
                // $out->writeln('<error>Duplicated url for '. $product->getId() .'</error>');
            // }
        // }
    }
	
	private function generateSohoUrl($requestPath,$targetPath,$storeId)
	{
		try {
		$urlRewriteModel = $this->_urlRewriteFactory->create();
		/* set current store id */
		$urlRewriteModel->setStoreId($storeId);
		/* this url is not created by system so set as 0 */
		$urlRewriteModel->setIsSystem(0);
		/* unique identifier - set random unique value to id path */
		$urlRewriteModel->setIdPath(rand(1, 100000000));
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
