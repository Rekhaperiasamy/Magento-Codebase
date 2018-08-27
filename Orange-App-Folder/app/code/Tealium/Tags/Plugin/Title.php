<?php
/**
 * @author Lex Beelen <lex@weprovide.com>
 */
//namespace Magento\Framework\View\Page\Title; 
use Magento\Framework\App;
namespace Tealium\Tags\Plugin;

class Title
{
    public function __construct(){
      
	}
    public function afterSet($title)
    {
	  $sessionTitle=$title->get();
	  $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();        
      $catalogSession = $objectManager->create('\Magento\Catalog\Model\Session');
	  //$catalogSession->unsCustomTitle();
	  $catalogSession->setCustomTitle($sessionTitle);
	  return $title;
    }
}