<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 29/09/2015
 * Time: 23:08
 */

namespace Magenest\AbandonedCartReminder\Controller\Adminhtml\Rule;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action;

class Index extends Action
{


    public function execute()
    {
        /*
            * @var \Magento\Backend\Model\View\Result\Page $resultPage
        */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Magenest_AbandonedCartReminder::abandonedcartreminder');
        $resultPage->getConfig()->getTitle()->prepend(__('Follow up Emails'));
        $resultPage->getConfig()->getTitle()->prepend(__('Rule'));
        $resultPage->addContent($resultPage->getLayout()->createBlock('Magenest\AbandonedCartReminder\Block\Adminhtml\Rule\Rule'));
        return $resultPage;

    }//end execute()

    /**
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_AbandonedCartReminder::rule');

    }//end _isAllowed()
}//end class
