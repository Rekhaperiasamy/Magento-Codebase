<?php
namespace Orange\Selligent\Observer;
use Magento\Framework\Event\ObserverInterface;
use Orange\Selligent\Helper\Selligent;
use Magento\Framework\App\ObjectManager;

class OrderCapture implements ObserverInterface
{
    protected $_logger;
    protected $order;
    public function __construct(\Magento\Sales\Model\Order $order,
    \Psr\Log\LoggerInterface $logger, //log injection
    array $data = []
    ) {
    $this->_logger = $logger;
       // parent::__construct($data);
    }
    public function execute(\Magento\Framework\Event\Observer $observer)
    {    $objectManager = ObjectManager::getInstance();
		$orderD = $observer->getEvent()->getOrderIds();       
        //$objectManager->get('Orange\Selligent\Helper\Selligent')->csvGen($orderD);
   
         $objectManager->get('Orange\Selligent\Helper\Selligent')->doSelligentCall($orderD);
	
    } 

}