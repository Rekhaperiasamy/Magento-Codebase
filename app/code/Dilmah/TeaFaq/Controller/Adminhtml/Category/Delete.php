<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Netstarter
 * @package     Dilmah_TeaFaq
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\TeaFaq\Controller\Adminhtml\Category;

use Dilmah\TeaFaq\Controller\Adminhtml\Category;

/**
 * Class Delete
 *
 * @package Dilmah\TeaFaq\Controller\Adminhtml\Category
 */
class Delete extends Category
{
    /**
     * Object Manager
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * Context
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->_objectManager = $context->getObjectManager();
        parent::__construct($context);
    }

    /**
     * Delete action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('category_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create('Dilmah\TeaFaq\Model\Category');
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccess(__('The category has been deleted.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {

                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['category_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find a category to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
