<?php

namespace Orange\Crossellorange\Controller\RoadSixtyFiveCheckout;
use Magento\Framework\App\ObjectManager;
class RoadSixtyFiveCheckout extends \Magento\Framework\App\Action\Action
{

	/**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $_cacheTypeList;

    /**
     * @var \Magento\Framework\App\Cache\StateInterface
     */
    protected $_cacheState;

    /**
     * @var \Magento\Framework\App\Cache\Frontend\Pool
     */
    protected $_cacheFrontendPool;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\Cache\StateInterface $cacheState
     * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
       \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\StateInterface $cacheState,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheState = $cacheState;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->resultPageFactory = $resultPageFactory;
    }
	
    /**
     * Flush cache storage
     *
     */
    public function execute()
    {
		$zipcodeParam = $this->getRequest()->getParam('zipcode');
		$streetParam = $this->getRequest()->getParam('street');
		if(isset($zipcodeParam) && !empty($zipcodeParam) && empty($streetParam)){			
			$zipcode = $zipcodeParam;				
			$objectManager = ObjectManager::getInstance();			
			$datas=$objectManager->get('Orange\Crossellorange\Helper\RoadSixtyFive')->getZipCityCheckout($zipcode);    
			echo json_encode($datas);exit;
		}
		if(isset($streetParam) && !empty($streetParam)){
			$zipcode = $zipcodeParam;							
			$street = $streetParam;			
			$objectManager = ObjectManager::getInstance();	
			$datas=$objectManager->get('Orange\Crossellorange\Helper\RoadSixtyFive')->getStreetNameId($zipcode,$street);    						
			echo json_encode($datas);exit;
			
		}
		/*$objectManager = ObjectManager::getInstance();	
		echo 'Mail Testing=>';
		$orderId = '52';
		$objectManager->get('Orange\Emails\Helper\Emails')->entraMailProcess($orderId);
	    exit;*/		
        
    }
}