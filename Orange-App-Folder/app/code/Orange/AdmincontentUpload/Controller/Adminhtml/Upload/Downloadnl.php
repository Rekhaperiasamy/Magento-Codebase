<?php

namespace Orange\AdmincontentUpload\Controller\Adminhtml\Upload;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class Downloadnl extends \Magento\Backend\App\Action {

    public function __construct(\Magento\Backend\App\Action\Context $context, \Orange\Upload\Helper\Data $data) {
        parent::__construct($context);
        $this->_routingLog = $data;
        $this->_logFile = '/var/log/tranlationnl.log';
    }

    /**
     *  Download NL File
     * 
     */
    public function execute() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $scopeConfig = $objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');
        $log_mode = $scopeConfig->getValue('custom_modulelog/modulelog__configuration/setting_mode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        try {
            $filepath = BP . '/app/i18n/orange/nl_be/nl_BE.csv';


            if (!is_file($filepath) || !is_readable($filepath)) {
                if (isset($log_mode) && $log_mode == 1) {
                    $this->_routingLog->logCreate($this->_logFile, 'File is Not There');
                }
                $this->messageManager->addError('Please Insert File');
            } else {
                if (isset($log_mode) && $log_mode == 1) {
                    $this->_routingLog->logCreate($this->_logFile, 'Download Started');
                }
                $this->getResponse()
                        ->setHttpResponseCode(200)
                        ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
                        ->setHeader('Pragma', 'public', true)
                        ->setHeader('Content-type', 'application/force-download')
                        ->setHeader('Content-Length', filesize($filepath))
                        ->setHeader('Content-Disposition', 'attachment' . '; filename=' . basename($filepath));
                $this->getResponse()->clearBody();
                $this->getResponse()->sendHeaders();
                readfile($filepath);
                if (isset($log_mode) && $log_mode == 1) {
                    $this->_routingLog->logCreate($this->_logFile, 'Download ended');
                }
            }
        } catch (Exception $ex) {
            if (isset($log_mode) && $log_mode == 1) {
                $this->_routingLog->logCreate($this->_logFile, 'Please insert File');
            }
            $this->messageManager->addError('Please Insert File');
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('*/*/');
        return $resultRedirect;
    }

}

?>
