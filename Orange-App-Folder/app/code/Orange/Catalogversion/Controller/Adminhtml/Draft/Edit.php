<?php
namespace Orange\Catalogversion\Controller\Adminhtml\Draft;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
	public function execute()
    {
		$id = $this->getRequest()->getParam('id');
		$model = $this->_objectManager->create('Orange\Catalogversion\Model\Catalogdraft');
		$registryObject = $this->_objectManager->get('Magento\Framework\Registry');
		if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This row no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
		$registryObject->register('catalogversion_catalogdraft', $model);
		$this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }
}
