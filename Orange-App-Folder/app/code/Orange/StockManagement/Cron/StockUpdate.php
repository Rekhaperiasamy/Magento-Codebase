<?php
namespace Orange\StockManagement\Cron;
class StockUpdate
{
    
    
    public function execute()
    {
        try {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $objectManager->create('Orange\StockManagement\Model\Stock')->updatestock();
            return $this;
        }
        catch (\Exception $e) {
            
        }
    }
}