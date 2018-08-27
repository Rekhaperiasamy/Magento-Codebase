<?php
namespace Orange\Emails\Controller\Test;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;
 
class Emails extends \Magento\Framework\App\Action\Action
{
    protected $_resultPageFactory;
 
     public function __construct(Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory)
    {
        $this->_resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
 
    public function execute()
    {
		
	     $objectManager = ObjectManager::getInstance();
	   /*$storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
	   $storeId = $storeManager->getStore()->getId();
       $model = $objectManager->create('Orange\Errormessage\Model\Errormessage');            
	   $tableCollection = $model->getCollection()        
	    ->addFieldToFilter('code', array('eq' => '02'))
        ->addFieldToFilter('store_id', array('eq' => $storeId));                
					
		foreach($tableCollection as $collection){			
		echo $collection->getData('message'); 
		}exit;*/
		/*$storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface');
		$currentStore = $storeManager->getStore();		
		$currentStoreName = $storeManager->getStore()->getName();					
		if($currentStoreName == 'Dutch'){ 
			echo __('Vos informations personnelles ne seront pas utilisées à dautres effets. Consultez notre <a href=https://corporate.orange.be/nl/privacy-policy>politique de confidentialité.</a>');	
		} else {
			echo __('Vos informations personnelles ne seront pas utilisées à dautres effets. Consultez notre <a href=https://corporate.orange.be/fr/vie-privee>politique de confidentialité.</a>');	
		}	 				*/
		//echo "<a href='".$link_address."'>politique de confidentialité.</a>";				
		
		$orderD = 100001418;//bundle product new subscription		
		$orderD = 100001440;
		$orderD = 100001442;
		$orderD = 100001998;
		$orderD = 100001999;
		//$orderD = 100001995;
		$orderD = 100001527;
		//$orderD = 100001066;
		//$orderD = 100001439;
		//$orderD = 100001436;
	//$orderD = 100001439;
		//$orderD = 100001428;
		//bundle product new subscription		
		//$orderD = 100000184; //virtual product new
		//$orderD = 100000165; //2 virtual 1 simple //3 individual products
		$objectManager->get('Orange\Emails\Helper\Oracle')->getHardWareDetailss(); 
		$objectManager->get('Orange\Emails\Helper\Oracle')->getPrepaidDetails(); 		
		//$objectManager->get('Orange\Emails\Helper\Emails')->entraMailProcess($orderD,'Acc');
		//$objectManager->get('Orange\Emails\Helper\orderMail')->entraMailProcess($orderD);
		echo 'Routing file gererated';

    }
}
