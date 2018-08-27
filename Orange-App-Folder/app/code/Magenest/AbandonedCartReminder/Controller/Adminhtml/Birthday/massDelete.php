<?php
namespace Magenest\AbandonedCartReminder\Controller\Adminhtml\Birthday;

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
        $birthdayIds = $this->getRequest()->getParam('birthday');
        if (!is_array($birthdayIds) || empty($birthdayIds)) {
            $this->messageManager->addError(__('Please select birthday(s).'));
        } else {
            try {
                foreach ($birthdayIds as $postId) {
                    $post = $this->_objectManager->get('Magenest\AbandonedCartReminder\Model\Birthday')->load($postId);
                    $post->delete();
                }

                $this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been deleted.', count($birthdayIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/index');

    }//end execute()

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_AbandonedCartReminder::birthday');

    }//end _isAllowed()
}//end class
