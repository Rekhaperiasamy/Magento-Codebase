<?php
namespace Orange\SalesRule\Cron;

class RuleUpdate
{    
    public function execute()
    {
        $ob = \Magento\Framework\App\ObjectManager::getInstance();
        $storeConfig = $ob->create('Magento\Framework\App\Config\ScopeConfigInterface');
        $log_mode = $storeConfig->getValue('custom_modulelog/modulelog__configuration/setting_mode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        
        try 
        {
            $date = date('m-d-Y');
                        if(isset($log_mode) && $log_mode==1){
                            $this->logCreate('/var/log/promotion_reports.log','Cron start');
                        }
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$collection = $objectManager->create('Magento\SalesRule\Model\Rule')->getCollection()
								->addFieldToFilter('is_active', array('eq' => '1'));
			$dataCount = count($collection);
                        if(isset($log_mode) && $log_mode==1){
                            $this->logCreate('/var/log/promotion_reports.log','Promotion reports rule count : ' .$dataCount);							
                        }
			foreach($collection as $coll)
			{
				$ruleId =  $coll['rule_id'];
				$todate =  $coll['to_date'];
				$currentDateTimePardot = date('Y-m-d H:i:s');
				$currentDate = date("Y-m-d", strtotime($currentDateTimePardot));
				$toDate = date('Y-m-d', strtotime($todate));
                                //if(isset($log_mode) && $log_mode==1){
                                    $this->logCreate('/var/log/promotion_reports.log','Promotion current date and to date : ' .$currentDate.'=='.$toDate );				
                                //}
				if($currentDate == $toDate)
				{
					$promotionUpdate = $objectManager->get('Magento\SalesRule\Model\Rule')->load($ruleId);
					$promotionUpdate->setIsActive(0);
					$promotionUpdate->save();
                                        if(isset($log_mode) && $log_mode==1){
                                            $this->logCreate('/var/log/promotion_reports.log','Decativated Rule : '.$ruleId);
                                        }
				}
			}
        }
        catch (\Exception $e) {
            if(isset($log_mode) && $log_mode==1){
                $this->logCreate('/var/log/promotion_reports.log',$e->getMessage());
            }
        }
    }

    public function logCreate($fileName, $data) 
    {
        $writer = new \Zend\Log\Writer\Stream(BP . "$fileName");
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($data);
    }
}