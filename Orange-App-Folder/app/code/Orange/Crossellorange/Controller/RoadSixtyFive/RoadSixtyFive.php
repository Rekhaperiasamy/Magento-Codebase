<?php

namespace Orange\Crossellorange\Controller\RoadSixtyFive;
use Magento\Framework\App\ObjectManager;
class RoadSixtyFive extends \Magento\Framework\App\Action\Action
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
		$cityParam = $this->getRequest()->getParam('city');
		$szipcodeParam = $this->getRequest()->getParam('szipcode');
		$sstreetParam = $this->getRequest()->getParam('sstreet');
		$scityParam = $this->getRequest()->getParam('scity');
		if(isset($zipcodeParam) && !empty($zipcodeParam) && empty($streetParam)){		
			$zipcode = $zipcodeParam;				
			$objectManager = ObjectManager::getInstance();	
			$datas=$objectManager->get('Orange\Crossellorange\Helper\RoadSixtyFive')->getZipCity($zipcode);    
			echo json_encode($datas);exit;
		}
		if(isset($streetParam) && !empty($streetParam) && empty($cityParam)){
			$zipcode = $zipcodeParam;							
			$street = $streetParam;			
			$objectManager = ObjectManager::getInstance();	
			$datas=$objectManager->get('Orange\Crossellorange\Helper\RoadSixtyFive')->getStreetNameId($zipcode,$street);    						
			echo json_encode($datas);exit;
			
		}
	    if (isset($streetParam) && !empty($streetParam) && isset($cityParam) && !empty($cityParam) 
			&& isset($zipcodeParam) && !empty($zipcodeParam)) {
			$zipcode = $zipcodeParam;							
			$street = $streetParam;
			$city = $cityParam;
			$szipcode = $szipcodeParam;							
			$sstreet = $sstreetParam;
			$scity = $scityParam;
			$gotzip = 0;
			$gotcity = 0;	
			$gotszip = 0;
			$gotscity = 0;
			$objectManager = ObjectManager::getInstance();
			$citydatas=$objectManager->get('Orange\Crossellorange\Helper\RoadSixtyFive')->getZipCity($zipcode);   
 			foreach($citydatas as $key => $value)
			{
				if (is_array($value))
				foreach($value as  $innervalue)
				{
					if (is_array($innervalue)) {
						if (in_array( $zipcode, $innervalue ))
						$gotzip = $gotzip + 1;
						if (array_search(strtolower($city), array_map('strtolower', $innervalue)))
						$gotcity = $gotcity + 1;
					}
				} 
			}
			if (isset($szipcode) && !empty($szipcode)) {
				$scitydatas=$objectManager->get('Orange\Crossellorange\Helper\RoadSixtyFive')
				->getZipCity($szipcode); 
				foreach($scitydatas as $key => $value)
				{
					if (is_array($value))
					foreach($value as  $innervalue)
					{
						if (is_array($innervalue)) {
							if (in_array( $szipcode, $innervalue ))
							$gotszip = $gotszip + 1;
							if (array_search(strtolower($scity), array_map('strtolower', $innervalue)))
							$gotscity = $gotscity + 1;
						}
					}
				}
			}
			if ($gotzip == 0)
				$zipstatus = false;
			else if ($gotzip > 0)
				$zipstatus = true;
			if ($gotszip == 0)
				$szipstatus = false;
			else if ($gotszip > 0)
				$szipstatus = true;
			if ($gotcity == 0)
				$citystatus = false;
			else if ($gotcity > 0)
				$citystatus = true;
			if ($gotscity == 0)
				$scitystatus = false;
			else if ($gotscity > 0)
				$scitystatus = true;
			$gotstreet = 0;	
			$gotsstreet = 0;	
			$streetdatas=$objectManager->get('Orange\Crossellorange\Helper\RoadSixtyFive')
			->getStreetNameId($zipcode,$street);
			foreach($streetdatas as $key => $value)
			{
				if (is_array($value)) 
				foreach($value as  $innervalue)
				{
					if (is_array($innervalue)) {
						foreach ($innervalue as $key => $value)
						{
							if (mb_strtolower($street) == mb_strtolower($value)) {
								$gotstreet = $gotstreet + 1 ;
							}
						}
					}
				}
			} 
			if (isset($szipcode) && !empty($szipcode) && isset($sstreet) && !empty($sstreet)) {
				$sstreetdatas=$objectManager->get('Orange\Crossellorange\Helper\RoadSixtyFive')
				->getStreetNameId($szipcode,$sstreet);
				foreach($sstreetdatas as $skey => $svalue)
				{
					if (is_array($svalue))
					foreach($svalue as  $sinnervalue)
					{
						if (is_array($sinnervalue)) {
							foreach ($sinnervalue as $key => $value)
							{
								if (mb_strtolower($sstreet) == mb_strtolower($value)) {
									$gotsstreet = $gotsstreet + 1 ;
								}
							}
						}
					}
				}
			}	
			if ($gotstreet == 0)
			$streetstatus = false;
			else if ($gotstreet > 0)
			$streetstatus = true;
			if ($gotsstreet == 0)
			$sstreetstatus = false;
			else if ($gotsstreet > 0)
			$sstreetstatus = true;
			$output = array($zipstatus,$citystatus,$streetstatus,$szipstatus,$scitystatus,$sstreetstatus);
			$gotzip = 0;
			$gotcity = 0;
			$gotstreet = 0;
			$gotszip = 0;
			$gotscity = 0;
			$gotsstreet = 0;
			echo json_encode($output);
			exit;
		}
    }
}
