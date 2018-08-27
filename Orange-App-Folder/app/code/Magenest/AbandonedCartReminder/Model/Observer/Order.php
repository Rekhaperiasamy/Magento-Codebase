<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 13/10/2015
 * Time: 13:20
 */
namespace Magenest\AbandonedCartReminder\Model\Observer;

use Psr\Log\LoggerInterface as Logger;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class Order extends \Magenest\AbandonedCartReminder\Model\Processor\AbandonedCartReminder implements ObserverInterface
{

    protected $_scopeConfig;


    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        Logger $logger
    ) {
        $this->_scopeConfig = $scopeConfig;

    }//end __construct()


    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function salesOrderPlaceAfterListener(\Magento\Framework\Event\Observer $observer)
    {
        /*
            * @var  $order \Magento\Sales\Model\Order
        */
        $order = $observer->getEvent()->getOrder();

        $incrementId = $order->getIncrementId();
        $data        = array('increment_id' => $incrementId);

        // $this->_salesOrder->sync($data);
    }//end salesOrderPlaceAfterListener()


    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /*
            * @var  $order \Magento\Sales\Model\Order
        */
        $order    = $observer->getEvent()->getOrder();
        $customer = $order->getCustomer();
        $storeId  = $order->getStoreId();

    }//end execute()


    /**
     * build email target and
     *
     * @return mixed
     */
    public function run()
    {
        // TODO: Implement run() method.
    }//end run()


    public function isValidate($rule)
    {
        // TODO: Implement isValidate() method.
    }//end isValidate()


    public function prepareMail()
    {
        // TODO: Implement prepareMail() method.
    }//end prepareMail()


    public function postCreateMail()
    {
        // TODO: Implement postCreateMail() method.
    }//end postCreateMail()
}//end class
