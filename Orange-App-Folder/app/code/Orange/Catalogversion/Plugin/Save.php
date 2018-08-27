<?php
/**
 * @author Lex Beelen <lex@weprovide.com>
 */
//namespace Magento\Framework\View\Page\Title; 
use Magento\Framework\App;
namespace Orange\Catalogversion\Plugin;

class Save
{
    protected $_request;
    public function __construct(\Magento\Framework\App\RequestInterface $request){
      $this->_request = $request;
	}
    public function beforeExecute($data)
    {
	  $params=$this->_request->getParams();
	  $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
	  $backendSession = $objectManager->create('\Magento\Backend\Model\Session');
	  if(isset($params) && isset($params['save_type']) && $params['save_type']=='draft' ){
	     $backendSession->setPdSaveType('draft');
	   }else{
	     $backendSession->setPdSaveType('nodraft');
	   }
	 }
}