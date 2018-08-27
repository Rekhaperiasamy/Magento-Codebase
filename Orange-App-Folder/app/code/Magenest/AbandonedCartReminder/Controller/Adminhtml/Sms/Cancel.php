<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 27/06/2016
 * Time: 15:15
 */

namespace Magenest\AbandonedCartReminder\Controller\Adminhtml\Sms;

use Magento\Framework\Controller\ResultFactory;

use Magento\Backend\App\Action;

class Cancel extends Action
{
    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $ids = $this->getRequest()->getParam('id');

        if (is_numeric($ids)) {
            $Ids[] = $ids;
        } else {
            $Ids = $ids;
        }

        /*
            * @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect
        */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!is_array($Ids)) {
            $this->messageManager->addWarning('Please select item(s)');
        } else {
            try {
                foreach ($Ids as $id) {
                    /*
                        * @var  $mailModel \Magenest\AbandonedCartReminder\Model\Sms
                    */
                    $mailModel = $this->_objectManager->create('Magenest\AbandonedCartReminder\Model\Sms')->load($id);

                    $mailModel->setStatus(4)->save();
                }

                $this->messageManager->addSuccess(__('Total of %d record(s) were successfully cancelled', count($Ids)));
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }//end if

        $resultRedirect->setPath('*/*/index');
        return $resultRedirect;

    }//end execute()
}
