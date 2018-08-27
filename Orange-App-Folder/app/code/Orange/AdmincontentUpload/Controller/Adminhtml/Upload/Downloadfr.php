<?php
namespace Orange\AdmincontentUpload\Controller\Adminhtml\Upload;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class Downloadfr extends \Magento\Backend\App\Action {

    public function __construct(\Magento\Backend\App\Action\Context $context,\Orange\Upload\Helper\Data $data)
    {
        parent::__construct($context);
		$this->_routingLog 			= $data;
		$this->_logFile 			= '/var/log/tranlationfr.log';
    }
	 /**
     *  Download FR File
     * 
     */
  
     
	   public function execute()
    {
	$objectManager = ObjectManager::getInstance();
	$doc_root =   $this->directory_list->getRoot();
	$path          = $doc_root . '/app/i18n/orange/fr_fr/fr_FR.csv';
	$downloadfile  = $path;
    $attachment = file_get_contents($downloadfile);
	header('Content-type: text/xml');
    header('Content-Disposition: attachment; filename="'.$pFileName.'"');
    echo $attachment;
	exit;
    }
}


?>
