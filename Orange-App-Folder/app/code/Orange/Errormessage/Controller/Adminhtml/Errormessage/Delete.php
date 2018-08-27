<?php
namespace Orange\Errormessage\Controller\Adminhtml\Errormessage;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
		$id = $this->getRequest()->getParam('id');
		try {
				$banner = $this->_objectManager->get('Orange\Errormessage\Model\Errormessage')->load($id);
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
