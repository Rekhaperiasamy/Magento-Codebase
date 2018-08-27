<?php

namespace Orange\StockManagement\Block\Adminhtml;

class Stockimport extends \Magento\Framework\View\Element\Template
{
    
    protected $_storeManager;
    
    public function __construct(\Magento\Framework\View\Element\Template\Context $context)
    {
        $this->_storeManager = $context->getStoreManager();
        parent::__construct($context);
    }
    
    protected function _toHtml()
    {
        
        return parent::_toHtml();
    }
    
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }
    
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/csvupload');
    }
    
    public function storeData()
    {
        /** @var \Magento\Framework\ObjectManagerInterface $om */
        $om      = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magento\Store\Model\StoreManagerInterface $manager */
        $manager = $om->get('Magento\Store\Model\StoreManagerInterface');
        return $manager;
    }
    
}

