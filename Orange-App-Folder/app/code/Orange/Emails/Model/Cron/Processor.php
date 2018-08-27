<?php
namespace Orange\Emails\Model\Cron;
use Magento\Framework\App\ObjectManager;
class Processor
{	
	public function execute()
	{
			$objectManager = ObjectManager::getInstance();		
			$objectManager->get('Orange\Emails\Helper\Oracle')->getHardWareDetailss();
			$objectManager->get('Orange\Emails\Helper\Oracle')->getPrepaidDetails();
	}
}