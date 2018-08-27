<?php

namespace Orange\Reserve\Controller\Index;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\ResultFactory;

class Validation extends \Magento\Framework\App\Action\Action {

    public function execute() {
        if ($this->getRequest()->isAjax()) {
            $ipAddress = $this->getRequest()->getParam('ip');
            $objectManager = ObjectManager::getInstance();
            $helper = $objectManager->get('Orange\Reserve\Helper\Data');
            $myArray = explode(',', $helper->validationCriteria());

            if (count($myArray) > 1) {
                $returnValue = 'two';
            } else if (count($myArray) == 1) {
                if (in_array(2, $myArray)) {
                    $helperIntervalInDays = $helper->intervalDays();
                    $helperOneDayOrderQTY = $helper->intervalPeriodOrder();
                    $collection = $objectManager->create('Orange\Reserve\Model\Blacklist')->getCollection();
                    $collection->addFilter('ip_address', array('in' => $ipAddress));
                    $popItem = $collection->getFirstItem();

                    if ($popItem['ip_address'] == $ipAddress) {
                        if ($helperIntervalInDays == '1') {
                            $dateStart = date('Y-m-d' . ' 00:00:00');
                            $dateEnd = date('Y-m-d' . ' 23:59:59');
                        } else {
                            $dateStart = date('Y-m-d' . ' 00:00:00', strtotime($helperIntervalInDays . 'Days'));
                            $dateEnd = date('Y-m-d' . ' 23:59:59');
                        }

                        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
                        $connectionDb = $resource->getConnection();
                        $collectionReserve = "SELECT * FROM reserve_reserve WHERE created_date >= '" . $dateStart . "' and created_date <= '" . $dateEnd . "';";

                        $result = $connectionDb->fetchAll($collectionReserve);
                        if (count($result)) {
                            if (count($result) >= $helperOneDayOrderQTY) {
                                $returnValue = 'one';
                                foreach ($result as $res) {
                                    $ipAddressFetching = get_object_vars(json_decode($res['other_details']));
                                    if (isset($ipAddressFetching['ip_address']) &&  $ipAddress == $ipAddressFetching['ip_address']) {
                                        $returnValue = 'three';
                                    }
                                }
                            }
                        } else {
                            $returnValue = 'one';
                        }
                    } else {
                        $returnValue = 'one';
                    }
                } else {
                    $returnValue = 'one';
                }
            } else {
                $returnValue = 'one';
            }
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData($returnValue);
            return $resultJson;
        }else {
            echo __('This is Not An Ajax Call');
            header("Location: http://".$_SERVER['SERVER_NAME']);
            die();
        }
    }

}
