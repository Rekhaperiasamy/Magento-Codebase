<?php
namespace Magenest\AbandonedCartReminder\Controller\Adminhtml\Rule;

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
        $ruleIds = $this->getRequest()->getParam('rule');
        if (!is_array($ruleIds) || empty($ruleIds)) {
            $this->messageManager->addError(__('Please select rule(s).'));
        } else {
            try {
                $status = (int) $this->getRequest()->getParam('status');
                foreach ($ruleIds as $ruleId) {
                    $post = $this->_objectManager->get('Magenest\AbandonedCartReminder\Model\Rule')->load($ruleId);
                    $post->setStatus($status)->save();
                }

                $this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been updated.', count($ruleId))
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
