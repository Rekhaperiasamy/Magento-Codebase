<?php
namespace Orange\Promo\Controller\Adminhtml\Promo;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
		$id = $this->getRequest()->getParam('id');
		try {
				$banner = $this->_objectManager->get('Orange\Promo\Model\Promo')->load($id);
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
