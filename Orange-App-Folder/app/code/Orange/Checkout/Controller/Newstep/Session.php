<?php
namespace Orange\Checkout\Controller\Newstep;

class Session extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;
	protected $_checkoutSession;
	public function __construct(
       \Magento\Framework\App\Action\Context $context,
	   \Magento\Framework\View\Result\PageFactory $resultPageFactory,
	   \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
	   \Magento\Checkout\Model\Session $checkoutSession
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
		$this->resultJsonFactory = $resultJsonFactory;
		$this->_checkoutSession 	 = $checkoutSession;
    }
	
	public function objectToArray( $object ){
       if( !is_object( $object ) && !is_array( $object ) ){
    return $object;
      }
    if( is_object( $object ) ){
    $object = get_object_vars( $object );
    }
    return array_map( 'objectToArray', $object );
}
	
    /**
     * Flush cache storage
     *
     */
    public function execute()
    {
		$dataArray = array();
		if($this->getRequest()->getParam('aStep') == 'step1' || $this->getRequest()->getParam('aStep') == 'step2')
		{
			$dataObj =  json_decode($this->getRequest()->getParam('saveData'));
			foreach ($dataObj as $obj)
			{
				$dataArray[$obj->name] = $obj->value;
			}
			$this->_checkoutSession->setNewcheckout($dataArray);	
			return  $this->resultJsonFactory->create()->setData($dataArray); 
		}
   }

}