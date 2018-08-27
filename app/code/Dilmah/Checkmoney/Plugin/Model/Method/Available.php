<?php

namespace Dilmah\Checkmoney\Plugin\Model\Method;

class Available
{
    private $app_state;

    public function __construct(\Magento\Framework\App\State $app_state){
        $this->app_state = $app_state;
    }

    public function afterIsAvailable(\Magento\Payment\Model\Method\AbstractMethod $subject, $result)
    {
        $area_code  = $this->app_state->getAreaCode();
        //echo $area_code;
          // echo $subject->getcode; die();
        if($area_code != \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE){
            if($subject->getCode() == 'checkmo') {
                return false;
            }
        }
        return $result;
    }
}
