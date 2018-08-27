<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 20/11/2015
 * Time: 16:09
 */

namespace Magenest\AbandonedCartReminder\Controller\Adminhtml\Rule;

class Delete extends \Magenest\AbandonedCartReminder\Controller\Adminhtml\Rule
{


    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if (!$id) {
            $this->messageManager->addError(__('Please select rule'));
        } else {
            try {
                $post = $this->_objectManager->get('Magenest\AbandonedCartReminder\Model\Rule')->load($id);
                $post->delete();
                $this->messageManager->addSuccess(
                    __('The rule have been deleted.')
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
