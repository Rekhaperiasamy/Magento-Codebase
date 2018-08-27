<?php
namespace Orange\Limitqty\Block;

use Magento\Framework\App\ObjectManager;
 
class Limit extends \Magento\Framework\View\Element\Template
{
 protected $_storeManager; 
 protected $_collectionFactory;
 
 public function __construct(
        \Magento\Backend\Block\Template\Context $context,
		\Orange\Limitqty\Model\ResourceModel\Limitqty\Collection $collectionFactory,
		array $data = array()
		 )
		 {
		 $this->_storeManager = $context->getStoreManager();
		 $this->_collectionFactory = $collectionFactory;
         parent::__construct($context, $data);		 
         }
public function _prepareLayout()
	{
		return parent::_prepareLayout();
	}
	
	protected function getConnection()
    {
        if (!$this->connection) {
            $this->connection = $this->_resource->getConnection('core_read');
        }
        return $this->connection;
    }
	
	public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }
	public function getCollection()
	{
    return $this->_collectionFactory->create()->getCollection();
    }
	
}