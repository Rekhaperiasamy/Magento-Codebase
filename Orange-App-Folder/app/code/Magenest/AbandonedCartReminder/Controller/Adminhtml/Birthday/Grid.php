<?php
namespace Magenest\AbandonedCartReminder\Controller\Adminhtml\Birthday;

class Grid extends \Magento\Backend\App\Action
{


    public function execute()
    {
        $resultLayout = $this->_resultLayoutFactory->create();
        return $resultLayout;

    }//end execute()

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_AbandonedCartReminder::birthday');

    }//end _isAllowed()
}//end class
