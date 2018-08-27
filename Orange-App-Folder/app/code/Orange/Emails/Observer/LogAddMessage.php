<?php
namespace Orange\Emails\Observer;
use Magento\Framework\Event\ObserverInterface;
use Orange\Emails\Helper\Emails;
use Magento\Framework\App\ObjectManager;

class LogAddMessage implements ObserverInterface
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
    { 
	
     $objectManager = ObjectManager::getInstance();
       $orderD = $observer->getEvent()->getOrderIds();
	   $items =  $objectManager->get('Magento\Checkout\Model\Session');       
       echo '<pre>'; print_r($items); 
	   print_r('kitten'); exit;
	   /*
       $objectManager->get('Orange\Emails\Helper\Emails')->getOrderDetails();    
       $objectManager->get('Orange\Emails\Helper\Emails')->entraMailProcess($orderD); */       
    } 

}