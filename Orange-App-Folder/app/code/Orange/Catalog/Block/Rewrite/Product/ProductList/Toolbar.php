<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Orange\Catalog\Block\Rewrite\Product\ProductList;

use Magento\Framework\App\ObjectManager;
use Magento\Catalog\Helper\Product\ProductList;
use Magento\Catalog\Model\Product\ProductList\Toolbar as ToolbarModel;

/**
 * Product list toolbar
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Toolbar extends \Magento\Catalog\Block\Product\ProductList\Toolbar
{
	protected $_productCount;

    public function setCollection($collection) 
	{
        try {
			$this->_collection = $collection;				
			$this->_collection->setCurPage($this->getCurrentPage());

			// we need to set pagination only if passed value integer and more that 0
			$limit = (int)$this->getLimit();
			if ($limit) {
				$this->_collection->setPageSize($limit);
			}
			if($this->_getPriceFilter()!='')
			{
				$pricerange = explode("-", $this->_getPriceFilter());
				if($this->getCustomerTypeName() == 'SOHO') {
					if($pricerange[0] =='') {
						$this->_collection->getSelect()->where(new \Zend_Db_Expr("catalog_subscription.value/1.21 < ".$pricerange[1]));
					} else if($pricerange[1] =='') {
						$this->_collection->getSelect()->where(new \Zend_Db_Expr("catalog_subscription.value/1.21 > ".$pricerange[0]));
					} else {
						$ltValue = $pricerange[1] + 1;
						$gtValue = $pricerange[0] - 1;
						$this->_collection->getSelect()->where(new \Zend_Db_Expr("catalog_subscription.value/1.21 < ".$ltValue));
						$this->_collection->getSelect()->where(new \Zend_Db_Expr("catalog_subscription.value/1.21 > ".$gtValue));
					}
				}
				else 
				{
					if($pricerange[0] =='') {
						$this->_collection->addAttributeToFilter('subscription_amount', array('lt' => $pricerange[1]));
					} else if($pricerange[1] =='') {
						$this->_collection->addAttributeToFilter('subscription_amount', array('gt' => $pricerange[0]));
					} else {
						$ltValue = $pricerange[1] + 1;
						$gtValue = $pricerange[0] - 1;
						$this->_collection->addAttributeToFilter('subscription_amount', array('lt' => $ltValue))
								->addAttributeToFilter('subscription_amount', array('gt' => $gtValue));
					}
				}
			}
			if($this->_getAccfamilyFilter()!='')
			{
				$this->_collection->addAttributeToFilter('accessory_family', array(array('like' => '%'.$this->_getAccfamilyFilter().'%')));
			}
			if($this->_getSearchFilter()!='')
			{
				//$this->_collection->addAttributeToFilter('handset_family', array(array('like' => '%'.$this->_getSearchFilter().'%')));
				$this->_collection->addAttributeToFilter(
					array(
						array('attribute'=> 'handset_family','like' => '%'.$this->_getSearchFilter().'%'),
						array('attribute'=> 'handset_description','like' => '%'.$this->_getSearchFilter().'%')
					)
				);

			}
			if($this->_getBrandFilter()!='')
			{
				$this->_collection->addAttributeToFilter('brand',array('eq' => $this->_getBrandFilter()));
			}
			if($this->_getCategoryFilter()!='')
			{
				$this->_collection->addAttributeToFilter('category_id', array('in' => array('finset' => $this->_getCategoryFilter())));
			}		
			if ($this->_getCustomSort() != '' && $this->_getCustomSortDir() != '') {
				$subsAsc = 0;
				if ($this->_getCustomSort() == 'subscription'):
					//$sortoption = 'subscription_price';
					if($this->getCustomerTypeName() == 'SOHO') {
						$sortoption = 'min_soho_subsidy_price';
					} 
					else {
						$sortoption = 'min_subsidy_price';
					}
					if ($this->_getCustomSortDir() == 'desc') {
						$srtVal = 'desc';
					} else {
						$srtVal = 'asc';
						$subsAsc = 1;
					}
				elseif ($this->_getCustomSort() == 'popular'):
					$sortoption = 'popularity';
					$sortArray = array('sort=popular&dir=desc', 'sort=price&dir=asc', 'sort=price&dir=desc');
					if (in_array($this->_getCustomSortDir(), $sortArray)) {
						$srtVal = $this->_getCustomSortDir();
					} else {
						$srtVal = 'desc';
					}
				elseif ($this->_getCustomSort() == 'price'):
					$sortoption = 'final_price';
					if ($this->_getCustomSortDir() == 'desc') {
						$srtVal = 'desc';
					} else {
						$srtVal = 'asc';
					}					
				else:
					$sortoption = 'tier_price';
					if ($this->_getCustomSortDir() == 'desc') {
						$srtVal = 'desc';
					} else {
						$srtVal = 'asc';
					}
				endif;	
				$this->_collection->getSelect()->reset(\Zend_Db_Select::ORDER);
				if($subsAsc ==1) {
					$this->_collection->getSelect()->order(array('ISNULL('.$sortoption.')', $sortoption . ' ' . $srtVal));
				} else {
					$this->_collection->getSelect()->order($sortoption . ' ' . $srtVal);
				}				
			}
			else {
				$this->_collection->getSelect()->order('priority ASC'); // By Default sort products based on family priority
			   $this->_collection->getSelect()->order('popularity DESC');		
			}
			return $this;
        } 
		catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something Went Wrong.'));
            header("Location: http://" . $_SERVER['SERVER_NAME']);
            die();
		}
    }

	public function getStartingSubscriptionPrice($device) 
	{
        try {
			$upsellProducts = $this->getUpsellProducts($device);
			if (count($upsellProducts) > 0) {
				$subscriptionAmounts = array();
				foreach ($upsellProducts as $product):
					$subscriptions = $this->getSubscriptionProduct($product);
					foreach ($subscriptions as $subscriptionItem):
						$subscriptionAmounts[] = $subscriptionItem->getSubscriptionAmount();
					endforeach;
				endforeach;
				sort($subscriptionAmounts, SORT_NUMERIC); // sorts from least subscription amount
				return $subscriptionAmounts;
			}
			return false;
        } 
		catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something Went Wrong.'));
            header("Location: http://" . $_SERVER['SERVER_NAME']);
            die();
		}
    }
	
    public function getUpSellProducts(\Magento\Catalog\Model\Product $product) 
	{
        try {
			$pId = $product->getId();
			$objectManager = ObjectManager::getInstance();
			$product = $objectManager->create('Magento\Catalog\Model\Product')->load($pId);
			$upSellProductIds = $product->getUpSellProductIds();
			if(isset($upSellProductIds)){
				$products = array();
				foreach($upSellProductIds as $productId) {
					$product = $objectManager->get('Magento\Catalog\Model\Product')->load($productId);
					$products[] = $product;
					unset($product);
				}
				return $products;
			}
			return null;
        } 
		catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something Went Wrong.'));
            header("Location: http://" . $_SERVER['SERVER_NAME']);
            die();
		}
    }
	
	/**
     * Get the Subscription product
     * Virtual product
     * @return Object
     */
    public function getSubscriptionProduct($bundledProduct) 
	{
        try {
			$objectManager = ObjectManager::getInstance();
			$typeInstance = $bundledProduct->getTypeInstance();
			$requiredChildrenIds = $typeInstance->getChildrenIds($bundledProduct->getId(), true);
			$productIds = array();
			foreach ($requiredChildrenIds as $ids) {
				$productIds[] = $ids;
			}		
			$productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
			$collection = $productCollection->create()
				->addAttributeToSelect('*')
				->addAttributeToFilter('entity_id', array('in' => $productIds))
				->addAttributeToFilter('type_id', 'virtual')			
				->load();
			return $collection;
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something Went Wrong.'));
            header("Location: http://" . $_SERVER['SERVER_NAME']);
            die();
		}
    }
	
    private function _getCustomSort() 
	{
        $sort = $this->test_input($this->getRequest()->getParam('sort'));
		return $sort;
	}
	
    private function _getCustomSortDir() 
	{
        $dir = $this->test_input($this->getRequest()->getParam('dir'));
		return $dir;
	}
	
    private function _getBrandFilter() 
	{
        $brandId = $this->test_input($this->getRequest()->getParam('brand'));
		return $brandId;
	}
	
    private function _getSearchFilter() 
	{
        $search = $this->getRequest()->getParam('search');
		$data = trim($search);
        $data = strip_tags($data);
        $data = preg_replace('/[^A-Za-z0-9\. -]/', '', $data);
		return $data;
	}
	
    private function _getCategoryFilter() 
	{
        $categoryId = $this->test_input($this->getRequest()->getParam('family'));
		return $categoryId;
	}

    private function _getAccfamilyFilter() 
	{
        $search = $this->getRequest()->getParam('acc_family');
		$data = trim($search);
        $data = strip_tags($data);
        $data = preg_replace('/[^A-Za-z0-9\. -]/', '', $data);
		return $data;
	}

    private function _getPriceFilter() 
	{
        $pricerange = $this->test_input($this->getRequest()->getParam('subsprice'));
		return $pricerange;
	}

    function test_input($data) 
	{
        $data = trim($data);
        $data = strip_tags($data);
        $data = str_replace(' ', '-', $data);
        $data = preg_replace('/[^A-Za-z0-9\. -]/', '', $data);
        return $data;
	}
	
	/**
	 *	Get Context Name	
	 *
	 */
	public function getCustomerTypeName()
	{
		$objectManager = ObjectManager::getInstance();
		$customerGroupName = $objectManager->create('Orange\Customer\Model\Session')->getCustomerTypeName();		
		return $customerGroupName;		
	}
}