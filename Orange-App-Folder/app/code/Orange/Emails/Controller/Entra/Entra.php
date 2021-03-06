<?php
namespace Orange\Emails\Controller\Entra;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;
 
class Entra extends \Magento\Framework\App\Action\Action
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

		$objectManager->get('Orange\Emails\Helper\Entra')->execute();


    }
}
