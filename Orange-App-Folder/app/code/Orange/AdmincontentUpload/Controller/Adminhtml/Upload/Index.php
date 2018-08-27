<?php
namespace Orange\AdmincontentUpload\Controller\Adminhtml\Upload;

class Index extends \Magento\Backend\App\Action {

    public function __construct(\Magento\Backend\App\Action\Context $context)
    {
        parent::__construct($context);
    }

    public function execute() {

        $this->_view->loadLayout();

        $this->_view->renderLayout();
    }
   
}
