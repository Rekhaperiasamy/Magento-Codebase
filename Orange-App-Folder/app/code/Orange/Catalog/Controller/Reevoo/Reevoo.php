<?php

namespace Orange\Catalog\Controller\Reevoo;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;
 
class Reevoo extends \Magento\Framework\App\Action\Action
{
    protected $_resultPageFactory;
 
    public function __construct(Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory)
    {
        $this->_resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
 
    public function execute()
    {		
                $objectManager = ObjectManager::getInstance();		
		$reevoo = $objectManager->get('Orange\Catalog\Helper\ReevooFeedCsv')->generatesFeeds();        
		echo 'Reevoo feeds done';
    }
}
