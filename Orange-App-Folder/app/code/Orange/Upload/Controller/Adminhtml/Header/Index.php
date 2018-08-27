<?php
namespace Orange\Upload\Controller\Adminhtml\Header;

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
