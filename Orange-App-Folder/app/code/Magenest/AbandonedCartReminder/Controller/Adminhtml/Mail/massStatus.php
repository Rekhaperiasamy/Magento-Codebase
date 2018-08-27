<?php
namespace Magenest\AbandonedCartReminder\Controller\Adminhtml\Mail;

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
        $mailIds = $this->getRequest()->getParam('mail');
        if (!is_array($mailIds) || empty($mailIds)) {
            $this->messageManager->addError(__('Please select mail(s).'));
        } else {
            try {
                $status = (int) $this->getRequest()->getParam('status');
                foreach ($mailIds as $mailId) {
                    $post = $this->_objectManager->get('Magenest\AbandonedCartReminder\Model\Mail')->load($mailId);
                    $post->setStatus($status)->save();
                }

                $this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been updated.', count($mailId))
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
        return $this->_authorization->isAllowed('Magenest_AbandonedCartReminder::mail');

    }//end _isAllowed()
}//end class
