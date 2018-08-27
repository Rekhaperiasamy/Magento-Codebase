<?php
namespace Orange\Seo\Controller\Sales;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;
 
class Report extends \Magento\Framework\App\Action\Action
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
           echo  $objectManager->get('Orange\Seo\Helper\Salespad')->generatesalsepad();
    }
}
