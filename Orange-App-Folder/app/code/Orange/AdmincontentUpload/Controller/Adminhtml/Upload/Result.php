<?php

namespace Orange\AdmincontentUpload\Controller\Adminhtml\Upload;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class Result extends Action {

   
    public $context;
    public $store;
	protected $_fileCsv;

    public function __construct(
    \Magento\Framework\App\Action\Context $context,
	\Magento\Framework\File\Csv $fileCsv
    ) {
        
        $this->context = $context;
        $this->_fileCsv = $fileCsv;
        return parent::__construct($context);
    }

    /**
     * Based on Language Upload Files
     **/
    public function execute() {

        if ($this->getRequest()->getParams('zip_file')) {

            try {
                 
                $filename = $_FILES["zip_file"]["name"];
                $source = $_FILES["zip_file"]["tmp_name"];
                $type = $_FILES["zip_file"]["type"];
				$size = $_FILES['zip_file']['size'];
                $name = explode(".", $filename);
				$rowempty = 0;
				$lang = $this->getRequest()->getParam('language');
				$filenoextV = basename($filename);
				$filenoext = basename($filenoextV);
				
						$fh = fopen($_FILES['zip_file']['tmp_name'], 'r+');
                        if (!$fh) {
                            throw new Exception('File open failed. Please check the file permission');
                        }
                        $all_rows = array();
						$count_row = true;
						 while (($rownew = fgetcsv($fh, 1024, ",")) !== FALSE) {
     								if(count($rownew ) < 2 )
									{
									$rowempty = 1;
									}
									else if($rownew[0] == "" || $rownew[1] == ""  ) 
									{
									$rowempty = 1;
									}
                          }
						
                        $row = fgetcsv($fh, 9999);
                        fclose($fh);
						$row_count = count($row);
					
					$continue = strtolower($name[1]) == 'csv' ? true : false;
                    echo $size;
					echo "<br/>";
					echo $row_count;
				 
					if (!$continue) {
						$this->messageManager->addError('The file you are trying to upload is not a CSV file. Please try again.');
					}else if($rowempty == 1)
						 {
						 $this->messageManager->addError('The file you are trying to upload is not a Valid Translation file. Please try again.');
						 }else if($size != 0 && $row_count != 0){
						
					if($lang == 'nl'){
						if($filenoextV == 'nl_BE.csv'){	
							$path = BP . '/app/i18n/orange/nl_be/nl_BE.csv';
						}else{
							$this->messageManager->addError('File Name Miss Match or Wrong. Please Make sure the file name "nl_BE.csv"');
							$resultRedirect = $this->resultRedirectFactory->create();
							$resultRedirect->setPath('*/*/');
							return $resultRedirect;					
						}
					}else if($lang == 'fr'){
						if($filenoextV == 'fr_FR.csv'){	
							$path = BP . '/app/i18n/orange/fr_fr/fr_FR.csv';
						}else{
							$this->messageManager->addError('File Name Miss Match or Wrong. Please Make sure the file name "fr_FR.csv"');
							$resultRedirect = $this->resultRedirectFactory->create();
							$resultRedirect->setPath('*/*/');
							return $resultRedirect;					
						}
				  }

					$targetdir = $path;
					$targetpath = $path;

					if (is_dir($targetdir)) {
						$this->rmdir_recursive($targetdir);
					}
					if (move_uploaded_file($source, $targetpath)) {
						
					/**
					* If Need UTF8 Use utf8_encode
					**/	
					//$log = file_get_contents($targetpath);
					//file_put_contents($targetpath, utf8_encode($log));	  
				    //$this->clear();
					
						$this->messageManager->addSuccess('Your file was uploaded.Please Clear the Cache and Index');
					} else {
						$this->messageManager->addError('There was a problem with the upload. Please try again.');
					}
					}else{
			
						$this->messageManager->addError('Please Check The File Have Content.');
						$resultRedirect = $this->resultRedirectFactory->create();
						$resultRedirect->setPath('*/*/');
						return $resultRedirect;
					}
				
				
            } catch (Exception $ex) {

                $this->messageManager->addError('Please Insert File');
            }
        } else {
            $this->messageManager->addError('There was a problem with the upload. Please try again.');
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('*/*/');
        return $resultRedirect;
    }

    public function clear() {
        exec('php bin/magento cache:clean');
    }
    /**
     * While injection this function clear existing files
     * 
     */
    public function rmdir_recursive($dir) {
        foreach (scandir($dir) as $file) {
            if ('.' === $file || '..' === $file)
                continue;
            if (is_dir("$dir/$file"))
                $this->rmdir_recursive("$dir/$file");
            else
                unlink("$dir/$file");
        }

        rmdir($dir);
    }

}

?>
