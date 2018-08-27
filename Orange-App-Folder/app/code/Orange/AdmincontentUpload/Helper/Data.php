<?php

namespace Orange\AdmincontentUpload\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterfac
     **/
    protected $_scopeConfig;
    protected $_request;

    public function __construct(
    \Magento\Framework\App\Helper\Context $context, \Magento\Framework\App\Request\Http $request
    ) {
        parent::__construct($context);
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_request = $request;
    }

    public function logCreate($fileName, $data) {
        $writer = new \Zend\Log\Writer\Stream(BP . "$fileName");
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($data);
    }

    public function storeInfo() {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $manager = $om->get('Magento\Store\Model\StoreManagerInterface');
        return $manager;
    }
   
}