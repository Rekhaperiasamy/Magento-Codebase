<?php
namespace Orange\Catalog\Cron;
use \Psr\Log\LoggerInterface;

class Schedulecache {
    protected $logger;
    protected $_cacheTypeList;
	protected $_cacheFrontendPool;
    public function __construct(
    \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
    \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
	LoggerInterface $logger
	) {
	     //parent::__construct($context);
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->logger = $logger;
    }

/**
   * Write to system.log
   *
   * @return void
   */

    public function execute() {
	    $types = array('block_html','collections','full_page');
	    foreach ($types as $type) {
			$this->_cacheTypeList->cleanType($type);
	    }
	    foreach ($this->_cacheFrontendPool as $cacheFrontend) {
          $cacheFrontend->getBackend()->clean();
        }
		$_objectManager=\Magento\Framework\App\ObjectManager::getInstance();
		$_objectManager->get('Psr\Log\LoggerInterface')->debug('cache clean up executed');  
	    
    }

}