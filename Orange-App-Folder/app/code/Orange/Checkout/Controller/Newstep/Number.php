<?php
namespace Orange\Checkout\Controller\Newstep;
class Number extends \Magento\Framework\App\Action\Action
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

      if($this->getRequest()->getParam('te_existing_number_chk') != "")
       {
	   $te_existing_number_chk = $this->getRequest()->getParam('te_existing_number_chk');
       if (0 === strpos($te_existing_number_chk, '9')) {
         //Prepaid
		 $numbertype  =1;
        }
	   elseif(0 === strpos($te_existing_number_chk, '8'))	
	   {
	    //Postpaid
		 $numbertype  =2;
	   }
	   else
	   {
	    //Othersubcription
		 $numbertype  =3;
	   }
       
      return  $this->resultJsonFactory->create()->setData($numbertype);  
	
   }
   
   }

}