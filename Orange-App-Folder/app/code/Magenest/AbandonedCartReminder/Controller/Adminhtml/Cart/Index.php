<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 12/11/2015
 * Time: 00:37
 */

namespace Magenest\AbandonedCartReminder\Controller\Adminhtml\Cart;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action;


use Magento\Framework\App\ResponseInterface;

class Index extends Action
{


    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        // echo "Cart List";
        /*
            * @var \Magento\Backend\Model\View\Result\Page $resultPage
        */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Magenest_AbandonedCartReminder::abandonedcartreminder');
        $resultPage->getConfig()->getTitle()->prepend(__('Follow up Emails'));
        $resultPage->getConfig()->getTitle()->prepend(__('Abandoned Cart List'));
        $resultPage->addContent($resultPage->getLayout()->createBlock('Magenest\AbandonedCartReminder\Block\Adminhtml\Cart\Grid'));
        return $resultPage;

    }//end execute()

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_AbandonedCartReminder::cart');

    }//end _isAllowed()
}//end class
