<?php
//Test Commit
//Test file change

namespace Orange\Seo\Controller\Adminhtml\Reevoo;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Feed extends \Magento\Framework\App\Action\Action {

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;
    protected $_orderCollectionFactory;

    public function __construct(
    Context $context
    ) {        	
        parent::__construct($context);
    }

    public function execute() {
	$objectManager = ObjectManager::getInstance();		
	echo $objectManager->get('Orange\Catalog\Helper\ReevooFeedCsv')->generatesFeeds(); 
	$baseDir = $objectManager->get('\Magento\Store\Model\StoreManagerInterface')->getStore()->getBaseUrl();
	$baseDir =substr($baseDir,0,-4);	
	$reportFolder = $baseDir.'/pub/media/reevoo/';
	$pFileName = 'MBD_product.csv';	
	$pFileNameFr = 'MBF_product.csv';	
	$url = $baseDir.'/media/reevoo/'.$pFileName;	
	$urlFr=  $baseDir.'/media/reevoo/'.$pFileNameFr;	
	echo '<h3>Reevoo Feed Files Download</h3></br>';
	echo '<div style="float:left;background-color: #e7e7e7; /* Green */border: none;color: white;padding: 10px 32px;text-align: center;text-decoration: none;display: inline-block;font-size: 16px;margin: 4px 2px;cursor: pointer;-webkit-transition-duration: 0.4s; /* Safari */transition-duration: 0.4s;
	">
	<a href = "'.$url.'" download>Click to Download Reevoo Feed File for Nl</a></div>';	
	echo '<div style="float:left;background-color: #e7e7e7; /* Green */border: none;color: white;padding: 10px 32px;text-align: center;text-decoration: none;display: inline-block;font-size: 16px;margin: 4px 2px;cursor: pointer;-webkit-transition-duration: 0.4s; /* Safari */transition-duration: 0.4s;
	"><a href = "'.$urlFr.'" download>Click to Download Reevoo Feed File for Fr</a></div>';	
      }

	  
}
