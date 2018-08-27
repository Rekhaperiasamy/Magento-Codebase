<?php

namespace Orange\Upload\Controller\Adminhtml\Header;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Archive\Zip;

class Result extends Action {

    public $zip;
    public $context;
    public $store;

    public function __construct(
    \Magento\Framework\App\Action\Context $context
    ) {

        $this->zip = new Zip;
        $this->context = $context;

        return parent::__construct($context);
    }

    /**
     * Based on Zip file we create custom js,css,header files
     * 
     */
    public function execute() {
        if ($this->getRequest()->getParams('zip_file')) {

            try {
                /**
                 * zip_file => Name Given In PHTML File 
                 * 
                 */
                $filename = $_FILES["zip_file"]["name"];
                $source = $_FILES["zip_file"]["tmp_name"];
                $type = $_FILES["zip_file"]["type"];
                $name = explode(".", $filename);
				
				if(strpos(strtolower($filename), 'soho') !== false ) {
					$accepted_types = array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed');
					foreach ($accepted_types as $mime_type) {
						if ($mime_type == $type) {
							$okay = true;
							break;
						}
					}

					$continue = strtolower($name[1]) == 'zip' ? true : false;

					if (!$continue) {
						$this->messageManager->addError('The file you are trying to upload is not a .zip file. Please try again.');
					}
					$path = BP . '/lib/web/common-header/';

					$filenoextV = basename($filename, '.zip');
					$filenoext = basename($filenoextV, '.ZIP');
					$targetdir = $path . $filenoext;
					$targetzip = $path . $filename;

					if (is_dir($targetdir)) {
						$this->rmdir_recursive($targetdir);
					}

					//mkdir($targetdir, 0777);
					if (move_uploaded_file($source, $targetzip)) {
						$zip = new \ZipArchive();
						$x = $zip->open($targetzip);
						if ($x === true) {
							$zip->extractTo($targetdir);
							$zip->close();
							unlink($targetzip);
						}

						$commPath = $targetdir . '/common-header/';
						$this->headerFooterSoho($commPath);
						$this->moveContent($name[0]);
						$this->mergeFiles($commPath);
						$this->clear();
						$this->messageManager->addSuccess('Your .zip file was uploaded and unpacked.');
					} else {
						$this->messageManager->addError('There was a problem with the upload. Please try again.');
					}
				
				} else {
					$accepted_types = array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed');
					foreach ($accepted_types as $mime_type) {
						if ($mime_type == $type) {
							$okay = true;
							break;
						}
					}

					$continue = strtolower($name[1]) == 'zip' ? true : false;

					if (!$continue) {
						$this->messageManager->addError('The file you are trying to upload is not a .zip file. Please try again.');
					}
					$path = BP . '/lib/web/common-header/';

					$filenoextV = basename($filename, '.zip');
					$filenoext = basename($filenoextV, '.ZIP');
					$targetdir = $path . $filenoext;
					$targetzip = $path . $filename;

					if (is_dir($targetdir)) {
						$this->rmdir_recursive($targetdir);
					}

					//mkdir($targetdir, 0777);
					if (move_uploaded_file($source, $targetzip)) {
						$zip = new \ZipArchive();
						$x = $zip->open($targetzip);
						if ($x === true) {
							$zip->extractTo($targetdir);
							$zip->close();
							unlink($targetzip);
						}

						$commPath = $targetdir . '/common-header/';
						$this->headerFooter($commPath);
						$this->moveContent($name[0]);
						$this->mergeFiles($commPath);
						$this->clear();
						$this->messageManager->addSuccess('Your .zip file was uploaded and unpacked.');
					} else {
						$this->messageManager->addError('There was a problem with the upload. Please try again.');
					}
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
    public function moveContent($name){
	  $s1=$s2="";
	  $s1=BP.'/lib/web/common-header/css/b2c-global-header-footer.css';
	  $s2=BP.'/lib/web/common-header/css/custom.css';
	  exec('cp -r '.BP.'/lib/web/common-header/'.$name.'/common-header/* '.BP.'/lib/web/common-header/');
	  
	  if(file_exists($s1)){
	    copy($s1,$s2);
	  }
	
	}
	/* public function moveContentSoho($name){
	  $s1=$s2="";
	  $s1=BP.'/lib/web/common-header/css/b2c-global-header-footer.css';
	  $s2=BP.'/lib/web/common-header/css/custom.css';
	  exec('cp -r '.BP.'/lib/web/common-header/'.$name.'/common-header/* '.BP.'/lib/web/common-header/');
	  
	  if(file_exists($s1)){
	    copy($s1,$s2);
	  }
	
	}*/
    public function clear() {
	  exec('mv '.BP.'/lib/web/common-header/*  '.BP.'/pub/common-header/');
	}

    public function headerFooter($path) {
        try {
            $store_view = array('nl', 'fr');
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $identifier = array();
            foreach ($store_view as $view) {
                $identifier['header'] = 'header_static_' . $view;
                $identifier['footer'] = 'footer_static_' . $view;
                $file_header = file_get_contents($path . $view . '/header-body.html');
                $file_footer = file_get_contents($path . $view . '/footer-body.html');

                foreach ($identifier as $key => $identify) {
                    $block = $objectManager->create('Magento\Cms\Model\Block');
                    $block->load($identify, 'identifier');
                    if ($key == 'header') {
                        $block->setContent($file_header);
                    } else {
                        $block->setContent($file_footer);
                    }
                    $block->save();
                }
                $file_header = '';
                $file_footer = '';
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
	
	public function headerFooterSoho($path) {
        try {
            $store_view = array('nl', 'fr');
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $identifier = array();
            foreach ($store_view as $view) {
                $identifier['header'] = 'header_static_' . $view . '_soho';
                $identifier['footer'] = 'footer_static_' . $view . '_soho';				
                $file_header = file_get_contents($path . $view . '/header-body.html');
                $file_footer = file_get_contents($path . $view . '/footer-body.html');

                foreach ($identifier as $key => $identify) {
                    $block = $objectManager->create('Magento\Cms\Model\Block');
                    $block->load($identify, 'identifier');
                    if ($key == 'header') {
                        $block->setContent($file_header);
                    } else {
                        $block->setContent($file_footer);
                    }
                    $block->save();
                }
                $file_header = '';
                $file_footer = '';
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
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

    public function mergeFiles($targetdir) {
        $path = $targetdir;
        $this->fileMerger($path);
    }

    /**
     * 
     * $dir => path directory
     * This function iused for new custom file creation    
     */
    public function fileMerger($dir) {
	    
        $ext = array();

        $newfilePath = BP . '/lib/web/common-header/';

        $ext['css'] = $dir . 'css/';
        $ext['js'] = $dir . 'js/';
        // mkdir($targetdir, 0777);
        foreach ($ext as $key => $val) {
            $files = scandir($val);
            $fileExt = "custom." . $key;
            $out = fopen($newfilePath . $key . '/' . $fileExt, "w");
            $line = '';
			$newarray = '';
			if($key == 'js'){
				$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
				$scopeConfig = $objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');
				$configData = $scopeConfig->getValue('common/common_configuration/header_js_file_sequence');
				$predefarray = explode('||',$configData);
				$newarray = array_intersect($predefarray, $files);
				foreach ($newarray as $file) {					
						$in = fopen($val . $file, "r");
						$line .= fread($in, filesize($val . $file));
						fclose($in);					
				 }
			}else{
            foreach ($files as $file) {
                if ($file != "." && $file != "..") {
                    $in = fopen($val . $file, "r");
                    $line .= fread($in, filesize($val . $file));
                    fclose($in);
                }
            }
			}
           
            fwrite($out, $line);
            fclose($out);
        }
    }

}

?>
