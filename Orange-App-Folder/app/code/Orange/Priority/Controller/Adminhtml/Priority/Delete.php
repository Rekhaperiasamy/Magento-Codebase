<?php

namespace Orange\Priority\Controller\Adminhtml\Priority;

class Delete extends \Magento\Backend\App\Action {

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute() {
        $id = $this->getRequest()->getParam('id');
        try {
            $banner = $this->_objectManager->get('Orange\Priority\Model\Priority')->load($id);
            $banner->delete();
            $this->messageManager->addSuccess(
                    __('Delete successfully !')
            );
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }

}
