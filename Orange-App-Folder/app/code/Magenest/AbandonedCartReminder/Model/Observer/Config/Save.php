<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 17/06/2016
 * Time: 11:35
 */
namespace Magenest\AbandonedCartReminder\Model\Observer\Config;

use Magento\Customer\Model\Logger;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magenest\AbandonedCartReminder\Helper\Data as Helper;

class Save implements ObserverInterface
{

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var CustomerSetupFactory
     */
    protected $customerSetupFactory;


    /**
     * @param Logger               $logger
     * @param CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(
        Logger $logger,
        CustomerSetupFactory $customerSetupFactory
    ) {
        $this->logger               = $logger;
        $this->customerSetupFactory = $customerSetupFactory;

    }//end __construct()


    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /*
            * @var  $configData  \Magento\Framework\App\Config\Value
        */
        $configData = $observer->getEvent()->getConfigData();
        $path       = $configData->getPath();
        if ($path == Helper::XML_PATH_CONFIG_MOBILE_REQUIRED) {
            $value = $configData->getFieldsetDataValue('is_required');

            if ($value == '1') {
                $this->turnMobileToRequiredField();
            } else {
                $this->turnMobileToNotRequiredField();
            }
        }

    }//end execute()


    /**
     * turn mobile field to required field
     */
    public function turnMobileToRequiredField()
    {
        $customerSetup = $this->customerSetupFactory->create();
        $my_attribute  = $customerSetup->getEavConfig()->getAttribute(\Magento\Customer\Model\Customer::ENTITY, 'mobile_number');
        $my_attribute->setData('is_required', 1)->save();

    }//end turnMobileToRequiredField()


    /**
     * Turn mobile field to not required field
     */
    public function turnMobileToNotRequiredField()
    {
        $customerSetup = $this->customerSetupFactory->create();
        $my_attribute  = $customerSetup->getEavConfig()->getAttribute(\Magento\Customer\Model\Customer::ENTITY, 'mobile_number');
        $my_attribute->setData('is_required', 0)->save();

    }//end turnMobileToNotRequiredField()
}//end class
