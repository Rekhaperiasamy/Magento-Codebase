<?php

namespace Orange\Customcache\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{
    
    public function __construct(Context $context, \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList, \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool)
    {
        parent::__construct($context);
        $this->_cacheTypeList     = $cacheTypeList;
        $this->_cacheFrontendPool = $cacheFrontendPool;
    }
    
    public function execute()
    {
        try {
            $types = array(
                'config',
                'layout',
                'block_html',
                'collections',
                'reflection',
                'db_ddl',
                'eav',
                'config_integration',
                'config_integration_api',
                'full_page',
                'translate',
                'config_webservice'
            );
            foreach ($types as $type) {
                $this->_cacheTypeList->cleanType($type);
            }
            foreach ($this->_cacheFrontendPool as $cacheFrontend) {
                $cacheFrontend->getBackend()->clean();
            }
            $this->clear();
            $this->messageManager->addSuccess('Magento Cache Purged Successfully');
            
        }
        catch (Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        return $this->_redirect('adminhtml/cache');
    }
    
    
    public function clear()
    {
        try {
            exec('php bin/magento cache:flush');
            exec('php bin/magento cache:clean');
        }
        catch (Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        return true;
    }
    
}
