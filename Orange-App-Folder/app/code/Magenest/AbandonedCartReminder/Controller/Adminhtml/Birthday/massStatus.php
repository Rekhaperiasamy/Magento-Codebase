<?php
namespace Magenest\AbandonedCartReminder\Controller\Adminhtml\Birthday;

use Magento\Backend\App\Action;

class MassStatus extends \Magento\Backend\App\Action
{


    /**
     * Update blog post(s) status action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $birthdayIds = $this->getRequest()->getParam('birthday');
        if (!is_array($birthdayIds) || empty($birthdayIds)) {
            $this->messageManager->addError(__('Please select birthday(s).'));
        } else {
            try {
                $status = (int) $this->getRequest()->getParam('active');
                foreach ($birthdayIds as $birthdayId) {
                    $post = $this->_objectManager->get('Magenest\AbandonedCartReminder\Model\Birthday')->load($birthdayId);
                    $post->setStatus($status)->save();
                }

                $this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been updated.', count($birthdayId))
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
