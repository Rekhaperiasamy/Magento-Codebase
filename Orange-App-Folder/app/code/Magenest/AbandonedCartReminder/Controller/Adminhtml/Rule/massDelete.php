<?php
namespace Magenest\AbandonedCartReminder\Controller\Adminhtml\Rule;

use Magento\Backend\App\Action;

/**
 * Class MassDelete
 */
class MassDelete extends \Magento\Backend\App\Action
{


    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $blogpostIds = $this->getRequest()->getParam('rule');
        if (!is_array($blogpostIds) || empty($blogpostIds)) {
            $this->messageManager->addError(__('Please select rule(s).'));
        } else {
            try {
                foreach ($blogpostIds as $postId) {
                    $post = $this->_objectManager->get('Magenest\AbandonedCartReminder\Model\Rule')->load($postId);
                    $post->delete();
                }

                $this->messageManager->addSuccess(
                    __('A total of %d record(s) have been deleted.', count($blogpostIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/index');

    }//end execute()

    /**
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_AbandonedCartReminder::rule');

    }//end _isAllowed()
}//end class
