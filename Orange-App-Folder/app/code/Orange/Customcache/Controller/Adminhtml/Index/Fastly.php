<?php

namespace Orange\Customcache\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Fastly\Cdn\Model\PurgeCache;
use Fastly\Cdn\Model\Config;
use Psr\Log\LoggerInterface;

class Fastly extends \Magento\Backend\App\Action
{
    protected $purgeCache;
    protected $config;
    protected $storeManager;
    /** @var  LoggerInterface */
    protected $_logger;
    
    public function __construct(Context $context, PurgeCache $purgeCache, \Magento\Store\Model\StoreManagerInterface $storeManager, Config $config,LoggerInterface $logger)
    {
        parent::__construct($context);
        $this->purgeCache   = $purgeCache;
        $this->storeManager = $storeManager;
        $this->config       = $config;
        $this->_logger      = $logger;
    }
    
    public function execute()
    {
        try {
            
            $contentType = array(
                'text',
                'css',
                'script',
                'collections',
                'image'
            );
            
            
            foreach ($contentType as $type) {
                $result = $this->purgeCache->sendPurgeRequest($type);
                $this->_logger->debug("FCDN debug ",["result" => $result , "type" => $type]);
                if ($result) {
                    $this->getMessageManager()->addSuccessMessage(__('Fastly CDN has been cleaned - ' . $type));
                } else {
                    $this->getMessageManager()->addErrorMessage(__('The purge request was not processed successfully.' . $type));
                }
            }
            
            $store = array(
                '1',
                '2'
            );
            
            foreach ($store as $storeId) {
                $store = $this->storeManager->getStore($storeId);

                foreach ($store->getIdentities() as $tag) {
                    $result = $this->purgeCache->sendPurgeRequest($tag);
                    $this->_logger->debug("FCDN debug store ",["result" => $result , "tag" => $tag]);
                }
                if ($result) {
                    $this->getMessageManager()->addSuccessMessage(__('Fastly CDN has been cleaned - ' . $tag));
                } else {
                    $this->getMessageManager()->addErrorMessage(__('The purge request was not processed successfully.'));
                }
                
            }
            
        }
        catch (Exception $e) {
            $this->getMessageManager()->addErrorMessage($e->getMessage());
            $this->_logger->critical("FCDN Error", ['Exception' => $e,]);
        }
        return $this->_redirect('adminhtml/cache');
    }
    
}
