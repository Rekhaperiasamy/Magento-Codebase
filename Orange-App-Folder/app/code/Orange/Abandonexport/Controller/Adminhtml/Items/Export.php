<?php

namespace Orange\Abandonexport\Controller\Adminhtml\Items;


class Export extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
	}
}

?>