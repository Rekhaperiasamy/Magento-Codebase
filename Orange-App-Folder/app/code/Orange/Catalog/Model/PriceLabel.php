<?php
namespace Orange\Catalog\Model;
use Magento\Framework\App\ObjectManager;

class PriceLabel extends \Magento\Framework\Model\AbstractModel
{
	const NL_SOHO_LABEL = ' excl. BTW';
	const FR_SOHO_LABEL = ' HTVA';	
	protected $_objectManager;
	protected $_storeManager; 
	
	public function __construct(\Magento\Store\Model\StoreManagerInterface $storeManager)
	{
		$this->_storeManager = $storeManager;
	}
	
	/**
	 * Price Label for SOHO Customer
	 * @return String
	 */
	public function getPriceLabel($groupId)
	{		
		$storeCode = $this->getStoreCode();		
		if($groupId == 'SOHO')
		{ //SOHO Customer
			if($storeCode == 'nl')
			{ //NL
				$priceLabel = self::NL_SOHO_LABEL;
			}
			else
			{ //FR
				$priceLabel = self::FR_SOHO_LABEL;
			}
		}
		else
		{ //B2C Customer
			$priceLabel = '';
		}
		return $priceLabel;
	}
	private function getobjectManager() 
	{	
		if($this->_objectManager === null)
		{
			$objectManager = ObjectManager::getInstance();	
		}
        return $objectManager;
    }	
	
	public function getStoreCode()
    {
        return $this->_storeManager->getStore()->getCode();
    }
}