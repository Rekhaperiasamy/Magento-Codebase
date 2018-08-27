<?php
namespace Dilmah\Orderemail\Observer;

use Magento\Framework\Event\ObserverInterface;

class AddExtraDataToTransport implements ObserverInterface
{  
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $transport = $observer->getEvent()->getTransport();
		$order = $transport->getOrder();
		$transport['is_pickup'] = $order->getShippingMethod() == "regionshipping_regionshipping";
        $transport['is_delivery'] = $order->getShippingMethod() == "tablerate_bestway";
   
    }
}